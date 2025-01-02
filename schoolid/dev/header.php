<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Выпадающее меню</title>
    <style>
        /* Сброс базовых стилей */
        * {
            box-sizing: border-box;
            margin: 0; /* Добавлено для устранения возможных отступов по умолчанию */
            padding: 0;
        }

        /* Стили для навигации */
        nav {
            padding: 15px 30px;
            justify-content: flex-start; /* Выравнивание содержимого по левому краю */
            text-align: left;
        }

        /* Логотип или бренд */
        .brending {
            font-size: 32px;
            margin-right: 40px; /* Отступ между брендингом и меню */
        }

        .brending a {
            color: black;
            text-decoration: none;
        }

        .blueletter {
            color: darkblue;
        }

        /* Меню навигации */
        .menu {
            list-style: none;
            display: flex;
            gap: 20px;
            border-radius: 15px;
            justify-content: left;
        }

        .menu > li {
            position: relative;
        }

        .menu a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .menu a:hover {
            background-color: #0078D7;
        }

        /* Стили для адаптации под мобильные устройства */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .menu {
                flex-direction: column;
                gap: 10px;
            }

            .brending {
                font-size: 24px;
                margin-bottom: 20px; /* Отступ снизу для брендинга на мобильных */
            }

            .menu a {
                width: 100%; /* Расширение ссылок на всю ширину на мобильных */
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <nav>
        <h2 class="brending"><a href="/schoolauth/dev/index.php">АИС "<span class="blueletter">Ш</span>кола"</a></h2>
        <p>Для разработчиков</p>
        <ul class="menu">
            <li><a href="/schoolauth/dev/">Приложения</a></li>
            <li><a href="/schoolauth">Callback</a></li>
            <li><a href="/logout.php">Выйти</a></li>
        </ul>
    </nav>
</body>
</html>