<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    checkLogin();

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["fullname"])){
            $stmt = $conn->prepare("INSERT INTO `messages`(`id`, `text`, `from`, `to`, `school`) VALUES(?, ?, ?, ?, ?)");
            $i = 1;
            $newtext = "Чат был создан";
            $stmt->bind_param("issss", $i, $newtext, $currentfullname, $_POST["fullname"], $currentschool);
            $stmt->execute();
            $stmt->close();
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        }
    }
    $chatsres = $conn->query("
        SELECT DISTINCT `to` AS user 
        FROM `messages` 
        WHERE `from` = '$currentfullname' AND `school` = '$currentschool'
        UNION
        SELECT DISTINCT `from` AS user 
        FROM `messages` 
        WHERE `to` = '$currentfullname' AND `school` = '$currentschool'
    ");

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Школа. Чаты</title>
    <link rel="stylesheet" href="/style.css">
    <link rel="shortcut icon" href="/img/chats.png" type="image/png">
    <style>
        .chats {
            display: flex;
            height: 90vh; /* Используйте 100vh для полного экрана */
            padding: 100px;
            background-color: white;
            padding-top: 15px;
        }

        .contacts {
            width: 20%;
            border: 2px solid black;
            height: 100%; /* Убедитесь, что высота задана */
        }

        .chat {
            width: 80%;
            border: 2px solid black;
            height: 100%;
            margin-left: 15px;
            background-color: white;
            position: relative; /* Для позиционирования логотипа */
        }

        .logo {
            width: 100px; /* Ширина логотипа */
            height: 100px; /* Высота логотипа */
            background-image: url("/img/chats.png"); /* Путь к логотипу */
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            position: absolute;
            top: 50%; /* Расположение по центру */
            left: 50%;
            transform: translate(-50%, -50%); /* Центрирование по координатам */
            opacity: 0.5; /* Прозрачность логотипа */
            border-radius: 50%;
        }
        .contactsheader{
            background-color: lightgray;
            padding: 5px;
            display: flex;
            justify-content: center;
        }
        .contactsheader button{
            padding: 3px;
            border-radius: 0;
            margin-left: auto;
            margin-left: 5px;
        }
        .contact{
            background-color: lightblue;
            padding: 5px;
            color: black;
            margin: 5px;
        }
        .chat-box {
            width: 100%;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .message {
            margin-bottom: 10px;
            padding: 5px;
        }

        .sender {
            color: #007bff;
        }

        .text {
            display: inline-block;
            margin-left: 5px;
        }
        .my-message {
            text-align: left;
        }

        /* Сообщения собеседника — справа */
        .their-message {
            text-align: right;
        }
        .newmessage {
            display: flex; /* Используем Flexbox для выравнивания */
            align-items: center; /* Вертикальное выравнивание по центру */
            height: 60px; /* Высота формы */
            width: 100%; /* Полная ширина формы */
            box-sizing: border-box; /* Учитываем отступы */
            gap: 10px; /* Расстояние между текстовым полем и кнопкой */
        }

        .newmessage textarea {
            flex: 1; /* Растягиваем текстовое поле на доступное пространство */
            height: 100%; /* Высота текстового поля равна высоте формы */
            padding: 10px; /* Внутренние отступы */
            font-size: 14px; /* Размер шрифта */
            resize: none; /* Убираем возможность изменения размера */
            border: 1px solid #ccc; /* Цвет границы */
            border-radius: 5px; /* Скругленные углы */
            box-sizing: border-box; /* Учитываем отступы */
        }

        .newmessage input[type="submit"] {
            width: 120px; /* Фиксированная ширина кнопки */
            height: 100%; /* Высота кнопки равна высоте формы */
            font-size: 14px; /* Размер шрифта */
            background-color: #4285f4; /* Синий фон кнопки */
            color: white; /* Белый цвет текста */
            border: none; /* Убираем границы */
            border-radius: 5px; /* Скругленные углы */
            cursor: pointer; /* Указатель при наведении */
            box-sizing: border-box; /* Учитываем отступы */
        }

        .newmessage input[type="submit"]:hover {
            background-color: #3367d6; /* Более темный оттенок при наведении */
        }
        .downloadapp{
            background-color:#007bff;
            color: white;
            display: flex;
            padding: 5px;
            justify-content: center;
        }
        .downloadapp h2{
            margin-top: 10px;
            margin-right: 15px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
</head>
<body>
    <div class="modal" id="unavaliable">
        <h2>Временно недоступно...</h2>
    </div>
    <div class="modal" id="newchat">
        <form method="post">
            <select name="fullname">
                <option value="" selected disabled>Выберите собеседника</option>
                <?php
                    $excludedNames = [];
                    while ($chat = $chatsres->fetch_assoc()) {
                        $excludedNames[] = $chat['to'];
                    }
                    $res = $conn->query("SELECT `fullname` FROM `users` WHERE `school` = '$currentschool' AND `fullname` != '$currentfullname'");
                    if ($res->num_rows > 0) {
                        while ($row = $res->fetch_assoc()) {
                            // Exclude names already in $excludedNames
                            if (!in_array($row['fullname'], $excludedNames)) {
                                echo "<option value='{$row['fullname']}'>{$row['fullname']}</option>";
                            }
                        }
                    }
                ?>
            </select>
            <input type="submit" value="+">
        </form>
    </div>
    <div class="downloadapp">
        <h2>Общайтесь со своими друзьями и учителями через мобильное приложение!</h2>
        <a href="#unavaliable" rel="modal:open"><img src="/img/downloadfromrustore.png" width="140" height="50"></a>
    </div>
    <section class="chats">
        <div class="contacts">
            <div class="contactsheader">
                <h3>Школа. Чаты</h3>
                <button><a href="/">На сайт</a></button>
                <button><a href="#newchat" rel="modal:open">+</a></button>
            </div>
            <?php
                $stmt = $conn->prepare("
                    SELECT DISTINCT 
                        CASE 
                            WHEN `to` = ? THEN `from` 
                            ELSE `to` 
                        END AS `chat_partner` 
                    FROM `messages` 
                    WHERE (`from` = ? OR `to` = ?) AND `school` = ?
                ");
                $stmt->bind_param("ssss", $currentfullname, $currentfullname, $currentfullname, $currentschool);
                $stmt->execute();
                $chatsres = $stmt->get_result();
                if($chatsres->num_rows == 0) {
                    echo "<div class='nochats'>";
                    echo "<h2>Чатов нет</h2>";
                    echo "<p>Но вы можете создать новый</p>";
                    echo "</div>";
                } else {
                    while ($row = $chatsres->fetch_assoc()) {
                        // Используем chat_partner, который получаем в запросе
                        echo "<a href='?chat=" . htmlspecialchars($row['chat_partner']) . "'>
                                <div class='contact'>" . htmlspecialchars($row['chat_partner']) . "</div>
                              </a>";
                    }
                }                
            ?>
            </div>
        </div>
        <div class="chat">
            <?php if(isset($_GET["chat"])): ?>
                <?php
                    $chatUser = $_GET['chat']; // Пользователь, с которым идет чат

                    // Запрос на выборку сообщений
                    $res = $conn->query("
                        SELECT * 
                        FROM `messages` 
                        WHERE 
                            (`to` = '$currentfullname' AND `from` = '$chatUser') OR 
                            (`from` = '$currentfullname' AND `to` = '$chatUser')
                        ORDER BY `id` ASC
                    ");

                    // Проверка, есть ли сообщения
                    if ($res->num_rows > 0) {
                        echo "<div class='chat-box' id='chat-box'>";
                        while ($row = $res->fetch_assoc()) {
                            $sender = htmlspecialchars($row['from']);
                            $messageClass = ($sender === $currentfullname) ? 'my-message' : 'their-message';
                            $message = htmlspecialchars($row['text']);
                            
                            // Отображение сообщения
                            echo "<div class='message $messageClass'>";
                            echo "<span class='sender'><strong>$sender:</strong></span> ";
                            echo "<span class='text'>$message</span> ";
                            echo "</div>";
                        }
                        echo "</div>";
                    } else {
                        echo "<p>Сообщений пока нет.</p>";
                    }
                    
                    // Закрытие соединения
                    $conn->close();
                ?>
                <form action="newmessage.php" method="post" class="newmessage" style="width: 100%;">
                    <textarea name="message" placeholder="Введите сообщение..." required></textarea>
                    <input type="hidden" name="chat" value="<?=$chatUser?>">
                    <input type="submit" value="Отправить">
                </form>
                <script>
                    let lastMessageId = <?php echo isset($lastMessageId) ? $lastMessageId : 0; ?>;
                    let chatUser = <?php echo json_encode($chatUser); ?>;

                    // Функция для обновления сообщений
                    async function fetchMessages() {
                        try {
                            const response = await fetch(`fetch_messages.php?chat=${encodeURIComponent(chatUser)}&lastid=${lastMessageId}`);

                            if (!response.ok) {
                                throw new Error('Ошибка сети');
                            }

                            const data = await response.json();

                            if (Array.isArray(data) && data.length > 0) {
                                let chatBox = document.getElementById('chatbox');

                                if (!chatBox) {
                                    console.error('Не найден элемент с ID chatbox');
                                    return;
                                }

                                data.forEach(msg => {
                                    // Создаём элемент сообщения
                                    let messageDiv = document.createElement('div');
                                    messageDiv.classList.add('message', msg.messageClass);
                                    messageDiv.innerHTML = `<span class="sender"><strong>${msg.sender}</strong></span>: <span class="text">${msg.message}</span>`;
                                    chatBox.appendChild(messageDiv);
                                });

                                // Обновляем ID последнего сообщения
                                lastMessageId = data[data.length - 1].id;
                                // Прокручиваем чат вниз
                                chatBox.scrollTop = chatBox.scrollHeight;
                            }
                        } catch (error) {
                            console.error('Ошибка при получении сообщений:', error);
                        }
                    }

                    // Обновление сообщений каждые 3 секунды
                    setInterval(fetchMessages, 3000);
                </script>
            <?php else: ?>
                <div class="logo"></div>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>