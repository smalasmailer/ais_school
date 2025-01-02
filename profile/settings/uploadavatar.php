<?php
require "../../config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maxFileSize = 2 * 1024 * 1024; // 2 MB
    $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/profile/avatars/'; // Путь на сервере для сохранения файлов

    // Проверка, существует ли директория, если нет - создаем
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }

    // Получаем логин пользователя
    $login = $_COOKIE["acclogin"]; 

    // Получаем текущий URL аватара из базы данных
    $stmt = $conn->prepare("SELECT avatarurl FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Удаляем старый аватар, если он существует
    if ($row && !empty($row['avatarurl'])) {
        // Извлекаем только относительный путь
        $oldAvatarPath = $_SERVER['DOCUMENT_ROOT'] . parse_url($row['avatarurl'], PHP_URL_PATH);
        if (file_exists($oldAvatarPath)) {
            if (unlink($oldAvatarPath)) {
                echo "Старый аватар успешно удален: " . $row['avatarurl'] . '<br>';
            } else {
                echo "Ошибка при удалении старого аватара: " . $row['avatarurl'] . '<br>';
            }
        } else {
            echo "Старый аватар не существует: " . $oldAvatarPath . '<br>';
        }
    } else {
        echo "У пользователя нет текущего аватара.<br>";
    }

    // Получаем информацию о загружаемом файле
    $fileTmpPath = $_FILES['avatar']['tmp_name'];
    $fileName = $_FILES['avatar']['name'];
    $fileSize = $_FILES['avatar']['size'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Проверяем размер файла
    if ($fileSize > $maxFileSize) {
        echo "Файл превышает максимальный размер в 2 МБ.";
    } elseif (!in_array($fileExtension, ['png', 'jpg', 'jpeg'])) {
        echo "Допустимые форматы: PNG, JPG, JPEG.";
    } else {
        // Генерируем новое имя файла, чтобы избежать конфликтов
        $newFileName = uniqid('', true) . '.' . $fileExtension;
        $destinationPath = $uploadDirectory . $newFileName;

        // Перемещаем загруженный файл в указанное место
        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            echo "Файл успешно загружен: " . $newFileName . '<br>';

            // Создаем URL для сохранения в базе данных
            $avatarUrl = 'https://shkolnik.site/profile/avatars/' . $newFileName;

            // Подготовка SQL-запроса для обновления avatarurl
            $stmt = $conn->prepare("UPDATE users SET avatarurl = ? WHERE login = ?");
            $stmt->bind_param("ss", $avatarUrl, $login);

            // Выполнение запроса
            if ($stmt->execute()) {
                echo "Аватар успешно обновлен в базе данных.";
                header("Location: index.php");
                exit();
            } else {
                echo "Ошибка обновления аватара: " . $stmt->error;
            }

            // Закрытие подключения
            $stmt->close();
        } else {
            echo "Ошибка при загрузке файла.";
        }
    }
}
?>