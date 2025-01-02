<?php
    require "config.php";
    if(!isset($_COOKIE["login"], $_COOKIE["password"])){
        header("Location: index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои оценки</title>
</head>
<body>
    <?php 
        require "header.php"; 
        $today = date("Y-m-d", time());
        $curPer = getCurrentPeriodByDate($conn, $school, $today);
        if(!isset($_GET["period"])):
    ?>
        <br>
        <form method="GET">
            <select name="period">
                <option value="" selected disabled>Выберите период</option>
                <option value="1">I период</option>
                <option value="2">II период</option>
                <option value="3">III период</option>
                <option value="4">IV период</option>
            </select>
            <input type="submit" value="Просмотр">
        </form>
        <p>Или</p>
        <a href="?period=<?=$curPer?>"><button>Текущий период</button></a>
    <?php else: ?>
        <h2>Текущие оценки</h2>
        <?php
            $per = $_GET["period"];

            $res = $conn->query("SELECT `lesson` FROM `workload` WHERE `group` = '$groupname' AND `school` = '$school'");
            $res1 = $conn->query("SELECT `lessonname`, `mark` FROM `marks` WHERE `groupname` = '$groupname' AND `studentname` = '$fullname' AND `school` = '$school' AND `period` = '$per'");
            $res2 = $conn->query("SELECT `mark` FROM `totalmarks` WHERE `student` = '$fullname' AND `groupname` = '$groupname' AND `period` = '$per' AND `school` = '$school'");

            if($res->num_rows>0 && $res1->num_rows>0 && $res2->num_rows>0){
                echo "<table border='1' cellpadding='5' cellspacing='0'>";
                echo "<tr>";
                echo "<th>Урок</th>";
                echo "<th>Оценки</th>";
                echo "<th>Итоговая оценка</th>";
                echo "</tr>";

                while ($lessonRow = $res->fetch_assoc()) {
                    $lesson = $lessonRow['lesson'];

                    // Найдем оценки по уроку
                    $marks = [];
                    while ($markRow = $res1->fetch_assoc()) {
                        if ($markRow['lessonname'] === $lesson) {
                            $marks[] = $markRow['mark'];
                        }
                    }

                    // Найдем итоговую оценку по уроку
                    $totalMark = null;
                    while ($totalMarkRow = $res2->fetch_assoc()) {
                        $totalMark = $totalMarkRow['mark'];
                    }

                    // Вывод строки таблицы
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($lesson) . "</td>";
                    echo "<td>" . (empty($marks) ? '-' : implode(", ", $marks)) . "</td>";
                    echo "<td>" . ($totalMark === null ? '-' : htmlspecialchars($totalMark)) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else{
                echo "Нет выставленных оценок";
            }
        ?>
    <?php endif; ?>
</body>
</html>