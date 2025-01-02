<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lessonId'], $_POST["lesson"], $_POST["grade"], $_POST["author"])) {
    $lessonId = $_POST['lessonId'];
    $lesson = $_POST["lesson"];
    $grade = $_POST["grade"];
    $author = $_POST["author"];

    // Удаляем запись из базы
    $stmt = $conn->prepare("DELETE FROM `ktp` WHERE `lessonid` = ? AND `ktplesson` = ? AND `ktpgrade` = ? AND `author` = ? AND `school` = ?");
    $stmt->bind_param("sssss", $lessonId, $lesson, $grade, $author, $currentschool);

    if ($stmt->execute()) {
        echo "Урок успешно удален.";
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
