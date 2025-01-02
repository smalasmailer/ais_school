<?php
    require "config.php";
    require 'Parsedown.php';
    if(!isset($_COOKIE["login"], $_COOKIE["password"])){
        header("Location: index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дневник</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
</head>
<body>
    <?php
        require "header.php";
        if(!isset($_GET["date"])):
    ?>
    <h2>Выбери дату для отображения:</h2>
    <form method="get">
        <input type="date" name="date"><br>
        <input type="submit" value="Просмотр">
    </form>
    <p>Или</p>
    <?php
        $today = date("Y-m-d", time());
    ?>
    <button><a href="?date=<?= $today ?>">Сегодня</a></button>
    <?php else:
            echo "<br>";
            $curPer = getCurrentPeriodByDate($conn, $school, $_GET["date"]);
            $res = $conn->query("SELECT `dayid`, `lessonname`, `lessontopic`, `homework`, `teacher`, `type` FROM `timetable` WHERE `groupname` = '$groupname' AND `date` = '$_GET[date]' AND `period` = $curPer AND `school` = '$school' ORDER BY `dayid` ASC");
            
            if($res->num_rows>0){
                while($row = $res->fetch_assoc()){
                    echo "<div class='modal' id='id$row[dayid]-lesson'>";
                    $res1 = $conn->query("SELECT `mark` FROM `marks` WHERE `date` = '$_GET[date]' AND `dayid` = '$row[dayid]' AND `lessonname` = '$row[lessonname]' AND `groupname` = '$groupname' AND `studentname` = '$fullname' AND `school` = '$school' AND `period` = $curPer");
                    echo "<p>Преподаватель: $row[teacher]</p>";
                    echo "<p>Тема: $row[lessontopic]</p>";
                    echo "<p>ДЗ: $row[homework]</p>";
                    
                    $mark = $res1->fetch_assoc()["mark"] ?? "не выставлена";
                    echo "<p>Оценка: $mark</p>";
                    
                    echo "</div>";

                    echo "<center><div class='lesson'>";
                    echo "<h1 class='nomargin'>$row[dayid]-й урок</h1>";
                    echo "<h2 class='nomargin'>$row[lessonname]</h2>";
                    echo "<p>Тип урока: $row[type]</p>";
                    echo "<a href='#id$row[dayid]-lesson' rel='modal:open'><button>Сведения</button></a>";
                    echo "</div></center><br>";
                }
            } else{
                echo "Сегодня нет уроков!";
            }

            echo "<a href='marks.php'><button>Закрыть</button></a>";
        endif;
    ?>
</body>
</html>