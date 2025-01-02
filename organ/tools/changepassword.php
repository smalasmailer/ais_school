<?php
    require "../../config.php";
    if($currentrole != "ОУ"){
        header("Location:../index.php");
    }
    $dirlogin = $_POST["directorlogin"];
    $dirpassword = $_POST["newpassword"];

    $res = $conn->query("UPDATE `users` SET `password`='$dirpassword' WHERE `login` = '$dirlogin'");
    if($res){
        header("Location: ../index.html");
    }