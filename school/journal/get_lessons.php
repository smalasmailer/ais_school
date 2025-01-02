<?php
    require "../../config.php";

    if (isset($_POST['group'])) {
        $group = $_POST['group'];

        // Выполняем запрос для получения уроков
        $result = $conn->query("SELECT `lesson` FROM `workload` WHERE `group` = '$group' AND `school` = '$currentschool' AND `teacher` = '$currentfullname'");

        // Генерируем опции для select
        if ($result->num_rows > 0) {
            echo "<option value=''>Выберите урок</option>"; // Начальная опция
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['lesson'] . "'>" . $row['lesson'] . "</option>";
            }
        } else {
            echo "<option value=''>Уроки не найдены</option>";
        }
    }
?>