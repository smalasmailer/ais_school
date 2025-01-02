<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Пример Callback</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        body{
            text-align: left;
            padding: 25px;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet" />
    <meta name="description" content="Пример использования API Школа ID">
</head>
<body>
    <?php require "../devheader.php"; ?>
    <h2>Пример Callback-скрипта</h2>
    <p>Получение данных о пользователе:</p>
    <pre><code class="language-javascript">
    $secret = "секретный_код_приложения";
    if(isset($_GET["code"])){
        $code = $_GET["code"];
    }

    $get_userinfo_url = "https://ais-school.ru/schoolid/api/get.php?code=$code"; // пример, ссылка может отличаться от действительности
    $userinfo_response = file_get_contents($get_userinfo_url);

    echo "Имя пользователя: " . $userinfo_response["user_id"] . "\n";
    echo "ФИО: " . $userinfo_response["fullname"] . "\n";
    echo "Роль: " . $userinfo_response["role"] . "\n";
    echo "Организация (школа): " . $userinfo_response["school"] ?? "отсутствует" . "\n";
    echo "Организация (ОУ): " . $userinfo_response["organ"] ?? "отсутствует" . "\n";
    echo "Связанные профили: ";
    while($userinfo_response["linked"] as $profile){
        echo "$profile";
    }
    </code></pre>

    <h3>Вывод:</h3>
    <pre><code>
        Имя пользователя: rybkint
        ФИО: Рыбкин Тимур Иванович
        Роль: Директор
        Организация (школа): ЧОУ "Гимназия №0"
        Организация (орган): отсутствует
        Связанные профили: Яндекс, ВК
    </code></pre>

    <hr>
    <h2>Как подключить вход через Школа ID?</h2>
    <p>Данная возможность будет доступна вместе с выходом обновления 2.1 в 2025 году. О всех новостях можно узнать в нашем <a href="https://t.me/omp_school">ТГ</a></p>

    <!-- Подключите скрипт перед закрывающим тегом </body> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
</body>
</html>