<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if(isset($_GET["appid"], $_GET["redirect_uri"])){
        $appid = $_GET["appid"];
        $redir = $_GET["redirect_uri"];
    } else{
        header("Location: /index.php");
        exit();
    }

    $res = $conn->query("SELECT `appname` FROM `idapps` WHERE `appid` = '$appid'");
    $appname = $res->fetch_assoc()["appname"];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        .wrapper {
            display: flex;
            justify-content: center; /* Горизонтальное выравнивание */
            align-items: center;     /* Вертикальное выравнивание */
            height: 100vh;           /* Высота контейнера равна 100% высоты окна */
        }

        .authinfo {
            width: 450px;
            height: 200px;
            background-color: lightblue;
            margin-top: -50px; /* Отступ сверху */
            margin-left: 30px; /* Отступ слева */
            padding: 15px;
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="authinfo">
            <h2>Авторизация</h2>
            <p><?= $appname ?></p>
            <br>
            <?php if(isset($_COOKIE["acclogin"])): ?>
                <a href="redirect.php?appid=<?= $appid ?>&redirect_uri=<?= $redir ?>"><button>Войти как <?= $currentfullname ?></button></a>
            <?php else: ?>
                <a href="/login"><button>Войти в Школа ID</button></a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>