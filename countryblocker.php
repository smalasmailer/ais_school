<?php

// Убедитесь, что функция объявляется только один раз
if (!function_exists('getClientIpCountryBlocker')) {
    function getClientIpCountryBlocker() {
        // Проверяем и возвращаем IP клиента
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // В некоторых случаях может быть несколько IP-адресов из-за использования прокси
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return null;
    }
}

if (!function_exists('getIpInfoCountryBlocker')) {
    function getIpInfoCountryBlocker($ip = null) {
        if ($ip === null) {
            return null;
        }

        // Указываем адрес API для получения информации об IP
        $endpoint = "http://ip-api.com/json/$ip";
        $response = @file_get_contents($endpoint);
        if ($response === false) {
            return null;
        }

        return json_decode($response, true);
    }
}

if (!function_exists('getCountryNameCountryBlocker')) {
    function getCountryNameCountryBlocker($ip) {
        $info = getIpInfoCountryBlocker($ip);
        if ($info && isset($info['country'])) {
            return $info['country'];
        }
        return null;
    }
}

if (!function_exists('whiteListCountryBlocker')) {
    function whiteListCountryBlocker($curip) {
        // Список разрешённых стран
        $whitelist = ['Russia', 'Belarus', 'Kazakhstan'];
        $country = getCountryNameCountryBlocker($curip);

        if ($country === null || !in_array($country, $whitelist)) {
            http_response_code(403);
            header("Location: /accessblocked.php?country=" . urlencode($country));
            exit;
        }
    }
}

// Получаем IP клиента и проверяем доступ
$curip = getClientIpCountryBlocker();
whiteListCountryBlocker($curip);
?>