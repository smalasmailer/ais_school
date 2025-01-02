<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: ../../index.html");
        exit();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["schemeschool"], $_POST["scheme"])) {
            $schemeschool = $_POST["schemeschool"];
            $scheme = $_POST["scheme"];
    
            // Get count of calls
            $res = $conn->query("SELECT COUNT(`id`) AS count FROM `{$schemeschool}_calls`");
            $row = $res->fetch_assoc();
            $lessons = $row["count"];
    
            // Get grade for the selected scheme
            $stmt = $conn->prepare("SELECT `grade` FROM `schemes` WHERE `scheme` = ? AND `school` = ?");
            $stmt->bind_param("ss", $scheme, $schemeschool);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $grade = $row["grade"];
        } else {
            header("Location: ../../index.html");
            exit();
        }
    }    
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр схемы</title>
</head>
<body>
    <?php require "../adminheader.php"; ?>
    <h2>Схема: <?php echo $scheme; ?></h2>
    <center>
    <table class="timetable">
            <thead>
                <tr>
                    <th>№</th>
                    <th>ПН</th>
                    <th>ВТ</th>
                    <th>СР</th>
                    <th>ЧТ</th>
                    <th>ПТ</th>
                    <th>СБ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    for ($i = 1; $i <= $lessons; $i++) {
                        echo "<tr><td>$i</td>";
                        foreach (['понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'] as $dayweek) {
                            $res = $conn->query("SELECT `lesson`, `teacher` FROM `$scheme` WHERE `dayid` = '$i' AND `dayweek` = '$dayweek'");
                            $row = $res->fetch_assoc();

                            // Проверьте, существует ли $row
                            if ($row) {
                                $content = "{$row['lesson']}<br>{$row['teacher']}";
                                echo "<td data-dayid='$i' data-dayweek='$dayweek' data-lesson='{$row['lesson']}' data-teacher='{$row['teacher']}'>$content</td>";
                            } else {
                                // Если $row не существует, то используем значения по умолчанию
                                echo "<td data-dayid='$i' data-dayweek='$dayweek' data-lesson='' data-teacher=''>&nbsp;</td>";
                            }
                        }
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </center>
</body>
</html>