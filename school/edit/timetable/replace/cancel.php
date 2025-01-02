<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

// Проверка роли пользователя
if ($currentrole != "Директор" && $currentrole != "Завуч") {
    header("Location: ../../index.php");
    exit();
}

// Проверка на наличие параметров
if (!isset($_GET["groupname"]) || !isset($_GET["date"]) || !isset($_GET["dayid"]) || !isset($_GET["lessonname"]) || !isset($_GET["teacher"])) {
    header("Location: index.php");
    exit();
}

$groupname = $_GET["groupname"];
$date = $_GET["date"];
$dayid = $_GET["dayid"];
$lessonname = $_GET["lessonname"];
$teacher = $_GET["teacher"];
$period = $_GET["period"];

// Удаление записи из таблицы timetable
$sql = "DELETE FROM `timetable` WHERE `groupname` = '$groupname' AND `date` = '$date' AND `dayid` = '$dayid' AND `lessonname` = '$lessonname' AND `teacher` = '$teacher' AND '$period'";

if ($conn->query($sql) === TRUE) {
    echo "Запись успешно удалена!";
} else {
    echo "Ошибка при удалении записи: " . $conn->error;
}

$conn->close();
header("Location: replace.php?groupname=$groupname&date=$date&period=$period");
exit();
?>