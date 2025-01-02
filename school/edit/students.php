<?php
require "../../config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/simple-xlsx/simplexlsx.class.php";

if (session_status() == PHP_SESSION_NONE || session_status() == PHP_SESSION_DISABLED) {
    session_start();
}

if (!isset($_COOKIE["acclogin"])) {
    header("Location: ../../index.html");
    exit();
}

if ($currentrole != "Директор" && $currentrole != "Администратор") {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['import'])) {
    $file = $_FILES['file']['tmp_name'];

    // Проверка, что файл загружен
    if (file_exists($file)) {
        $xlsx = new SimpleXLSX($file);
        $rows = $xlsx->rows();

        // Пропускаем заголовок
        array_shift($rows);

        foreach ($rows as $row) {
            // Предполагается, что порядок колонок: Группа, ФИО, Дата рождения, Логин, Пароль
            $groupname = $row[0];
            $fullname = $row[1];
            $birthday = $row[2];
            $personlogin = $row[3];
            $personpassword = $row[4];
            $school = $currentschool;

            // Проверка на существование пользователя
            $res = $conn->query("SELECT `login` FROM `students` WHERE `login` = '$personlogin'");
            if ($res->num_rows == 0) {
                // Если не существует, добавляем ученика
                $conn->query("INSERT INTO `students`(`groupname`, `fullname`, `birthday`, `school`, `login`, `password`) VALUES('$groupname', '$fullname', '$birthday', '$school', '$personlogin', '$personpassword')");
                $stmt = $conn->prepare("INSERT INTO `personalfile`(`fullname`, `grade`, `sex`, `birthday`, `docnumber`, `docserial`, `nationality`, `kindergarden`, `enrollment`, `arrival`, `school`) VALUES(?, ?, NULL, ?, NULL, NULL, NULL, NULL, NULL, NULL, ?)");
                $stmt->bind_param("ssss", $fullname, $groupname, $birthday, $school);
                $stmt->execute();
            }
        }

        header("Location: students.php?success=1");
    } else {
        echo "Ошибка загрузки файла.";
    }
}

if (isset($_GET["groupname"]) && isset($_GET["fullname"]) && isset($_GET["birthday"]) && isset($_GET["personlogin"]) && isset($_GET["personpassword"])) {
    $groupname = $_GET["groupname"];
    $fullname = $_GET["fullname"];
    $birthday = $_GET["birthday"];
    $personlogin = $_GET["personlogin"];
    $personpassword = $_GET["personpassword"];
    $school = $_GET["school"];

    $res = $conn->query("SELECT `login` FROM `students` WHERE `login` = '$personlogin'");
    if($res->num_rows > 0){
        die("Данный пользователь уже существует. <a href='students.php'>Вернуться</a>");
    }

    $conn->query("INSERT INTO `students`(`groupname`, `fullname`, `birthday`, `school`, `login`, `password`) VALUES('$groupname', '$fullname', '$birthday', '$school', '$personlogin', '$personpassword');");
    $stmt = $conn->prepare("INSERT INTO `personalfile`(`fullname`, `grade`, `sex`, `birthday`, `docnumber`, `docserial`, `nationality`, `kindergarden`, `enrollment`, `arrival`, `school`) VALUES(?, ?, '', ?, '', NULL, '', '', '', '', ?)");
    $stmt->bind_param("ssss", $fullname, $groupname, $birthday, $school);
    $stmt->execute();

    header("Location: students.php");
}

if (isset($_GET["student"])) {
    $student = $_GET["student"];
    $conn->query("DELETE FROM `students` WHERE `fullname` = '$student' AND `school` = '$currentschool';");

    $conn->query("DELETE FROM `personalfile` WHERE `fullname` = '$student' AND `school` = '$currentschool'");

    header("Location: students.php");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ученики</title>
</head>
<body>
    <?php require "../schoolhead.php"; ?>
    <h1>Создание или редактирование учеников школы</h1>

    <?php
        if(isset($_GET["success"]) && $_GET["success"] == 1){
            echo "Импорт успешно завершен";
        }
    ?>
    <div>
        <center>
            <form method="get" style="max-width:250px;" class="editform">
                <select name="groupname" required style="width: 100%;">
                    <?php
                        $res = $conn->query("SELECT `groupname` FROM `groups` WHERE `school` = '$currentschool'");
                        while($row = $res->fetch_assoc()){
                            $groupname = $row['groupname'];
                            echo "<option value='$groupname'>$groupname</option>";
                        }
                    ?>
                </select>
                <input type="text" name="fullname" placeholder="Полное имя" required><br>
                <input type="date" name="birthday"> Дата рождения<br>
                <input type="hidden" name="school" value="<?php echo htmlspecialchars($currentschool); ?>">
                <input type="text" name="personlogin" placeholder="Логин" required><br>
                <input type="password" name="personpassword" placeholder="Пароль" required><br>
                <input type="submit" value="Зарегистрировать">
            </form>
        </center>
    </div>
    <hr>
    <h2>Импорт учеников через Excel</h2>
    <p><a href="import/studentsexample.xlsx" download>Скачать шаблон</a></p>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" accept=".xlsx" required>
        <input type="submit" name="import" value="Импортировать">
    </form>
    <hr>
    <h2>Редактирование учеников</h2>
    <p>Нажмите на ученика для редактирования личного дела</p>
    <div>
        <center>
            <?php
            $res = $conn->query("SELECT `groupname`, `fullname`, `school` FROM `students` WHERE `school` = '$currentschool'");
            echo "<table border='1'>";
            echo "<tr><td>Класс</td><td>ФИО</td><td>Удалить</td></tr>";
            while($row = $res->fetch_assoc()){
                $groupname = $row['groupname'];
                $fullname = $row['fullname'];
                echo "<tr><td>$groupname</td><td><a href='personalfile.php?student=$fullname'>$fullname</a></td><td><a href='?student=$fullname'>X</a></td></tr>";
            }
            echo "</table>";
            ?>
        </center>
    </div>
</body>
</html>