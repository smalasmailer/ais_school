<?php
    if(isset($_GET["country"])){
        $country = $_GET["country"];
    } else{
        header("Location: countryblocker.php");
    }

    function getClientIpAB() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // In some cases, there might be multiple IPs in this header
            // due to proxies, and you typically want the first one.
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return null;
    }
    
    function getIpInfoAB($ip = null) {
        if ($ip === null) {
            return null;
        }
    
        $endpoint = "http://ip-api.com/json/{$ip}";
        $response = file_get_contents($endpoint);
        if ($response === false) {
            return null;
        }
    
        return json_decode($response);
    }
    
    function getCountryNameAB($ip) {
        $info = getIpInfoAB($ip);
        if ($info && isset($info->country)) {
            return $info->country;
        }
        return null;
    }
    
    function isBlocked($curip) {
        $white_list = ['Russia', 'Belarus', 'Kazakhstan'];
        $country = getCountryNameAB($curip);
    
        if ($country !== null && !in_array($country, $white_list)) {
            return false;
        } else{
            return true;
        }
    }

    $curip = getClientIpAB();
    if(isBlocked($curip)){
        header("Location: index.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access blocked</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <h2>Access from <?= $country; ?> is blocked.</h2><br>
    <p>We're sorry, but our system is not available in your country. If you are in Russia, Kazakhstan or Belarus, please try turning off your VPN.</p><br>
    <h2>Why is access blocked from other countries?</h2><br>
    <p>Access from countries other than Russia, Kazakhstan and Belarus is not available, as our system is not intended for use in other countries. You can follow all the news in our <a href="https://vk.com/ais_school">VK</a></p><br>
    <br><hr><br>
    <h2>Доступ из <?= $country; ?> заблокирован.</h2><br>
    <p>Извините, но наша система недоступна в вашей стране. Если вы из России, Казахстана или Беларуси, то попробуйте выключить VPN.</p><br>
    <h2>Почему система недоступна в других странах?</h2><br>
    <p>Система недоступна в странах, кроме России, Казахстана и Беларуси, т. к. она не адаптирована под другие страны. За всеми новостями вы можете следить в нашем <a href="https://vk.com/ais_school">ВК</a></p>
</body>
</html>