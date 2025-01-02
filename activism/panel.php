<?php
require "../config.php";
if($currentrole != "Активист"){
    header("Location: /");
    exit();
}

if (isset($_GET["action"], $_GET["orgshort"], $_GET["orgfull"], $_GET["orgtype"])) {
    $orgshort = $_GET["orgshort"];
    $orgfull = $_GET["orgfull"];
    $action = $_GET["action"];
    $orgtype = $_GET["orgtype"];
    if ($action == "add") {
        if ($orgtype == "Школа") {
            // Подготовленный запрос для избежания SQL-инъекций
            $stmt = $conn->prepare("SELECT * FROM `requests_school` WHERE `orgfull` = ? AND `orgshort` = ?");
            $stmt->bind_param('ss', $orgfull, $orgshort);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $fullname = $row['fullname'];
                $email = $row['email'];
                $funddate = $row['funddate'];
                $reqlogin = $row['reqlogin'];
                $reqpassword = $row['reqpassword'];
                $avatarurl = ' ';
                // Добавление школы
                $stmt = $conn->prepare("INSERT INTO `schools`(`director`, `directorlogin`, `directoremail`, `orgfull`, `orgshort`, `funddate`, `orgtype`, `isblog`, `organ`) VALUES(?, ?, ?, ?, ?, ?, ?, 0, 'Школа')");
                $stmt->bind_param('sssssss', $fullname, $reqlogin, $email, $orgfull, $orgshort, $funddate, $orgtype);
                $stmt->execute();

                $hashpassw = password_hash($reqpassword, PASSWORD_BCRYPT);

                // Добавление пользователя
                $stmt = $conn->prepare("INSERT INTO `users`(`login`, `password`, `role`, `school`, `fullname`, `avatarurl`) VALUES(?, ?, 'Директор', ?, ?, ?)");
                $stmt->bind_param('sssss', $reqlogin, $hashpassw, $orgshort, $fullname, $avatarurl);
                $stmt->execute();
                // Удаление заявки
                $stmt = $conn->prepare("DELETE FROM `requests_school` WHERE `orgshort` = ? AND `orgfull` = ?");
                $stmt->bind_param('ss', $orgshort, $orgfull);
                $stmt->execute();
                // Перенаправление
                header("Location: accept.php/?orgfull=$orgfull&orgshort=$orgshort&fullname=$fullname&reqlogin=$reqlogin&reqpassword=$reqpassword&email=$email");
                exit();
            } else {
                echo "Заявка не найдена.";
            }
        } else {
            // Обработка для других типов организаций
            $stmt = $conn->prepare("SELECT `fullname`, `reqlogin`, `reqpassword`, `email` FROM `requests_organ` WHERE `orgshort` = ? AND `orgfull` = ?");
            $stmt->bind_param('ss', $orgshort, $orgfull);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $fullname = $row['fullname'];
                $reqlogin = $row['reqlogin'];
                $reqpassword = $row['reqpassword'];
                $email = $row['email'];
                // Добавление организации
                $stmt = $conn->prepare("INSERT INTO `organs`(`orgshort`, `orgfull`, `directorname`, `license`) VALUES(?, ?, ?, 1)");
                $stmt->bind_param('sss', $orgshort, $orgfull, $fullname);
                $stmt->execute();
                // Добавление пользователя

                $hashpassw = password_hash($reqpassword, PASSWORD_BCRYPT);

                $stmt = $conn->prepare("INSERT INTO `users`(`login`, `password`, `role`, `school`, `fullname`, `avatarurl`) VALUES(?, ?, 'ОУ', ' ', ?, ' ')");
                $stmt->bind_param('sss', $reqlogin, $hashpassw, $fullname);
                $stmt->execute();
                // Удаление заявки
                $stmt = $conn->prepare("DELETE FROM `requests_organ` WHERE `orgfull` = ? AND `orgshort` = ?");
                $stmt->bind_param('ss', $orgfull, $orgshort);
                $stmt->execute();
                // Перенаправление
                header("Location: accept.php/?orgfull=$orgfull&orgshort=$orgshort&fullname=$fullname&reqlogin=$reqlogin&reqpassword=$reqpassword&email=$email");
                exit();
            } else {
                echo "Заявка не найдена.";
            }
        }
    } else {
        // Обработка удаления заявки
        if ($orgtype == "Школа") {
            $res = $conn->query("SELECT `fullname`, `email` FROM `requests_school` WHERE `orgshort` = '$orgshort'");
            $row = $res->fetch_assoc();
            $fullname = $row["fullname"];
            $diremail = $row["email"];
            $stmt = $conn->prepare("DELETE FROM `requests_school` WHERE `orgfull` = ? AND `orgshort` = ?");
            $stmt->bind_param('ss', $orgfull, $orgshort);
            $stmt->execute();
            header("Location: deny.php?orgshort=$orgshort&dir=$fullname&dirmail=$diremail");
            exit();
        } else {
            $res = $conn->query("SELECT `fullname`, `email` FROM `requests_organ` WHERE `orgshort` = '$orgshort'");
            $row = $res->fetch_assoc();
            $fullname = $row["fullname"];
            $diremail = $row["email"];
            $stmt = $conn->prepare("DELETE FROM `requests_organ` WHERE `orgfull` = ? AND `orgshort` = ?");
            $stmt->bind_param('ss', $orgfull, $orgshort);
            $stmt->execute();
            header("Location: deny.php?orgshort=$orgshort&dir=$fullname&dirmail=$diremail");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аккаунт активиста</title>
    <link rel="stylesheet" href="../school/timetable/calls.css">
    <style>
        *{
            font-family: Arial;
        }
        .tab {
            overflow: hidden;
            background-color: #f1f1f1;
        }
        .tab button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            transition: 0.3s;
            font-size: 17px;
        }
        .tab button.active {
            background-color: #ccc;
        }
        .tabcontent {
            display: none;
            padding: 6px 12px;
            border-top: none;
        }
        .tabcontent.active {
            display: block;
        }
        .login-form {
            width: 300px;
            margin: 100px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            text-align: center;
        }
        .login-form input {
            width: 90%;
            padding: 10px;
            margin: 5px 0;
        }
        .login-form button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius:15px;
        }
        .login-form button:hover{
            background-color: #57a639;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h2>Аккаунт активиста (<?php echo $currentfullname ?>)</h2>

    <!-- Вкладки -->
    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'panel1')">Заявки на регистрацию школы</button>
        <button class="tablinks" onclick="openTab(event, 'panel2')">Заявки на регистрацию органа</button>
        <button class="tablinks" onclick="openTab(event, 'panel3')">Подробнее</button>
        <button class="tablinks"><a href="/profile/settings/">Профиль</a></button>
        <button class="tablinks"><a href="/logout.php">Выйти</a></button>
    </div>

    <!-- Содержимое вкладок -->
    <div id="panel1" class="tabcontent">
        <h3>Просмотр заявок школ</h3>
        <?php
            // Здесь предполагается, что подключение к базе данных уже настроено
            $res = $conn->query("SELECT * FROM `requests_school`");
            if ($res->num_rows > 0) {
                echo "<table>";
                echo "<tr><td>Полное имя</td><td>Почта</td><td>Полное наименование</td><td>Краткое наименование</td><td>Дата основания</td><td>Тип организации</td><td>Желаемый логин</td><td>Желаемый пароль</td><td>Действие</td></tr>";
                while ($row = $res->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>$row[fullname]</td><td>$row[email]</td><td>$row[orgfull]</td><td>$row[orgshort]</td><td>$row[funddate]</td><td>$row[orgtype]</td><td>$row[reqlogin]</td><td>$row[reqpassword]</td><td><a href='?action=add&orgshort=$row[orgshort]&orgfull=$row[orgfull]&orgtype=Школа'><button>Принять</button></a><a href='?action=deny&orgshort=$row[orgshort]&orgfull=$row[orgfull]&orgtype=Школа'><button>Отклонить</button></a></td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else{
                echo "Заявок нет.<br>";
                echo "<a href='https://tenor.com/ru/view/peach-goma-peach-and-goma-gif-5809717672824744439'><img src='https://media1.tenor.com/m/UKBDcTYHKfcAAAAC/peach-goma-peach-and-goma.gif' style='width: 250px; height: 200px;'></a>";
            }
        ?>
    </div>

    <div id="panel2" class="tabcontent">
        <h3>Просмотр заявок ОУ</h3>
        <?php
            $res = $conn->query("SELECT * FROM `requests_organ`");
            if ($res->num_rows > 0) {
                echo "<table>";
                echo "<tr><td>Полное имя</td><td>Полное наименование</td><td>Краткое наименование</td><td>Желаемый логин</td><td>Желаемый пароль</td><td>Действие</td></tr>";
                while ($row = $res->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>$row[fullname]</td><td>$row[orgfull]</td><td>$row[orgshort]</td><td>$row[reqlogin]</td><td>$row[reqpassword]</td><td><a href='?action=add&orgshort=$row[orgshort]&orgfull=$row[orgfull]&orgtype=Орган'><button>Принять</button></a><a href='?action=deny&orgshort=$row[orgshort]&orgfull=$row[orgfull]&orgtype=Орган'><button>Отклонить</button></a></td>";
                    echo "</tr>";
                }
            } else{
                echo "Заявок нет.<br>";
                echo "<a href='https://tenor.com/ru/view/peach-goma-peach-and-goma-gif-5809717672824744439'><img src='https://media1.tenor.com/m/UKBDcTYHKfcAAAAC/peach-goma-peach-and-goma.gif' style='width: 250px; height: 200px;'></a>";
            }
        ?>
    </div>

    <div id="panel3" class="tabcontent">
        <h3>О кабинете</h3>
        <p>Кабинет активиста АИС "Школьник". v1.3 (31.10.2024)</p>
        <h3>Функционал</h3>
        <p>Активист может просматривать заявки на подключение школ, заявки на подключение органов и обращения в тех. поддержку</p>
        <a href="https://vk.com/timurybkin"><button>Разработчик</button></a><br>
        <a href="https://vk.com/ais_shkolnik"><button>Сообщество ВК</button></a>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;

            // Скрываем все вкладки
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }

            // Убираем класс "active" с кнопок вкладок
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }

            // Отображаем текущую вкладку и делаем кнопку активной
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
        // Функция для проверки новых заявок
        function checkNewRequests() {
            fetch('check_new_requests.php')
                .then(response => response.json())
                .then(data => {
                    if (data.new_school > 0 || data.new_organ > 0) {
                        // Если есть новые заявки, показываем уведомление
                        if (Notification.permission === "granted") {
                            let message = `Новых заявок в школах: ${data.new_school}, в органах: ${data.new_organ}`;
                            new Notification("Новые заявки на подключение", {
                                body: message,
                                icon: '../img/logo.png' // Можете заменить на URL иконки
                            });
                        } else if (Notification.permission !== "denied") {
                            Notification.requestPermission().then(permission => {
                                if (permission === "granted") {
                                    let message = `Новых заявок в школах: ${data.new_school}, в органах: ${data.new_organ}`;
                                    new Notification("Новые заявки на подключение", {
                                        body: message,
                                        icon: '../img/icon.png'
                                    });
                                }
                            });
                        }
                    }
                });
        }

        // Запрашиваем разрешение на показ уведомлений при загрузке страницы
        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }

        // Регулярно опрашиваем сервер каждые 15 секунд
        setInterval(checkNewRequests, 15000);

    </script>
</body>
</html>