<?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    if(isset($_GET["orgshort"], $_GET["dir"], $_GET["dirmail"])){
        $orgshort = $_GET["orgshort"];
        $fullname = $_GET["dir"];
        $email = $_GET["dirmail"];
        
        // Убедитесь, что эта переменная инициализирована

        // Преобразуем заголовки в строку
        $headers = "From: support@ais-school.ru\r\n" .
                   "Reply-To: support@ais-school.ru\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        $subject = "Заявка на подключение организации в АИС «Школа»";
        $message = "Здравствуйте, $fullname!\n" .
                   "Ваша заявка на подключение организации в АИС «Школа» была отклонена.\n" .
                   "Организация: $orgshort\n";

        // Отправляем письмо
        if (mail($email, $subject, $message, $headers)) {
            // Редирект, если письмо отправлено успешно
            header("Location: /activism/panel.php");
            exit();
        } else {
            // Вывод ошибки, если письмо не отправлено
            echo "Ошибка: не удалось отправить письмо.";
        }        
    }
?>