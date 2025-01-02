<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: /");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Логи</title>
</head>
<body>
    <?php require "../adminheader.php";
    $res = $conn->query("SELECT * FROM `logs`");
    while($row = $res->fetch_assoc()){
        echo "<p>$row[action] ($row[user])</p>";
    }
    ?>
</body>
</html>