<?php
require "../../config.php";
if ($currentrole != "Ученик") {
    header("Location: ../../index.html");
    exit();
}
$periodQuery = $conn->query("SELECT * FROM `{$currentschool}_periods`");
if ($periodQuery->num_rows > 0) {
    $periodData = $periodQuery->fetch_assoc();
    
    // Функция для определения текущего периода по дате
    function getCurrentPeriod($date, $periodData) {
        $date = new DateTime($date);
        
        for ($i = 1; $i <= 4; $i++) {
            $start = new DateTime($periodData["period{$i}_from"]);
            $end = new DateTime($periodData["period{$i}_to"]);
            
            if ($date >= $start && $date <= $end) {
                return $i;
            }
        }
        return null; // Если дата не попадает ни в один из периодов
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дневник</title>
    <link rel="stylesheet" href="../student.css">
    <style>
        form input, select {
            width: auto;
        }
        .onlinelesson button{
            width: 100%;
            background-color: lightcoral;
        }
        .onlinelesson button:hover{
            background-color: darkred;
        }
    </style>
</head>
<body>
    <center>
        <?php
        if (isset($_GET["date"]) && isset($_GET["period"])) {
            $getdate = $_GET["date"];
            $period = getCurrentPeriod($getdate, $periodData);
    
            // Если период не определен, использовать период по умолчанию
            if ($period === null) {
                $period = 1;
            }
        
            // Остальной код остается неизменным
            $date = DateTime::createFromFormat('Y-m-d', $getdate);
            $formdate = $date->format("d.m.y");
        
            $tomorrow = date("Y-m-d", strtotime("$getdate +1 day"));
            $yesterday = date("Y-m-d", strtotime("$getdate -1 day"));
            echo "<section class='changeday'><a href='?date=$yesterday&period=$period'><button>Пред.</button></a> $formdate ($period пер.) <a href='?date=$tomorrow&period=$period'><button>След.</button></a></a>";
            echo "<h2>Оценки</h2>";

            $res = $conn->query("SELECT `dayid`, `lessonname`, `mark`, `typemark` FROM `marks` WHERE `date` = '$getdate' AND `period` = '$period' AND `groupname` = '$currentgroupname' AND `school` = '$currentschool' AND `studentname` = '$currentfullname' ORDER BY `dayid` ASC");
            if ($res->num_rows > 0) {
                echo "<table>";
                echo "<tr><td>№</td><td>Урок</td><td>Оценка</td><td>Тип</td></tr>";
                while ($row = $res->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["dayid"] . "</td>";
                    echo "<td>" . $row["lessonname"] . "</td>";

                    echo "<td style=";
                    if ($row["mark"] == 1 || $row["mark"] == 2) {
                        echo "'background-color:red;padding:5px;'><a href='stats.php?dayid=$row[dayid]&lessonname=$row[lessonname]&group=$currentgroupname&date=$getdate&period=$period'>$row[mark]</a>";
                    } else if ($row["mark"] == 3) {
                        echo "'background-color:orange;padding:5px;'><a href='stats.php?dayid=$row[dayid]&lessonname=$row[lessonname]&group=$currentgroupname&date=$getdate&period=$period'>$row[mark]</a>";
                    } else if ($row["mark"] == 4 || $row["mark"] == 5) {
                        echo "'background-color:lightgreen;padding:5px;'><a href='stats.php?dayid=$row[dayid]&lessonname=$row[lessonname]&group=$currentgroupname&date=$getdate&period=$period'>$row[mark]</a>";
                    } else if ($row["mark"] == "н") {
                        echo "'background-color:gray;padding:5px;'><a href='stats.php?dayid=$row[dayid]&lessonname=$row[lessonname]&group=$currentgroupname&date=$getdate&period=$period'>$row[mark]</a>";
                    }
                    echo "</td></a>";

                    echo "<td>" . $row["typemark"] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "Нет оценок";
            }
            echo "<hr>";
            echo "<h2>Расписание</h2>";
            $res = $conn->query("SELECT `dayid`, `lessonname`, `teacher`, `lessontopic`, `homework`, `onlinelesson` FROM `timetable` WHERE `date` = '$getdate' AND `groupname` = '$currentgroupname' AND `period` = '$period' AND `school` = '$currentschool' ORDER BY `dayid` ASC");

            if ($res->num_rows > 0) {
                echo "<table>";
                echo "<tr><td>№</td><td>Урок</td><td>Учитель</td><td>Изучаемая тема</td><td>Домашнее задание к уроку</td></tr>";

                while ($row = $res->fetch_assoc()) {
                    echo "<tr>";

                    if (isset($row["onlinelesson"]) && trim($row["onlinelesson"]) !== "не указана") {
                        $link = "<a class='onlinelesson' href='" . htmlspecialchars($row["onlinelesson"]) . "'><button>Дистанционный урок</button></a>";
                    } else {
                        $link = "";
                    }
                
                    // Вывод данных
                    echo "<td>{$row['dayid']}</td>";
                    echo "<td>{$row['lessonname']}<br>{$link}</td>";
                    echo "<td>{$row['teacher']}</td>";
                    echo "<td>{$row['lessontopic']}</td>";
                    echo "<td>{$row['homework']}</td>";
                    echo "</tr>";
                }
            
                echo "</table>";
            } else{
                echo "Нет уроков";
            }
        } else {
            echo '
                <form method="get">
                    Укажите дату: <input type="date" name="date"><br>
                    Укажите период: <select name="period"><br>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select><br>
                    <input type="submit" value="Показать">
                </form>
            ';
        }
        ?>
        <hr>
        <a href="../"><button>Вернуться на главную</button></a><br>
        <a href="../../logout.php"><button>Выйти из профиля</button></a><br><br>
    </center>
</body>
</html>