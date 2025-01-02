<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "ОУ"){
        header("Location: /");
        exit();
    }
    if($license){
        header("Location: ../index.html");
        exit();
    }
?>  
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Причина блокировки</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <?php require "../organheader.php"; ?>
    <?php
        $res = $conn->query("SELECT `reason` FROM `blockedorgans` WHERE `orgshort` = '$organname'");
        $row = $res->fetch_assoc();
        $reason = $row["reason"];
    ?>
    <p>Ваш орган был заблокирован по причине <? echo $reason; ?></p>
</body>
</html>