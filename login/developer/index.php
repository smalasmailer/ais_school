<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if(isset($_COOKIE["acclogin"])){
        header("Location: /login/loginsuccess.php");
        exit();
    }

    if(isset($_POST["login"], $_POST["password"])){
        $login = $_POST["login"];
        $password = $_POST["password"];

        $stmt = $conn->prepare("SELECT `password` FROM `users` WHERE `login` = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $res = $stmt->get_result();

        if($res->num_rows>0){
            $row = $res->fetch_assoc();
            if(password_verify($password, $row["password"])){
                setcookie("acclogin", $login, time() + 86400, "/");
                header("Location: /schoolauth/dev");
                exit();
            } else{
                header("Location: ?error=authinvalid");
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
    <title>АИС "Школа" для разработчиков</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <?php 
        require $_SERVER["DOCUMENT_ROOT"] . "/devheader.php";
        if(isset($_GET["error"])){
            $error = $_GET["error"];
            if($error == "authinvalid"){
                echo "<span class='error'>Ошибка входа: неверный логин или пароль</span>";
            }
        }
    ?>
    <h2>Авторизация в Школа ID</h2>
    <form method="post">
        <input type="text" name="login" placeholder="Логин"><br>
        <input type="text" name="password" placeholder="Пароль"><br>
        <input type="submit" value="Авторизация">
    </form>
    <h3>У вас нет аккаунта Школа ID?</h3>
    <a href="register.php">Зарегистрируйтесь прямо сейчас!</a>
</body>
</html>