<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Завуч"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>АРМ Завуч</title>
    <style>
        .admintools{
            display: flex;
            justify-content: center;
        }
        .admintools a{
            background-color: black;
            color: white;
            text-decoration: none;
            color: white;
            padding: 5px;
            margin-right: 5px;
        }
        .admintools a:hover{
            background-color: gray;
        }
    </style>
</head>
<body>
    <?php require "../zavuchheader.php"; ?>
    <h2>АРМ Завуч</h2><br>
    <h3>Замены</h3>
    <div class="admintools">
        <a href="../edit/timetable/replace/">Замены</a>
    </div><br>
    <h3>Отчёты</h3>
    <div class="admintools">
        <a href="reports/">Новый отчёт</a>
    </div>
</body>
</html>