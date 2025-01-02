<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Ученик"){
        header("Location: /");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки профиля</title>
    <link rel="stylesheet" href="student.css">
</head>
<body>
    <br>
    <center><h2>Привязка Telegram</h2>
    <?php
        $res = $conn->query("SELECT `tg` FROM `students` WHERE `login` = '$acclogin'");
        $tg = $res->fetch_assoc()["tg"];
        if(!empty($tg)){
            echo "<p>Telegram привязан: $tg. <a href='tg_disconnect.php'>Отвзять</a></p>";
        } else{
            echo '<div class="telegram"><script async src="https://telegram.org/js/telegram-widget.js?22" data-telegram-login="ais_school_bot" data-size="large" data-auth-url="https://ais-school.ru/login/tg_callback.php" data-request-access="write"></script></div>';
        }
    ?></center>
</body>
</html>