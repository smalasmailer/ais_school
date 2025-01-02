<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if(isset($acclogin)){
        header("Location: loginsuccess.php");
        exit();
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
            background: url("/img/loginbg.png");
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
        <h2>Вход через Telegram для учителей</h2>
        <a href="index.php"><button style="width: 15%">Назад</button></a>
        <br><br>
        <script async src="https://telegram.org/js/telegram-widget.js?22" data-telegram-login="ais_school_bot" data-size="large" data-auth-url="https://ais-school.ru/login/teacher_telegram_callback.php" data-request-access="write"></script>
    </div>
</body>
</html>