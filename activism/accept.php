<?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    if(isset($_GET["orgshort"]) && isset($_GET["orgfull"]) && isset($_GET["fullname"]) && isset($_GET["reqlogin"]) && isset($_GET["reqpassword"]) && isset($_GET["email"])){
        $orgshort = $_GET["orgshort"];
        $orgfull = $_GET["orgfull"];
        $fullname = $_GET["fullname"];
        $reqlogin = $_GET["reqlogin"];
        $reqpassword = $_GET["reqpassword"];
        $email = $_GET["email"];

        $headers = array(
            'From' => "support@ais-school.ru",
            'Reply-To' => "support@ais-school.ru",
            'X-Mailer' => 'PHP/' . phpversion()
        );

        $subject = "Заявка на подключение организации в АИС «Школа»";
        $message = "Здравствуйте, $fullname!\n
        Ваша заявка на подключение организации в АИС «Школа» была одобрена.\n
        Логин для входа: $reqlogin\n
        Пароль для входа: $reqpassword\n
        Организация: $orgfull ($orgshort)\n";

        mail($email, $subject, $message, $headers);
        header("Location: /activism/panel.php");
    }