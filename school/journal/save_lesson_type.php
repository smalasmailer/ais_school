<?php
require "../../config.php";

if (isset($_POST['group'], $_POST['lesson'], $_POST['date'], $_POST['dayid'], $_POST['period'], $_POST['type'])) {
    $group = $_POST['group'];
    $lesson = $_POST['lesson'];
    $date = $_POST['date'];
    $dayid = $_POST['dayid'];
    $period = $_POST['period'];
    $type = $_POST['type'];

    $stmt = $conn->prepare("
        UPDATE timetable 
        SET type = ? 
        WHERE date = ? AND lessonname = ? AND groupname = ? AND dayid = ? AND period = ?
    ");
    $stmt->bind_param('ssssii', $type, $date, $lesson, $group, $dayid, $period);

    if ($stmt->execute()) {
        echo "Тип урока успешно обновлён.";
    } else {
        echo "Ошибка при обновлении типа урока.";
    }
    $stmt->close();
}
?>