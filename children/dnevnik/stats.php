<?php
require "../../config.php";

if (isset($_GET["dayid"]) && isset($_GET["lessonname"]) && isset($_GET["group"]) && isset($_GET["date"]) && isset($_GET["period"])) {
    $dayid = $_GET["dayid"];
    $lessonname = $_GET["lessonname"];
    $group = $_GET["group"];
    $date = $_GET["date"];
    $period = $_GET["period"];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика по оценкам</title>
    <link rel="stylesheet" href="../student.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #marksChart {
            width: 200px;  /* Задайте желаемую ширину */
            height: 200px; /* Задайте желаемую высоту */
        }
        .chart-container {
            position: relative;
            display: inline-block;
        }
        .current-mark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px; /* Размер текста */
            font-weight: bold;
            color: black; /* Цвет текста */
            text-align: center; /* Центрирование текста */
        }
        .statistic {
            margin-bottom: 10px; /* Отступ между строками */
            font-size: 24px;
        } 
        .lessonname{
            padding: 5px;
            background-color:blueviolet;
            color: white;
            border-radius: 15px;
        } 
    </style>
</head>
<body>
    <center>
        <h2>Статистика</h2>
        <a href="index.php?date=<?php echo $date; ?>&period=<?php echo $period; ?>"><button>Вернуться в дневник</button></a>
        <div class="lessonname"><h2><?php echo $lessonname ?></h2>
        <h3>Тип: <?php
            $res = $conn->query("SELECT `type` FROM `timetable` WHERE `lessonname` = '$lessonname' AND `date` = '$date' AND `period` = '$period' AND `school` = '$currentschool' AND `dayid` = '$dayid' AND `groupname` = '$group'");
            $row = $res->fetch_assoc();
            $type = $row["type"];
            echo $type;
        ?></h3></div>
        <section class="stats">
            <?php
            $res = $conn->query("SELECT `mark` FROM `marks` WHERE `dayid` = '$dayid' AND `lessonname` = '$lessonname' AND `groupname` = '$group' AND `studentname` = '$currentfullname' AND `date` = '$date' AND `period` = '$period' AND `school` = '$currentschool'");
            $mark = $res->fetch_assoc()["mark"];

            // Подсчёт оценок
            $one = $two = $three = $four = $five = $skip = 0;

            $res = $conn->query("SELECT `mark` FROM `marks` WHERE `dayid` = '$dayid' AND `lessonname` = '$lessonname' AND `groupname` = '$group' AND `date` = '$date'");
            while ($row = $res->fetch_assoc()) {
                if ($row["mark"] == 1) {
                    $one++;
                } elseif ($row["mark"] == 2) {
                    $two++;
                } elseif ($row["mark"] == 3) {
                    $three++;
                } elseif ($row["mark"] == 4) {
                    $four++;
                } elseif ($row["mark"] == 5) {
                    $five++;
                } elseif ($row["mark"] == "н" || $row["mark"] == "п" || $row["mark"] == "б") {
                    $skip++;
                }
            }

            // Подсчёт общего числа оценок
            $totalMarks = $five + $four + $three + $two + $one + $skip;
            ?>
            <div class="chart-container">
                <!-- Элемент для отображения диаграммы -->
                <canvas id="marksChart"></canvas>
            </div>
            <script>
                const ctxa = document.getElementById('marksChart').getContext('2d');
                const marksCharta = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Пятерки и Четверки', 'Тройки', 'Двойки и Колы', 'Н, П, Б'],
                        datasets: [{
                            data: [<?php echo $five + $four; ?>, <?php echo $three; ?>, <?php echo $two + $one; ?>, <?php echo $skip; ?>],
                            backgroundColor: [
                                'lightgreen', // Зеленый
                                'orange',     // Оранжевый
                                'red',        // Красный
                                'gray'        // Серый
                            ],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: 0,
                        plugins: {
                            legend: {
                                display: false, // Отключаем отображение легенды
                            },
                            tooltip: {
                                enabled: false // Отключаем отображение подсказок при наведении
                            },
                            title: {
                                display: false
                            }
                        }
                    },
                });
            </script>
            <br>
            
        </section>
        <div style="display: flex;">
            <?php if ($five > 0) echo "<p style='margin-bottom: 10px; font-size: 24px; margin-right: 10px; background-color: lightgreen; padding: 10px; border-radius: 15px;'>Пятерок: $five</p>"; ?>
            <?php if ($four > 0) echo "<p style='margin-bottom: 10px; font-size: 24px; margin-right: 10px; background-color: lightgreen; padding: 10px; border-radius: 15px;'>Четверок: $four</p>"; ?>
            <?php if ($three > 0) echo "<p style='margin-bottom: 10px; font-size: 24px; margin-right: 10px; background-color: orange; padding: 10px; border-radius: 15px;'>Троек: $three</p>"; ?>
            <?php if ($two > 0) echo "<p style='margin-bottom: 10px; font-size: 24px; margin-right: 10px; background-color: red; padding: 10px; border-radius: 15px;'>Двоек: $two</p>"; ?>
            <?php if ($one > 0) echo "<p style='margin-bottom: 10px; font-size: 24px; margin-right: 10px; background-color: red; padding: 10px; border-radius: 15px;'>Колов: $one</p>"; ?>
            <?php if ($skip > 0) echo "<p style='margin-bottom: 10px; font-size: 24px; margin-right: 10px; background-color: gray; padding: 10px; border-radius: 15px;'>Пропусков: $skip</p>"; ?>
        </div>
        <p style='font-size: 24px;'>Твоя оценка: <?php echo $mark; ?></p>
    </center>
</body>
</html>