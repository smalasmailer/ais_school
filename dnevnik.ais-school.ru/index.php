<?php
require "config.php";

if (isset($_COOKIE["login"], $_COOKIE["password"])) {
    header("Location: dnevnik.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["login"], $_POST["password"])) {
        $login = $_POST["login"];
        $password = $_POST["password"];

        // Подготовленный запрос
        $stmt = $conn->prepare("SELECT `fullname`, `password` FROM `students` WHERE `login` = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Проверка пароля
            if ($password == $user["password"]) {
                setcookie("login", $login, time() + 1209600, "/", "", true, true);
                setcookie("password", $user['password'], time() + 1209600, "/", "", true, true);
                header("Location: dnevnik.php");
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Дневник</title>
</head>
<body>
    <h2>Авторизация</h2>
    <form method="post">
        <input type="text" name="login"><br>
        <input type="password" name="password"><br>
        <input type="submit" value="Войти">
    </form>
</body>
</html>