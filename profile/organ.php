<?php
    require "../config.php";
    if(isset($_GET["organ"])){
        $organ = $_GET["organ"];
    } else{
        header("Location: ../index.html");
        exit();
    }

    if($currentrole != "Админ"){
        header("Location: ../index.html");
        exit();
    }

    $res = $conn->query("SELECT `orgfull`, `directorname`, `license` FROM `organs` WHERE `orgshort` = '$organ'");
    if($res->num_rows>0){
        $row = $res->fetch_assoc();
        $orgfull = $row["orgfull"];
        $directorname = $row["directorname"];
        $license = $row["license"];
    } else{
        header("Location: ../login/loginsuccess.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Орган <?php echo $organ;?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .aboutorgan{
            padding:15px;
            border-radius:5px;
            background-color:gray;
            max-width:500px;
            color:white;
        }
        .aboutorgan input{
            width:75%;
        }

        .goodrate{
            font-family: Arial;
            background-color: lightgreen;
            padding:5px;
        }
        .mediumrate{
            font-family: Arial;
            background-color: orange;
            padding:5px;
        }
        .badrate{
            font-family: Arial;
            background-color: red;
            padding:5px;
        }

        .comment{
            background-color:darkgray;
            max-width:500px;
            padding:15px;
            border-radius:5px;
        }
        .schoolslist button{
            margin:5px;
        }
        .blockreason{
            background-color: red;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <? require "../sysadmin/adminheader.php"; ?>
    <h2>Сведения об «<?php echo $organ;?>»</h2>
    <center>
        <div class="aboutorgan">
            <h2>Сведения</h2>
            <p>Краткое наименование: <?php echo $organ?></p><br>
            <p>Полное наименование: <?php echo $orgfull;?></p><br>
            <p>Директор/руководитель: <?php echo $directorname;?></p><br>
            <p>Лицензия на выдачу школ: <?php
                if($license){
                    echo "<font color='lightgreen'>Есть</font><br><br>";
                } else{
                    $res = $conn->query("SELECT `reason` FROM `blockedorgans` WHERE `orgshort` = '$organ'");
                    $row = $res->fetch_assoc();
                    echo "<font color='darkred'>Нет</font><br><br>";
                    echo "<div class='blockreason'>Орган был заблокирован по причине: $row[reason]</div><br>";
                }
            ?></p>
            <hr>
            <h2>Школы</h2>
            <?php
                $res = $conn->query("SELECT `orgshort` FROM `schools` WHERE `organ` = '$organ'");
                if($res->num_rows>0){
                    echo "<div class='schoolslist'>";
                    while($row = $res->fetch_assoc()){
                        echo "<a href='school.php?school={$row["orgshort"]}'><button>".$row["orgshort"]."</button></a>";
                    }
                    echo "</div>";
                } else{
                    echo "Нет организаций";
                }
            ?>
    </center>
</body>
</html>