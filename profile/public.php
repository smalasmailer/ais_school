<?php
    require "../config.php";
    if(isset($_GET["profile"])){
        $profile = $_GET["profile"];
        $res = $conn->query("SELECT * FROM `students` WHERE `login` = '$profile'");
        if($res->num_rows>0){
            die("Нельзя просматривать профили учеников. <a href='/'>Главная</a>");
        }
    } else{
        header("Location: ../index.html");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль: <?php echo $profile;?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .aboutprofile{
            padding:15px;
            border-radius:5px;
            background-color:gray;
            max-width:500px;
            color:white;
        }
        .aboutschool h3{
            background-color:black;
            padding:5px;
            max-width:250px;
        }
    </style>
</head>
<body>
    <nav>
        <ul class="menu">
            <li><a href="../index.html" style='display:flex;justify-content:center;'>Школа</a></li>
            <li><a href="settings/">Настроить профиль</a></li>
            <li><a href="../login">Войти в аккаунт</a></li>
        </ul>
    </nav>
    <h2>Сведения о профиле</h2>
    <center><div class="aboutprofile">
        <?php
            $res = $conn->query("SELECT `role`, `school`, `fullname` FROM `users` WHERE `login` = '$profile'");

            if($res->num_rows>0){
                $row = $res->fetch_assoc();

                echo "<p>Роль: ".$row["role"]."</p>";
                echo "<p>Полное имя: ". $row["fullname"] ."</p>";
                
                if(!empty($row["school"])){
                    echo "<p>Работает в школе: <a href='school.php?school=". urlencode($row['school']) . "'><button>$row[school]</button></a></p>";
                } else{
                    $res = $conn->query("SELECT `orgshort` FROM `organs` WHERE `directorname` = '$row[fullname]'");
                    $row = $res->fetch_assoc();
                    echo "<p>Работает в органе: <a href='organ.php?organ=". urlencode($row['orgshort']) ."'><button>$row[orgshort]</button></a></p>";
                }
            }
        ?>
    </div></center>
</body>
</html>