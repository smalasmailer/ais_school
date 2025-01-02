<?php
require "../../config.php";

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

if (isset($_GET["lesson_id"])) {
    $lesson_id = $_GET["lesson_id"];
    $conn->query("DELETE FROM `lessons` WHERE `lesson` = '$lesson_id';");
}

if(isset($_GET["lname"]) && isset($_GET["school"])){
    $lname = $_GET["lname"];
    $school = $_GET["school"];

    $stmt = $conn->prepare("INSERT INTO lessons(lesson, school) VALUES(?, ?)");
    $stmt->bind_param("ss", $lname, $school);
    $stmt->execute();
    header("Location: lessons.php");
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Предметы</title>
</head>
<body>
    <?php require "../schoolhead.php"; ?>
    <h1>Создание или редактирование предметов</h1>
    <div>
        <center>
            <form method="get" style="max-width:250px;" class="editform">
                <input type="text" name="lname" placeholder="Название предмета" required><br>
                <input type="hidden" name="school" value="<?php echo htmlspecialchars($currentschool); ?>">
                <input type="submit" value="Добавить">
            </form>
        </center>
    </div>
    <hr>
    <h2>Редактирование предметов</h2>
    <div>
        <center>
            <?php
            $stmt = $conn->prepare("SELECT lesson FROM lessons WHERE school = ?");
            $stmt->bind_param("s", $currentschool);
            $stmt->execute();
            $res = $stmt->get_result();
            
            if ($res->num_rows > 0) {
                echo "<table border='1'>";
                while ($row = $res->fetch_assoc()) {  // Перенесите fetch_assoc() внутрь условия while
                    $l = htmlspecialchars($row['lesson']);
                    echo "<tr>";
                    echo "<td>$l</td>";
                    echo "<td><a href='?lesson_id=$l'>X</a></td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            ?>
        </center>
        <a href="import/lessons.php">Импорт</a>
    </div>
</body>
</html>