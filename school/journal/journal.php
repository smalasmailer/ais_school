<?php
    require "../../config.php";
    $acclogin = $_COOKIE["acclogin"];
    if($currentrole != "Учитель" && $currentrole != "Завуч" && $currentrole != "Директор"){
        header("Location: /");
        exit();
    }

    // Получение названия школы
    $res = $conn->query("SELECT `school` FROM `users` WHERE login = '$acclogin'");
    if ($res->num_rows>0){
        $school = $res->fetch_assoc()["school"];
    }

    if(isset($_GET["group"]) && isset($_GET["lesson"]) && isset($_GET["date"]) && isset($_GET["idless"]) && isset($_GET["period"])){
        $group = $_GET["group"];
        $lesson = $_GET["lesson"];
        $date = $_GET["date"];
        $idless = $_GET["idless"];
        $period = $_GET["period"];

        $res = $conn->query("SELECT `lessontopic`, `homework`, `onlinelesson` FROM `timetable` WHERE `date` = '$date' AND `lessonname` = '$lesson' AND `groupname` = '$group' AND `dayid` = '$idless' AND `period` = '$period'");
        if($res->num_rows > 0){
            $row = $res->fetch_assoc();
            $lessontopic = $row["lessontopic"];
            $homework = $row["homework"];
            $onlinelesson = $row["onlinelesson"] ?? "не указана";
        } else{
            header("Location: add/1.php");
            exit();
        }
    } else{
        header("Location: open.php");
        exit();
    }

    if(isset($_POST["newtype"])){
        $newtype = $_POST["newtype"];
        $conn->query("UPDATE `timetable` SET `type` = '$newtype' WHERE `groupname` = '$group' AND `lessonname` = '$lesson' AND `date` = '$date' AND `dayid` = '$idless' AND `period` = '$period' AND `teacher` = '$currentfullname'");
        $conn->query("UPDATE `marks` SET `typemark` = '$newtype' WHERE `groupname` = '$group' AND `lessonname` = '$lesson' AND `date` = '$date' AND `dayid` = '$idless' AND `period` = '$period'");
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Журнал <?php echo $group;?>-<?php echo $lesson?></title>
    <style>
        *{
            font-family: Arial;
            text-align: left;
        }
        .marks{
            margin: 0;
        }
        .aboutlesson{
            padding:15px;
            border-radius:5px;
            background-color:gray;
            max-width:500px;
            color:white;
        }
        .aboutlesson input{
            border:0;
            padding:5px;
        }
        button {
            padding: 10px;
            background-color: #8cc646;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input{
            border:0;
        }
        .lesson{
            max-width:650px;
            margin: 0 auto;
        }
        .changetype{
            display: flex;
            padding: 0 auto;
        }
    </style>
</head>
<body>
    <?php
        require "journalhead.php";
    ?>
    <div class="lesson">
        <h2>Страница урока</h2>
        <div class="aboutlesson">
            <p>Тема урока: <input type="text" id="lessontopic" value="<?php echo htmlspecialchars($lessontopic); ?>" readonly></p>
            <p>Домашнее задание к данному уроку: <input type="text" id="homework" value="<?php echo htmlspecialchars($homework); ?>" readonly></p>
            
            <?php 
            // Преобразование даты в ДД.ММ.ГГ
            $formattedDate = date('d.m.y', strtotime($date)); 
            ?>
            
            <p>Дата проведения: <?php echo htmlspecialchars($formattedDate); ?> (<?php echo htmlspecialchars($idless); ?> урок)</p>
            <p>Группа: <?php echo htmlspecialchars($group); ?></p>
            <p>Предмет: <?php echo htmlspecialchars($lesson); ?></p>
            
            <a href="allmarks.php?group=<?php echo urlencode($group); ?>&lesson=<?php echo urlencode($lesson); ?>&period=<?php echo urlencode($period); ?>">
                <button>Все оценки по предмету</button>
            </a>
            
            <a href="topics.php?group=<?php echo urlencode($group); ?>&lesson=<?php echo urlencode($lesson); ?>&period=<?php echo urlencode($period); ?>">
                <button>КТП</button>
            </a>
            
            <a href="checklessons.php?group=<?php echo urlencode($group); ?>">
                <button>Все уроки</button>
            </a>
        </div>
        <br>
        <h2>Типы уроков</h2><br>
        <table>
            <tr><th>Тип урока</th><th>Действие</th></tr>
            <?php
                $res = $conn->query("SELECT `type` FROM `timetable` WHERE `dayid` = '$idless' AND `lessonname` = '$lesson' AND `date` = '$date' AND `period` = '$period' AND `groupname` = '$group' AND `school` = '$school'");
                while($row = $res->fetch_assoc()){
                    echo "<tr><td>$row[type]</td><td><a href='deltype.php?dayid=$idless&lessonname=$lesson&date=$date&period=$period&group=$group&type=$row[type]'>Удалить</a></td></tr>";
                }
            ?>
            <tr><td colspan=2 style="text-align:center;"><form method="get" action="addtype.php">
                    <input type="hidden" name="dayid" value="<? echo $idless?>">
                    <input type="hidden" name="lessonname" value="<? echo $lesson?>">
                    <input type="hidden" name="date" value="<? echo $date; ?>">
                    <input type="hidden" name="period" value="<? echo $period; ?>">
                    <input type="hidden" name="group" value="<? echo $group; ?>">
                    <select name="type">
                        <?php
                            $res = $conn->query("SELECT `name` FROM `types` WHERE `school` = '$currentschool'");
                            if (!$res) {
                                echo "Ошибка SQL: " . $conn->error;
                            }

                            if ($res->num_rows > 0) {
                                
                                while ($row = $res->fetch_assoc()) {
                                    echo "<option value='{$row['name']}'>{$row['name']}</option>";
                                }
                            } else {
                                echo "<option>Нет данных</option>";
                            }
                        ?>
                    </select>
                    <input type="submit" value="Добавить тип">
                </form>
            </td></tr>
        </table>
        <h2>Онлайн-урок</h2>
        <?php
            
        ?>
        <p>Текущая ссылка: <?php echo $onlinelesson; ?></p>
        <form action="setonline.php" method="post">
            <input type="hidden" name="dayid" value="<?php echo $idless; ?>">
            <input type="hidden" name="lessonname" value="<?php echo $lesson; ?>">
            <input type="hidden" name="date" value="<?php echo $date; ?>">
            <input type="hidden" name="period" value="<?php echo $period; ?>">
            <input type="hidden" name="groupname" value="<?php echo $group; ?>">
            <input type="text" name="onlinelesson" placeholder="Укажите ссылку на конференцию" style="width: 60%;">
            <input type="submit" value="Сохранить">
        </form>
</body>