<?php
    require "../../config.php";
    require "../schoolhead.php";

    if (isset($_GET["period1_from"]) && isset($_GET["period2_from"]) && isset($_GET["period3_from"]) && isset($_GET["period4_from"]) && isset($_GET["period1_to"]) && isset($_GET["period2_to"]) && isset($_GET["period3_to"]) && isset($_GET["period4_to"])){
        $period1_from = $_GET["period1_from"];
        $period1_to = $_GET["period1_to"];

        $period2_from = $_GET["period2_from"];
        $period2_to = $_GET["period2_to"];

        $period3_from = $_GET["period3_from"];
        $period3_to = $_GET["period3_to"];

        $period4_from = $_GET["period4_from"];
        $period4_to = $_GET["period4_to"];

        $conn->query("CREATE TABLE IF NOT EXISTS `{$currentschool}_periods`(
            period1_from DATE NOT NULL,
            period1_to DATE NOT NULL,
            period2_from DATE NOT NULL,
            period2_to DATE NOT NULL,
            period3_from DATE NOT NULL,
            period3_to DATE NOT NULL,
            period4_from DATE NOT NULL,
            period4_to DATE NOT NULL
        )");
        $conn->query("INSERT INTO `{$currentschool}_periods`(period1_from, period1_to, period2_from, period2_to, period3_from, period3_to, period4_from, period4_to) VALUES ('$period1_from', '$period1_to', '$period2_from', '$period2_to', '$period3_from', '$period3_to', '$period4_from', '$period4_to')");
        header("Location: periods.php");
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Периоды</title>
    <style>
    	.period{
    		display: flex;
    		width: 500px;
    	}
    	.period p{
    		margin-top: 5%;
    	}
    </style>
</head>
<body>
    <h2>Управление периодами</h2>
    <?php
        $res = $conn->query("SHOW TABLES LIKE '{$currentschool}_periods'");
        if ($res) {
            if ($res->num_rows > 0) {
                echo "<h3>Периоды уже заданы:</h3>";
                $period1 = $conn->query("SELECT period1_from, period1_to FROM `{$currentschool}_periods`");
                $period1res = $period1->fetch_assoc(); // Используйте fetch_assoc вместо fetch_row для ассоциативного массива
                $period2 = $conn->query("SELECT period2_from, period2_to FROM `{$currentschool}_periods`");
                $period2res = $period2->fetch_assoc();
                $period3 = $conn->query("SELECT period3_from, period3_to FROM `{$currentschool}_periods`");
                $period3res = $period3->fetch_assoc();
                $period4 = $conn->query("SELECT period4_from, period4_to FROM `{$currentschool}_periods`");
                $period4res = $period4->fetch_assoc();
                            
                echo "<p>I период с {$period1res['period1_from']}; до {$period1res['period1_to']};</p>";
                echo "<p>II период с {$period2res['period2_from']}; до {$period2res['period2_to']};</p>";
                echo "<p>III период с {$period3res['period3_from']}; до {$period3res['period3_to']};</p>";
                echo "<p>IV период с {$period4res['period4_from']}; до {$period4res['period4_to']};</p>";

            }else {
                echo "<h3>Периоды не были заданы:</h3>";
            echo "<center><form method='get' style='max-width:250px;'>";
            echo "
                <div class='period'><p>I период: с:</p> <input type='date' name='period1_from'><p>; до:</p> <input type='date' name='period1_to'></div><br>
                <div class='period'><p>II период: с:</p> <input type='date' name='period2_from'><p>; до:</p> <input type='date' name='period2_to'></div><br>
                <div class='period'><p>III период: с:</p> <input type='date' name='period3_from'><p>; до:</p> <input type='date' name='period3_to'></div><br>
                <div class='period'><p>IV период: с:</p> <input type='date' name='period4_from'><p>; до:</p> <input type='date' name='period4_to'></div><br>
                <input type='submit' value='Подтвердить'>
            ";
            echo "</form></center>";
        }
    }
    ?>
</body>
</html>