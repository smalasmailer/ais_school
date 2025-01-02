<?php
    require "../../config.php";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Организации</title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        table {
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
            background-color: green;
            color:white;
        }
    </style>
</head>
<body>
    <?php require "../organheader.php"; ?>
    <a href="createschool.php">Зарегистрировать организацию</a>
    <?php
        $res = $conn->query("SELECT `orgshort` FROM `schools` WHERE `organ` = '$organname'");
        if($res->num_rows>0){
            echo "<center><table border='1' style='margin-bottom:15px;'>";
            echo "<tr><td>Кр. наименование</td></tr>";
            while($row = $res->fetch_assoc()){
                echo "<tr><td><button><a href='check.php?view=" . urlencode($row['orgshort']) . "'>" .$row['orgshort']."</a></button></td></tr>";
            }
            echo "</table></center>";
        }
    ?>
</body>
</html>