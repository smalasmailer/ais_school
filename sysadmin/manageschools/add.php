<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"] . "/index.html");
        exit();
    }
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["director"], $_POST["directoremail"], $_POST["orgfull"], $_POST["orgshort"], $_POST["funddate"], $_POST["orgtype"], $_POST["login"], $_POST["password"])){
            $director = $_POST["director"];
            $directoremail = $_POST["directoremail"];
            $orgfull = $_POST["orgfull"];
            $orgshort = $_POST["orgshort"];
            $funddate = $_POST["funddate"];
            $orgtype = $_POST["orgtype"];
            $login = $_POST["login"];
            $password = $_POST["password"];

            $conn->query("INSERT INTO `schools`(`director`, `directorlogin`, `directoremail`, `orgfull`, `orgshort`, `funddate`, `orgtype`, `isblog`, `organ`) VALUES('$director', '$login', '$directoremail', '$orgfull', '$orgshort', '$funddate', '$orgtype', 0, 'Школа')");
            $conn->query("INSERT INTO `users`(`login`, `password`, `role`, `school`, `fullname`, `avatarurl`) VALUES('$login', '$password', 'Директор', '$orgshort', '$director', 'https://zornet.ru/_fr/19/5561097.jpg')");
            $conn->query("INSERT INTO `logs`(`user`, `action`) VALUES('$currentfullname', 'Создание школы: $orgshort ($director: $directoremail)')");
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить школу</title>
</head>
<body>
    <?php require "../adminheader.php"; ?>
    <h2>Добавление школы</h2>
    <form method="post">
        <input type="text" name="director" placeholder="ФИО директора"><br>
        <input type="text" name="directoremail" placeholder="Почта директора"><br>
        <input type="text" name="orgfull" placeholder="Полное наименование"><br>
        <input type="text" name="orgshort" placeholder="Краткое наименование"><br>
        <input type="date" name="funddate">*<br>
        <select name="orgtype">
            <option value="" disabled selected>Тип организации</option>
            <option value="СШ">СШ</option>
            <option value="Гимназия">Гимназия</option>
            <option value="Лицей">Лицей</option>
        </select><br>
        <input type="text" name="login" placeholder="Логин директора"><br>
        <input type="text" name="password" placeholder="Пароль директора"><br>
        <input type="submit" value="Добавить"><br>
        <p>*дата основания</p>
    </form>
</body>
</html>