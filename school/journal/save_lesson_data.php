<?php
require "../../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка наличия поля 'field'
    if (!isset($_POST['field'])) {
        echo "Поле 'field' не передано.";
        exit;
    }

    $group = $_POST['group'];
    $lesson = $_POST['lesson'];
    $date = $_POST['date'];
    $dayid = $_POST['dayid'];
    $field = $_POST['field'];
    $value = $_POST['value'];

    // Проверка допустимых полей
    $validFields = ['lessontopic', 'homework']; // Убедитесь, что все нужные поля здесь
    if (!in_array($field, $validFields)) {
        echo "Неизвестное поле: " . htmlspecialchars($field);
        exit;
    }

    // Подготовка SQL-запроса
    $sql = "UPDATE `timetable` SET `$field` = ? WHERE `dayid` = ? AND `date` = ? AND `lessonname` = ? AND `groupname` = ? AND `school` = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssssss', $value, $dayid, $date, $lesson, $group, $currentschool);
        
        if ($stmt->execute()) {
            echo "Темы успешно сохранены!";
        } else {
            echo "Ошибка при сохранении тем: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Ошибка при подготовке запроса: " . $conn->error;
    }
} else {
    echo "Некорректный запрос.";
}
?>