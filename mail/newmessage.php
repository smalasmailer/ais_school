<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
checkLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["message"], $_POST["chat"])) {
        $message = $_POST["message"];
        $chat = $_POST["chat"];
        $from = $currentfullname;

        // Проверяем, есть ли уже сообщения между текущим пользователем и собеседником
        // Если есть, то используем автоинкремент для id
        $stmt = $conn->prepare("SELECT MAX(`id`) AS max_id FROM `messages` WHERE (`from` = ? AND `to` = ?) OR (`from` = ? AND `to` = ?) AND `school` = ?");
        $stmt->bind_param("sssss", $currentfullname, $chat, $chat, $currentfullname, $currentschool);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $id = $row['max_id'] + 1; // Следующий id для нового сообщения

        // Вставляем новое сообщение в базу данных
        $stmt = $conn->prepare("INSERT INTO `messages`(`id`, `text`, `from`, `to`, `school`) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $id, $message, $from, $chat, $currentschool);
        $stmt->execute();

        // Перенаправляем на страницу чата
        header("Location: index.php?chat=" . urlencode($chat));
        exit;
    }
}
?>
