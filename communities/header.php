<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

    if($currentrole == "ОУ"){
        require "../organ/organheader.php";
    } elseif($currentrole == "Активист" || $currentrole == "Ученик"){
        require "../header.php";
    } elseif($currentrole == "Админ"){
        require "../sysadmin/adminheader.php";
    } else{
        require "../school/teacherhead.php";
    }