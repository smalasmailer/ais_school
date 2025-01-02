<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: /");
        exit();
    }
    $author = $currentfullname . " (сис. админ.)";
    if(isset($_GET["delpost"])){
        $header = $_GET["delpost"];
        $conn->query("DELETE FROM `posts` WHERE `header` = '$header' AND `author` = '$author'");
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр новостей</title>
    <style>
        .post{
            padding: 5px;
            background-color:gainsboro;
            max-width: 500px;
        }
    </style>
</head>
<body>
    <?php 
    require "../adminheader.php";
    echo $author;
    $res = $conn->query("SELECT * FROM `posts` WHERE `author` = '$author'");

    if($res->num_rows>0){
        while($row = $res->fetch_assoc()){
            echo "<center><div class='post'><h3>$row[header]<br>$row[author]</h3><p>$row[text]</p><br>В $row[school]<br><a href='?delpost=". urlencode($row["header"]) . "'>Удалить</a></div></center><br>";
        }
    }
    ?>
</body>
</html>