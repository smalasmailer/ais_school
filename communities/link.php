<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Директор" && $currentrole != "Завуч"){
        header("Location: /");
        exit();      
    }
    if (isset($_GET["community"])) {
        $publicid = $_GET["community"];

        $res = $conn->query("SELECT `login` FROM `users` WHERE `fullname` = '$currentfullname'");
        $login = $res->fetch_assoc()["login"];

        $isAuthor = false;

        if ($_COOKIE["acclogin"] == $login) {
            $isAuthor = true;
        }

        if(!$isAuthor){
            header("Location: index.php");
            exit();
        }

        $conn->query("UPDATE `schools` SET `community` = '$publicid' WHERE `orgshort` = '$currentschool'");
        header("Location: settings.php?id=$publicid");
        exit();
    }
?>