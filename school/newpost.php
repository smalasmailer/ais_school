<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Учитель" && $currentrole != "Директор" && $currentrole != "Завуч"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["header"], $_POST["text"])){
            $header = $_POST["header"];
            $text = $_POST["text"];
            $sql = "INSERT INTO `posts` (`header`, `text`, `author`, `school`) VALUES ('{$header}', '{$text}', '$currentfullname', '$currentschool')";
            $conn->query($sql);
            header("Location: teacher.php");
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить запись</title>
</head>
<body>
    <?php require "teacherhead.php"; ?>
    <form method="post">
        <input type="text" name="header" placeholder="Заголовок"><br>
        <textarea name="text" placeholder="Укажите текст записи"></textarea> (вы можете использоват HTML)<br>
        <input type="text" value="<?php echo $currentfullname; ?>"><br>
        <input type="submit" value="Создать">
    </form>
</body>
</html>