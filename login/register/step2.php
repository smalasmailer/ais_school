<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if(isset($_COOKIE["acclogin"])){
        header("Location: ../loginsuccess.php");
        exit();
    }

    if (isset($_POST["login"], $_POST["password"])) {
        $login = $_POST["login"];
        $password = $_POST["password"];

        // Используйте подготовленные выражения для защиты от SQL-инъекций
        $stmt = $conn->prepare("SELECT `yid`, `tg`, `password` FROM `users` WHERE `login` = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();

            // Используем password_verify для проверки хеша
            if (password_verify($password, $row['password'])) {
                $yid = $row["yid"];
                $tg = $row["tg"];

                if (!empty($yid) || !empty($tg)) {
                    // Сохраняем информацию в сессии, если необходимо
                    $_SESSION["setup_account_login"] = $login;

                    header("Location: ../index.php");
                    exit();
                }
            } else {
                // Неверный пароль
                header("Location: index.php?error=invalidpassword");
                exit();
            }
        } else {
            // Пользователь не найден
            header("Location: index.php?error=notexists");
            exit();
        }
    }

    $_SESSION["setup_account_login"] = $login;
    $_SESSION["setup_account_password"] = $password;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Этап 2. Привязка профиля</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        .icons{
            display: flex;
            justify-content: center;
        }
        .icon{
            margin: 5px;
        }
    </style>
</head>
<body>
    <?php require $_SERVER["DOCUMENT_ROOT"] . "/header.php" ?>
    <h2>Привяжите профиль</h2>
    <p>Выберите одну из соц. сетей:</p>
    <div class="icons">
        <div class="icon">
            <a href="https://oauth.yandex.ru/authorize?response_type=code&client_id=abfbff2112fa4d03acd14f505258cd5f&redirect_uri=<?php echo urlencode($site); ?>/login/register/yandex.php">
                <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="44" height="44" rx="22" fill="#FC3F1D"></rect>
                    <path d="M25.2438 12.3208H23.0173C19.2005 12.3208 17.292 14.2292 17.292 17.0919C17.292 20.2726 18.5643 21.863 21.427 23.7714L23.6535 25.3618L17.292 35.222H12.2029L18.2463 26.316C14.7475 23.7714 12.839 21.5449 12.839 17.41C12.839 12.3208 16.3378 8.82202 23.0173 8.82202H29.6969V35.222H25.2438V12.3208Z" fill="white"></path>
                </svg>
            </a>
        </div>
        <div class="icon">
            <script src="https://unpkg.com/@vkid/sdk@<3.0.0/dist-sdk/umd/index.js"></script>
            <script type="text/javascript">
                if ('VKIDSDK' in window) {
                    const VKID = window.VKIDSDK;

                    VKID.Config.init({
                        app: 52559297,
                        redirectUrl: '<?= $site ?>/login/register/vk.php',
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
                            fetch(`<?= $site ?>/login/register/vk.php`, {
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
                            window.location.href = "step3.php";
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
</body>
</html>