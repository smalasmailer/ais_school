<?php
    require "../config.php";
    if(isset($_GET["school"])){
        $school = $_GET["school"];
    } else{
        header("Location: ../index.html");
        exit();
    }

    $res = $conn->query("SELECT `director`, `directoremail`, `orgfull`, `funddate`, `orgtype`, `organ` FROM `schools` WHERE `orgshort` = '$school'");
    if($res->num_rows==0){
        header("Location: ../index.html");
        exit();
    } else{
        $row = $res->fetch_assoc();
        $director = $row["director"];
        $directoremail = $row["directoremail"];
        $orgfull = $row["orgfull"];
        $funddate = $row["funddate"];
        $orgtype = $row["orgtype"];
        $organ = $row["organ"];
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Школа "<?php echo $school?>"</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .aboutschool{
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
        .post{
            padding: 5px;
            background-color:gainsboro;
            max-width: 500px;
        }
    </style>
</head>
<body>
    <nav>
        <ul class="menu">
            <li><a href="../index.html">Школа</a></li>
            <li><a href="../login/">Войти в профиль</a></li>
        </ul>
    </nav>
    <h2>Информация о школе <?php echo $school?></h2>
    <?php
        if(isset($_GET["fromstudent"])){
            if($_GET["fromstudent"]){
                echo "<a href='../children/'><button>Вернуться в дневник</button></a>";
            }
        }
    ?>
    <center>
        <div class="aboutschool">
            <h3>Данные о руководителе</h3>
            <p>Директор: <?php echo $director?><br>(почта: <?php echo $directoremail?>)</p>

            <hr>

            <h3>Наименование</h3>
            <p>Краткое наименование: <?php echo $school;?></p>
            <p>Полное наименование: <?php echo $orgfull?></p>

            <hr>

            <h3>Прочее</h3>
            <p>Дата основания: <?php echo $funddate?></p>
            <p>Тип: <?php echo $orgtype?></p>
            <p>Подключивший орган: <?php 
            if($organ == "Школьник"){
                echo "Самостоятельная организация";
            } else{
                echo "<a href='organ.php?organ=$organ'>$organ</a>";
            }
            ?></p>
        </div>
        <h2>Школьные записи</h2>
        <div class="posts">
            <?php 
                $res = $conn->query("SELECT `header`, `text`, `author` FROM `posts` WHERE `school` = '$school'");
                while ($row = $res->fetch_assoc()){
                    echo "<div class='post'>";
                    echo "<h3>$row[header]<br>$row[author]</h3>";
                    echo "<p>$row[text]</p></div>";
                }
            ?>
        </div>
    </center>
</body>
</html>