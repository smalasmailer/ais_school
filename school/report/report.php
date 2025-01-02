<?php
    require "../../config.php";
    if(isset($_POST["reporttype"]) && isset($_POST["groupname"]) && isset($_POST["lessonname"]) && isset($_POST["periodname"])){
        $reporttype = $_POST["reporttype"];
        $groupname = $_POST["groupname"];
        $lessonname = $_POST["lessonname"];
        $periodname = $_POST["periodname"];
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Построенный отчёт</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <p>ais-school.ru</p>
    <?php
        if($reporttype == "allcreated"){
            $res = $conn->query("SELECT `dayid`, `date` FROM `timetable` WHERE `lessonname` = '$lessonname' AND `groupname` = '$groupname' AND `teacher` = '$currentfullname' AND `period` = '$periodname' AND `school` = '$currentschool'");
            echo "<h2>Отчёт по созданным (проведённым) урокам</h2>";
            echo "<p>Преподаватель: $currentfullname</p>";
            if($res->num_rows > 0){
                echo "<table>";
                echo "<tr><td>№</td><td>Дата проведения</td><td>Урок</td><td>Группа</td></tr>";
                while($row = $res->fetch_assoc()){
                    // Преобразуем дату в объект DateTime и форматируем
                    $date = DateTime::createFromFormat('Y-m-d', $row["date"]);
                    $formattedDate = $date->format('d.m.Y'); // ДД-ММ-ГГГГ
                    echo "<tr><td>".$row["dayid"]."</td><td>".$formattedDate."</td><td>".$lessonname."</td><td>".$groupname."</td></tr>";
                }
                echo "</table>";
            } else{
                echo "Уроки не созданы";
            }
        } else if($reporttype == "homeworks"){
            $res = $conn->query("SELECT `dayid`, `date`, `homework` FROM `timetable` WHERE `lessonname` = '$lessonname' AND `groupname` = '$groupname' AND `teacher` = '$currentfullname' AND `period` = '$periodname' AND `school` = '$currentschool'");
            echo "<h2>Отчёт по выданным домашним заданиям</h2>";
            echo "<p>Преподаватель: $currentfullname</p>";
            if($res->num_rows > 0){
                echo "<table>";
                echo "<tr><td>К какому уроку</td><td>Домашнее задание</td></tr>";
                while($row = $res->fetch_assoc()){
                    $date = DateTime::createFromFormat('Y-m-d', $row["date"]);
                    $formattedDate = $date->format('d.m.Y'); // ДД-ММ-ГГГГ
                    echo "<tr><td>К ". $formattedDate ." (". $row['dayid'] ." уроку)<td>".$row["homework"]."</td></tr>";
                }
            } else{
                echo "ДЗ не проставлены";
            }
        } else if($reporttype == "topics"){
            $res = $conn->query("SELECT `dayid`, `date`, `lessontopic` FROM `timetable` WHERE `lessonname` = '$lessonname' AND `groupname` = '$groupname' AND `teacher` = '$currentfullname' AND `period` = '$periodname' AND `school` = '$currentschool'");
            echo "<h2>Отчёт по выставленным темам</h2>";
            echo "<p>Преподаватель: $currentfullname</p>";
            if($res->num_rows > 0){
                echo "<table>";
                echo "<tr><td>Урок</td><td>Тема</td></tr>";
                while($row = $res->fetch_assoc()){
                    $date = DateTime::createFromFormat('Y-m-d', $row["date"]);
                    $formattedDate = $date->format('d.m.Y'); // ДД-ММ-ГГГГ
                    echo "<tr><td>". $formattedDate ." (". $row['dayid'] ." урок)<td>".$row["lessontopic"]."</td></tr>";
                }
            } else{
                echo "Темы не проставлены";
            }
        } else if($reporttype == "marks"){
            $res = $conn->query("
                SELECT `studentname`, AVG(`mark`) AS average_mark 
                FROM `marks` 
                WHERE `lessonname` = '$lessonname' 
                    AND `groupname` = '$groupname' 
                    AND `period` = '$periodname' 
                    AND `school` = '$currentschool'
                GROUP BY `studentname`
            ");
            echo "<h2>Отчёт по среднему баллу по группе ($groupname)</h2>";
            echo "<p>Преподаватель: $currentfullname</p>";
            if($res->num_rows > 0){
                echo "<table>";
                echo "<tr><td>Имя ученика</td><td>Средний балл</td></tr>";
                while($row = $res->fetch_assoc()){
                    echo "<tr><td>".$row["studentname"]."</td><td>".round($row["average_mark"], 2)."</td></tr>";
                }
                echo "</table>";
            } else{
                echo "Оценки не проставлены";
            }

        }
    ?>
    <script>
      window.onload = function() {
        window.print();
    
        window.onafterprint = function() {
          window.close();
        };
      };
    </script>
</body>
</html>