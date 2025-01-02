<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
if ($currentrole != "Админ") {
    header("Location: /index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр схем</title>
</head>
<body>
    <?php require "../adminheader.php"; ?>
    <h2>Схемы</h2>
    <?php if (isset($_POST["school"])): 
        $school = $_POST["school"];
    ?>
        <form method="post" action="checkscheme.php">
            <input type="hidden" name="schemeschool" value="<?php echo htmlspecialchars($school, ENT_QUOTES); ?>">
            <select name="scheme" required>
                <option value="" disabled selected>Выберите схему</option>
                <?php
                // Use prepare() method correctly
                $res = $conn->query("SELECT `scheme`, `grade` FROM `schemes` WHERE `school` = '$school'");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row["scheme"], ENT_QUOTES) . "'>" 
                        . htmlspecialchars($row["scheme"], ENT_QUOTES) . " (" . htmlspecialchars($row["grade"], ENT_QUOTES) . ")</option>";
                }
                ?>
            </select>
            <input type="submit" value="Просмотр">
        </form>
    <?php else: ?>
        <form method="post">
            <select name="school" required>
                <option value="" disabled selected>Выберите школу</option>
                <?php
                $res = $conn->query("SELECT `orgshort` FROM `schools`");
                while ($row = $res->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row["orgshort"], ENT_QUOTES) . "'>" 
                        . htmlspecialchars($row["orgshort"], ENT_QUOTES) . "</option>";
                }
                ?>
            </select>
            <input type="submit" value="Далее">
        </form>
    <?php endif; ?>
</body>
</html>