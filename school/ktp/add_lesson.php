<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["lesson"], $_POST["grade"])) {
    $lesson = $_POST["lesson"];
    $grade = $_POST["grade"];
    $author = $currentfullname;
    $school = $currentschool;

    // Получаем последний номер урока из таблицы `ktp`
    $res = $conn->query("SELECT `lessonId` FROM `ktp` WHERE `ktplesson` = '$lesson' AND `ktpgrade` = '$grade' AND `author` = '$author' AND `school` = '$school'");
    $lastLessonId = $res->num_rows + 1;

    // Вставляем новую строку
    $stmt = $conn->prepare("INSERT INTO ktp (lessonid, topic, homework, ktplesson, ktpgrade, author, school) VALUES (?, 'Укажите ДЗ', 'Укажите тему', ?, ?, ?, ?)");
    $stmt->bind_param("issss", $lastLessonId, $lesson, $grade, $author, $school);

    if ($stmt->execute()) {
        echo "Урок успешно добавлен";
    } else {
        echo "Ошибка: " . $stmt->error;
    }

    $stmt->close();
}
?>
