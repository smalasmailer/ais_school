<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if(isset($_GET["scheme"])){
        $scheme = $_GET["scheme"];
    }
    if($currentrole != "Директор"){
        header("Location: ../../index.php");
        exit();
    }

    $res = $conn->query("SELECT COUNT(`id`) AS lesson_count FROM `{$currentschool}_calls`");
    $row = $res->fetch_assoc();
    $lessons = $row["lesson_count"] ?? 0; // Предотвращаем ошибки при отсутствии данных
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Печать расписания</title>
    <link rel="stylesheet" href="/style.css">
    <script>
        window.print();
    </script>
</head>
<body>
<style>
        table.timetable {
            border: 2px solid #FFFFFF;
            width: 100%;
            text-align: center;
            border-collapse: collapse;
        }
        table.timetable td, table.timetable th {
            border: 1px solid #FFFFFF;
            padding: 3px 4px;
        }
        table.timetable tbody td {
            font-size: 16px;
            border: 1px solid black;
        }
        table.timetable tbody td:hover{
            background-color: gray;
            color: white;
        }
        table.timetable td:nth-child(even) {
            background: #EBEBEB;
        }
        table.timetable thead {
            background: #FFFFFF;
            border-bottom: 4px solid #333333;
        }
        table.timetable thead th {
            font-size: 16px;
            font-weight: bold;
            color: #333333;
            text-align: center;
            border-left: 2px solid #333333;
        }
        table.timetable thead th:first-child {
            border-left: none;
        }

        table.timetable tfoot {
            font-size: 16px;
            font-weight: bold;
            color: #333333;
            border-top: 4px solid #333333;
        }
        table.timetable tfoot td {
            font-size: 16px;
        }
    </style>
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
        <p>Отчёт сделан при помощи АИС "Школьник"</p>
    </center>
</body>
</html>