<?php
    require "../../../config.php";
    if($currentrole != "Директор"){
        header("Location: ../../index.php");
        exit();
    }
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET["group_id"])){
            $group_id = $_GET["group_id"];
        }
    }
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $group_id = $_POST["group_id"];
        $mainteacher = $_POST["mainteacher"];

        $res = $conn->query("SELECT `groupname` FROM `groups` WHERE `mainteacher` = '$mainteacher' AND `school` = '$currentschool'");
        if($res->num_rows>0){
            $group = $res->fetch_assoc()["groupname"];
            die("Учитель уже назначен на $group класс");
        }

        $conn->query("UPDATE `groups` SET `mainteacher` = '$mainteacher' WHERE `groupname` = '{$group_id}' AND `school` = '$currentschool'");
        header("Location: index.php?group_id={$group_id}");
        exit();
    }
?> 
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование класса</title>
    <link rel="stylesheet" href="../../../style.css">
    <style>
        input{
            width:auto;
        }
    </style>
</head>
<body>
    <h2><?php echo $group_id; ?></h2>
    <a href="../groups.php"><button>Вернуться на страницу групп</button></a>
    <?php
        $res = $conn->query("SELECT `mainteacher` FROM `groups` WHERE `groupname` = '$group_id' AND `school` = '$currentschool'");
        $row = $res->fetch_assoc();
        $mainteacher = $row["mainteacher"] ?? "не указан";
    ?>
    <p>Текущий кл. руководитель: <?php echo $mainteacher; ?></p>
    <form method="post">
        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
        <select name="mainteacher">
            <option value="" selected disabled>Выберите кл. рук.</option>
            <?php
                $res = $conn->query("SELECT `fullname` FROM `users` WHERE `role` = 'Учитель' AND `school` = '$currentschool'");
                while($row = $res->fetch_assoc() ){
                    echo "<option value='".$row["fullname"]."'>".$row["fullname"]."</option>";
                }
            ?>
        </select>
        <input type="submit" value="Сохранить">
    </form>
</body>
</html>