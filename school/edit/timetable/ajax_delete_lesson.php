<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data["dayid"]) && isset($data["dayweek"]) && isset($data["scheme"])) {
        $dayid = $data["dayid"];
        $dayweek = $data["dayweek"];
        $scheme = $data["scheme"];

        // Запрос на удаление урока
        $stmt = $conn->prepare("DELETE FROM `$scheme` WHERE `dayid` = ? AND `dayweek` = ?");
        $stmt->bind_param("is", $dayid, $dayweek);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Ошибка при удалении"]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Некорректные данные"]);
    }
}
?>
<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data["dayid"]) && isset($data["dayweek"]) && isset($data["scheme"])) {
        $dayid = $data["dayid"];
        $dayweek = $data["dayweek"];
        $scheme = $data["scheme"];

        // Запрос на удаление урока
        $stmt = $conn->prepare("DELETE FROM `$scheme` WHERE `dayid` = ? AND `dayweek` = ?");
        $stmt->bind_param("is", $dayid, $dayweek);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Ошибка при удалении"]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Некорректные данные"]);
    }
}
?>
<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data["dayid"]) && isset($data["dayweek"]) && isset($data["scheme"])) {
        $dayid = $data["dayid"];
        $dayweek = $data["dayweek"];
        $scheme = $data["scheme"];

        // Запрос на удаление урока
        $stmt = $conn->prepare("DELETE FROM `$scheme` WHERE `dayid` = ? AND `dayweek` = ?");
        $stmt->bind_param("is", $dayid, $dayweek);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Ошибка при удалении"]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Некорректные данные"]);
    }
}
?>
