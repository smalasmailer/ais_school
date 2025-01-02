<?php
    require "../../config.php";

    if($currentrole != "Директор" && $currentrole != "Администратор"){
        header("Location: ../../index.html");
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Школьник.Сайт</title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="calls.css">
</head>
<body>
    <?php require "../schoolhead.php"; ?>
    <h2>Редактирование нагрузки</h2>
    <form method="get">
    <?php
        if(isset($_GET["error"])){
            $error = $_GET["error"];
            if($error == "arledyadded"){
                echo "Данная нагрузка уже была добавлена<br>";
            }
        }
        if(isset($_GET["group"])){
            $group = $_GET["group"];

            $conn->query("CREATE TABLE IF NOT EXISTS `workload`(
                `group` TEXT NOT NULL,
                `teacher` TEXT NOT NULL,
                `lesson` TEXT NOT NULL,
                `school` TEXT NOT NULL
            )");

            $res = $conn->query("SELECT `lesson` FROM `lessons` WHERE `school` = '$currentschool'");

            echo "<form method='get'>";

            if ($res->num_rows > 0) {
                echo "<select name='lessonname'>";
            
                while ($row = $res->fetch_assoc()) {
                    $lesson = $row["lesson"];
                    echo "<option value='$lesson'>$lesson</option>";
                }
                echo "</select>";
            } else {
                echo "Вы не добавили предметы";
            }


            $res = $conn->query("SELECT `fullname` FROM `users` WHERE (`role` = 'Завуч' OR `role` = 'Учитель' OR `role` = 'Директор') AND `school` = '$currentschool'");

            if ($res->num_rows > 0) {
                echo "<select name='teachername'>";

                while ($row = $res->fetch_assoc()) {
                    $teacher = $row["fullname"]; // Исправлено с "teacher" на "fullname"
                    echo "<option value='$teacher'>$teacher</option>";
                }
                echo "</select>";
            } else {
                echo "Вы не добавили учителей";
            }

            echo "<input type='hidden' value='$group' name='groupname'>";
            echo "<input type='submit' value='Назначить'>";

            echo "</form>";
        }
        if(isset($_GET["lessonname"]) && isset($_GET["teachername"]) && isset($_GET["groupname"])) {
            $lessonname = $_GET["lessonname"];
            $teachername = $_GET["teachername"];
            $groupname = $_GET["groupname"];

            $res = $conn->query("SELECT * FROM `workload` WHERE `group` = '$groupname' AND `teacher` = '$teachername' AND `lesson` = '$lessonname' AND `school` = '$currentschool'");
            if($res->num_rows>0){
                header("Location: load.php?error=arledyadded");
            } else{
                // Подготовка SQL-запроса
                $stmt = $conn->prepare("INSERT INTO `workload` (`group`, `teacher`, `lesson`, `school`) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $groupname, $teachername, $lessonname, $currentschool);

                // Выполнение запроса
                if ($stmt->execute()) {
                    echo "Данные успешно добавлены.";
                } else {
                    echo "Ошибка: " . $stmt->error;
                }

                // Закрытие подготовленного выражения
                $stmt->close();
                header("Location: load.php");
            }
        }
        if (!isset($_GET["group"])){
            echo '<select name="group">';
            // Отладка

            if ($currentrole != "Директор" && $currentrole != "Администратор") {
                header("Location: ../index.php");
                exit();
            }

            // Получаем группы из базы данных
            $res = $conn->query("SELECT `groupname` FROM `groups` WHERE `school` = '$currentschool';");
            if ($res && $res->num_rows > 0) {
                $groups = $res->fetch_all(MYSQLI_ASSOC);
                foreach ($groups as $group) {
                    echo "<option value='{$group['groupname']}'>{$group['groupname']}</option>";
                }
            } else {
                echo "<option disabled>Группы не найдены</option>";
            }
            echo "</select>";
            echo '<input type="submit" value="Настроить"><br>';
        }
        echo "<h2>Просмотр заданной нагрузки</h2>";
        $res = $conn->query("SELECT * FROM `workload` WHERE `school` = '$currentschool' ORDER BY `lesson` ASC");
        if ($res->num_rows > 0) {
            echo "<center>";
            echo "<table border='1'>
                    <tr>
                        <th>Группа</th>
                        <th>Преподаватель</th>
                        <th>Урок</th>
                        <th>Удалить</th>
                    </tr>";

            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['group']}</td>
                        <td>{$row['teacher']}</td>
                        <td>{$row['lesson']}</td>
                        <td><a style='color:black;' href='?deleteteacher={$row['teacher']}&deletelesson={$row['lesson']}&deletegroup={$row['group']}'>Удалить</a></td>
                      </tr>";
            }
            echo "</table>";
            echo "</center>";
        } else {
            echo "Нет данных для отображения.";
        }

        if(isset($_GET["deleteteacher"]) && isset($_GET["deletelesson"]) && isset($_GET["deletegroup"])){
            $deleteteacher = $_GET["deleteteacher"];
            $deletelesson = $_GET["deletelesson"];
            $deletegroup = $_GET["deletegroup"];

            $stmt = $conn->prepare("DELETE FROM workload WHERE teacher = ? AND lesson = ? AND `group` = ? AND `school` = ?");
            $stmt->bind_param("ssss", $deleteteacher, $deletelesson, $deletegroup, $currentschool);

            if ($stmt->execute()) {
                echo "Данные успешно удалены.";
            } else {
                echo "Ошибка: " . $stmt->error; // Обработка ошибки
            }

            // Закрытие подготовленного выражения
            $stmt->close();
            header("Location: load.php");
        }

    ?>
        
    </form>
</body>
</html>