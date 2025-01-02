<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    $res = $conn->query("SELECT `name` FROM `types` WHERE `school` = '$currentschool'");
    if($res->num_rows<1){
        $conn->query("INSERT INTO `types`(`name`, `school`) VALUES('отв', '$currentschool')");
    }
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["name"])){
            $name = trim($_POST["name"]);

            if($name == "отв"){
                header("Location: " . $_SERVER["PHP_SELF"]);
                exit();
            }

            $conn->query("INSERT INTO `types`(`name`, `school`) VALUES('$name', '$currentschool')");
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        }
    } elseif($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET["del"])){
            $del = $_GET["del"];
            $conn->query("DELETE FROM `types` WHERE `name` = '$del' AND `school` = '$currentschool'");
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style.css">
    <title>Типы уроков</title>
</head>
<body>
    <?php require "../schoolhead.php"; ?>
    <h2>Типы уроков</h2>
    <h3>Добавить</h3>
    <p>Указывайте краткое название! (к/р, с/р, пр, п/р)</p>
    <form method="post">
        <input type="text" name="name" placeholder="Отображаемое имя" require>
        <input type="submit" value="Добавить">
    </form><br>
    <h3>Управление</h3>
    <?php
        $res = $conn->query("SELECT `name` FROM `types` WHERE `school` = '$currentschool'");
        echo "<center><table>";
        if($res->num_rows>0){
            echo "<tr><td>Тип урока</td><td>Удалить</td></tr>";
            while($row = $res->fetch_assoc()){
                if($row["name"] == "отв"){
                    echo "<tr><td>$row[name]</td><td>нельзя</td></tr>";
                } else{
                    echo "<tr><td>$row[name]</td><td><a href='?del=$row[name]'>X</a></td></tr>";
                }
            }
        } else{
            echo "Ни одного типа не добавлено";
        }
        echo "</table></center>";
    ?>
</body>
</html>