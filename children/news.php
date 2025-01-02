<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Ученик"){
        header("Location: /");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новости</title>
    <link rel="stylesheet" href="student.css">
</head>
<body>
    <center>
        <h1>Новостная лента</h1>
        <a href="index.php"><button>На главную</button></a>
        <section class="posts">
            <?php
                $res = $conn->query("SELECT * FROM `posts` WHERE `school` = '$currentschool' ORDER BY `id` DESC");
                if($res->num_rows>0){
                    while($row = $res->fetch_assoc()){
                        echo "<div class='post'>";
                        echo "<h3>$row[header]<br>$row[author]</h3>";
                        echo "<p>$row[text]</p>";
                        echo "</div><br>";
                    }
                } else{
                    echo "Постов нет.";
                }
            ?>
        </section>
    </center>
</body>
</html>