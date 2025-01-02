<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
        exit();
    }
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["organ"])){
            $conn->query("UPDATE `organs` SET `license` = 1 WHERE `orgshort` = '" . $_POST["organ"] . "'");
            $conn->query("DELETE FROM `blockedorgans` WHERE `orgshort` = '" . $_POST["organ"] . "'");
            $conn->query("INSERT INTO `logs`(`user`, `action`) VALUES('$currentfullname', 'Вернул лицензию ОУ: $_POST[organ]')");
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
    <title>Вернуть лицензию</title>
</head>
<body>
    <?php require "../adminheader.php"; ?>
    <h2>Вернуть лицензию</h2>
    <form method="post">
        <select name="organ">
            <?php
                $res = $conn->query("SELECT `orgshort` FROM `organs` WHERE `license` = 0");
                while($row = $res->fetch_assoc()){
                    echo "<option value='" . $row["orgshort"] . "'>" . $row["orgshort"] . "</option>";
                }
            ?>
        </select>
        <input type="submit" value="Вернуть">
    </form>
</body>
</html>