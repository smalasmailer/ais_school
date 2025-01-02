<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

if (isset($_GET["scheme"]) && $currentrole == "Директор") {
    $scheme = $conn->real_escape_string($_GET["scheme"]); // Защита от SQL-инъекций

    $stmt = $conn->prepare("SELECT `grade` FROM `schemes` WHERE `scheme` = ?");
    $stmt->bind_param("s", $scheme);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $grade = $row["grade"] ?? 'Неизвестно';
    $stmt->close();
} else {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $monday = $_POST['monday'];
    $period = (int)$_POST['period'];
    $start_date = new DateTime($monday);

    $days_of_week = ['понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'];

    $stmt = $conn->prepare("SELECT `dayid`, `lesson`, `dayweek`, `teacher` FROM `$scheme`");
    $stmt->execute();
    $schedule_res = $stmt->get_result();

    while ($lesson = $schedule_res->fetch_assoc()) {
        $week_day = stripslashes($lesson['dayweek']);
        if (in_array($week_day, $days_of_week)) {
            $day_index = array_search($week_day, $days_of_week);
            $current_date = (clone $start_date)->modify("+$day_index day")->format('Y-m-d');

            $lessonname = $lesson['lesson'];
            $teacher = $lesson['teacher'];
            $dayid = $lesson['dayid'];
            $homework = "";

            $insert_stmt = $conn->prepare(
                "INSERT INTO `timetable` (`dayid`, `date`, `lessonname`, `groupname`, `teacher`, `lessontopic`, 
                `homework`, `type`, `period`, `school`) 
                VALUES (?, ?, ?, ?, ?, '', ?, 'отв', ?, ?)"
            );
            $insert_stmt->bind_param(
                "isssssds", 
                $dayid, $current_date, $lessonname, $grade, $teacher, $homework, $period, $currentschool
            );
            $insert_stmt->execute();
            $insert_stmt->close();
        }
    }

    echo "<p>Расписание успешно опубликовано!</p>";
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Публикация расписания</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <?php require "../../schoolhead.php"; ?>
    <h2>Публикация "<?php echo htmlspecialchars($scheme); ?>"</h2>
    <form method="post">
        <label for="monday">Выберите дату понедельника той недели, на которую надо опубликовать:<br>
            <input type="date" name="monday" required>
        </label><br>
        <label for="group">Расписание публикуется для:<br>
            <input type="text" name="group" disabled value="<?php echo htmlspecialchars($grade); ?>">
        </label><br>
        <label for="period">В 
            <select name="period">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>-м периоде
        </label><br>
        <input type="submit" value="Публикация">
    </form>
</body>
</html>