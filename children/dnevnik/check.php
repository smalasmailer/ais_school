<?php
require "../../config.php";

$dayid = $_GET["dayid"] ?? null;
$lessonname = $_GET["lesson"] ?? "Не указан";
$teacher = $_GET["teacher"] ?? "Не указан";
$date = $_GET["date"] ?? "Не указана";
$period = $_GET["period"] ?? "Не указан";

// Инициализация значений по умолчанию
$homework = "Нет данных";
$lessontopic = "Нет данных";
$mark = "Нет оценки";

if ($dayid && $lessonname && $teacher && $date && $period) {
    // Запрос к timetable
    $stmt = $conn->prepare("SELECT `homework`, `lessontopic` FROM `timetable` 
                            WHERE `dayid` = ? AND `lessonname` = ? AND `teacher` = ? 
                              AND `date` = ? AND `period` = ?");
    $stmt->bind_param("issss", $dayid, $lessonname, $teacher, $date, $period);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $homework = $row["homework"] ?? "Нет данных";
        $lessontopic = $row["lessontopic"] ?? "Нет данных";
    }

    // Запрос к marks
    $stmt = $conn->prepare("SELECT `mark` FROM `marks` 
                            WHERE `dayid` = ? AND `lessonname` = ? 
                              AND `date` = ? AND `period` = ? AND `studentname` = ?");
    $stmt->bind_param("issss", $dayid, $lessonname, $date, $period, $currentfullname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $mark = $result->fetch_assoc()["mark"];
    }

    // Назначение класса оценки
    $markClass = match ($mark) {
        "5", "4" => "mark-good",
        "3" => "mark-average",
        "2", "1" => "mark-bad",
        "н", "п", "б", "Нет оценки" => "mark-none",
        default => "mark-default",
    };
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница урока</title>
    <link rel="stylesheet" href="../student.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lessoninfo {
            width: 300px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        h2 {
            margin: 0 0 20px 0;
        }

        hr {
            margin: 10px 0;
        }

        .mark-good {
            background-color: lightgreen;
            color: black;
            padding: 5px;
            border-radius: 5px;
        }

        .mark-average {
            background-color: orange;
            color: white;
            padding: 5px;
            border-radius: 5px;
        }

        .mark-bad {
            background-color: red;
            color: white;
            padding: 5px;
            border-radius: 5px;
        }

        .mark-none {
            background-color: gray;
            color: white;
            padding: 5px;
            border-radius: 5px;
        }

        .mark-default {
            background-color: lightgray;
            color: black;
            padding: 5px;
            border-radius: 5px;
        }

        .getmark {
            border: 1px solid black;
            background-color: #666666;
            color: white;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .hovershadow:hover{
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        }
        a{
            text-decoration: none;
        }
    </style>
</head>
<body>
    <center>
        <h2>Страница урока</h2>
        <div class="lessoninfo">
            <p>Предмет: <?php echo htmlspecialchars($lessonname); ?></p>
            <hr>
            <p>ДЗ к уроку: <?php echo htmlspecialchars($homework); ?></p>
            <p>Изучаемая тема: <?php echo htmlspecialchars($lessontopic); ?></p>
            <hr>
            <p>Время проведения: <?php echo htmlspecialchars($date); ?> (<?php echo htmlspecialchars($dayid); ?> урок)</p>
            <p>Период проведения: <?php echo htmlspecialchars($period); ?></p>
            <div class="getmark">
                <?php if($mark != "Нет оценки"): ?>
                    <p>Оценка: <a href="stats.php?dayid=<?php echo $dayid; ?>&lessonname=<?php echo $lessonname; ?>&group=<?php echo $currentgroupname; ?>&date=<?php echo $date; ?>&period=<?php echo $period; ?>"><span class="<?php echo $markClass; ?>"><?php echo htmlspecialchars($mark); ?></span></a></p>
                <?php else: ?>
                    <p>Оценка: <a href="#"><span class="<?php echo $markClass; ?>"><?php echo htmlspecialchars($mark); ?></span></a></p>
                <?php endif; ?>
                </div><br>
            <a href="index.php?date=<?php echo $date; ?>&period=<?php echo $period; ?>"><button class="hovershadow">В дневник</button></a>
        </div>
    </center>
</body>
</html>