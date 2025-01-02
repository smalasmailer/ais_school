<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Ученик"){
        header("Location: /");
        exit();
    }

    $conn->query("UPDATE `students` SET `tg` = NULL WHERE `login` = '$acclogin'");
    header("Location: profile.php");
    exit();