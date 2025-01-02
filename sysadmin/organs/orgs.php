<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Органы</title>
</head>
<body>
    <?php
        require "../adminheader.php";
    ?>
    <h2>Органы</h2>
    <form action="/profile/organ.php" method="get">
        <select name="organ">
            <?php
                $res = $conn->query("SELECT `orgshort` FROM `organs`");
                while($row = $res->fetch_assoc()){
                    echo "<option value='{$row["orgshort"]}'>" . $row['orgshort'] . "</option>";
                }
            ?>
        </select><br>
        <input type="submit" value="Показать">
    </form>
</body>
</html>