<?php
    require "../config.php";
    if($currentrole != "Ученик"){
        header("Location: ../index.html");
        exit();
    }
    $date = date("Y-m-d");
    $showDate = date("d.m.y");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ученик</title>
    <link rel="stylesheet" href="student.css">
</head>
<body>
    <center>
        <br><h2>Привет, <?php echo htmlspecialchars($currentfullname, ENT_QUOTES, 'UTF-8'); ?>!</h2>
        <div class="childrenmenu">
            <a href="dnevnik/"><button>Дневник</button></a>
            <a href="news.php"><button>Новости</button></a>
            <a href="dnevnik/total.php"><button>Итог. оценки</button></a>
            <a href="dnevnik/timetable.php"><button>Расписание</button></a>
            <a href="profile.php"><button>Настройки</button></a>
            <a href="/logout.php"><button>Выйти</button></a>
        </div>
        <section class="main">
            <div class="mainelement">    
                <h3>Расписание на сегодня<br><?php echo $showDate; ?></h3>
                <?php
                    $curper = getCurrentPeriod1($conn, $currentschool);
                    $res = $conn->query("SELECT `dayid`, `lessonname`, `teacher` FROM `timetable` WHERE `groupname` = '$currentgroupname' AND `school` = '$currentschool' AND `period` = '$curper' AND `date` = '$date' ORDER BY `dayid` ASC");
                    if($res->num_rows>0){
                        echo "<table>";
                        echo "<tr><th>№</th><th>Урок</th><th>Преподаватель</th></tr>";
                        while($row = $res->fetch_assoc()){
                            echo "<tr><td>$row[dayid]</td><td><a href='dnevnik/check.php?dayid=$row[dayid]&lesson=$row[lessonname]&teacher=$row[teacher]&date=$date&period=$curper' target='_blank'>$row[lessonname]</a></td><td>$row[teacher]</td></tr>";
                        }
                        echo "</table>";
                    } else{
                        echo "Нет уроков";
                    }
                ?>
            </div>
            <div class="mainelement">
                <h3>Последняя новость</h3>
                <div class="lastpost">
                    <?php
                        $res = $conn->query("SELECT `id` FROM `posts` WHERE `school` = '$currentschool'");
                        $news = $res->num_rows;

                        if($news>0){
                            $res = $conn->query("SELECT `header`, `text`, `author` FROM `posts` WHERE `id` = $news");
                            $row = $res->fetch_assoc();
                            
                            echo "<h3>$row[header]<br>$row[author]</h3>";
                            echo "<p>$row[text]</p>";
                        } else{
                            echo "Новостей нет";
                        }
                    ?>
                </div>
            </div>
        </section>
        <hr>
        <a href="../logout.php"><button>Выйти из профиля</button></a>
    </center>
</body>
</html>