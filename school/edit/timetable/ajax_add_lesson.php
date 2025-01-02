<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

header('Content-Type: application/json'); // Отправляем JSON-ответ

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $lesson = $_POST['lesson'] ?? null;
    $dayweek = $_POST['dayweek'] ?? null;
    $dayid = $_POST['dayid'] ?? null;
    $scheme = $_POST['scheme'] ?? null;
    $grade = $_POST['grade'] ?? null;

    if ($lesson && $dayweek && $dayid && $scheme) {
        // Подготовка запроса для получения учителя
        $stmt = $conn->prepare("SELECT `teacher` FROM `workload` WHERE `group` = ? AND `school` = ? AND `lesson` = ?");
        $stmt->bind_param("sss", $grade, $currentschool, $lesson);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $teacher = $row['teacher'];

            // Подготовка запроса для вставки урока в расписание
            $insert_stmt = $conn->prepare("INSERT INTO `$scheme` (`dayid`, `lesson`, `dayweek`, `teacher`) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("isss", $dayid, $lesson, $dayweek, $teacher);
            
            if ($insert_stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'dayid' => $dayid,
                    'dayweek' => $dayweek,
                    'lesson' => $lesson,
                    'teacher' => $teacher
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении урока']);
            }
            $insert_stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Учитель не найден']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
    }
}
<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

header('Content-Type: application/json'); // Отправляем JSON-ответ

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $lesson = $_POST['lesson'] ?? null;
    $dayweek = $_POST['dayweek'] ?? null;
    $dayid = $_POST['dayid'] ?? null;
    $scheme = $_POST['scheme'] ?? null;
    $grade = $_POST['grade'] ?? null;

    if ($lesson && $dayweek && $dayid && $scheme) {
        // Подготовка запроса для получения учителя
        $stmt = $conn->prepare("SELECT `teacher` FROM `workload` WHERE `group` = ? AND `school` = ? AND `lesson` = ?");
        $stmt->bind_param("sss", $grade, $currentschool, $lesson);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $teacher = $row['teacher'];

            // Подготовка запроса для вставки урока в расписание
            $insert_stmt = $conn->prepare("INSERT INTO `$scheme` (`dayid`, `lesson`, `dayweek`, `teacher`) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("isss", $dayid, $lesson, $dayweek, $teacher);
            
            if ($insert_stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'dayid' => $dayid,
                    'dayweek' => $dayweek,
                    'lesson' => $lesson,
                    'teacher' => $teacher
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении урока']);
            }
            $insert_stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Учитель не найден']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
    }
}
<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

header('Content-Type: application/json'); // Отправляем JSON-ответ

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $lesson = $_POST['lesson'] ?? null;
    $dayweek = $_POST['dayweek'] ?? null;
    $dayid = $_POST['dayid'] ?? null;
    $scheme = $_POST['scheme'] ?? null;
    $grade = $_POST['grade'] ?? null;

    if ($lesson && $dayweek && $dayid && $scheme) {
        // Подготовка запроса для получения учителя
        $stmt = $conn->prepare("SELECT `teacher` FROM `workload` WHERE `group` = ? AND `school` = ? AND `lesson` = ?");
        $stmt->bind_param("sss", $grade, $currentschool, $lesson);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $teacher = $row['teacher'];

            // Подготовка запроса для вставки урока в расписание
            $insert_stmt = $conn->prepare("INSERT INTO `$scheme` (`dayid`, `lesson`, `dayweek`, `teacher`) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("isss", $dayid, $lesson, $dayweek, $teacher);
            
            if ($insert_stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'dayid' => $dayid,
                    'dayweek' => $dayweek,
                    'lesson' => $lesson,
                    'teacher' => $teacher
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении урока']);
            }
            $insert_stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Учитель не найден']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
    }
}
