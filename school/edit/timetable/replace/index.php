<?php
    require "../../../../config.php";
    if($currentrole != "Директор" && $currentrole != "Завуч"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"] . "/index.html");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Замены</title>
</head>
<body>
    <?php require "../../../adminheader.php"; ?>
    <form action="replace.php" method="get">
        <h3>Выберите класс</h3>
        <select name="groupname">
        <?php
            $res = $conn->query("SELECT `groupname` FROM `groups` WHERE `school` = '$currentschool'");
            while($row = $res->fetch_assoc()){
                echo "<option value='$row[groupname]'>$row[groupname]</option>";
            }
        ?>
        </select><br><br>
        <h3>Выберите дату замены</h3>
        <input type="date" name="date"><br>
        <h3>Выберите период</h3>
        <select name="period">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
        </select><br>
        <input type="submit" value="Заменить">
    </form>
</body>
</html>