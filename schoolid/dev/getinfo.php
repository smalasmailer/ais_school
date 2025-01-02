<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    
    $res = $conn->query("SELECT `fullname`, `role` FROM `users` WHERE `login` = '$_COOKIE[acclogin]'");
    $row = $res->fetch_assoc();
    $fullname = $row["fullname"];
    $role = $row["role"];

    function getDevLogin(){
        global $role;

        if($role != "Разработчик"){
            header("Location: /index.php");
            exit();
        }
    }

    function getAppInfo($appid){
        global $conn, $fullname;
        $res = $conn->query("SELECT * FROM `idapps` WHERE `appid` = '$appid' AND `developer` = '$fullname'");
        $row = $res->fetch_assoc();
        
        return ["secret" => $row["secret"], "active" => $row["active"]];
    }