<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
if($currentrole != "Директор"){
    header("Location: ../../index.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["scheme"])) {
        $scheme = $_GET["scheme"];

        if(isset($_GET["clear"]) && $_GET["clear"]){
            $conn->query("TRUNCATE TABLE `$scheme`");
            header("Location: edit.php?scheme=". urlencode($scheme));
            exit();
        }
    } else {
        header("Location: ../../index.php");
        exit(); // Добавляем exit для предотвращения выполнения лишнего кода
    }
} elseif($_SERVER["REQUEST_METHOD"] == "POST"){
    if($_POST["action"] == "add"){
        $scheme = $_POST["scheme"];
        $lesson = $_POST["lesson"];
        $dayid = $_POST["dayid"];
        $dayweek = $_POST["dayweek"];
        $grade = $_POST["grade"];

        $res = $conn->query("SELECT `teacher` FROM `workload` WHERE `group` = '$grade' AND `lesson` = '$lesson' AND `school` = '$currentschool'");
        $row = $res->fetch_assoc();
        $teacher = $row["teacher"] ?? "Неизвестно";

        $res = $conn->query("SELECT * FROM `$scheme` WHERE `dayid` = '$dayid' AND `lesson` = '$lesson' AND `dayweek` = '$dayweek' AND `teacher` = '$teacher'");
        if(!$res->num_rows>0){
            $conn->query("INSERT INTO `$scheme` (`dayid`, `lesson`, `dayweek`, `teacher`) VALUES ('$dayid', '$lesson', '$dayweek', '$teacher')");
        }

        header("Location: edit.php?scheme=". urlencode($scheme));
        exit();
    } elseif($_POST["action"] == "rem"){
        $scheme = $_POST["scheme"];
        $dayid = $_POST["dayid"];
        $dayweek = $_POST["dayweek"];

        $conn->query("DELETE FROM `$scheme` WHERE `dayid` = '$dayid' AND `dayweek` = '$dayweek'");
        header("Location: edit.php?scheme=". urlencode($scheme));
        exit();
    }

}

// Получаем количество уроков из базы данных
$res = $conn->query("SELECT COUNT(`id`) AS lesson_count FROM `{$currentschool}_calls`");
$row = $res->fetch_assoc();
$lessons = $row["lesson_count"] ?? 0; // Предотвращаем ошибки при отсутствии данных

// Получаем класс из схемы
$res = $conn->query("SELECT `grade` FROM `schemes` WHERE `scheme` = '$scheme'");
$row = $res->fetch_assoc();
$grade = $row["grade"] ?? 'Неизвестно'; // Валидация на случай пустого результата
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование схемы</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .tablemenu {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        table.timetable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: white;
        }
        table.timetable th, table.timetable td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        table.timetable th {
            background-color: #f2f2f2;
        }
        details summary {
            background-color: darkblue;
            color: white;
            padding: 5px;
            border-radius: 4px;
            cursor: pointer;
            max-width: 250px;
            margin: 5px auto;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .timetable td:hover {
            background-color: #f0f0f0;
        }
        .edittb{
            background-color:#c5c5c5; 
            max-width: 850px;
            text-align: center;
            padding: 5px;
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <?php require "../../schoolhead.php"; ?>
    <h2>Редактирование схемы <?php echo htmlspecialchars($scheme); ?></h2>
    <p>Если не отображаются ячейки для добавления уроков, добавьте расписание звонков.</p>
    <center><div class="edittb">
        <center>
            <details>
                <summary>Добавить урок</summary>
                <form method="post">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="scheme" value="<?php echo htmlspecialchars($scheme); ?>">
                    <input type="hidden" name="grade" value="<?php echo htmlspecialchars($grade); ?>">
                    <label for="lesson">Урок:</label>
                    <select name="lesson">
                        <?php
                            $res = $conn->query("SELECT `lesson` FROM `workload` WHERE `group` = '$grade' AND `school` = '$currentschool'");
                            while ($row = $res->fetch_assoc()) {
                                echo "<option value='{$row['lesson']}'>{$row['lesson']}</option>";
                            }
                        ?>
                    </select><br>
                    <label for="dayid"><select name="dayid">
                        <?php
                            for ($i = 1; $i <= $lessons; $i++) {
                                echo "<option value='{$i}'>{$i}</option>";
                            }
                        ?>
                    </select>-м уроком</label><br>
                    <label for="dayweek">В <select name="dayweek">
                        <option value="понедельник">ПН</option>
                        <option value="вторник">ВТ</option>
                        <option value="среда">СР</option>
                        <option value="четверг">ЧТ</option>
                        <option value="пятница">ПТ</option>
                        <option value="суббота">СБ</option>
                    </select></label><br>
                    <input type="submit" value="+">
                </form>
            </details><br>
            <details>
                <summary>Удалить урок</summary>
                <form method="post">
                    <input type="hidden" name="action" value="rem">
                    <input type="hidden" name="scheme" value="<?php echo htmlspecialchars($scheme); ?>">
                    <input type="hidden" name="grade" value="<?php echo htmlspecialchars($grade); ?>">
                    <label for="dayid"><select name="dayid">
                        <?php
                            for ($i = 1; $i <= $lessons; $i++) {
                                echo "<option value='{$i}'>{$i}</option>";
                            }
                        ?>
                    </select>-м уроком</label>
                    <label for="dayweek"> в <select name="dayweek">
                        <option value="понедельник">ПН</option>
                        <option value="вторник">ВТ</option>
                        <option value="среда">СР</option>
                        <option value="четверг">ЧТ</option>
                        <option value="пятница">ПТ</option>
                        <option value="суббота">СБ</option>
                    </select></label><br>
                    <input type="submit" value="-">
                </form>
            </details><br>
            <details>
                <summary>Публикация</summary>
                <a href="publish.php?scheme=<?php echo htmlspecialchars($scheme); ?>"><button>Публикация</button></a>
            </details>
        </center>
    <br>
    <center>
        <div class="tablemenu">
            <a href="?scheme=<?php echo htmlspecialchars($scheme); ?>&clear=1"><button>Очистить схему</button></a>
            <a href="print.php?scheme=<?php echo htmlspecialchars($scheme); ?>"><button>На печать</button></a>
        </div>
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
    </div></center>
</body>
</html>