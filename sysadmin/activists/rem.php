<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET["fullname"])){
            $fullname = $_GET["fullname"];

            $conn->query("DELETE FROM `users` WHERE `role` = 'Активист' AND `fullname` = '$fullname'");
            $conn->query("INSERT INTO `logs`(`user`, `action`) VALUES('$currentfullname', 'Удаление активиста: $fullname')");
            header("Location: /sysadmin/activists");
            exit();
        }
    }