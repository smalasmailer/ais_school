<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if(!in_array($currentrole, ["Учитель", "Завуч", "Директор"])){
        header("Location: /");
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Модули</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
</head>
<body>
    <?php require "teacherhead.php"; ?>
    <div class="modal" id="unavaliable">
        <h2>Данный модуль недоступен по тех. причинам</h2>
        <p>О всех новостях можно узнать в <a href="https://vk.com/ais_school">сообществе</a></p>
    </div>
    <h2>Модули</h2>
    <a href="#unavaliable" rel="modal:open"><button>КТП</button></a>
</body>
</html>