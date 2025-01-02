<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "ОУ"){
        header("Location: /");
        exit();
    }
    if(isset($_GET["school"])){
        $school = $_GET["school"];
        $res = $conn->query("SELECT `organ` FROM `schools` WHERE `orgshort` = '$school'");
        $organ = $res->fetch_assoc()["organ"];

        if($organ == $organname){
            $conn->query("UPDATE `schools` SET `isfreeze` = 1 WHERE `orgshort` = '$school'");
        }
        die("<script>window.history.go(-1)</script>");
    }
?>