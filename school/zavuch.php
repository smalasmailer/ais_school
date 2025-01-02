<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Завуч"){
        header("Location: ../index.html");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Завуч</title>
</head>
<body>
    <?php require "zavuchheader.php"; ?><br>
    <h2>Здравствуйте, <?php echo $currentfullname; ?></h2>
    <p>Выберите действие в верхнем меню</p>
</body>
</html>