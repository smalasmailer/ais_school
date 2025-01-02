<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if(isset($_COOKIE["acclogin"])){
        header("Location: ../loginsuccess.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Этап 3. Привязка профиля</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <?php require $_SERVER["DOCUMENT_ROOT"] . "/header.php" ?>
    <h2>Готово</h2>
    <p>Ваш профиль был настроен и готов к работе.</p>
    <a href="../index.php"><button>Вернуться на страницу входа</button></a>
</body>
</html>