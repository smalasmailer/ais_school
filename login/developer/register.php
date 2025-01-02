<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if(isset($_COOKIE["acclogin"])){
        header("Location: /login/loginsuccess.php");
        exit();
    }
    if(isset($_POST["login"], $_POST["password"], $_POST["fullname"])){
        $login = $_POST["login"];
        $password = $_POST["password"];
        $fullname = $_POST["fullname"];

        $stmt = $conn->prepare("SELECT `login` FROM `users` WHERE `login` = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows>0){
            header("?error=loginexists");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("INSERT INTO `users`(`login`, `password`, `role`, `school`, `fullname`, `avatarurl`) VALUES (?, ?, ?, NULL, ?, NULL)");
        $role = "Разработчик";

        $stmt->bind_param("ssss", $login, $hashed_password, $role, $fullname);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            // Обработка ошибок
            header("?error=sqlerror&comment=$stmt->error");
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>АИС "Школа" для разработчиков</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <?php 
        require $_SERVER["DOCUMENT_ROOT"] . "/devheader.php";
        if(isset($_GET["error"])){
            $error = $_GET["error"];
            if($error == "loginexists"){
                echo "<span class='error'>Ошибка создания профиля: логин уже занят</span>";
            } elseif($error == "sqlerror"){
                echo "<span class='error'>Ошибка БД: $_GET[comment]</span>";
            }
        }
    ?>
    <h2>Регистрация в Школа ID</h2>
    <form method="post">
        <input type="text" name="fullname" placeholder="ФИО"><br>
        <input type="text" name="login" placeholder="Логин"><br>
        <input type="text" name="password" placeholder="Пароль"><br>
        <input type="submit" value="Регистрация">
    </form>
    <h3>У вас есть аккаунт Школа ID?</h3>
    <a href="index.php">Войти</a>
</body>
</html>