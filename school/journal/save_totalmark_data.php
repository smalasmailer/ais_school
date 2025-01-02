<?php
require "../../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group = $_POST['group'];
    $lesson = $_POST['lesson'];
    $student = $_POST['student'];
    $mark = $_POST['mark'];
    $period = $_POST['period'];
    $school = $currentschool;

    // Логирование входных данных
    error_log("POST данные: group={$group}, lesson={$lesson}, student={$student}, mark={$mark}, period={$period}, school={$school}");

    // Подготовка SQL-запроса для обновления или вставки
    $res = $conn->query("SELECT `mark` FROM `totalmarks` WHERE `student` = '$student' AND `school` = '$school' AND `lesson` = '$lesson' AND `groupname` = '$group' AND `period` = '$period'");
    if($res->num_rows > 0){
        // Логирование процесса обновления
        error_log("Обновление оценки для {$student}");
        
        $res = $conn->query("UPDATE `totalmarks` SET `mark`='$mark' WHERE `groupname` = '$group' AND `lesson` = '$lesson' AND `student` = '$student' AND `period` = '$period' AND `school` = '$school'");
        if($res){
            echo "Успешно";
        } else{
            error_log("Ошибка обновления: $conn->error");
            echo "Ошибка: $conn->error";
        }
    } else {
        // Логирование процесса вставки
        error_log("Вставка новой оценки для {$student}");

        $res = $conn->query("INSERT INTO `totalmarks`(`mark`, `lesson`, `student`, `groupname`, `period`, `school`) VALUES('$mark', '$lesson', '$student', '$group', '$period', '$school')");
        if($res){
            echo "Успешно";
        } else{
            error_log("Ошибка вставки: $conn->error");
            echo "Ошибка: $conn->error";
        }
    }
} else {
    echo "Некорректный запрос.";
    error_log("Некорректный запрос.");
}
?>