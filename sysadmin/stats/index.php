<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: ../../index.html");
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика по школам</title>
    <style>
        .schoolstats{
            display:flex;
            justify-content:center;
        }
        .stat{
            width: 150px;
            text-align: left;
            margin-right: 5px;
            padding-left: 5px;
            border-radius: 15px;
        }
        .workers{
            background-color: darkred;
            color: white;
        }
        .students{
            background-color: darkgreen;
            color: white;
        }
        .marks{
            background-color: pink;
        }
        .avgmark{
            background-color: purple;
            color: white;
        }
        .schemes{
            background-color: darkblue;
            color: white;
        }
    </style>
</head>
<body>
    <?php require "../adminheader.php"; ?>
    <h2>Выберите школу</h2>
    <form method="post">
        <select name="school">
            <?php
                $res = $conn->query("SELECT `orgshort` FROM `schools`");
                while($row = $res->fetch_assoc()){
                    echo "<option value='" . $row["orgshort"] . "'>" . $row["orgshort"] . "</option>";
                }
            ?>
            <input type="submit" value="Показать">
        </select>
    </form>
    <p>ИЛИ<br><a href="all.php"><button>Статистика по всем школам</button></a></p>
    <?php
        if(isset($_POST["school"])):
            $school = $_POST["school"];

            $res = $conn->query("SELECT COUNT(`fullname`) FROM `users` WHERE `school` = '$school'");
            $row = $res->fetch_assoc();
            $users = $row["COUNT(`fullname`)"];

            $res = $conn->query("SELECT COUNT(`fullname`) FROM `students` WHERE `school` = '$school'");
            $row = $res->fetch_assoc();
            $students = $row["COUNT(`fullname`)"];

            $res = $conn->query("SELECT COUNT(`mark`) FROM `marks` WHERE `school` = '$school'");
            $row = $res->fetch_assoc();
            $marks = $row["COUNT(`mark`)"];

            $res = $conn->query("SELECT AVG(`mark`) FROM `marks` WHERE `school` = '$school'");
            if($marks == 0){
                $avgmarks = "Нет оц.";
            } else{
                $row = $res->fetch_assoc();
                $avgmarks = round($row["AVG(`mark`)"], 2);
            }

            $res = $conn->query("SELECT COUNT(`scheme`) FROM `schemes` WHERE `school` = '$school'");
            $row = $res->fetch_assoc();
            $schemes = $row["COUNT(`scheme`)"];
    ?>
            <h2>Люди</h2>
            <section class="schoolstats">
                <div class="stat workers">
                    <h2><?php echo $users;?></h2>
                    <p>Сотрудников</p>
                </div>
                <div class="stat students">
                    <h2><?php echo $students;?></h2>
                    <p>Учеников</p>
                </div>
            </section>
            
            <h2>Оценки</h2>
            <section class="schoolstats">
                <div class="stat marks">
                    <h2><?php echo $marks; ?></h2>
                    <p>Оценок</p>
                </div>
                <div class="stat avgmark">
                    <h2><?php echo $avgmarks; ?></h2>
                    <p>Cр. балл</p>
                </div>
            </section>
            <h3>Схемы расписания</h3>
            <section class="schoolstats">
                <div class="stat schemes">
                    <h2><?php echo $schemes; ?></h2>
                    <p>Схем</p>
                </div>
            </section>
    <?php endif; ?>
</body>
</html>