<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: /");
        exit();
    }
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST["header"], $_POST["text"])) {
            $header = $_POST["header"];
            $text = $_POST["text"];
            $author = $currentfullname . " (сис. админ.)";
        
            $res = $conn->query("SELECT `orgshort` FROM `schools`");
            while ($row = $res->fetch_assoc()) {
                $res1 = $conn->query("SELECT `id` FROM `posts` WHERE `school` = '$row[orgshort]'");
                $id = $res1->num_rows + 1;
                $conn->query("INSERT INTO `posts` (`id`, `header`, `text`, `author`, `school`) VALUES ('$id', '$header', '$text', '$author', '$row[orgshort]')");
                $conn->query("INSERT INTO `logs`(`user`, `action`) VALUES('$currentfullname', 'Публикация поста для всех школ: $header ($text)')");
            }
        }
        
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новости для всех школ</title>
</head>
<body>
    <?php require "../adminheader.php"; ?>
    <form method="post">
        <input type="text" name="header" placeholder="Заголовок"><br>
        <textarea name="text" placeholder="Текст новости"></textarea><br>
        <input type="submit" value="Отправить">
    </form>
</body>
</html>