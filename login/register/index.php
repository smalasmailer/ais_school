<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if(isset($_COOKIE["acclogin"])){
        header("Location: ../loginsuccess.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <?php require "../../header.php" ?>
    <h2>Регистрация аккаунта</h2><br>
    <?php
        if(isset($_GET["error"])){
            if($_GET["error"] == "notexists"){
                echo '<p>Ошибка: <span style="background-color: lightcoral; padding: 5px;">Аккаунт не найден</span></p><br>';
            }
        }
    ?>
    <p>Логин и пароль выдаются администраторами организации</p>
    <form method="post" action="step2.php">
        <input type="text" name="login" placeholder="Логин"><br>
        <input type="password" name="password" placeholder="Пароль"><br>
        <input type="submit" value="Далее">
    </form>
</body>
</html>