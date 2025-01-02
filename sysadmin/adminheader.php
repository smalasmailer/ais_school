<?php
if (session_status() == PHP_SESSION_NONE || session_status() == PHP_SESSION_DISABLED) {
    session_start();
}
if (!isset($_COOKIE['acclogin'])) {
    header("Location: ../");
    exit();
}

echo "<link rel='stylesheet' href='$site/style.css'>";

$stmt = $conn->prepare("SELECT `fullname` FROM `blacklist` WHERE `fullname` = ?");
$stmt->bind_param('s', $currentfullname);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo htmlspecialchars($currentfullname);
    $res->close();
    die("Вы состоите в <a href='../blacklist.php'>ЧС</a>");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        select{
            width: auto;
        }
        nav {
            position: relative;
        }

        .admmenu {
            list-style: none;
            padding: 0;
            margin: 0;
            background-color: blue; /* Основной фон меню */
        }

        .admmenu > li {
            display: inline-block;
            position: relative; /* Для правильного позиционирования dropdown */
        }

        .admmenu a {
            display: block;
            padding: 10px 15px;
            color: white; /* Цвет текста */
            text-decoration: none;
        }
        .admmenu a:hover{
            background-color: darkblue;
        }
    </style>
</head>
<body>
    <nav>
        <ul class="admmenu">
            <li><a href="<?php echo $site;?>/sysadmin"><span style="color: cyan;">Ш</span>кола</a></li>
            <li><a href="<?php echo $site;?>/profile/settings">Профиль</a></li>
            <li><a href="<?php echo $site; ?>/logout.php">Выйти</a></li>
        </ul>
    </nav>
</body>
</html>