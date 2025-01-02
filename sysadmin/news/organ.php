<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: /");
        exit();
    }
    $author = $currentfullname . " (сис. админ.)";
    if(isset($_GET["header"], $_GET["text"],$_GET["organ"])){
        $header = $_GET["header"];
        $text = $_GET["text"];

        $res = $conn->query("SELECT `orgshort` FROM `schools` WHERE `organ` = '$_GET[organ]'");
        while($row = $res->fetch_assoc()){
            $res1 = $conn->query("SELECT `id` FROM `posts` WHERE `school` = '$row[orgshort]'");
            $id = $res1->num_rows + 1;
            $conn->query("INSERT INTO `posts`(`id`, `header`, `text`, `author`, `school`) VALUES('$id', '$header', '$text', '$author', '$row[orgshort]')");
            $conn->query("INSERT INTO `logs`(`user`, `action`) VALUES('$currentfullname', 'Публикация новости всем школам ОУ: $school ($director: $directoremail)')");
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отправить новость школам ОУ</title>
</head>
<body>
    <?php require "../adminheader.php"; ?>
    <form method="get">
        <select name="organ">
            <?php
                $res = $conn->query("SELECT `orgshort` FROM `organs`");
                while($row = $res->fetch_assoc()){
                    echo "<option value='$row[orgshort]'>$row[orgshort]</option>";
                }
            ?>
        </select><br>
        <input type="text" name="header" placeholder="Заголовок"><br>
        <textarea name="text" placeholder="Текст новости"></textarea><br>
        <input type="submit" value="Отправить">
    </form>
</body>
<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: /");
        exit();
    }
    $author = $currentfullname . " (сис. админ.)";
    if(isset($_GET["header"], $_GET["text"],$_GET["organ"])){
        $header = $_GET["header"];
        $text = $_GET["text"];

        $res = $conn->query("SELECT `orgshort` FROM `schools` WHERE `organ` = '$_GET[organ]'");
        while($row = $res->fetch_assoc()){
            $res1 = $conn->query("SELECT `id` FROM `posts` WHERE `school` = '$row[orgshort]'");
            $id = $res1->num_rows + 1;
            $conn->query("INSERT INTO `posts`(`id`, `header`, `text`, `author`, `school`) VALUES('$id', '$header', '$text', '$author', '$row[orgshort]')");
            $conn->query("INSERT INTO `logs`(`user`, `action`) VALUES('$currentfullname', 'Публикация новости всем школам ОУ: $school ($director: $directoremail)')");
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отправить новость школам ОУ</title>
</head>
<body>
    <?php require "../adminheader.php"; ?>
    <form method="get">
        <select name="organ">
            <?php
                $res = $conn->query("SELECT `orgshort` FROM `organs`");
                while($row = $res->fetch_assoc()){
                    echo "<option value='$row[orgshort]'>$row[orgshort]</option>";
                }
            ?>
        </select><br>
        <input type="text" name="header" placeholder="Заголовок"><br>
        <textarea name="text" placeholder="Текст новости"></textarea><br>
        <input type="submit" value="Отправить">
    </form>
</body>
</html>