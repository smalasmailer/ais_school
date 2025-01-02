<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
        exit();
    }
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET["fullname"], $_GET["login"], $_GET["password"])){
            $fullname = $_GET["fullname"];
            $login = $_GET["login"];
            $password = $_GET["password"];

            $res = $conn->query("SELECT * FROM `users` WHERE `login` = '$login'");
            if($res->num_rows>0){
                die("Пользователь с логином '$login' уже существует. <a href='index.php'>Назад</a>");
            }

            $conn->query("INSERT INTO `users`(`login`, `password`, `role`, `school`, `fullname`, `avatarurl`) VALUES('$login', '$password', 'Активист', ' ', '$fullname', ' ')");
            $conn->query("INSERT INTO `logs`(`user`, `action`) VALUES('$currentfullname', 'Создание нового активиста: $fullname')");
            header("Location: /sysadmin/activists/");
            exit();
        }
    }