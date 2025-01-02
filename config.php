<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', $_SERVER["DOCUMENT_ROOT"] . '/php-error.log');

ob_start();
if (session_status() == PHP_SESSION_NONE || session_status() == PHP_SESSION_DISABLED) {
    session_start();
}
$site = "http://localhost";
$works = false;

if($works == true && $_SERVER["PHP_SELF"] != "/login/tester.php"){
    if(isset($_COOKIE["istester"]) && $_COOKIE["istester"] == true){
        echo "Вы вошли как тестировщик";
    } else{
        header("Location: /works.html");
        exit();
    }
}

$host = 'localhost';
$user = 'секрет';
$password = 'секрет';
$db = 'shkolnik';
$roles = array("Учитель", "Завуч");
$hideroles = array("Администратор", "Директор", "Завуч", "Учитель", "Ученик", "ОУ");

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    error_log("Ошибка подключения к базе данных: " . $conn->connect_error);
    die("Ошибка подключения к базе данных.");
}
$conn->set_charset("utf8mb4");

// Получаем данные пользователя
if (isset($_COOKIE["acclogin"])) {
    $acclogin = $_COOKIE["acclogin"];

    // Получаем роль, школу и полное имя
    $res = $conn->query("SELECT `role`, `school`, `fullname`, `yid` FROM `users` WHERE `login` = '$acclogin'");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $currentrole = $row["role"];
        $currentschool = $row["school"];
        $currentfullname = $row["fullname"];
        $currentyid = $row["yid"] ?? "нет";
        $res = $conn->query("SELECT `organ`, `isfreeze` FROM `schools` WHERE `orgshort` = '$currentschool'");
        $res = $conn->query("SELECT `community`, `organ`, `isfreeze` FROM `schools` WHERE `orgshort` = '$currentschool'");
        if($res->num_rows>0){
            $row = $res->fetch_assoc();
            $organschool = $row["organ"];
            $isfreeze = $row["isfreeze"];
            $schoolcommunity = $row["community"] ?? "не привязано";

            if($isfreeze && $_SERVER["PHP_SELF"] != "/logout.php"){
                die("Ваша организация заморожена ОУ. <a href='/logout.php'>Выйти</a>");
            }
        }

        $res = $conn->query("SELECT `groupname` FROM `groups` WHERE `mainteacher` = '$currentfullname'");
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $currentgroup = $row["groupname"] ?? "нет";
        } else {
            $currentgroupname = "";
        }

        $res = $conn->query("SELECT `word` FROM `codewords` WHERE `login` = '$acclogin' AND `fullname` = '$currentfullname'");
        if($res->num_rows>0){
            $row = $res->fetch_assoc();
            $currentword = $row["word"];
        } else{
            $currentword = "";
        }
    }
    else {
        $currentschool = "";
        $currentfullname = "";
        $currentrole = "Ученик";
    }

    // Получаем URL аватара
    $res = $conn->query("SELECT `avatarurl` FROM `users` WHERE `login` = '$acclogin'");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $currentavatarurl = $row["avatarurl"];
    } else {
        $currentavatarurl = "$site/profile/noavatar.png";
    }

    // Получаем данные об организации
    $res = $conn->query("SELECT `orgshort`, `license` FROM `organs` WHERE `directorname` = '$currentfullname'");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $organname = $row["orgshort"];
        $license = $row["license"];

        if($_SERVER["PHP_SELF"] != "/organ/index.php"){
            $res = $conn->query("SELECT `reason` FROM `blockedorgans` WHERE `orgshort` = '$organname'");
            $idBlocked = false;
            if($res->num_rows>0){
                header("Location: /organ/index.php");
            }
        }
    }

    // Полное имя и группа ученика
    $res = $conn->query("SELECT `groupname`, `fullname`, `school` FROM `students` WHERE `login` = '$acclogin'");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $currentgroupname = $row["groupname"];
        $currentfullname = $row["fullname"];
        $currentschool = $row["school"];
    }
}
if (!function_exists('getCurrentPeriod1')) {
    function getCurrentPeriod1($conn, $school) {
        // Получаем сегодняшнюю дату
        $currentDate = new DateTime();

        // Извлекаем даты начала и окончания каждого периода
        $periodQuery = $conn->query("SELECT * FROM `{$school}_periods`");
        if ($periodQuery->num_rows > 0) {
            $periodData = $periodQuery->fetch_assoc();

            // Проверяем каждый период
            for ($i = 1; $i <= 4; $i++) {
                $start = new DateTime($periodData["period{$i}_from"]);
                $end = new DateTime($periodData["period{$i}_to"]);

                // Если текущая дата находится в пределах периода, возвращаем номер периода
                if ($currentDate >= $start && $currentDate <= $end) {
                    return $i;
                }
            }
        }

        return 1;
    }
}

if(!function_exists("checkLogin")){
    function checkLogin(){
        if(!isset($_COOKIE["acclogin"])){
            header("Location: /");
        }
        return;
    }
}
