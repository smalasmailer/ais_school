<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lessonId'], $_POST["lesson"], $_POST["grade"], $_POST["author"], $_POST["topic"], $_POST["homework"])) {
    $lessonId = $_POST['lessonId'];
    $lesson = $_POST["lesson"];
    $grade = $_POST["grade"];
    $author = $_POST["author"];
    $topic = $_POST["topic"];
    $homework = $_POST["homework"];

    // Удаляем запись из базы
    $stmt = $conn->prepare("UPDATE `ktp` SET `topic` = ?, `homework` = ? WHERE `lessonId` = ? AND `ktpgrade` = ? AND `author` = ? AND `school` = ?");
    $stmt->bind_param("ssssss", $topic, $homework, $lessonId, $grade, $author, $currentschool);
    if ($stmt->execute()) {
        echo "Урок успешно обновлен.";
    } else {
        http_response_code(500);
        echo "Ошибка при удалении урока.";
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "Неверный запрос.";
}
?>
