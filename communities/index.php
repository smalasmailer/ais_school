<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    checkLogin();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сообщества</title>
    <style>
        .community{
            background-color: lightskyblue;
            text-align: left;
            max-width: 500px;
            padding: 15px;
            border-radius: 15px;
            color: black;
        }
        a{
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php require "header.php"; ?>
    <h2>Сообщества</h2>
    <p><font color="darkred">Предупреждаем, что сообщества в открытом BETA-тестировании среди пользователей системы, в том числе и учеников. Если чего-то не хватает или возникла ошибка, просим писать в наш чат в <a href='https://vk.com/ais_school'>ВК</a></font></p>
    <hr>
    <a href="create.php"><button>Новое сообщество</button></a><br><br>
    <section class="communities">
        <?php
            $res = $conn->query("SELECT * FROM `communities`");
            if($res->num_rows>0){
                while($row = $res->fetch_assoc()){
                    echo "<center><a href='/group.php?id=". urlencode($row["publicid"]) . "'><div class='community'>";
                    echo "<h2>$row[name]</h2>";
                    echo "<p>Категория: $row[category]<br><br>$row[description]</p>";
                    echo "</div></a></center>";
                }
            } else{
                echo "<p>Сообществ нет</p>";
                echo "<a href='https://tenor.com/ru/view/peach-goma-goma-and-peach-gif-5596612042147710615'><img src='https://media1.tenor.com/m/TaspAA3jRpcAAAAC/peach-goma-goma-and-peach.gif'></a>";
            }
        ?>
    </section>
</body>
</html>