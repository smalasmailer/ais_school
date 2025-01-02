<?php
    require "../../config.php";
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET["student"])){
            $student = $_GET["student"];
            $res = $conn->query("SELECT `school` FROM `personalfile` WHERE `fullname` = '$student'");

            if($res->num_rows>0){
                if($res->fetch_assoc()["school"] != $currentschool){
                    header("Location: students.php");
                    exit();
                }
            } else{
                $res = $conn->query("SELECT `fullname` FROM `students` WHERE `fullname` = '$student' AND `school` = '$currentschool'");
                if($res->num_rows>0){
                    $res = $conn->query("SELECT `groupname`, `birthday` FROM `students` WHERE `fullname` = '$student' AND `school` = '$currentschool'");
                    $row = $res->fetch_assoc();
                    $groupname = $row["groupname"];
                    $birthday = $row["birthday"];

                    $conn->query("INSERT INTO `personalfile`(`fullname`, `grade`, `sex`, `birthday`, `docnumber`, `docserial`, `nationality`, `kindergarden`, `enrollment`, `arrival`, `school`) VALUES('$student', '$groupname', NULL, '$birthday', NULL, NULL, NULL, NULL, NULL, NULL, '$currentschool')");

                    $res->free();
                } else{
                    header("Location: students.php");
                    exit();
                }
            }
        }
    } elseif($_SERVER["REQUEST_METHOD"] == "POST"){
        $student = $_POST["student"];
        $grade = $_POST["groupname"];
        if($currentrole != "Директор" && $currentgroup != $grade){
            header("Location: ../index.php");
            exit();
        }
        $sex = $_POST["sex"];
        $birthday = $_POST["birthday"];
        $docnumber = $_POST["docnumber"];
        $docserial = $_POST["docserial"];
        $nationality = $_POST["nationality"];
        $kindergarden = $_POST["kindergarden"];
        $enrollment = $_POST["enrollment"];
        $arrival = $_POST["arrival"];

        $conn->query("UPDATE `personalfile` SET `grade` = '$grade', `sex` = '$sex', `birthday` = '$birthday', `docnumber` = '$docnumber', `docserial` = '$docserial', `nationality` = '$nationality', `kindergarden` = '$kindergarden', `enrollment` = '$enrollment', `arrival` = '$arrival' WHERE `fullname` = '$student' AND `school` = '$currentschool'");

        header("Location: personalfile.php?student=$student");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личное дело ученика <?php echo $student; ?></title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap');

    * {
        font-family: "Montserrat", sans-serif;
        box-sizing: border-box;
    }

    body {
        background-color: #EAEFFF; /* Светлый фон */
        color: #333; /* Цвет текста */
        text-align: center;
    }

    /* Стили для личного дела */
    .file {
        background-color: #4A90E2; /* Основной фон */
        max-width: 500px;
        padding: 15px;
        border-radius: 10px;
        color: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: transform 0.2s;
        margin: 10px auto; /* Центрирование */
    }

    /* Стили для секции файла */
    .filesection {
        background-color: #A1C8E7; /* Светло-синий фон */
        padding: 15px;
        color: black;
        width: 400px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin: 10px auto; /* Центрирование */
    }

    /* Стили для кнопки отправки */
    input[type="submit"] {
        background-color: #007BFF;
        color: white;
        border: 0;
        border-radius: 15px;
        padding: 10px 20px;
        font-size: 16px;
        transition: background-color 0.3s, transform 0.2s;
    }

    input[type="submit"]:hover {
        cursor: pointer;
        background-color: #0056b3; /* Темно-синий при наведении */
        transform: scale(1.05);
    }

    /* Стили для данных */
    .data {
        background-color: #BDC3C7; /* Темно-серый фон */
        padding: 3px;
        border-radius: 5px;
        color: #333;
        margin-top: 10px; /* Отступ сверху */
    }

    /* Стили для заголовка */
    h1, h2 {
        color: #333; /* Цвет заголовка */
    }
</style>
</head>
<body>
    <?php
        if(isset($_GET["fromklruk"]) && $_GET["fromklruk"] == 1){
            echo "<button onclick='window.history.go(-1);'>Назад на страницу класса</button>";
        } else{
            echo '<a href="students.php">Вернуться на страницу учеников</a>';
        }
    ?>
    <?php
        $res = $conn->query("SELECT * FROM `personalfile` WHERE `fullname` = '$student' AND `school` = '$currentschool'");
        $row = $res->fetch_assoc();

        $grade = $row["grade"] ?? "не указан";
        $sex = $row["sex"] ?? "не указан";
        $birthday = $row["birthday"] ?? "не указан";
        $docnumber = $row["docnumber"] ?? "не указан";
        $docserial = $row["docserial"] ?? "не указан";
        $nationality = $row["nationality"] ?? "не указан";
        $kindergarden = $row["kindergarden"] ?? "не указан";
        $enrollment = $row["enrollment"] ?? "не указан";
        $arrival = $row["arrival"] ?? "не указан";
    ?>
    <div class="file">
        <h2><?php echo $student; ?></h2>
        <p>Зачислен в <span class="data"><?php echo $grade;?></span> класс</p>
        <hr>
        <p>Пол: <span class="data"><?php echo $sex; ?></span></p>
        <p>Дата рождения: <span class="data"><?php echo $birthday; ?></span></p>
        <p>Свидетельство о рождении: <span class="data"><?php echo $docnumber; ?></span> <span class="data">№<?php echo $docserial; ?></span></p>
        <p>Национальность: <span class="data"><?php echo $nationality; ?></span></p>
        <p>Где воспитывался (обучался) до поступления в 1 класс: <span class="data"><?php echo $kindergarden; ?></span></p>
        <p>Дата зачисления в ОО: <span class="data"><?php echo $enrollment; ?></span></p>
        <p>Дата прибытия в класс: <span class="data"><?php echo $arrival; ?></span></p>
    </div>
    <hr>
    <h2>Редактирование личного дела</h2>
    <div class="file">
        <form method="post">
            <input type="hidden" name="student" value="<?php echo $student; ?>">
            <?php
                $res = $conn->query("SELECT `groupname` FROM `students` WHERE `fullname` = '$student' AND `school` = '$currentschool'");
                $row = $res->fetch_assoc();
                $groupname = $row["groupname"];
                echo "<input type='hidden' name='groupname' value='$groupname'>";
            ?>
            <div class="filesection">
                <label for="sex">Пол: <select name="sex" required>
                    <option value="М">М</option>
                    <option value="Ж">Ж</option>
                </select></label><br>
                <label for="birthday">Дата рождения: <input type="date" name="birthday" requied></label>
            </div><br>
            <div class="filesection">
                <label for="doc">Свидетельство о рождении:<br>
                <label for="docnumber">№ <input type="text" name="docnumber" style="width:50px;" required></label> <label for="docserial">Серия: <input type="number" name="docserial" style="width:50px;" required></label></label>
            </div><br>
            <div class="filesection">
                <label for="nationality">Национальность: <input type="text" name="nationality" required></label><br>
                <label for="kindergarden">Где воспитывался (обучался) до поступления в 1 класс: <input type="text" name="kindergarden" required></label>
            </div><br>
            <div class="filesection">
                <label for="enrollment">Дата зачисления в ОО: <input type="date" name="enrollment" required></label><br>
                <label for="arrival">Дата прибытия в класс: <input type="date" name="arrival" required></label>
            </div><br>
            <input type="submit" value="Сохранить">
        </form>
    </div>
</body>
</html>