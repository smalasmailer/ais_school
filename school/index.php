<?php
    require "../config.php";
    if($currentrole == "Ученик"){
        header("Location: ../children");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Школьник.Сайт</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php 
        require "schoolhead.php";    ?>
    <?php
        if($currentrole == "Учитель"){
            header("Location: teacher.php");
        } elseif($currentrole == "Директор"){
            header("Location: admin.php");
        } elseif($currentrole == "Завуч"){
            header("Location: zavuch.php");
        }
        exit();
    ?>
</body>
</html>