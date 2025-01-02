<?php
    require "../../config.php";
    if(!$license){
        header("Location: blocked.php");
        exit();
    } 
    if(isset($_GET["director"]) && isset($_GET["directoremail"]) && isset($_GET["orgfull"]) && isset($_GET["orgshort"]) && isset($_GET["funddate"]) && isset($_GET["orgtype"]) && isset($_GET["login"]) && isset($_GET["password"])){
        $director = $_GET["director"];
        $directoremail = $_GET["directoremail"];
        $orgfull = $_GET["orgfull"];
        $orgshort = $_GET["orgshort"];
        $funddate = $_GET["funddate"];
        $orgtype = $_GET["orgtype"];
        $login = $_GET["login"];
        $password = $_GET["password"];

        $conn->query("INSERT INTO `schools`(`director`, `directorlogin`, `directoremail`, `orgfull`, `orgshort`, `funddate`, `orgtype`, `isblog`, `organ`) VALUES ('$director', '$login', '$directoremail', '$orgfull', '$orgshort', '$funddate', '$orgtype', '0', '$organname');");
        $conn->query("INSERT INTO `users`(`login`, `password`, `role`, `school`, `fullname`, `avatarurl`) VALUES ('$login', '$password', 'Директор', '$orgshort', '$director', '');");
        header("Location: ../index.html");
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Орган управления</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <?php require "../organheader.php"; ?>
    <?php
    if($currentrole != "ОУ"){
        header("Location: ../../index.html");
    }
    
    ?>
    <h1>Панель управления органа «<?php echo $organname;?>»</h1>
    <?php
        if($license){
            echo "<p><font color='green'>У вас имеется разрешение на создание школ</font></p>";
        } else{
            echo "<p><font color='red'>У вас нет разрешения на создание школ</font></p>";
        }
    ?>
    <center><form method="get" style="max-width:300px;">
        <input type="text" name="director" placeholder="ФИО директора" required>
        <input type="email" name="directoremail" placeholder="Email директора" required>
        <input type="text" name="orgfull" placeholder="Полное наименование организации" required>
        <input type="text" name="orgshort" placeholder="Краткое наименование организации" required>
        <input type="date" name="funddate" style="width:50%;" required> Дата основания<br>
        <select name="orgtype" style="width:100%;" required>
            <option value="Выберите тип организации" disabled>Выберите тип организации</option>
            <option value="СШ">СШ</option>
            <option value="Гимназия">Гимназия</option>
            <option value="Лицей">Лицей</option>
        </select>
        <hr>
        <input type="hidden" name="organ" value="">
        <input type="text" name="login" placeholder="Логин директора">
        <input type="password" name="password" placeholder="Пароль директора">
        <input type="submit" value="Создать">
    </form></center>
</body>
</html>