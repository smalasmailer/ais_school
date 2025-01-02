<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Админ"){
        header("Location: ../index.html");
        exit();
    }

    echo "<meta charset='utf-8'>";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["school"])){
            $school = $conn->real_escape_string($_POST["school"]);

            $res = $conn->query("SELECT `director`, `directoremail` FROM `schools` WHERE `orgshort` = '$school'");
            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $directoremail = $row["directoremail"];
                $director = $row["director"];
            } else {
                // Обработать ошибку, если школа не найдена
                exit("Школа не найдена");
            }

            $delete_queries = [
                "DELETE FROM `schools` WHERE `orgshort` = '$school'",
                "DELETE FROM `users` WHERE `school` = '$school'",
                "DELETE FROM `students` WHERE `school` = '$school'",
                "DELETE FROM `marks` WHERE `school` = '$school'",
                "DELETE FROM `timetable` WHERE `school` = '$school'",
                "DELETE FROM `totalmarks` WHERE `school` = '$school'",
                "DELETE FROM `personalfile` WHERE `school` = '$school'",
                "DELETE FROM `types` WHERE `school` = '$school'",
                "DELETE FROM `workload` WHERE `school` = '$school'",
                "DELETE FROM `lessons` WHERE `school` = '$school'",
                "DELETE FROM `groups` WHERE `school` = '$school'"
            ];
            $conn->query("INSERT INTO `logs`(`user`, `action`) VALUES('$currentfullname', 'Удаление школы: $school ($director: $directoremail)')");

            foreach ($delete_queries as $query) {
                if (!$conn->query($query)) {
                    // Вывести ошибку при выполнении запроса
                    echo "Ошибка при выполнении запроса: " . $conn->error;
                }
            }

            $res = $conn->query("SELECT `scheme` FROM `schemes` WHERE `school` = '$school'");
            if($res && $res->num_rows > 0){
                while($row = $res->fetch_assoc()){
                    $scheme_name = $conn->real_escape_string($row['scheme']);
                    try{
                        $conn->query("DROP TABLE `$scheme_name`");
                    } catch (Exception){
                        echo "Пропуск";
                    }
                    $conn->query("DELETE FROM `schemes` WHERE `scheme` = '$scheme_name' AND `school` = '$school'");
                    
                }
            }

            header("Location: " . $_SERVER["PHP_SELF"] . "?director=" . urlencode($director) . "&directoremail=" . urlencode($directoremail));
        }
    }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить школу</title>
</head>
<body>
    <?php require "../adminheader.php";
        if($_SERVER["REQUEST_METHOD"] == "GET"){
            if(isset($_GET["director"], $_GET["directoremail"])){
                $director = $_GET["director"];
                $directoremail = $_GET["directoremail"];
                echo "<p>Удалена школа $director ($directoremail)</p>";

                $headers = array(
                    'From' => "support@ais-school.ru",
                    'Reply-To' => "support@ais-school.ru",
                    'X-Mailer' => 'PHP/' . phpversion()
                );
                $subject = "Состояние организации в АИС «Школа»";
                $message = "Здравствуйте, $director!\nК сожалению ваша организация была удалена системным администратором нашей платформы :(\nС уважением, Команда АИС Школа";
                if(!mail($directoremail, $subject, $message, $headers)){
                    echo "Письмо не отправлено";
                }
            }
        }
        ?>
    <h2>Удалить школу</h2>
    <form method="post">
        <select name="school" require>
            <option value="" disabled selected>Выберите школу</option>
            <?php
                $res = $conn->query("SELECT `orgshort` FROM `schools`");
                while($row = $res->fetch_assoc()){
                    echo "<option value='$row[orgshort]'>$row[orgshort]</option>";
                }
            ?>
        </select>
        <input type="submit" value="Удалить">
    </form>
</body>