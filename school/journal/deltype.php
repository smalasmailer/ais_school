<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Учитель" && $currentrole != "Директор" && $currentrole != "Завуч"){
        header("Location: /");
        exit();
    }
    if(isset($_GET["dayid"], $_GET["lessonname"], $_GET["date"], $_GET["period"], $_GET["group"], $_GET["type"])){
        $dayid = $_GET["dayid"];
        $lessonname = $_GET["lessonname"];
        $date = $_GET["date"];
        $period = $_GET["period"];
        $group = $_GET["group"];
        $type = $_GET["type"]; 

        $conn->query("DELETE FROM `timetable` WHERE `dayid` = '$dayid' AND `lessonname` = '$lessonname' AND `date` = '$date' AND `groupname` = '$group' AND `teacher` = '$currentfullname' AND `type` = '$type' AND `school` = '$currentschool'");
        $conn->query("DELETE FROM `marks` WHERE `dayid` = '$dayid' AND `lessonname` = '$lessonname' AND `date` = '$date' AND `groupname` = '$group' AND `typemark` = '$type' AND `school` = '$currentschool'");
        header("Location: journal.php?group=$group&lesson=$lessonname&date=$date&idless=$dayid&period=$period");
        exit();
    }
?>