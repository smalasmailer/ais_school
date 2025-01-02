<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">
    <style>
        /* Сброс базовых стилей */
        * {
            box-sizing: border-box;
        }

        /* Стили для навигации */
        nav {
            background-color: darkblue;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        /* Меню навигации */
        .menuteacher {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .left, .right {
            list-style: none;
            display: flex;
            align-items: center;
        }

        .left > li, .right > li {
            margin: 0 15px;
        }

        .left > li > a, .right > li > a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .overmenu{
            display: flex;
            text-align: left;
            padding-left: 50px;
        }
        .el{
            margin-right: 15px;
        }

        /* Стили для адаптации под мобильные устройства */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .menuteacher {
                flex-direction: column;
                gap: 10px;
            }

            .dropdown-content {
                position: static;
                min-width: 100%;
            }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
</head>
<body>
    <div class="modal" id="indevelop">
        <h2>В разработке...</h2>
    </div>
    <div class="overmenu">
        <h3 class="el"><a href="/school/" style="text-decoration: none;">АИС "<span style="color: darkblue;">Ш</span>кола"</a></h3>
        <a href="/mail/"><img src="/img/mail.png" width="24" height="24" style="margin-right: 15px;"></a>
        <a href="/communities/" class="el"><img src="/img/community.svg" width="24" height="24"></a>
    </div>
    <nav>
        <ul class="menuteacher">
            <div class="left">
                <li><a href="<?php echo $site;?>/school/teacher.php">Учительская</a></li>
            </div>
            <div class="right">
                <li><a href="<?php echo $site; ?>/profile/settings"><?php echo $currentfullname; ?></a></li>
            </div>
        </ul>
    </nav>
</body>
</html>