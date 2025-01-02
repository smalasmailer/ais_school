<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    $roleRedirects = [
        "Учитель" => "../school/teacher.php",
        "Завуч" => "../school/zavuch.php",
        "Директор" => "../school/admin.php",
        "Ученик" => "../children/",
        "ОУ" => "../organ/",
        "Админ" => "../sysadmin/",
        "Активист" => "../activism/panel.php"
    ];
    
    if (isset($roleRedirects[trim($currentrole)])) {
        header("Location: " . $roleRedirects[trim($currentrole)]);
    } else {
        header("Location: index.php");
    }
    exit();
    