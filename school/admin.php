<?php
    require "../config.php";
    if($currentrole != "Директор" && $currentrole != "Администратор"){
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Администрация</title>
    <style>
        /* Основные стили */
        body {
            background-color: #f4f6f9;
            color: #333;
        }
        h2, h3 {
            color: #333;
        }
        a {
            text-decoration: none;
            color: inherit;
        }

        /* Контейнер и выравнивание */
        .container {
            max-width: 1200px;
            margin: 15px auto;
            padding: 0 20px;
        }
        .settings-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .element {
            width: 150px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            text-align: center;
            padding: 15px;
        }
        .element:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .element img {
            max-width: 80px;
            margin-bottom: 10px;
        }
        .element p {
            margin: 0;
            font-weight: bold;
            color: #555;
        }

        /* Заголовки */
        h2 {
            text-align: center;
            margin-top: 0;
        }
        h3 {
            text-align: center;
            color: #444;
        }
    </style>
</head>
<body>
    <?php require "teacherhead.php"; ?>
    <div class="container">
        <h2>Администраторская панель</h2><br>

        <h3>Настройка школы</h3><br>
        <div class="settings-section">
            <div class="element">
                <a href="edit/groups.php">
                    <img src="icons/groups.png" alt="Группы">
                    <p>Группы</p>
                </a>
            </div>
            <div class="element">
                <a href="edit/lessons.php">
                    <img src="icons/lessons.png" alt="Предметы">
                    <p>Предметы</p>
                </a>
            </div>
            <div class="element">
                <a href="edit/people.php">
                    <img src="icons/teachers.png" alt="Учителя">
                    <p>Учителя</p>
                </a>
            </div>
            <div class="element">
                <a href="edit/students.php">
                    <img src="icons/students.png" alt="Ученики">
                    <p>Ученики</p>
                </a>
            </div>
            <div class="element">
                <a href="edit/types.php">
                    <img src="icons/types.png" alt="Типы">
                    <p>Типы уроков</p>
                </a>
            </div>
        </div>
        <br>
        <h3>Расписание</h3><br>
        <div class="settings-section">
            <div class="element">
                <a href="timetable/calls.php">
                    <img src="icons/calls.png" alt="Звонки">
                    <p>Звонки</p>
                </a>
            </div>
            <div class="element">
                <a href="timetable/periods.php">
                    <img src="icons/period.png" alt="Периоды">
                    <p>Периоды</p>
                </a>
            </div>
            <div class="element">
                <a href="timetable/load.php">
                    <img src="icons/load.png" alt="Нагрузка">
                    <p>Нагрузка</p>
                </a>
            </div>
            <div class="element">
                <a href="edit/timetable">
                    <img src="icons/timetable.png" alt="Схемы">
                    <p>Схемы</p>
                </a>
            </div>
            <div class="element">
                <a href="edit/timetable/replace/">
                    <img src="icons/timetable.png" alt="Замены">
                    <p>Замены</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>