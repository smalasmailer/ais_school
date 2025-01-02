<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Завуч"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчёт</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <?php
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(isset($_POST["teacher"], $_POST["lesson"], $_POST["group"], $_POST["period"], $_POST["type"])){
                $teacher = $_POST["teacher"];
                $lesson = $_POST["lesson"];
                $group = $_POST["group"];
                $period = $_POST["period"];
                $type = $_POST["type"];

                if($type == "marks"){
                    $res = $conn->query("SELECT `studentname`, AVG(`mark`) as avg_mark FROM `marks` WHERE `lessonname` = '$lesson' AND `groupname` = '$group' AND `period` = '$period' AND `school` = '$currentschool' GROUP BY `studentname`");
                    if($res->num_rows>0){
                        echo "<table><tr>";
                        echo "<th>Ученик</th><th>Средний балл</th>";
                        echo "</tr>";
                        while($row = $res->fetch_assoc()){
                            echo "<tr><td>{$row['studentname']}</td><td>{$row['avg_mark']}</td></tr>";
                        }
                        echo "<tr><td colspan='2'><center style='color: blue;'>ais-school.ru</center></td></tr>";
                        echo "</table>";
                    }
                } elseif($type == "ktp"){
                    $res = $conn->query("SELECT `dayid`, `date`, `lessontopic`, `homework`, `type` FROM `timetable` WHERE `teacher` = '$teacher' AND `lessonname` = '$lesson' AND `groupname` = '$group' AND `period` = '$period'");
if($res->num_rows > 0){
    echo "<table><tr>";
    echo "<th>Время проведения</th><th>Тема урока</th><th>ДЗ к уроку</th><th>Тип урока</th>";
    echo "</tr>";

    while($row = $res->fetch_assoc()){
        // Преобразование даты в формат ДД.ММ.ГГ
        $formattedDate = date("d.m.Y", strtotime($row['date']));
        
        echo "<tr>";
        echo "<td>{$formattedDate} ({$row['dayid']}-м уроком)</td>";
        echo "<td>{$row['lessontopic']}</td>";
        echo "<td>{$row['homework']}</td>";
        echo "<td>{$row['type']}</td>";
        echo "</tr>";
    }

    echo "<tr><td colspan='4'><center style='color: blue;'>ais-school.ru</center></td></tr>";
    echo "</table>";
}

                }
            }
        }
    ?>
</body>
</html>