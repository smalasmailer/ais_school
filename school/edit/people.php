<?php
require "../../config.php";
require $_SERVER["DOCUMENT_ROOT"] . "/simple-xlsx/simplexlsx.class.php"; // Подключение библиотеки

if (session_status() == PHP_SESSION_NONE || session_status() == PHP_SESSION_DISABLED) {
    session_start();
}

if (!isset($_COOKIE["acclogin"])) {
    header("Location: ../../index.html");
    exit();
}

if ($currentrole != "Директор" && $currentrole != "Администратор") {
    header("Location: ../index.php");
    exit();
}

if (isset($_FILES['xlsx_file']) && $_FILES['xlsx_file']['error'] == UPLOAD_ERR_OK) {
    $xlsx = SimpleXLSX::parse($_FILES['xlsx_file']['tmp_name']);

    if ($xlsx) {
        $rows = $xlsx->rows();
        $imported_count = 0;
        $existing_count = 0;

        foreach ($rows as $key => $row) {
            if ($key === 0) continue; // Пропускаем заголовок

            $personlogin = $row[0];
            $personpassword = $row[1];
            $role = $row[2];
            $fullname = $row[3];

            // Проверка на существование пользователя
            $stmt = $conn->prepare("SELECT `login` FROM `users` WHERE `login` = ?");
            $stmt->bind_param("s", $personlogin);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                echo "<p>Пользователь с логином <strong>$personlogin</strong> уже существует. Пропускаем его.</p>";
                $existing_count++;
                continue; // Пропускаем существующего пользователя
            }

            // Хеширование пароля
            $hashedPassword = password_hash($personpassword, PASSWORD_BCRYPT);

            // Вставка нового сотрудника
            $avatarurl = ' ';
            $stmt = $conn->prepare("INSERT INTO `users`(`login`, `password`, `role`, `fullname`, `school`, `avatarurl`) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $personlogin, $hashedPassword, $role, $fullname, $currentschool, $avatarurl);
            if ($stmt->execute()) {
                $imported_count++;
            }
        }

        echo "<p>Импорт завершен! Успешно импортировано: $imported_count, пропущено: $existing_count.</p>";
    } else {
        echo "<p>Ошибка при загрузке файла</p>";
    }
}

if (isset($_GET["personlogin"], $_GET["personpassword"], $_GET["role"], $_GET["school"], $_GET["fullname"])) {
    $personlogin = $_GET["personlogin"];
    $personpassword = $_GET["personpassword"];
    $role = $_GET["role"];
    $school = $_GET["school"];
    $fullname = $_GET["fullname"];

    $stmt = $conn->prepare("SELECT `login` FROM `users` WHERE `login` = ?");
    $stmt->bind_param("s", $personlogin);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        die("Пользователь с логином '$personlogin' уже существует. <a href='people.php'>Вернуться</a>");
    }

    // Хеширование пароля
    $hashedPassword = password_hash($personpassword, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO `users`(`login`, `password`, `role`, `school`, `fullname`, `avatarurl`) VALUES (?, ?, ?, ?, ?, ?)");
    $av = " ";
    $stmt->bind_param("ssssss", $personlogin, $hashedPassword, $role, $school, $fullname, $av);
    $stmt->execute();

    header("Location: people.php");
}

if (isset($_GET["employee_id"])) {
    $employee_id = $_GET["employee_id"];
    $conn->query("DELETE FROM `users` WHERE `fullname` = '$employee_id' AND `school` = '$currentschool';");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сотрудники</title>
</head>
<body>
    <?php require "../schoolhead.php"; ?>
    <h1>Создание или редактирование людей школы</h1>
    
    <div>
        <center>
            <form method="get" style="max-width:250px;" class="editform">
                <input type="text" name="personlogin" placeholder="Логин" required><br>
                <input type="password" name="personpassword" placeholder="Пароль" required><br>
                <select name="role" required>
                    <?php
                    foreach ($roles as $r) {
                        echo "<option value='{$r}'>{$r}</option>";
                    }
                    ?>
                </select>
                <input type="hidden" name="school" value="<?php echo htmlspecialchars($currentschool); ?>">
                <input type="text" name="fullname" placeholder="Полное имя" required><br>
                <input type="submit" value="Зарегистрировать">
            </form>
        </center>
    </div>

    <hr>

    <h2>Импорт сотрудников из Excel</h2>
    <div>
        <center>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="xlsx_file" accept=".xlsx" required>
                <input type="submit" value="Импортировать">
            </form>
        </center>
    </div>

    <hr>

    <h2>Редактирование людей</h2>
    <div>
        <center>
            <?php
            $directorrole = "Директор";

            $stmt = $conn->prepare("SELECT fullname FROM users WHERE school = ? AND role != ?");
            $stmt->bind_param("ss", $currentschool, $directorrole);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                echo "<table border='1'>";
                while ($row = $res->fetch_assoc()) {
                    $fn = htmlspecialchars($row['fullname']);
                    echo "<tr>";
                    echo "<td><a href='$site/profile/check.php?user=$fn&school=$currentschool'>$fn</a></td>";
                    echo "<td><a href='?employee_id=$fn'>X</a></td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "<p>Вы можете нажать на имя сотрудника, чтобы увидеть его логин и пароль, а также роль.</p>";
            }
            ?>
        </center>
    </div>
    <button><a href="students.php">Добавление учеников</a></button>
</body>
</html>
