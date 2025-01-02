<?php
require "../../config.php";

if (!isset($_COOKIE["acclogin"])) {
    header("Location: ../../index.html");
    exit();
}

if ($currentrole != "Директор" && $currentrole != "Администратор") {
    header("Location: ../../index.html");
    exit();
}

if(isset($_GET["groupname"]) && isset($_GET["school"])){
    $groupname = $_GET["groupname"];
    $schoolname = $_GET["school"];
    $conn->query("INSERT INTO `groups`(`groupname`, `mainteacher`, `school`) VALUES('$groupname', NULL, '$schoolname')");
    header("Location: groups.php");
}

if (isset($_GET["group_id"])) {
    $group_id = $_GET["group_id"];

    // Подготовка запроса
    $stmt = $conn->prepare("DELETE FROM `groups` WHERE `groupname` = ?");
    $stmt->bind_param("s", $group_id); // "s" указывает, что параметр - строка

    if ($stmt->execute()) {
        header("Location: groups.php");
        exit(); // Завершение скрипта после редиректа
    } else {
        echo "Ошибка при удалении записи: " . $stmt->error;
    }

    $stmt->close(); // Закрыть подготовленный запрос
}


// Получаем имя директора для исключения из списка
$res = $conn->query("SELECT `role`, `school` FROM users WHERE login = '$acclogin'");
$row = $res->fetch_assoc();
$school = $row['school'];

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Классы</title>
</head>
<body>
    <?php require "../schoolhead.php"; ?>
    <h1>Создание или редактирование классов</h1>
    <div>
        <center>
            <form method="get" style="max-width:250px;" class="editform">
                <input type="text" name="groupname" placeholder="Имя класса" required><br>
                <input type="hidden" name="school" value="<?php echo htmlspecialchars($currentschool); ?>">
                <input type="submit" value="Добавить">
            </form>
        </center>
    </div>
    <hr>
    <h2>Редактирование классов</h2>
    <div>
        <center>
            <p>Нажмите на группу для назначения классного руководства</p>
            <?php
                $res = $conn->query("SELECT `groupname` FROM `groups` WHERE school = '$school'");

                if ($res && $res->num_rows > 0) {
                    echo "<table border='1'>";
                    while ($row = $res->fetch_assoc()) {
                        $grp = htmlspecialchars($row['groupname']);
                        echo "<tr>";
                        echo "<td><a href='group/?group_id=$grp'>$grp</a></td>";
                        echo "<td><a href='?group_id=$grp'>X</a></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                echo "Нет данных для отображения.";
            }
            ?>
        </center>
    </div>
</body>
</html>