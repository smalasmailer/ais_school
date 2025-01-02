<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
if($currentrole != "Админ"){
    header("Location: /");
    exit();
}

if(isset($_GET["completed"])){
    $date = $conn->real_escape_string($_GET["completed"]);
    $stmt = $conn->prepare("UPDATE `auditplan` SET `status` = 'Проведен' WHERE `date` = ?");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}

if(isset($_GET["help"], $_GET["comment"])){
    $date = $conn->real_escape_string($_GET["help"]);
    $comment = htmlspecialchars($_GET["comment"]);

    $stmt = $conn->prepare("UPDATE `auditplan` SET `status` = 'Не по плану', `comment` = ? WHERE `date` = ?");
    $stmt->bind_param("ss", $comment, $date);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>План аудитов</title>
</head>
<body>
    <?php require "adminheader.php"; ?>
    <h2>План аудитов</h2>
    <a href="planaud.php">Запланировать аудит</a>
    <br>
    <h3>Не были проведены</h3>
    <?php
        $res = $conn->query("SELECT * FROM `auditplan` WHERE `status` = 'Не проведен' ORDER BY `date` ASC");
        if($res->num_rows > 0){
            echo "<center><table>";
            echo "<tr><th>Дата проведения</th><th>Ответственный</th><th>Назначил</th><th>Действие</th></tr>";
            while($row = $res->fetch_assoc()){
                $formdate = htmlspecialchars(date("d.m.y", strtotime($row["date"])));
                $main = htmlspecialchars($row["main"]);
                $creator = htmlspecialchars($row["creator"]);
                echo "<tr><td>$formdate</td><td>$main</td><td>$creator</td>";
                if($row["main"] == $currentfullname){
                    echo "<td>
                        <a href='?completed=" . urlencode($row["date"]) . "'><button>Проведен</button></a><hr>
                        <form method='get'>
                            <input type='hidden' name='help' value='" . htmlspecialchars($row["date"]) . "'>
                            <input name='comment' placeholder='Комментарий'>
                            <input type='submit' value='Пошло не по плану'>
                        </form>
                    </td>";
                } else{
                    echo "<td>Нельзя</td>";
                }
                echo "</tr>";
            }
            echo "</table></center>";
        } else {
            echo "Нет запланированных аудитов";
        }
        $res->free();
    ?>

    <h3>Проведены</h3>
    <?php
        $res = $conn->query("SELECT * FROM `auditplan` WHERE `status` = 'Проведен' ORDER BY `date` ASC");
        if($res->num_rows > 0){
            echo "<center><table>";
            echo "<tr><th>Дата проведения</th><th>Ответственный</th><th>Назначил</th></tr>";
            while($row = $res->fetch_assoc()){
                $formdate = htmlspecialchars(date("d.m.y", strtotime($row["date"])));
                $main = htmlspecialchars($row["main"]);
                $creator = htmlspecialchars($row["creator"]);
                echo "<tr><td>$formdate</td><td>$main</td><td>$creator</td></tr>";
            }
            echo "</table></center>";
        } else {
            echo "Нет проведенных аудитов";
        }
        $res->free();
    ?>

    <h3>Пошли не по плану</h3>
    <?php
        $res = $conn->query("SELECT * FROM `auditplan` WHERE `status` = 'Не по плану' ORDER BY `date` ASC");
        if($res->num_rows > 0){
            echo "<center><table>";
            echo "<tr><th>Дата проведения</th><th>Ответственный</th><th>Назначил</th><th>Комментарий</th></tr>";
            while($row = $res->fetch_assoc()){
                $formdate = htmlspecialchars(date("d.m.y", strtotime($row["date"])));
                $main = htmlspecialchars($row["main"]);
                $creator = htmlspecialchars($row["creator"]);
                $comment = htmlspecialchars($row["comment"]);
                echo "<tr><td>$formdate</td><td>$main</td><td>$creator</td><td>$comment</td></tr>";
            }
            echo "</table></center>";
        } else {
            echo "Нет проведенных аудитов";
        }
        $res->free();
    ?>
</body>
</html>