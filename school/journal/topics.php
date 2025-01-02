<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/simple-xlsx/simplexlsx.class.php";

if (isset($_GET["lesson"]) && isset($_GET["period"]) && isset($_GET["group"])) {
    $lesson = $_GET["lesson"];
    $period = $_GET["period"];
    $group = $_GET["group"];
} else {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['ktpfile'])) {
    $file = $_FILES['ktpfile']['tmp_name'];
    $success = false; // Флаг успешного выполнения

    if ($xlsx = SimpleXLSX::parse($file)) {
        foreach ($xlsx->rows() as $index => $row) {
            if ($index === 0) continue; // Пропуск первой строки

            // Чтение и обработка даты
            $rawDate = $row[0];
            $date = false;

            if (is_numeric($rawDate)) {
                $date = DateTime::createFromFormat('Y-m-d', gmdate("Y-m-d", ($rawDate - 25569) * 86400));
            } elseif (DateTime::createFromFormat('Y-m-d H:i:s', $rawDate)) {
                $date = new DateTime($rawDate);
            } else {
                $date = DateTime::createFromFormat('d.m.Y', $rawDate);
            }

            if ($date) {
                $formattedDate = $date->format('Y-m-d');
            } else {
                echo "Некорректная дата: " . $row[0] . "<br>";
                continue; // Пропускаем некорректные даты
            }

            $lessonNumber = $conn->real_escape_string($row[1]);
            $topic = $conn->real_escape_string($row[2]);
            $homework = $conn->real_escape_string($row[3]);

            // Обновление данных в таблице
            $query = "UPDATE `timetable`
                      SET `lessontopic` = '$topic', `homework` = '$homework'
                      WHERE `lessonname` = '$lesson' AND `groupname` = '$group' AND `period` = '$period' AND `date` = '$formattedDate' AND `school` = '$currentschool' AND `dayid` = '$lessonNumber'";

            if ($conn->query($query)) {
                if ($conn->affected_rows > 0) {
                    $success = true; // Если хотя бы одна запись обновлена
                }
            } else {
                echo "Ошибка выполнения запроса: " . $conn->error . "<br>";
            }
        }
        if ($success) {
            // Если данные обновлены, перенаправляем на ту же страницу
            header("Location: topics.php?lesson=$lesson&period=$period&group=$group&success=1");
            exit(); // Завершаем выполнение скрипта
        } else {
            echo "Не удалось обновить записи.";
        }
    } else {
        echo SimpleXLSX::parseError();
    }
}

// Здесь можно добавить логику для отображения сообщения об успешном обновлении
if (isset($_GET['success'])) {
    echo "<p>КТП успешно обновлено!</p>";
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>КТП</title>
    <style>
        .topicdiv {
            display: flex;
            background-color: gray;
            border-radius: 5px;
            width: 1000px;
        }
        .topicdiv input {
            width: 45%;
            margin-left: 5px;
            margin-right: 5px;
        }
        .topicdiv p {
            color: white;
            width: 20%;
        }
        .topicdiv input:disabled {
            color: white;
        }
    </style>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <?php require "../schoolhead.php"; ?>

    <h2>КТП по <?php echo $lesson; ?> у <?php echo $group; ?> за <?php echo $period; ?> период</h2>

    <a href="allmarks.php?lesson=<?php echo $lesson; ?>&period=<?php echo $period; ?>&group=<?php echo $group; ?>">
        <button>Журнал по предмету</button>
    </a><br>
    
    <br>
    <button onclick="window.history.go(-1);">Вернуться на обратную страницу</button>
    
    <?php
    $res = $conn->query("SELECT DISTINCT `lessontopic`, `homework`, `dayid`, `date`, `type` FROM `timetable` WHERE `lessonname` = '$lesson' AND `groupname` = '$group' AND `period` = '$period' AND `school` = '$currentschool' ORDER BY `dayid`, `date` ASC");
    if ($res->num_rows > 0) {
        echo "<center><br><div class='topicdiv'>";
        echo "<p>Дата (№ урока)</p>";
        echo "<input type='text' disabled value='Темы'>";
        echo "<input type='text' disabled value='ДЗ'>";
        echo "</div><br>";

        while ($row = $res->fetch_assoc()) {
            echo "<div class='topicdiv'>";
            $date = DateTime::createFromFormat('Y-m-d', $row["date"]);
            $formDate = $date->format("d.m.y");
            echo "<p>$formDate ($row[dayid] урок)<br>Тип: $row[type]</p>";
            echo "<input type='text' value='{$row['lessontopic']}' onchange='saveData(this, \"lessontopic\", \"{$row['dayid']}\", \"{$row['date']}\", \"$lesson\", \"$group\")'>";
            echo "<input type='text' value='{$row['homework']}' onchange='saveData(this, \"homework\", \"{$row['dayid']}\", \"{$row['date']}\", \"$lesson\", \"$group\")'>";
            echo "</div><br>";
        }
        echo "</center>";
    }
    ?>
    <hr>
    <h2>Импорт КТП</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="ktpfile">.xlsx файл (<a href='ktpexample.xlsx' download>шаблон</a>):</label>
        <input type="file" name="ktpfile" id="ktpfile" accept=".xlsx">
        <button type="submit">Импортировать КТП</button>
    </form>
    <script>
        function saveData(input, field, dayid, date, lesson, group) {
            const value = input.value;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "save_lesson_data.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.responseText);
                }
            };

            xhr.send(`field=${field}&value=${encodeURIComponent(value)}&dayid=${dayid}&date=${date}&lesson=${lesson}&group=${group}`);
        }
    </script>
</body>
</html>