<?php
    require "../../../config.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/simple-xlsx/simplexlsx.class.php";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Импорт предметов</title>
    <link rel="stylesheet" href="../../../style.css">
</head>
<body>
    <?php
        require "../../schoolhead.php";
    ?>
    <h2>Импорт предметов</h2>
    <a href="lessonexample.xlsx" download>Скачать шаблон</a>
    <hr>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="lessonsheet" accept=".xlsx">
        <input type="submit" value="Импорт">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Проверка, загружен ли файл
        if (isset($_FILES['lessonsheet']) && $_FILES['lessonsheet']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['lessonsheet']['tmp_name'];
            $fileName = $_FILES['lessonsheet']['name'];
            $fileSize = $_FILES['lessonsheet']['size'];
            $fileType = $_FILES['lessonsheet']['type'];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

            $allowedExtensions = ['xlsx'];
            $maxFileSize = 2 * 1024 * 1024; // 2 MB

            // Проверка расширения файла
            if (in_array($fileExtension, $allowedExtensions)) {
                // Проверка размера файла
                if ($fileSize <= $maxFileSize) {
                    // Чтение файла через simplexlsx
                    $xlsx = SimpleXLSX::parse($fileTmpPath);
                    if ($xlsx) {
                        echo "<h3>Файл загружен и прочитан успешно:</h3>";
                        echo "<center><table border='1'>";
                        
                        // Подготовим SQL-запрос для вставки данных
                        $query = "INSERT INTO `lessons` (`lesson`, `school`) VALUES (?, ?)";
                        
                        // Подключение к базе данных через mysqli
                        if ($stmt = $conn->prepare($query)) {
                            // Пропускаем первую строку с заголовками
                            foreach (array_slice($xlsx->rows(), 1) as $row) {
                                $lessonName = $row[0]; // Имя предмета в первой ячейке строки

                                // Вывод данных в таблицу для визуальной проверки
                                echo "<tr>";
                                foreach ($row as $cell) {
                                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                                }
                                echo "</tr>";
                                
                                // Вставка данных в базу
                                $stmt->bind_param("ss", $lessonName, $currentschool);
                                
                                if (!$stmt->execute()) {
                                    echo "<p>Ошибка при импорте данных для урока " . htmlspecialchars($lessonName) . ": " . $stmt->error . "</p>";
                                }
                            }
                            $stmt->close();
                            echo "</table></center>";
                            echo "<p>Все данные успешно импортированы.</p>";
                            header("Location: ../lessons.php");
                            exit();
                        } else {
                            echo "<p>Ошибка подготовки запроса: " . $mysqli->error . "</p>";
                        }
                    } else {
                        echo "<p>Ошибка при чтении файла: " . SimpleXLSX::parseError() . "</p>";
                    }
                } else {
                    echo "<p>Файл слишком большой. Максимальный размер — 2 МБ.</p>";
                }
            } else {
                echo "<p>Недопустимый тип файла. Пожалуйста, загрузите файл в формате .xlsx.</p>";
            }
        } else {
            echo "<p>Ошибка загрузки файла.</p>";
        }
    }
    ?>
</body>
</html>