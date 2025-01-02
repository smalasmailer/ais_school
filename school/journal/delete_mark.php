<?php
require "../../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из POST-запроса
    $group = $_POST['group'] ?? '';
    $lesson = $_POST['lesson'] ?? '';
    $dayid = $_POST['dayid'] ?? '';
    $student = $_POST['student'] ?? '';
    $period = $_POST['period'] ?? '';
    $date = $_POST['date'] ?? '';

    // Проверка на пустые значения
    if (empty($group) || empty($lesson) || empty($dayid) || empty($student) || empty($period)) {
        die('Ошибка: Все поля обязательны для заполнения.');
    }

    // Удаляем запись
    $stmt = $conn->prepare("DELETE FROM marks WHERE studentname = ? AND lessonname = ? AND date = ? AND school = ? AND groupname = ? AND dayid = ?");
    $stmt->bind_param("ssssss", $student, $lesson, $date, $currentschool, $group, $dayid);

    if ($stmt->execute()) {
        echo "Оценка успешно удалена.";
    } else {
        echo "Ошибка: " . $stmt->error;
    }
} else {
    die('Ошибка: Неверный метод запроса.');
}