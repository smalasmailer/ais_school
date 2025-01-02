<?php
    require "../../config.php";
    if($currentrole != "ОУ"){
        header("Location: ../../index.html");
    }

    if(!isset($_GET["view"])){
        header("Location: ../index.html");
    }
    $school = $_GET["view"];

    $res = $conn->query("SELECT `orgshort` FROM `organs` WHERE `directorname` = '$currentfullname'");
    $organname = $res->fetch_assoc()['orgshort'];

    $res = $conn->query("SELECT `organ` FROM `schools` WHERE `orgshort` = '$school'");
    $schoolorgan = $res->fetch_assoc()['organ'];

    if ($schoolorgan != $organname){
       header("Location: ../index.html");
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $school;?></title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <?php require "../organheader.php"; ?>
    <a href="orgs.php">Вернуться назад</a>
    <?php
        $res = $conn->query("SELECT `director`, `directorlogin`, `directoremail`, `orgfull`, `orgshort`, `funddate`, `orgtype`, `isblog` FROM `schools` WHERE `orgshort` = '$school'");
        $row = $res->fetch_assoc();

        $director = $row['director'];
        $directorlogin = $row['directorlogin'];
        $directoremail = $row['directoremail'];
        $orgfull = $row['orgfull'];
        $orgshort = $row['orgshort'];
        $funddate = $row['funddate'];
        $orgtype = $row['orgtype'];
    ?>
    <h2>Просмотр сведений об <?php echo $school;?>:</h2>
    <?php
        echo "<p>ФИО директора: $director</p>";
        echo "<p>Почта директора: $directoremail</p>";
        echo "<p>Полное наименование: $orgfull</p>";
        echo "<p>Краткое наименование: $orgshort</p>";
        echo "<p>Дата основания: $funddate</p>";
        echo "<p>Тип организации: $orgtype</p>";
    ?>
    <hr>
    <h2>Управление логином и паролем</h2>
    <p>Текущий логин директора: <?php echo $directorlogin?></p>
    <form action="changepassword.php" method="post">
        <input type="hidden" name="directorlogin" value="<?php echo $directorlogin;?>">
        <input type="password" name="newpassword" placeholder="Укажите новый пароль" style="width:250px;" required>
        <input type="submit" value="Сменить" style="width:250px;">
    </form>
    <hr>
    <h2>Статистика по организации (за все периоды)</h2>
    <h3>Пропуски</h3>
    <?php
        // Инициализируем переменные перед циклом
        $noshow = 0;
        $ill = 0;
        $validreason = 0;

        // Выполняем запрос к базе данных
        $res = $conn->query("SELECT `mark` FROM `marks` WHERE `school` = '$school'");
        // Обрабатываем результаты запроса
        while ($row = $res->fetch_assoc()) {
            if ($row["mark"] == "н") {
                $noshow++;
            } elseif ($row["mark"] == "п") {
                $validreason++;
            } elseif ($row["mark"] == "б") {
                $ill++;
            }
        }

        // Выводим результаты
        echo "<p>Пропусков по <font color='red'>неуважительной</font> причине: $noshow</p>";
        echo "<p>Пропусков по <font color='green'>уважительной</font> причине: $validreason</p>";
        echo "<p>Пропусков по <font color='gray'>болезни</font> причине: $ill</p>";
    ?>
    <h3>Успеваемость</h3>
    <?php
        $res = $conn->query("SELECT AVG(`mark`) FROM `marks` WHERE `school` = '$school'");
        $row = $res->fetch_assoc();
        $mark = round($row['AVG(`mark`)'], 2);

        echo "<p>Общий средний балл по школе: $mark</p>";
    ?>
    <h3>Состав</h3>
    <?php
        $res = $conn->query("SELECT COUNT(`fullname`) FROM `users` WHERE `school` = '$school'");
        $row = $res->fetch_assoc();
        $teachers = $row['COUNT(`fullname`)'];

        $res = $conn->query("SELECT COUNT(`fullname`) FROM `students` WHERE `school` = '$school'");
        $row = $res->fetch_assoc();
        $students = $row['COUNT(`fullname`)'];

        echo "<p>Учителей: $teachers<br>Учеников: $students</p>";
    ?>
</body>
</html>