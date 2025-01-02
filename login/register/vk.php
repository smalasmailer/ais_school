<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    if (isset($input['access_token'])) {
        $access_token = $input['access_token'];

        // Используйте access_token для получения информации о пользователе
        $user_info_url = "https://api.vk.com/method/users.get?access_token={$access_token}&v=5.131";

        $user_info_response = file_get_contents($user_info_url);
        if ($user_info_response === FALSE) {
            echo json_encode(['error' => 'Ошибка при получении данных о пользователе.']);
            exit();
        }
        $user_info_data = json_decode($user_info_response, true);
        if (isset($user_info_data['error'])) {
            echo json_encode(['error' => 'Ошибка API: ' . $user_info_data['error']['error_msg']]);
            exit();
        }

        // Обработка ответа о пользователе
        if (isset($user_info_data['response'][0]['id'])) {
            $user_id = $user_info_data['response'][0]['id'];
            echo json_encode(['user_id' => $user_id, 'message' => 'Успешно получен ID пользователя.']);
            $login = $_SESSION["setup_account_login"];
            $conn->query("UPDATE `users` SET `vk` = '$user_id' WHERE `login` = '$login'");
        } else {
            echo json_encode(['error' => 'Не удалось получить информацию о пользователе.']);
        }
    } else {
        echo json_encode(['error' => 'Access token не был передан.']);
    }
} else {
    echo json_encode(['error' => 'Неправильный метод запроса.']);
}
?>