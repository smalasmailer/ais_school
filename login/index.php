<?php
require "../config.php";

if (isset($_COOKIE["acclogin"])) {
    header("Location: loginsuccess.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acclogin = $_POST["acclogin"];
    $accpassword = $_POST["accpassword"];

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Подготовка запроса на выборку пользователя по логину
    $stmt = $conn->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->bind_param("s", $acclogin);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if ($row) {  // Если пользователь найден
        // Проверка пароля
        if (password_verify($accpassword, $row["password"])) {
            if ($row["role"] == "Активист" || $row["role"] == "Админ") {
                header("Location: ?roleerror=1");
                exit();
            }
            setcookie("acclogin", $acclogin, time() + 3600, "/");
            header("Location: loginsuccess.php");
            exit();
        }
    } else {
        // Если пользователь не найден в таблице users, проверяем таблицу students
        $stmt = $conn->prepare("SELECT * FROM students WHERE login = ?");
        $stmt->bind_param("s", $acclogin);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        if ($row && password_verify($accpassword, $row["password"])) {
            setcookie("acclogin", $acclogin, time() + 3600, "/");
            header("Location: loginsuccess.php");
            exit();
        } else {
            echo "<script>alert('Неверный логин или пароль');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body {
            background-color: #f0f0f0;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            display: flex;
            background: url("/img/loginbg.png") no-repeat;
        }
        header button{
            color: white;
            background-color: transparent;
            border: 1px solid white;
            padding: 10px 15px;
            margin: 0 5px;
            cursor: pointer;
            transition:0.3s;
        }
        header button:hover{
            background-color: black;
            color: white;
            transition:0.3s;
        }
        .login-container {
            background-color: white;
            padding: 30px;
            width: 40%;
            height: 100%;
            text-align: left;
            margin-left: auto;
        }
        .login-container h2 {
            color: #007bff;
            margin-bottom: 20px;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: auto;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .login-container button:hover {
            background: linear-gradient(90deg, #0056b3, #003d80);
        }
        .login-container .instruction {
            margin-top: 15px;
            color: #777;
            font-size: 12px;
        }
        header{
            background-color: gray;
            padding:5px;
            display:flex;
            justify-content: start;
        }
        .loginmethod button{
            width: 48%;
            margin-right: 2%;
        }
        .loginmethod{
            display: flex;
        }
        .loginmethod .selected{
            color: black;
            text-decoration: underline;
            padding-right: 10px;
        }
        .loginmethod .unselected{
            color: lightcoral;
            text-decoration: none;
            padding-right: 10px;
        }
        .icons{
            display:flex;
            padding: 5px;
        }
        .icon{
            padding: 5px;
            margin-right: 5px;
            background-color: #add8e6;
            transition: 0.3s background-color;
        }
        .icon:hover{
            background-color: #72bcd4;
        }
        .telegram{
            margin-top: 5px;
        }
    </style>
    <script src="https://yastatic.net/s3/passport-sdk/autofill/v1/sdk-suggest-with-polyfills-latest.js"></script>
</head>
<body>
    <div class="login-container">
        <?php
            if(isset($_GET["tgerror"])){
                echo "Ошибка входа через Telegram: <span style='color: red'>не найден профиль</span>";
            } elseif(isset($_GET["roleerror"])){
                echo "Ошибка входа: <span style='color: red'>Вы не можете войти по логину и паролю</span>";
            }
        ?>
        <!--<h2>Авторизация в Школа ID</h2>
        <form method="post">
            <input type="text" name="acclogin" placeholder="Логин"><br>
            <input type="password" name="accpassword" placeholder="Пароль"><br>
            <input type="submit" value="Войти"><br>
            <a href="developer/">Для разработчиков</a>
        </form>
        <hr>
        <center>Войти через</center>-->
        <a href="register/">Регистрация</a>
        <h3>Учителю</h3>
        <div class="icons">
            <div class="icon"><a href="https://autofill.yandex.ru/suggest/popup?client_id=abfbff2112fa4d03acd14f505258cd5f&response_type=token&redirect_uri=<?php echo urlencode($site) ?>/login/yandex_callback.php&location=https%3A%2F%2Fais-school.ru%2Flogin%2F&theme=light&version=1.65.4&widget_kind=button&ym_uid=173027202486375291&source_id=&uuid=b130419a-3b2f-493c-9917-81906f57b06a&button_view=iconBg&button_theme=light&button_size=m&button_type=&button_border_radius=0&button_icon=ya&custom_bg=rgba(180%2C%20184%2C%20204%2C%200.14)&custom_bg_hovered=rgba(180%2C%20184%2C%20204%2C%200.2)&custom_border=rgba(180%2C%20184%2C%20204%2C%200.28)&custom_border_hovered=rgba(180%2C%20184%2C%20204%2C%200.28)&custom_border_width=0&publicId=bj55rn0mdcmgycjvmybrn10h68"><svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="44" height="44" rx="22" fill="#FC3F1D"></rect>
                <path d="M25.2438 12.3208H23.0173C19.2005 12.3208 17.292 14.2292 17.292 17.0919C17.292 20.2726 18.5643 21.863 21.427 23.7714L23.6535 25.3618L17.292 35.222H12.2029L18.2463 26.316C14.7475 23.7714 12.839 21.5449 12.839 17.41C12.839 12.3208 16.3378 8.82202 23.0173 8.82202H29.6969V35.222H25.2438V12.3208Z" fill="white"></path>
                </svg></a>
            </div>
            <div class="icon"><a href="tgteacher.php"><img src="/img/tg.png" alt="Telegram" width="44" height="44"></a></div>
            <?php
                $url = 'https://oauth.vk.com/authorize';
                $params = ['client_id' => $client_id, 'redirect_uri'  => $redirect_uri, 'response_type' => 'code'];
                $link = $url . '?' . urldecode(http_build_query($params));
            ?>
            <div class="icon"><a href="<?= $link ?>"></a>
                <script src="https://unpkg.com/@vkid/sdk@<3.0.0/dist-sdk/umd/index.js"></script>
                <script type="text/javascript">
                    if ('VKIDSDK' in window) {
                        const VKID = window.VKIDSDK;
                        VKID.Config.init({
                            app: 52559297,
                            redirectUrl: '<?= $site ?>/login/vk_callback.php',
                            responseMode: VKID.ConfigResponseMode.Callback,
                            source: VKID.ConfigSource.LOWCODE,
                        });

                        const oneTap = new VKID.OneTap();

                        oneTap.render({
                            container: document.currentScript.parentElement,
                            showAlternativeLogin: true,
                            styles: {
                            width: 40
                            }
                        })
                        .on(VKID.WidgetEvents.ERROR, vkidOnError)
                        .on(VKID.OneTapInternalEvents.LOGIN_SUCCESS, function (payload) {
                            const code = payload.code;
                            const deviceId = payload.device_id;

                            VKID.Auth.exchangeCode(code, deviceId)
                            .then(vkidOnSuccess)
                            .catch(vkidOnError);
                        });
                        
                        function vkidOnSuccess(data) {
                            // Проверяем наличие access_token или id_token
                            if (data && data.access_token) {
                                const accessToken = data.access_token;
                                const idToken = data.id_token;

                                // Здесь вы можете использовать access_token для обращения к VK API или
                                // использовать idToken для расшифровки информации о пользователе

                                // Пример: Отправка Access Token на сервер для дальнейшей обработки
                                fetch(`<?= $site ?>/login/vk_callback.php`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ access_token: accessToken })
                                })
                                .then(response => response.json())
                                .then(result => {
                                    console.log('Server response:', result);
                                })
                                .catch(e => console.log('Ошибка при обработке ответа сервера:', e));


                                console.log('Получен access_token:', accessToken);
                            } else {
                                console.error('Неожиданные данные', data);
                            }
                        }

                        // Обработка ошибки
                        function vkidOnError(error) {
                            console.error('Ошибка при авторизации', error);
                        }
                    }
                </script>
            </div>
        </div>
        <h3>Ученику</h3>
        <div class="icons">
            <div class="telegram"><script async src="https://telegram.org/js/telegram-widget.js?22" data-telegram-login="ais_school_bot" data-size="large" data-auth-url="https://ais-school.ru/login/tg_callback.php" data-request-access="write"></script></div>
        </div>
        <p>При авторизации в системе вы автоматически принимаете <a href="/rules.html">пользовательское соглашение</a></p>
    </div>
</body>
</html>