<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if (isset($_POST["schemename"], $_POST["grade"])) {
            $schemename = $conn->real_escape_string($_POST["schemename"]);
            $grade = $conn->real_escape_string($_POST["grade"]);
        
            // Проверка существования таблицы
            $res = $conn->query("SHOW TABLES LIKE '{$schemename}'");
            if ($res->num_rows > 0) {
                die("Придумайте другое название для схемы");
            }
        
            // Вставка новой записи в schemes
            $conn->query("INSERT INTO `schemes` (`scheme`, `school`, `grade`) VALUES ('$schemename', '$currentschool', '$grade')");
        
            // Создание новой таблицы
            $createTableQuery = "
                CREATE TABLE `{$schemename}` (
                    `dayid` INT,
                    `lesson` TEXT,
                    `dayweek` TEXT,
                    `teacher` TEXT
                )
            ";
        
            if (!$conn->query($createTableQuery)) {
                die("Ошибка при создании таблицы: " . $conn->error);
            }
        
            header("Location: index.php");
            exit();
        }
    }elseif($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET["deleteall"]) && $_GET["deleteall"]){
            $conn->query("DELETE FROM `marks` WHERE `school` = '$currentschool'");
            $conn->query("DELETE FROM `timetable` WHERE `school` = '$currentschool'");
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Схемы расписания</title>
</head>
<body>
    <?php
        require "../../schoolhead.php";
    ?>
    <h2>Схемы расписания</h2>
    <a href="?deleteall=1"><button>Удалить текущее расписание (все)</button></a>
    <hr>
    <?php
        $res = $conn->query("SELECT `scheme`, `grade` FROM `schemes` WHERE `school` = '$currentschool'");

        if($res->num_rows>0){
            while( $row = $res->fetch_assoc() ){
                echo "<a href='edit.php?scheme=$row[scheme]'><button>$row[scheme] ($row[grade])</button></a><br>";
            }
        } else{
            echo "Схем нет.";
        }
    ?>
    <hr>
    <h2>Создать схему</h2>
    <form method="post">
        <input type="text" name="schemename" placeholder="Название схемы">
        <select name="grade">
            <?php
                $res = $conn->query("SELECT `groupname` FROM `groups` WHERE `school` = '$currentschool'");
                while($row = $res->fetch_assoc()){
                    echo "<option value='$row[groupname]'>$row[groupname]</option>";
                }
            ?>
        </select>
        <input type="submit" value="Создать">
    </form>
</body>
</html>