<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
checkLogin();

if (isset($_GET['chat']) && isset($_GET['last_id'])) {
    $chatUser = $_GET['chat']; // Пользователь, с которым идет чат
    $lastId = (int)$_GET['last_id']; // ID последнего сообщения, чтобы получить новые

    // Запрос на выборку новых сообщений
    $stmt = $conn->prepare("
        SELECT * 
        FROM `messages` 
        WHERE 
            (
                (`to` = ? AND `from` = ?) OR 
                (`from` = ? AND `to` = ?)
            ) 
            AND `id` > ? 
        ORDER BY `id` ASC
    ");
    $stmt->bind_param("sssss", $currentfullname, $chatUser, $chatUser, $currentfullname, $lastId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Проверка, есть ли новые сообщения
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $sender = htmlspecialchars($row['from']);
        $messageClass = ($sender === $currentfullname) ? 'my-message' : 'their-message';
        $message = htmlspecialchars($row['text']);

        $messages[] = [
            'id' => $row['id'],  // Добавлено поле 'id'
            'sender' => $sender,
            'message' => $message,
            'messageClass' => $messageClass,
        ];
    }

    // Возвращаем новые сообщения в формате JSON
    header('Content-Type: application/json');
    echo json_encode($messages);

    $stmt->close();
    $conn->close();
}
?>