<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
        exit();
    }
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["organ"] ) && isset( $_POST["reason"] )){
            $organ = $_POST["organ"];
            $reason = $_POST["reason"];
            $conn->query("UPDATE `organs` SET `license` = 0 WHERE `orgshort` = '" . $organ . "'");
            $conn->query("INSERT INTO `blockedorgans` (`orgshort`, `reason`) VALUES ('$organ', '$reason')");
            $conn->query("INSERT INTO `logs`(`user`, `action`) VALUES('$currentfullname', 'Отобрал лицензию ОУ: $organ')");
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отобрать лицензию</title>
</head>
<body>
    <? require "../adminheader.php"; ?>
    <h2>Выберите ОУ</h2>
    <form method="post">
        <select name="organ">
            <?php
                $res = $conn->query("SELECT `orgshort` FROM `organs` WHERE `license` = 1");
                while($row = $res->fetch_assoc()){
                    echo "<option value='" . $row["orgshort"] . "'>" . $row["orgshort"] . "</option>";
                }
            ?>
        </select><br>
        <input type="text" name="reason" placeholder="Причина" style="width: 50%;">
        <input type="submit" value="Отобрать">
    </form>
</body>
</html>