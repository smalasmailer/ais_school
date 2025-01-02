<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Директор" && $currentrole != "Учитель" && $currentrole != "Завуч"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
        exit();
    }
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET["id"])){
            $header = $_GET["id"];
            $res = $conn->query("SELECT `author` FROM `posts` WHERE `header` = '$header'");
            if($res->fetch_assoc()["author"] == $currentfullname){
                $conn->query("DELETE FROM `posts` WHERE `header` = '$header'");
                header("Location: teacher.php");
                exit();
            }
        }
    }