<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
if(session_status() == PHP_SESSION_NONE || session_status() == PHP_SESSION_DISABLED){
    session_start();
}
if(!isset($_COOKIE['acclogin'])){
    header("Location: ../");
    exit();
}

echo "<link rel='stylesheet' href='$site/style.css'>";

$res = $conn->query("SELECT `fullname` FROM `blacklist` WHERE `fullname` = '$currentfullname'");
if($res->num_rows > 0){
    echo $currentfullname;
    $res->free();
    die("Вы состоите в <a href='../blacklist.php'>ЧС</a>");
}
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?php echo $site; ?>/style.css">
<style>
    body {
        margin: 0; /* Убираем стандартные отступы */
        font-family: Arial, sans-serif;
    }
    .content {
        margin-left: 220px; /* Отступ для контента */
        padding: 20px;
        box-sizing: border-box; /* Учитываем внутренние отступы */
    }
    .menu {
        list-style-type: none;
        padding: 0;
        margin: 0; /* Убираем внешние отступы у списка */
    }
    .menu li {
        margin: 10px 0; /* Отступы между элементами меню */
        margin: 5px;
    }
    .menu a {
        text-decoration: none;
        color: #fff; /* Цвет текста для контраста */
        padding: 10px; /* Padding для равномерного отступа */
        display: block; /* Делаем ссылки блочными для удобства клика */
        background-color: #007bff; /* Цвет фона элементов меню */
        border-radius: 4px; /* Закругление углов */
        text-align: center; /* Центрируем текст */
        transition: background-color 0.3s; /* Плавный переход цвета */
        width: 170px;
    }
    .menu a:hover {
        background-color: #0056b3; /* Цвет фона при наведении */
    }
    *{
        margin: 0; /* Убираем все отступы */
        /* padding: 0; /* Убираем все внутренние отступы */
        box-sizing: border-box; /* Учитываем отступы и границы в размерах */
    }
</style>
<section class="headsect">
<div class="sidebar">
    <nav>
        <ul class="menu">
            <li>
                <a href="<?php echo $site;?>/school">
                    АИС "Школа"
                </a>
            </li>
            <li><a href="<?php echo $site; ?>/school/myschool.php" class="dropbtn">Моя школа</a></li>
            <li><a href="<?php echo $site; ?>/school/teacher.php">Учительская</a></li>
            <li><a href="<?php echo $site; ?>/school/arm/">АРМ Завуч</a></li>
            <li><a href="<?php echo $site; ?>/profile/settings/">Мой профиль</a></li>
            <li><a href="<?php echo $site; ?>/logout.php">Выйти</a></li>
        </ul>
    </nav>
</div>
</section>