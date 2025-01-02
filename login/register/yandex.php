<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

if (isset($_GET['code'])) {
    $code = $_GET['code'];
} else {
    die("Ошибка: код авторизации отсутствует");
}

$client_id = 'abfbff2112fa4d03acd14f505258cd5f';
$client_secret = '708e64c049f44ee6956df5998559b464';

$params = [
    'grant_type' => 'authorization_code',
    'code' => $code,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
];

// Получаем access_token
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://oauth.yandex.ru/token');
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);

if (isset($data['access_token'])) {
    $access_token = $data['access_token'];
} else {
    die("Ошибка при получении access_token: " . $data['error_description']);
}

// Получаем информацию о пользователе
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://login.yandex.ru/info?format=json&oauth_token=$access_token");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$user_info = curl_exec($curl);
curl_close($curl);

$user_data = json_decode($user_info, true);

// Проверяем наличие Yandex ID
if (!isset($user_data['id'])) {
    die("Ошибка: не удалось получить Yandex ID пользователя");
}
$yandex_id = $user_data['id'];

$login = $_SESSION["setup_account_login"];

$conn->query("UPDATE `users` SET `yid` = '$yandex_id' WHERE `login` = '$login'");
header("Location: step3.php");
exit();
?>