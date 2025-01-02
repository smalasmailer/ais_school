<?php
require "../../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group = $_POST['group'];
    $lesson = $_POST['lesson'];
    $date = $_POST['date'];
    $dayid = $_POST['dayid'];
    $student = $_POST['student'];
    $mark = $_POST['mark'];
    $period = $_POST['period'];
    $school = $currentschool;

    // Получаем тип урока из timetable
    $res = $conn->query("SELECT `type` FROM `timetable` WHERE `lessonname` = '$lesson' AND `date` = '$date' AND `dayid` = '$dayid' AND `school` = '$currentschool' AND `period` = '$period' AND `groupname` = '$group'");
    $lessonType = $res->fetch_assoc()["type"];

    $res = $conn->query("SELECT `mark` FROM `marks` WHERE `studentname` = '$student' AND `school` = '$school' AND `dayid` = '$dayid' AND `lessonname` = '$lesson' AND `date` = '$date' AND `period` = '$period'");
    
    if ($res->num_rows > 0) {
        $res = $conn->query("UPDATE `marks` SET `mark`='$mark', `typemark`='$lessonType' WHERE `groupname` = '$group' AND `lessonname` = '$lesson' AND `date` = '$date' AND `dayid` = '$dayid' AND `studentname` = '$student' AND `period` = '$period'");
        if ($res) {
            echo "Успешно; $period";
            echo "Period: " . $period;
        } else {
            echo "Ошибка: $conn->error";
        }
    } else {
        $res = $conn->query("INSERT INTO `marks`(`dayid`, `date`, `lessonname`, `studentname`, `groupname`, `mark`, `typemark`, `period`, `school`) VALUES('$dayid', '$date', '$lesson', '$student', '$group', '$mark', '$lessonType', '$period', '$school')");
        if ($res) {
            echo "Успешно; $period";
            echo "Period: " . $period;
        } else {
            echo "Ошибка: $conn->error";
        }
    }
} else {
    echo "Некорректный запрос.";
}
?>