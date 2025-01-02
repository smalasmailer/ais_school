<?php
require "../../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group = $_POST['group'] ?? '';
    $lesson = $_POST['lesson'] ?? '';
    $dayid = $_POST['dayid'] ?? '';
    $student = $_POST['student'] ?? '';
    $mark = $_POST['mark'] ?? '';
    $period = $_POST['period'] ?? '';
    $date = $_POST['date'] ?? '';
    $lessonType = $_POST['lessonType'] ?? 'отв';

    if (empty($group) || empty($lesson) || empty($dayid) || empty($student) || empty($period)) {
        die('Ошибка: Все поля обязательны для заполнения.');
    }

    // Проверка допустимых значений для оценок
    if (!preg_match('/^([1-5](\/[1-5])?|н|п|б|\.|)$/u', $mark)) {
        die('Ошибка: Некорректная оценка.');
    }

    // Подготовка запроса для удаления или обновления оценок с учетом типа урока
    if ($mark === '') {
        // Удаляем оценки для конкретного типа урока с теми же параметрами
        $stmt = $conn->prepare("DELETE FROM marks WHERE studentname = ? AND lessonname = ? AND date = ? AND school = ? AND groupname = ? AND dayid = ? AND typemark = ?");
        $stmt->bind_param("sssssis", $student, $lesson, $date, $currentschool, $group, $dayid, $lessonType);
    } else {
        $res = $conn->query("SELECT `mark` FROM `marks` WHERE `studentname` = '$student' AND `lessonname` = '$lesson' AND `date` = '$date' AND `school` = '$currentschool' AND `groupname` = '$group' AND `dayid` = '$dayid' AND `typemark` = '$lessonType'");
        if ($res->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE marks SET mark = ?, period = ? WHERE studentname = ? AND lessonname = ? AND date = ? AND school = ? AND groupname = ? AND dayid = ? AND typemark = ?");
            $stmt->bind_param("sssssssis", $mark, $period, $student, $lesson, $date, $currentschool, $group, $dayid, $lessonType);
        } else {
            $stmt = $conn->prepare("INSERT INTO marks (studentname, lessonname, mark, date, school, groupname, period, typemark, dayid)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $student, $lesson, $mark, $date, $currentschool, $group, $period, $lessonType, $dayid);
        }
    }

    // Выполнение запроса и обработка результата
    if ($stmt->execute()) {
        echo "Оценка успешно сохранена для типа урока $lessonType.";
    } else {
        echo "Ошибка: " . $stmt->error;
    }
} else {
    die('Ошибка: Неверный метод запроса.');
}
?>