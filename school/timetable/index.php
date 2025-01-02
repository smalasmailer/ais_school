<?php
    require "../../config.php";
    require "../schoolhead.php";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расписание</title>
    <style>
        .raspcreate{
            display: flex;
            justify-content:center;
            height:40px;
            margin-bottom:15px;
        }
        .raspcreate button:hover{
            background-color: green;
            transition:0.3s;
        }
    </style>
</head>
<body>
    <h2>Составление расписания</h2>
    <div>
        <div class="raspcreate">
            <h3>1.</h3><button style="width:50%;"><a href="calls.php">Звонки</a></button>
        </div>
        <div class="raspcreate">
            <h3>2.</h3><button style="width:50%;"><a href="periods.php">Периоды</a></button>
        </div>
        <div class="raspcreate">
            <h3>3.</h3><button style="width:50%;"><a href="load.php">Нагрузка</a></button>
        </div>
    </div>
    <p>Уроки добавляются самим учителем по мере их проведения</p>
</body>
</html>