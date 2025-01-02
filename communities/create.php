<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    checkLogin();

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["name"], $_POST["category"], $_POST["description"], $_POST["publicid"])){
            $stmt = $conn->prepare("SELECT publicid FROM communities WHERE publicid = ?");
            $stmt->bind_param("s", $_POST['publicid']);
            $stmt->execute();
            $res = $stmt->get_result();
    
            if($res->num_rows > 0){
                header("Location: create.php?error=idexists");
                exit();
            }
    
            $stmt = $conn->prepare("INSERT INTO communities (name, author, description, category, publicid) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $_POST['name'], $currentfullname, $_POST['description'], $_POST['category'], $_POST['publicid']);
            $stmt->execute();
    
            header("Location: index.php");
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новое сообщество</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        input, textarea{
            width: 50%;
        }
    </style>
</head>
<body>
    <?php require "header.php"; ?>
    <h2>Создание нового сообщества</h2>
    <?php
        if(isset($_GET["error"])){
            if($_GET["error"] == "idexists"){
                echo "<p>Публичный ID уже используется</p>";
            }
        }
    ?>
    <form method="post">
        <input type="text" name="name" placeholder="Название сообщества"><br>
        <input type="text" name="category" placeholder="Категория сообщества"><br>
        <textarea name="description" placeholder="Описание сообщества"></textarea><br>
        <input type="text" name="publicid" placeholder="Публичный ID"><br>
        <input type="submit" value="Создать">
    </form>
</body>