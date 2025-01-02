<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Учитель" && $currentrole != "Завуч" && $currentrole != "Директор"){
        header("Location: /");
        exit();
    }
    if(isset($_POST["dayid"], $_POST["date"], $_POST["lessonname"], $_POST["period"], $_POST["onlinelesson"], $_POST["groupname"])){
        $dayid = $_POST["dayid"];
        $date = $_POST["date"];
        $lessonname = $_POST["lessonname"];
        $period = $_POST["period"];
        $onlinelesson = trim($_POST["onlinelesson"]);
        if($onlinelesson == ""){
            $onlinelesson = "не указана";
        }
        $teacher = $currentfullname;
        $groupname = $_POST["groupname"];

        $conn->query("UPDATE `timetable` SET `onlinelesson` = '$onlinelesson', `type` = 'д/у' WHERE `dayid` = '$dayid' AND `date` = '$date' AND `lessonname` = '$lessonname' AND `period` = '$period' AND `teacher` = '$teacher' AND `groupname` = '$groupname' AND `school` = '$currentschool'");
        echo "<script>window.history.go(-1);</script>";
    }