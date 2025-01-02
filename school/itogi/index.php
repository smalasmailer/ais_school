<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    checkLogin();
    if($currentrole != "Учитель" && $currentrole != "Завуч" && $currentrole != "Директор"){
        header("Location: /");
        exit();
    }
    $res = $conn->query("SELECT `dayid`, `date`, `lessonname`, `groupname` FROM `timetable` WHERE `teacher` = '$currentfullname' AND `school` = '$currentschool'");

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $dayid = $row["dayid"];
        $lessonname = $row["lessonname"];
        $date = $row["date"];
        $group = $row["groupname"];
    } else {
        $dayid = null;
        $lessonname = null;
        $date = null;
        $group = null;
    }
    $marks = [5, 4, 3, 2, 1, 'н', 'п', 'б'];

    $marks_counts = array_fill_keys($marks, 0);
        if($dayid != null && $lessonname != null && $date != null && $group != null){
            foreach ($marks as $mark) {
                if (is_numeric($mark)) {
                    $condition = "`mark` = $mark";
                } else {
                    // Используйте приведение типа к строке для символов
                    $condition = "`mark` = '$mark'";
                }

                $marksres = $conn->query("
                    SELECT COUNT(`mark`) as count_marks
                    FROM `marks`
                    WHERE `dayid` = '$dayid'
                    AND `lessonname` = '$lessonname'
                    AND `date` = '$date'
                    AND `groupname` = '$group'
                    AND `school` = '$currentschool'
                    AND $condition
                ");

                $result = $marksres->fetch_assoc();
                $marks_counts[$mark] = $result["count_marks"] ? $result["count_marks"] : 0;
            }
        }

    // Теперь у вас есть массив $marks_counts, содержащий количество для каждой оценки, включая нули.
    $total_marks = array_sum($marks_counts);

    $fivemarks = $marks_counts[5];
    $fourmarks = $marks_counts[4];
    $threemarks = $marks_counts[3];
    $twomarks = $marks_counts[2];
    $onemarks = $marks_counts[1];
    $skipsmarks = $marks_counts['н'] + $marks_counts['п'] + $marks_counts['б'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Итоги года</title>
    <link rel="stylesheet" href="/style.css">
    <meta name="description" content="Подведите итоги года вместе со Школой!">
    <style>
        .head{
            text-align: left;
            padding: 100px;
            background-color: darkblue;
            color: white;
        }
        .head h1{
            font-size: 100px;
        }
        .head h2{
            font-size: 60px;
            transition: 0.3s;
        }
        .head h2:hover{
            text-decoration: underline;
        }
        .five:hover{
            color: lightgreen;
        }
        .three:hover{
            color: lightsalmon;
        }
        .two:hover{
            color: lightcoral;
        }
    </style>
</head>
<body>
    <section class="head">
        <h1>Итоги года</h1>
        <h2>Вместе с АИС «Школой»</h2>
    </section>
    <section class="head" style="background-color: darkred">
        <h1>Выставлено</h1>
        <h2><?= $total_marks ?> оценок</h2>
        <h1>Из них</h1>
        <h2 class="five"><?= $fivemarks ?> пятерок</h2>
        <h2 class="five"><?= $fourmarks ?> четверок</h2>
        <h2 class="three"><?= $threemarks ?> троек</h2>
        <h2 class="two"><?= $twomarks ?> двоек</h2>
        <h2 class="two"><?= $onemarks ?> колов</h2>
    </section>
    <section class="head" style="background-color: darkorange">
        <h2><?= $skipsmarks ?> учеников</h2>
        <h1>Пропустили ваши уроки</h1>
    </section>
    <section class="head" style="background-color: darkmagneta">
        <h1>Поделитесь вашими результатами в нашем чате</h1>
        <a href="https://vk.com/ais_school"><button><h2>Перейти</h2></button></a>
    </section>
</body>
</html>