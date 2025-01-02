<?php
// Подключаем конфигурационный файл
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

// Функция для проверки подлинности данных
function checkTelegramAuthorization($auth_data) {
    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);

    // Сортируем данные и создаем строку для проверки хеша
    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
        $data_check_arr[] = "$key=$value";
    }
    sort($data_check_arr);
    $data_check_string = implode("\n", $data_check_arr);

    // Хешируем строку
    $secret_key = hash('sha256', 'BOTFATHER_TOKEN', true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);

    // Сравниваем хеши
    return hash_equals($hash, $check_hash);
}

// Получаем данные, переданные Telegram виджетом
$auth_data = $_GET;

// Проверяем подлинность
if (checkTelegramAuthorization($auth_data)) {
    // Если данные прошли проверку, можно использовать их для авторизации пользователя
    $user_id = $auth_data['id'];

    // Проверяем, существует ли пользователь в базе данных
    $res = $conn->query("SELECT `login` FROM `students` WHERE `tg` = '$user_id'");

    if($res->num_rows > 0) {
        // Если пользователь найден, устанавливаем куки и переадресуем
        $login = $res->fetch_assoc()["login"];
        setcookie("acclogin", $login, time() + 3600, "/"); // Установите время в будущем
        header("Location: loginsuccess.php");
        exit();
    } else {
        // Если пользователь не найден, проверяем на существование логина
        if (isset($acclogin)) {
            // Если логин существует, обновляем информацию о пользователе
            $conn->query("UPDATE `students` SET `tg` = '$user_id' WHERE `login` = '$acclogin'"); // Убедитесь, что вы добавили условие для обновления
            header("Location: loginsuccess.php");
            exit();
        }
    }
} else {
    // Если проверка не пройдена, отправляем ошибку
    echo 'Ошибка авторизации!';
}

?>
