<?php
    require "../../config.php";
    if(!isset($currentgroup)){
        die("Класс не назначен");
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $currentgroup; ?></title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        select, input{
            width: auto;
        }
    </style>
</head>
<body>
    <?php
    require "../schoolhead.php";
    ?>
    <h2>Состав класса:</h2>
    <?php
        $res = $conn->query("SELECT `fullname` FROM `students` WHERE `groupname` = '$currentgroup' AND `school` = '$currentschool'");
        if($res->num_rows>0){
            echo "<p>";
            while($row = $res->fetch_assoc()){
                echo $row['fullname']."<br>";
            }
            echo "</p>";
        }
    ?>
    <form action="../edit/personalfile.php" method="get">
        <select name="student">
            <option value="" disabled selected>Выберите студента</option>
            <?php
            $res = $conn->query("SELECT `fullname` FROM `students` WHERE `groupname` = '$currentgroup' AND `school` = '$currentschool'");
            if($res->num_rows>0){
                while($row = $res->fetch_assoc()){
                    echo "<option value='$row[fullname]'>$row[fullname]</option>";
                }
            }
            ?>
            <input type="hidden" name="fromklruk" value="1">
        </select>
        <input type="submit" value="Просмотр личного дела">
    </form>
</body>
</html>