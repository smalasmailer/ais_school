<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Завуч"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>АРМ Завуч: Отчёты</title>
</head>
<body>
    <?php require "../../zavuchheader.php"; ?>
    <h2>Выберите преподавателя, предмет и класс</h2>
    <form action="report.php" method="post">
        <select name="teacher">
            <?php
                $res = $conn->query("SELECT DISTINCT `teacher` FROM `workload` WHERE `school` = '$currentschool'");
                if($res->num_rows>0){
                    while($row = $res->fetch_assoc()){
                        echo "<option value='$row[teacher]'>$row[teacher]</option>";
                    }
                } else{
                    echo "<option value=''>Нет учителей</option>";
                }
            ?>
        </select>
        <select name="lesson">
            <?php
                $res = $conn->query("SELECT DISTINCT `lesson` FROM `workload` WHERE `school` = '$currentschool'");
                if($res->num_rows>0){
                    while($row = $res->fetch_assoc()){
                        echo "<option value='$row[lesson]'>$row[lesson]</option>";
                    }
                } else{
                    echo "<option value=''>Нет предметов</option>";
                }
            ?>
        </select>
        <select name="group">
        <?php
                $res = $conn->query("SELECT DISTINCT `group` FROM `workload` WHERE `school` = '$currentschool'");
                if($res->num_rows>0){
                    while($row = $res->fetch_assoc()){
                        echo "<option value='$row[group]'>$row[group]</option>";
                    }
                } else{
                    echo "<option value=''>Нет классов</option>";
                }
            ?>
        </select>
        <select name="type">
            <option value="marks">Ср. балл по предмету</option>
            <option value="ktp">КТП</option>
        </select>
        <select name="period">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
        </select><br>
        <input type="submit" value="Отобразить отчёт">
    </form>
</body>
</html>