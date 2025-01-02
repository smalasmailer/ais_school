<?php
    require "getinfo.php";
    getDevLogin();

    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()?><{}';
    $numbers = '1234567890';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["appname"])){
            $appname = $_POST["appname"];
            $stmt = $conn->prepare("INSERT INTO `idapps`(`appname`, `developer`, `secret`, `appid`, `active`) VALUES(?, ?, ?, ?, ?)");

            $randomString = substr(str_shuffle($characters), 0, 50);
            $randomNumber = substr(str_shuffle($numbers), 0, 10);
            $isActive = 1;
            
            $res = $conn->query("SELECT * FROM `idapps` WHERE `secret` = '$randomString'");
            if($res->num_rows>0){
                header("Location: index.php");
                exit();
            }

            $res = $conn->query("SELECT * FROM `idapps` WHERE `appid` = '$randomNumber'");
            if($res->num_rows>0){
                header("Location: index.php");
                exit();
            }

            $stmt->bind_param("ssssi", $appname, $fullname, $randomString, $randomNumber, $isActive);
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
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
    <style>
        .apps{
            display: flex;
            flex-wrap: wrap;
            max-width: 70%;
            justify-content: left;
            padding-left: 10%;
            gap: 20px;
        }
        .app{
            background-color: darkblue;
            border-radius: 15px;
            padding: 30px;
            color: white;
        }
        .app:hover{
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="modal" id="createnew">
        <h2>Создание нового приложения</h2>
        <form method="post">
            <input type="text" name="appname" placeholder="Название приложения">
            <input type="submit" value="Добавить">
        </form>
    </div>
    <?php
        require "header.php";
    ?>
    <h2>Мои приложения</h2><br>
    <section class="apps">
        <?php
            $res = $conn->query("SELECT * FROM `idapps` WHERE `developer` = '$fullname'");
            if($res->num_rows>0){
                while($row = $res->fetch_assoc()){
                    echo "<div class='app' onclick='window.location.href = \"control.php?app=$row[appid]\"'>";
                    echo "<h2>$row[appname]</h2>";
                    if($row["active"]){
                        echo "<p>Активно</p>";
                    } else{
                        echo "<p>Неактивно</p>";
                    }
                    echo "</div>";
                }
            }
        ?>
    </section><br>
    <button><a href="#createnew" rel="modal:open">Создать новое приложение</a></button>
</body>
</html>