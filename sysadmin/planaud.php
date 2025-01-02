<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
if($currentrole != "Админ"){
    header("Location: /");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(!empty($_POST["date"]) && !empty($_POST["main"])){
        $date = $_POST["date"];
        $main = $_POST["main"];
        
        $stmt = $conn->prepare("INSERT INTO `auditplan`(`date`, `creator`, `main`, `status`, `comment`) VALUES(?, ?, ?, 'Не проведен', NULL)");
        $stmt->bind_param("sss", $date, $currentfullname, $main);
        $stmt->execute();
        $stmt->close();
        
        header("Location: aucal.php");
        exit();
    } else {
        echo "Пожалуйста, заполните все поля.";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Запланировать аудит</title>
</head>
<body>
    <?php require "adminheader.php"; ?>
    <h2>Запланировать аудит</h2>
    <form method="post">
        <label for="date">Дата проведения: <input type="date" name="date" required></label><br>
        <label for="main">Ответственный: 
            <select name="main" required>
                <?php
                    $res = $conn->query("SELECT `fullname` FROM `users` WHERE `role` = 'Админ'");
                    while($row = $res->fetch_assoc()){
                        $fullname = htmlspecialchars($row["fullname"]);
                        echo "<option value='$fullname'>$fullname</option>";
                    }
                    $res->free();
                ?>
            </select>
        </label><br>
        <input type="submit" value="Запланировать">
    </form>
</body>
</html>