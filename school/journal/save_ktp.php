<?php
// Подключение к базе данных
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lessonId = $_POST['lessonId'];
    $lessonTheme = $_POST['theme'] ?? " ";
    $homework = $_POST['homework'] ?? " ";
    $date = $_POST['date'];

    // Обновление записи в базе данных
    $sql = "UPDATE timetable SET lessontopic = ?, homework = ? WHERE dayid = ? AND school = ? AND date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssiis', $lessonTheme, $homework, $lessonId, $currentschool, $date);
    $stmt->execute();
}
?>
<script>
    window.history.go(-1);
</script>