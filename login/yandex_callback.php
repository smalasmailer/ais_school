<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Yandex Auth</title>
<script src="https://yastatic.net/s3/passport-sdk/autofill/v1/sdk-suggest-token-with-polyfills-latest.js"></script>
<script>
    const hash = window.location.hash;
    if (hash.includes("access_token")) {
        const accessToken = new URLSearchParams(hash.substring(1)).get("access_token");

        fetch("yandex_callback.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "access_token=" + encodeURIComponent(accessToken)
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            window.open("loginsuccess.php", "_blank");
            window.close();
        })
        .catch(error => console.error("Ошибка:", error));
    }
</script>
</head>
<body>
    <p>Пожалуйста, дождитесь обработки данных...</p>
    <?php
        require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
        echo "<meta charset='utf-8'>";

        // Проверка наличия токена
        if (!empty($_POST['access_token'])) {
            $access_token = $_POST['access_token'];

            // Получаем информацию о пользователе через Yandex API
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://login.yandex.ru/info?format=json&oauth_token=$access_token");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $user_info = curl_exec($curl);
            curl_close($curl);

            $user_data = json_decode($user_info, true);

            // Проверка Yandex ID
            if (!isset($user_data['id'])) {
                die("");
            }

            $yandex_id = $user_data['id'];

            // Проверка пользователя в базе
            $res = $conn->query("SELECT `login` FROM `users` WHERE `yid` = '$yandex_id'");

            if ($res->num_rows > 0) {
                $user = $res->fetch_assoc();
                setcookie('acclogin', $user['login'], time() + (86400 * 30), "/");
            } else {
                // Обновляем Yandex ID для текущего пользователя
                $stmt = $conn->prepare("UPDATE `users` SET `yid` = ? WHERE `fullname` = ? AND `login` = ?");
                $stmt->bind_param("sss", $yandex_id, $currentfullname, $acclogin);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            die("");
        }
    ?>
</body>
</html>
