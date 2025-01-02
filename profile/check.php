<?php
    require "../config.php";
    if(isset($_GET["user"]) && isset($_GET["school"])){
        $requser = $_GET["user"];
        $reqschool = $_GET["school"];
    } else{
        exit();
    }

    if(session_status() == PHP_SESSION_NONE || session_status() == PHP_SESSION_DISABLED){
        session_start();
    }

    if (!isset($_COOKIE["acclogin"])){
        header("Location: ../index.php");
    }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Проверка статуса профиля</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php
    
        if(trim($reqschool) != trim($currentschool)){
            echo "<p>Вы не можете просмотреть данные сотрудника другой школы!</p>";
            exit();
        }
        if($currentrole != "Директор" && $currentrole != "Администратор"){
            echo "<p>Только директор или администратор организации может просмотреть данные сотрудника школы.</p>";
            exit();
        }

        $res = $conn->query("SELECT * FROM users WHERE school = '$reqschool' AND fullname = '$requser'");
        $row = $res->fetch_assoc();
        $userlogin = $row['login'];
        $userpassword = $row['password'];
        $userrole = $row['role'];
    ?>
    <h2><?php echo $requser?></h2>
    <p>Логин: <?php echo $userlogin;?>; пароль: <button onclick="alert('<?php echo $userpassword;?>');">показать</button></p>
    <p>Роль сотрудника: <?php echo $userrole;?></p>
    <a href="public.php?profile=<?php echo $userlogin;?>">Профиль сотрудника в АИС "Школьник"</a>
    <a href="../school/edit/people.php">Вернуться на страницу сотрудников</a>
</body>
</html>