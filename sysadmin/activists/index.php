<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Активисты</title>
</head>
<body>
    <?php require "../adminheader.php"; ?><br>
    <h2>Управление аккаунтами активистов</h2><br>
    <h3>Добавить активиста</h3>
    <form action="add.php" method="get">
        <input type="text" name="fullname" placeholder="ФИО активиста"><br>
        <input type="text" name="login" placeholder="Логин"><br>
        <input type="text" name="password" placeholder="Пароль"><br>
        <input type="submit" value="Создать">
    </form><br>
    <h3>Удалить активиста</h3>
    <?php
        $res = $conn->query("SELECT `fullname` FROM `users` WHERE `role` = 'Активист'");
        if($res->num_rows>0){
            echo "<center><table>";
            echo "<tr><th>Имя активиста</th></tr>";
            while($row = $res->fetch_assoc()){
                echo "<tr><td><a href='rem.php?fullname=$row[fullname]'>$row[fullname]</a></td></tr>";
            }
            echo "</table></center>";
            echo "Нажмите на имя активиста, чтобы удалить его";
        } else{
            echo "Активистов нет.";
        }
    ?>
</body>
</html>