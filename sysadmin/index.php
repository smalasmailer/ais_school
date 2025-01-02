<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: ../index.html");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Системный администратор</title>
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
    <?php require "adminheader.php";
    echo "<br>";
        if($currentyid == "нет" && empty($currentyid)){
            echo "<a href='/profile/settings' style='padding: 10px; background-color: lightcoral;'>Привяжите Яндекс ID к профилю!</a><br><br>";
        }    
    ?>
    <h2>Администрирование платформой</h2><br>
    <h3>Аудит системы</h3>
    <div class="admintools">
        <a href="aucal.php">Календарь аудитов</a>
        <a href="audit.php">Аудит</a>
    </div><br>
    <h3>Управление школами</h3>
    <div class="admintools">
        <a href="schemes/">Схемы</a>
        <a href="stats/">Статистика по школам</a>
        <a href="manageschools/add.php">Создать школу</a>
        <a href="manageschools/rem.php">Удалить школу</a>
    </div><br>
    <h3>Управление активистами</h3>
    <div class="admintools">
        <a href="activists/">Активисты</a>
    </div><br>
    <h3>Управление органами</h3>
    <div class="admintools">
        <a href="organs/dellic.php">Отобрать лицензию</a>
        <a href="organs/addlic.php">Вернуть лицензию</a>
        <a href="organs/orgs.php">Органы</a>
    </div><br>
    <h3>Новости</h3>
    <div class="admintools">
        <a href="news/all.php">Отправить всем школам</a>
        <a href="news/organ.php">Отправить всем школам органа</a>
        <a href="news/check.php">Просмотр опубликованных новостей</a>
    </div>
    <?php
        if($currentfullname == "Березин П. А." || $currentfullname == "Рыбкин Т. И.1"):
    ?>
        <br><h3>Отчеты</h3>
        <p>По профилям сис. админов и активистов</p>
        <div class="admintools">
            <a href="reports/yid.php">Кто привязал Яндекс ID</a>
            <a href="reports/tg.php">Кто привязал Telegram</a>
        </div><br>
        <h3>Логи</h3>
        <div class="admintools">
            <a href="logs/check.php">Логи</a>
        </div>
    <?php endif; ?>
</body>
</html>