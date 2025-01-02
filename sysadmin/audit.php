<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require "../config.php";

// Проверка роли администратора
if ($currentrole != "Админ") {
    header("Location: ../index.html");
    exit();
}

// Запрос списка всех школ
$sql_schools = "SELECT orgshort FROM `schools`";
$result_schools = $conn->query($sql_schools);

if (!$result_schools) {
    die("Ошибка запроса к таблице школ: " . $conn->error);
}

$schools_to_display = [];

while ($school = $result_schools->fetch_assoc()) {
    $school_name = $school['orgshort'];

    // Подсчет сотрудников
    $sql_users = "
        SELECT COUNT(*) as teacher_count 
        FROM `users` 
        WHERE `school` = ? 
        AND `role` IN ('Учитель', 'Директор', 'Завуч')
    ";
    $stmt_users = $conn->prepare($sql_users);
    $stmt_users->bind_param("s", $school_name);
    $stmt_users->execute();
    $result_users = $stmt_users->get_result();
    $teacher_count = $result_users->fetch_assoc()['teacher_count'] ?? 0;

    // Подсчет учеников
    $sql_students = "SELECT COUNT(*) as student_count FROM `students` WHERE `school` = ?";
    $stmt_students = $conn->prepare($sql_students);
    $stmt_students->bind_param("s", $school_name);
    $stmt_students->execute();
    $result_students = $stmt_students->get_result();
    $student_count = $result_students->fetch_assoc()['student_count'] ?? 0;

    if ($teacher_count < 5 || $student_count < 5) {
        $schools_to_display[] = [
            'name' => $school_name,
            'teachers' => $teacher_count,
            'students' => $student_count
        ];
    }
}
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["schooltodel"])){
        $school = $_POST["schooltodel"];

        $res = $conn->query("SELECT `director`, `directoremail` FROM `schools` WHERE `orgshort` = '$school'");
        $row = $res->fetch_assoc();
        $director = $row["director"];
        $directoremail = $row["directoremail"];

        $headers = array(
            'From' => "support@ais-school.ru",
            'Reply-To' => "support@ais-school.ru",
            'X-Mailer' => 'PHP/' . phpversion()
        );
        $subject = "Аудит школ";
        $message = "Здравствуйте, $director!\nК сожалению ваша организация была удалена при аудите платформы платформы :(\nОрганизация: $school\nС уважением, Команда АИС Школа";
        if(!mail($directoremail, $subject, $message, $headers)){
            echo "Письмо не отправлено";
        }

        $conn->query("DELETE FROM `schools` WHERE `orgshort` = '$school'");
        $conn->query("DELETE FROM `users` WHERE `school` = '$school'");
        $conn->query("INSERT INTO `logs`(`user`, `action`) VALUES('$currentfullname', 'Удалил школу (аудит): $school ($director: $directoremail)')");
        
        $res = $conn->query("SELECT `scheme` FROM `schemes` WHERE `school` = '$school'");
        if($res->num_rows>0){
            $schemes = $res->fetch_assoc();
            foreach($schemes as $scheme){
                $conn->query("DROP TABLE `$scheme`");
                $conn->query("DELETE FROM `schemes` WHERE `scheme` = '$scheme' AND `school` = '$school'");
            }
        }
        $conn->query("DELETE FROM `students` WHERE `school` = '$school'");
        $conn->query("DELETE FROM `timetable` WHERE `school` = '$school'");
        $conn->query("DELETE FROM `marks` WHERE `school` = '$school'");
        $conn->query("DELETE FROM `totalmarks` WHERE `school` = '$school'");
        $conn->query("DELETE FROM `personalfile` WHERE `school` = '$school'");
        $conn->query("DELETE FROM `types` WHERE `school` = '$school'");
        $conn->query("DELETE FROM `workload` WHERE `school` = '$school'");
        $conn->query("DELETE FROM `lessons` WHERE `school` = '$school'");
        $conn->query("DELETE FROM `groups` WHERE `school` = '$school'");

        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аудит системы</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <?php require "adminheader.php"; ?>
    <p>Здесь отображены школы, в которых:</p>
    <ul>
        <li>меньше 5 сотрудников</li>
        <li>меньше 5 учеников</li>
    </ul>

    <center><?php if (!empty($schools_to_display)): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Школа</th>
                <th>Количество сотрудников</th>
                <th>Количество учеников</th>
            </tr>
            <?php foreach ($schools_to_display as $school): ?>
                <tr>
                    <td><?= htmlspecialchars($school['name']) ?></td>
                    <td><?= $school['teachers'] ?></td>
                    <td><?= $school['students'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <form method="post">
            <h2>Удаление школы</h2>
            <select name="schooltodel" require>
                <?php
                    foreach($schools_to_display as $school){
                        echo "<option value='$school[name]'>$school[name]</option>";
                    }
                ?>
            </select>
            <input type="submit" value="Удалить">
        </form>
    <?php else: ?>
        <p>Нет школ, удовлетворяющих условиям.</p>
    <?php endif; ?></center>
</body>
</html>