<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=utf-8");

$host = 'localhost';
$dbLogin = 'smalasmailer';
$dbPassword = 'SmalaSmile2012!';
$dbName = 'shkolnik';

$conn = new mysqli($host, $dbLogin, $dbPassword, $dbName);
if ($conn->connect_error) {
    echo json_encode(["error" => $conn->connect_error]);
    die();
}
$conn->set_charset("utf8");

if (isset($_GET["act"])) {
    $act = $_GET["act"];
    if ($act == "checkAccount") {
        if (isset($_GET["login"], $_GET["password"])) {
            $userLogin = $_GET["login"];
            $userPassword = $_GET["password"];

            // Подготовленное выражение для защиты от SQL-инъекций
            $stmt = $conn->prepare("SELECT `fullname` FROM `students` WHERE `login` = ? AND `password` = ?");
            $stmt->bind_param("ss", $userLogin, $userPassword);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                echo json_encode(["status" => "success"]);
            } else {
                echo json_encode(["status" => "failed"]);
            }

            $stmt->close();
        }
    } elseif($act == "getInfo"){
        if (isset($_GET["login"])) {
            $userLogin = $_GET["login"];

            // Подготовленное выражение для защиты от SQL-инъекций
            $stmt = $conn->prepare("SELECT `groupname`, `fullname`, `school` FROM `students` WHERE `login` = ?");
            $stmt->bind_param("s", $userLogin);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();

            echo json_encode(["groupname" => $row["groupname"], "fullname" => $row["fullname"], "school" => $row["school"]]);

            $stmt->close();
        }
    } elseif($act == "getMarks") {
        if (isset($_GET["login"], $_GET["groupname"], $_GET["date"], $_GET["period"])) {
            $groupname = $_GET["groupname"];
            $date = $_GET["date"];
            $period = $_GET["period"];
    
            $res = $conn->query("SELECT `fullname` FROM `students` WHERE `login` = '$_GET[login]'");
            $fullname = $res->fetch_assoc()["fullname"];
    
            $res = $conn->query("SELECT `dayid`, `date`, `lessonname`, `mark`, `typemark` FROM `marks` WHERE `groupname` = '$groupname' AND `date` = '$date' AND `period` = '$period' AND `studentname` = '$fullname'");
            
            $marks = [];
            while ($row = $res->fetch_assoc()) {
                $marks[] = [
                    "dayid" => $row["dayid"],
                    "date" => $row["date"],
                    "lessonname" => $row["lessonname"],
                    "mark" => $row["mark"],
                    "typemark" => $row["typemark"],
                ];
            }
            echo json_encode($marks);
        }
    } elseif($act == "getTimetable"){
        if(isset($_GET["login"], $_GET["groupname"], $_GET["date"], $_GET["period"])){
            $login = $_GET["login"];
            $groupname = $_GET["groupname"];
            $date = $_GET["date"];
            $period = (int)$_GET["period"];

            $res = $conn->query("SELECT `school` FROM `students` WHERE `login` = '$login'");
            $school = $res->fetch_assoc()["school"];

            $query = "SELECT `dayid`, `lessonname`, `teacher`, `lessontopic`, `homework`
                      FROM `timetable`
                      WHERE `groupname` = '$groupname' 
                        AND `date` = '$date' 
                        AND `period` = $period
                        AND `school` = '$school'
                        AND `lessontopic` IS NOT NULL
                        AND `homework` IS NOT NULL";

            $res = $conn->query($query);

            $timetable = [];
            while($row = $res->fetch_assoc()){
                $timetable[] = [
                    "dayid" => $row["dayid"],
                    "lessonname" => $row["lessonname"],
                    "teacher" => $row["teacher"],
                    "lessontopic" => $row["lessontopic"],
                    "homework" => $row["homework"]
                ];
            }

            echo json_encode($timetable);
        }
    }
    
}
$conn->close();
?>