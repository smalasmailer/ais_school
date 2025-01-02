<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: /");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кто привязал Telegram</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<h2>Отчёт: Привязанные профили к Telegram</h2>
<h3>Привязанные профили</h3>
<?php
    // Запрос привязанных профилей
    $res = $conn->query("SELECT `fullname`, `role`, `tg` FROM `users` WHERE `tg` IS NOT NULL AND (`role` = 'Админ' OR `role` = 'Активист')");

    if($res->num_rows > 0){
        echo "<center><table><tr><th>Имя</th><th>Роль</th><th>Telegram</th></tr>";
        while($row = $res->fetch_assoc()){
            echo "<tr><td>{$row['fullname']}</td><td>{$row['role']}</td><td>{$row['tg']}</td></tr>";
        }
        echo "</table></center>";
    } else {
        echo "<p>Нет привязанных профилей</p>";
    }

    // Запрос непривязанных профилей
    echo "<h3>Непривязанные профили</h3>";
    $res = $conn->query("SELECT `fullname`, `role` FROM `users` WHERE `tg` IS NULL AND (`role` = 'Админ' OR `role` = 'Активист')");

    if($res->num_rows > 0){
        echo "<center><table><tr><th>Имя</th><th>Роль</th></tr>";
        while($row = $res->fetch_assoc()){
            echo "<tr><td>{$row['fullname']}</td><td>{$row['role']}</td></tr>";
        }
        echo "</table></center>";
    } else {
        echo "<p>Нет непривязанных профилей</p>";
    }
?>

</body>
</html>
