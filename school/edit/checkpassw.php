<?php
    require "../../config.php";

    if(isset($_GET["student"])){
        $student = $_GET["student"];

        $res = $conn->query("SELECT `school`, `login`, `password` FROM `students` WHERE `fullname` = '$student'");
        $row = $res->fetch_assoc();
        $school = $row["school"];
        $login = $row["login"];
        $password = $row["password"];

        if($school != $currentschool){
            echo "<script>alert('Вы не можете посмотреть данные ученика другой школы');</script>";
        } else{
            echo "<script>alert('Логин: " . json_encode($login) . "\\nПароль: " . json_encode($password) . "');</script>";
        }
    }
?>