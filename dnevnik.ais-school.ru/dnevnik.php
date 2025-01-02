<?php
    require "config.php";
    require 'Parsedown.php';

    $Parsedown = new Parsedown();
    $Parsedown->setMarkupEscaped(true);
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
    <title>Главная</title>
</head>
<body>
    <?php require "header.php"; ?>
    <h2>Привет, <?= $fullname ?>!</h2>
    <?php
        $currentdate = date("Y-m-d", time());
        if($currentdate == $origbirthday):
    ?>
        <div class="birthday">
            <p>С днем рождения тебя!</p>
        </div>
    <?php endif; ?>

    <h3>Твои последние оценки</h3>
    <center><section class="lastmarks">
        <?php
            $res = $conn->query("SELECT `date`, `lessonname`, `mark`, `typemark` FROM `marks` WHERE `studentname` = '$fullname' AND `groupname` = '$groupname' AND `school` = '$school'");
            
            if($res->num_rows>0){
                $row = $res->fetch_assoc();
                $date = $row["date"];
                $lessonname = $row["lessonname"];
                $marks = $row["mark"];
                $typemarks = $row["typemark"];
                if($res->num_rows >= 4){
                    for ($i=0; $i < 4; $i++) {
                        $formdate = date("d.m", strtotime($date));
                        echo "<div class='markinfo'>";
                        echo "<div class='mark'>";
                        echo "<h2";
                        if($marks == "5" || $marks == "4"){
                            echo " class='goodmark'>";
                        } elseif($mark == "3"){
                            echo " class='mediumark'>";
                        } elseif($mark == "2" || $mark == "1" || $mark == "н/а"){
                            echo " class='badmark'>";
                        } else{
                            echo " class='graymark'>";
                        }
                        echo "$marks</h2>";
                        echo "</div>";
                        echo "<h3>$lessonname</h3>";
                        echo "<h4>$formdate</h4>";
                        echo "</div>";
                    }
                } else{
                    for ($i=0; $i < $res->num_rows; $i++) {
                        $formdate = date("d.m", strtotime($date));
                        echo "<div class='markinfo'>";
                        echo "<div class='mark'>";
                        echo "<h2";
                        if($marks == "5" || $marks == "4"){
                            echo " class='goodmark'>";
                        } elseif($mark == "3"){
                            echo " class='mediumark'>";
                        } elseif($mark == "2" || $mark == "1" || $mark == "н/а"){
                            echo " class='badmark'>";
                        } else{
                            echo " class='graymark'>";
                        }
                        echo "$marks</h2>";
                        echo "</div>";
                        echo "<h3>$lessonname</h3>";
                        echo "<h4>$formdate</h4>";
                        echo "</div>";
                    }
                }
                
            } else{
                echo "Нет выставленных оценок.";
            }
        ?>
    </section></center><br>
    <h3>Последние новости</h3>
    <?php
        if($community != "нет"){
            $res = $conn->query("SELECT `text`, `author` FROM `communityposts` WHERE `community` = '$community' ORDER BY `id` DESC");
            if($res->num_rows>0){
                $row = $res->fetch_assoc();
                $text = $row["text"];
                $author = $row["author"];

                if($res->num_rows>10){
                    for ($i=0; $i < 10; $i++) { 
                        echo "<div class='post'>";
                        $htmltext = $Parsedown->text($text);
                        echo "<p>$htmltext</p>";
                        echo "<p><span style='color: gray;'>Написал: $row[author]</span></p>";
                        echo "</div>";
                    }
                } else{
                    for ($i=0; $i < $res->num_rows; $i++) { 
                        echo "<div class='post'>";
                        $htmltext = $Parsedown->text($text);
                        echo "<p>$htmltext</p>";
                        echo "<p><span style='color: gray;'>Написал: $row[author]</span></p>";
                        echo "</div>";
                    }
                }
            } else{
                echo "Нет новостей.";
            }
        }
    ?>
</body>
</html>