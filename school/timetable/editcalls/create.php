<?php
require "../../../config.php";
require "../../schoolhead.php";

if ($currentrole != "Директор" && $currentrole != "Администратор") {
    header("Location: ../../index.php");
    exit();
}

if (!isset($_POST["maxlessons"])) {
    die("Не указано максимальное количество уроков");
}

$maxlessons = $_POST["maxlessons"];

// Используйте обратные кавычки для имени таблицы 
$tableName = "`{$currentschool}_calls`";

$query = "CREATE TABLE IF NOT EXISTS $tableName (
    id INTEGER NOT NULL PRIMARY KEY,
    `from` TIME NOT NULL,
    `to` TIME NOT NULL
);";

if (!$conn->query($query)) {
    die("Ошибка создания таблицы: " . $conn->error);
}

// Вставка данных
for ($i = 1; $i <= $maxlessons; $i++) {
    $fromValue = $_POST["from_$i"];
    $toValue = $_POST["to_$i"];
    
    $insertQuery = "INSERT INTO $tableName (`id`, `from`, `to`) VALUES ('$i', '$fromValue', '$toValue')";
    
    if (!$conn->query($insertQuery)) {
        die("Ошибка вставки данных: " . $conn->error);
    }
}

header("Location: ../calls.php");
exit();
?>