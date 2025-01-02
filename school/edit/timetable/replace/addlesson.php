<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

// Проверка роли пользователя
if ($currentrole != "Директор" && $currentrole != "Завуч") {
    header("Location: ../../index.php");
    exit();
}

// Проверка на наличие параметров
if (!isset($_GET["groupname"]) || !isset($_GET["date"]) || !isset($_GET["dayid"]) || !isset($_GET["lessonname"]) || !isset($_GET["teacher"])) {
    header("Location: ../../index.php");
    exit();
}

$groupname = $_GET["groupname"];
$date = $_GET["date"];
$dayid = $_GET["dayid"];
$lessonname = $_GET["lessonname"];
$teacher = $_GET["teacher"];
$period = $_GET["period"];

// Добавление записи в таблицу timetable
$sql = "INSERT INTO `timetable` (`dayid`, `date`, `lessonname`, `groupname`, `teacher`, `lessontopic`, `homework`, `type`, `period`, `school`) VALUES ('$dayid', '$date', '$lessonname', '$groupname', '$teacher', ' ', ' ', 'текущая', '$period', '$currentschool')";

if ($conn->query($sql) === TRUE) {
    echo "Запись успешно добавлена!";
} else {
    echo "Ошибка при добавлении записи: " . $conn->error;
}

$conn->close();
header("Location: replace.php?groupname=$groupname&date=$date&period=$period");
exit();
