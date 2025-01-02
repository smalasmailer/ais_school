<?php
    require "../../config.php";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчёты</title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        optgroup{
            font-weight: 700;
        }
    </style>
</head>
<body>
    <?php require "../schoolhead.php"; ?>
    <h2>Отчёты</h2>
    <form action="report.php" method="post" target="_blank">
        <select name="reporttype" required>
            <option value="" disabled selected>Выберите отчёт*</option>
            <optgroup label="Уроки">
                <option value="allcreated">Все созданные уроки</option>
            </optgroup>
            <optgroup label="Свойства об уроках">
                <option value="homeworks">Все ДЗ</option>
                <option value="topics">Все темы</option>
            </optgroup>
            <optgroup label="Оценки">
                <option value="marks">Средний балл по группе</option>
            </optgroup>
        </select>
        <select name="groupname" required>
            <option value="" disabled selected>Выберите группу*</option>
            <?php
                $res = $conn->query("SELECT `groupname` FROM `groups` WHERE `school` = '$currentschool'");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='$row[groupname]'>$row[groupname]</option>";
                }
            ?>
        </select>
        <select name="lessonname" required>
            <option value="" disabled selected>Выберите урок*</option>
            <?php
                $res = $conn->query("SELECT DISTINCT `lesson` FROM `workload` WHERE `teacher` = '$currentfullname' AND `school` = '$currentschool'");
                
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='$row[lesson]'>$row[lesson]</option>";
                }
            ?>
        </select>
        <select name="periodname">
            <option value="" disabled selected>Выберите период*</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
        </select><br>
        <input type="submit" value="Построить отчёт" style="width:auto;">
    </form>
</body>
</html>