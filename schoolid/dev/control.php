<?php
    require "getinfo.php";
    getDevLogin();

    if(isset($_GET["app"])){
        $app = $_GET["app"];
    } else{
        header("Location: index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление приложением</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <?php require "header.php" ?>
    <h1>Управление приложением</h1>
</body>
</html>