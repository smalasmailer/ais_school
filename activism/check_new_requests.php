<?php
require "../config.php";
header('Content-Type: application/json');

// Проверка новых заявок для школ
$result = $conn->query("SELECT COUNT(*) AS count FROM `requests_school` WHERE `is_new` = 1");
$new_school = $result->fetch_assoc()['count'];

// Проверка новых заявок для органов
$result = $conn->query("SELECT COUNT(*) AS count FROM `requests_organ` WHERE `is_new` = 1");
$new_organ = $result->fetch_assoc()['count'];

// Отправка данных о новых заявках
$response = [
    'new_school' => $new_school,
    'new_organ' => $new_organ
];

echo json_encode($response);

// Обновление статуса заявок на 'не новые'
if ($new_school > 0) {
    $conn->query("UPDATE `requests_school` SET `is_new` = 0 WHERE `is_new` = 1");
}

if ($new_organ > 0) {
    $conn->query("UPDATE `requests_organ` SET `is_new` = 0 WHERE `is_new` = 1");
}
?>