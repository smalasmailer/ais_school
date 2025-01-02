<?php
require "../../config.php";
if ($currentrole != "Ученик") {
    header("Location: ../../index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Итоговые оценки</title>
    <link rel="stylesheet" href="../student.css">
    <style>
        form select, input {
            width: auto;
        }
    </style>
</head>
<body>
    <center>
        <h2>Итоговые оценки</h2>
        <?php
        if (isset($_GET["period"])) {
            $period = $_GET["period"];
            $groupname = $currentgroupname;
            $school = $currentschool;
            $student = $currentfullname;
        
            // Запрос для получения промежуточных оценок
            $res_marks = $conn->query("
                SELECT 
                    t.lesson AS lessonname,
                    GROUP_CONCAT(m.mark ORDER BY m.date SEPARATOR ', ') AS intermediate_marks,
                    AVG(m.mark) AS average_mark
                FROM workload t
                LEFT JOIN marks m ON t.lesson = m.lessonname 
                    AND t.group = m.groupname 
                    AND t.school = m.school 
                    AND m.studentname = '$student' 
                    AND m.period = '$period'
                WHERE t.group = '$groupname' AND t.school = '$school'
                GROUP BY t.lesson
            ");
        
            // Запрос для получения итоговых оценок
            $res_total = $conn->query("
                SELECT 
                    lesson, mark AS final_mark 
                FROM totalmarks 
                WHERE student = '$student' 
                    AND period = '$period' 
                    AND groupname = '$currentgroupname' 
                    AND school = '$currentschool'
            ");
        
            $final_marks = [];
            if ($res_total->num_rows > 0) {
                while ($row = $res_total->fetch_assoc()) {
                    $final_marks[$row['lesson']] = $row['final_mark'];
                }
            }
        
            // Вывод таблицы
            echo "<table>
                    <thead>
                        <tr>
                            <th>Предмет</th>
                            <th>Промежуточные оценки</th>
                            <th>Средний балл</th>
                            <th>Итоговая оценка</th>
                        </tr>
                    </thead>
                    <tbody>";
        
                    if ($res_marks->num_rows > 0) {
                        while ($row = $res_marks->fetch_assoc()) {
                            $lessonname = $row['lessonname'];
                            $intermediate_marks = str_replace(',', ' ', $row['intermediate_marks'] ?: 'Нет оценок');
                            $marks_array = array_map('trim', explode(',', $row['intermediate_marks'] ?: ''));
                            $valid_marks = [];
                            foreach ($marks_array as $mark) {
                                // Если оценка дробная, то преобразуем её в число
                                if (strpos($mark, '/') !== false) {
                                    $parts = explode('/', $mark);
                                    if (count($parts) == 2 && is_numeric($parts[0]) && is_numeric($parts[1]) && $parts[1] != 0) {
                                        $valid_marks[] = (float)$parts[0] / (float)$parts[1];
                                    }
                                } elseif (is_numeric($mark)) {
                                    // Если это обычная оценка, добавляем её в массив
                                    $valid_marks[] = (float)$mark;
                                }
                            }

                            // Если есть действительные оценки, считаем среднее
                            if (count($valid_marks) > 0) {
                                $average_mark = array_sum($valid_marks) / count($valid_marks);
                            } else {
                                $average_mark = 0; // или другое значение, например, 'Нет оценок'
                            }
                            $final_mark = isset($final_marks[$lessonname]) ? $final_marks[$lessonname] : 'Нет итоговой оценки';
                        
                            echo "<tr class='student-row'>
                                  <td>{$lessonname}</td>
                                  <td class='mark-input'>{$intermediate_marks}</td>
                                  <td class='average-cell'>{$average_mark}</td>
                                  <td>{$final_mark}</td>
                                  </tr>";
                        }
                        
                    } else{
                        echo "<td>нет оценок</td>";
                    }
        
            echo "</tbody></table>";
        
        } else {
            echo '
                <form method="get">
                    Период: <select name="period">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                    <input type="submit" value="Показать">
                </form>
            ';
        }
        ?>
        <hr>
        <a href="../"><button>Вернуться на главную</button></a><br>
        <a href="../../logout.php"><button>Выйти из профиля</button></a><br><br>
    </center>
</body>
</html>