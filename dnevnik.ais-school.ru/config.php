<?php
    $host = 'localhost';
    $user = 'smalasmailer';
    $password = 'Rq5XtD.kFhkUCTCS';
    $db = 'shkolnik';

    $conn = new mysqli($host, $user, $password, $db);
    if ($conn->connect_error) {
        error_log("Ошибка подключения к базе данных: " . $conn->connect_error);
        die("Ошибка подключения к базе данных.");
    }
    $conn->set_charset("utf8mb4");

    if(isset($_COOKIE["login"], $_COOKIE["password"])){
        $login = $_COOKIE["login"];
        $password = $_COOKIE["password"];

        $res = $conn->query("SELECT * FROM `students` WHERE `login` = '$login' AND `password` = '$password'");
        $row = $res->fetch_assoc();
        $groupname = $row["groupname"];
        $fullname = $row["fullname"];
        $origbirthday = $row["birthday"];
        $strtotimebirthday = strtotime($origbirthday);
        $convertbithday = date("d.m.y", $strtotimebirthday);
        $school = $row["school"];

        $res = $conn->query("SELECT `community` FROM `schools` WHERE `orgshort` = '$school'");
        if($res->num_rows>0){
            $community = $res->fetch_assoc()["community"] ?? "нет";
        }
    }

    if(!function_exists('getCurrentPeriodByDate')){
        function getCurrentPeriodByDate($conn, $school, $date){
            // Получаем сегодняшнюю дату
            $currentDate = $date;
    
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

    if(!function_exists('check_mobile_device')){
        function check_mobile_device() { 
            $mobile_agent_array = array('ipad', 'iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'cellphone', 'opera mobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser');
            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            foreach ($mobile_agent_array as $value) {
                if (strpos($agent, $value) !== false) return true;
            }
            return false;
        }
    }

    if(!check_mobile_device()){
        header("Location: pcunavaliable.html");
    }
?>