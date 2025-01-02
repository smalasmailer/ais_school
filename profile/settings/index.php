<?php
    require "../../config.php";
    if(!isset($_COOKIE["acclogin"])){
        header("Location: ../../index.html");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Параметры профиля</title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        .aboutprofile{
            padding:15px;
            border-radius:5px;
            background-color:gray;
            max-width:500px;
            color:white;
        }
        .aboutprofile h3{
            background-color:black;
            padding:5px;
            max-width:250px;
        }
        .settings{
            display: flex;
            justify-content: center;
        }
        .info, .linkprofile{
            background-color: darkblue;
            color: white;
            margin: 5px;
            padding: 5px;
        }
        .link{
            color: white;
        }
    </style>
</head>
<body>
    <?php
        if($currentrole == "Директор" || $currentrole == "Учитель" || $currentrole == "Завуч"){
            require $_SERVER["DOCUMENT_ROOT"] . "/school/teacherhead.php";
        } else{
            require $_SERVER["DOCUMENT_ROOT"] . "/header.php";
        }
    ?>  
    <h2>Здравствуйте, <?php echo $currentfullname;?>!</h2>
    <a href="/logout.php">Выйти из профиля</a><br><br>
    <center>
        <p>Внимание! С 1 декабря восстановление пароля по кодовому слову больше недоступно!<br>Рекомендуем привязать Яндекс и Telegram</p><br>
        <section class="settings">
            <div class="info">
                <h3>Информация</h3>
                <p>Имя в системе: <?php echo $currentfullname; ?></p><br>
                <p>Роль: <?php echo $currentrole; ?></p>
            </div>

            <div class="linkprofile">
                <h3>Привязать соц. сети</h3>
                <?php
                    $res = $conn->query("SELECT `yid` FROM `users` WHERE `fullname` = '$currentfullname' AND `login` = '$acclogin'");
                    $row = $res->fetch_assoc();
                    if(empty($row["yid"])):
                ?>
                    <h4>Войдите в Яндекс, чтобы привязать его</h4>
                    <a href="https://autofill.yandex.ru/suggest/popup?client_id=abfbff2112fa4d03acd14f505258cd5f&response_type=token&redirect_uri=https%3A%2F%2Fais-school.ru%2Flogin%2Fyandex_callback.php&location=https%3A%2F%2Fais-school.ru%2Flogin%2F&theme=light&version=1.65.4&widget_kind=button&ym_uid=173027202486375291&source_id=&uuid=b130419a-3b2f-493c-9917-81906f57b06a&button_view=iconBg&button_theme=light&button_size=m&button_type=&button_border_radius=0&button_icon=ya&custom_bg=rgba(180%2C%20184%2C%20204%2C%200.14)&custom_bg_hovered=rgba(180%2C%20184%2C%20204%2C%200.2)&custom_border=rgba(180%2C%20184%2C%20204%2C%200.28)&custom_border_hovered=rgba(180%2C%20184%2C%20204%2C%200.28)&custom_border_width=0&publicId=bj55rn0mdcmgycjvmybrn10h68"><button>Привязать Яндекс</button></a>
                <?php else: ?>
                    <p>Яндекс ID привязан: <? echo $row["yid"]; ?>. <a href="disconnect_yandex.php" class="link">Отвязать</a></p>
                <?php endif; ?><br><br>
                <?php 
                    $res = $conn->query("SELECT `tg` FROM `users` WHERE `fullname` = '$currentfullname' AND `login` = '$acclogin'");
                    $row = $res->fetch_assoc();
                    if(empty($row["tg"])):
                ?>
                    <h4>Войдите в Telegram, чтобы привязать его</h4>
                    <script async src="https://telegram.org/js/telegram-widget.js?22" data-telegram-login="ais_school_bot" data-size="large" data-auth-url="https://ais-school.ru/login/teacher_telegram_callback.php" data-request-access="write"></script>
                <?php else: ?>
                    <p>Telegram привязан: <? echo $row["tg"]; ?>. <a href="disconnect_telegram.php" class="link">Отвязать</a></p>
                <?php endif; ?><br><br>
                <?php 
                    $res = $conn->query("SELECT `vk` FROM `users` WHERE `fullname` = '$currentfullname' AND `login` = '$acclogin'");
                    $row = $res->fetch_assoc();
                    if(empty($row["vk"])):
                ?>
                    <h4>Войдите в ВК, чтобы привязать его</h4>
                    <div>
                        <script src="https://unpkg.com/@vkid/sdk@<3.0.0/dist-sdk/umd/index.js"></script>
                        <script type="text/javascript">
                            if ('VKIDSDK' in window) {
                                const VKID = window.VKIDSDK;

                                VKID.Config.init({
                                    app: 52559297,
                                    redirectUrl: '<?= $site ?>/login/linkvk.php',
                                    responseMode: VKID.ConfigResponseMode.Callback,
                                    source: VKID.ConfigSource.LOWCODE,
                                });

                                const oneTap = new VKID.OneTap();

                                oneTap.render({
                                    container: document.currentScript.parentElement,
                                    showAlternativeLogin: true,
                                    styles: {
                                    width: 360
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
                                        fetch(`<?= $site ?>/login/linkvk.php`, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({ access_token: accessToken })
                                        })
                                        .then(response => response.json())
                                        .then(result => {
                                            console.log('Server response:', result);

                                            location.reload();
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
                <?php else: ?>
                    <p>ВК привязан: <?= $row["vk"] ?>. <a href="disconnect_vk.php" class="link">Отвязать</a></p>
                <?php endif; ?>
            </div>
        </section>
    </center>
</body>