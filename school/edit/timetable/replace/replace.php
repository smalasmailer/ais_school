<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Директор" && $currentrole != "Завуч"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_GET["groupname"], $_GET["date"], $_GET["period"])){
            $groupname = $_GET["groupname"];
            $date = $_GET["date"];
            $period = $_GET["period"];
            $newDate = date("d.m.Y", strtotime($date));
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Замены у <? echo $groupname ?></title>
</head>
<body>
    <? require "../../../adminheader.php"; ?><br>
    <h1>Замены</h1>
    <h2>Расписание на <? echo $newDate; ?> у <? echo $groupname; ?></h2>
    <?php
        $res = $conn->query("SELECT `dayid`, `lessonname`, `teacher` FROM `timetable` WHERE `date` = '$date' AND `groupname` = '$groupname' AND `school` = '$currentschool' ORDER BY `dayid` ASC");
        echo "<center><table>";
        echo "<tr><th>№</th><th>Урок</th><th>Учитель</th><th>Действие</th></tr>";
        if($res->num_rows>0){
            while ($row = $res->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['dayid'] . "</td>";
                echo "<td>" . $row['lessonname'] . "</td>";
                echo "<td>" . $row['teacher'] . "</td>";
                echo "<td><a href='cancel.php?&groupname=" . $groupname . "&date=" . $date . "&dayid=". $row["dayid"] ."&lessonname=" . $row["lessonname"] . "&teacher=" . $row["teacher"] . "&period=". $period ."'>Отменить</a></td>";
                echo "</tr>";
            }
        } else{
            echo "<tr><td colspan='3'>Нет уроков в этот день</td></tr>";
        }
        echo "</table></center>";
    ?>
    <h3>Доставить урок</h3>
    <form action="addlesson.php" method="get">
        <input type="hidden" name="groupname" value="<? echo $groupname; ?>">
        <input type="hidden" name="date" value="<? echo $date; ?>">
        <select name="dayid">
            <?php
                $res = $conn->query("SELECT `id` FROM `{$currentschool}_calls`");
                if($res->num_rows>0){
                    while ($row = $res->fetch_assoc()) {
                        echo "<option value='" . $row["id"] . "'>" . $row["id"] . "</option>";
                    }
                }
            ?>
        </select>
        <select name="lessonname">
            <?php
                $res = $conn->query("SELECT DISTINCT `lesson` FROM `workload` WHERE `group` = '$groupname' AND `school` = '$currentschool'");
                if($res->num_rows>0){
                    while ($row = $res->fetch_assoc()) {
                        echo "<option value='" . $row["lesson"] . "'>" . $row["lesson"] . "</option>";
                    }
                }
            ?>
        </select>
        <select name="teacher">
            <?php
            $res = $conn->query("SELECT DISTINCT `teacher` FROM `workload` WHERE `group` = '$groupname' AND `school` = '$currentschool'");
            if($res->num_rows>0){
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='" . $row["teacher"] . "'>" . $row["teacher"] . "</option>";
                }
            }
            ?>
        </select>
        <input type="hidden" name="period" value="<? echo $period; ?>">
        <input type="submit" value="Доставить">
    </form>
</body>
</html>