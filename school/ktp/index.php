<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if(!in_array($currentrole, ["Учитель", "Завуч", "Директор"])){
        header("Location: /");
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["lesson"], $_POST["group"])){
            $conn->query("INSERT INTO `ktps`(`lesson`, `grade`, `author`, `school`) VALUES('$_POST[lesson]', '$_POST[group]', '$currentfullname', '$currentschool')");
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
    <title>КТП</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
    <style>
        .edit-icon {
            display: inline-block;
            width: 32px;
            height: 32px;
            background-size: cover;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .edit-icon img{
            width: 32px;
            width: 32px;
        }
        .edit-icon:hover {
            cursor: pointer;
        }

        .ktps {
            display: flex;
            flex-wrap: wrap;
            max-width: 70%;
            justify-content: start;
            padding-left: 10%;
        }
        .ktp {
            padding: 15px;
            background-color: lightsteelblue;
            text-align: left;
            border-radius: 5px;
            width: 300px;
            margin: 10px;
        }
    </style>
</head>
<body>
    <div id="ex1" class="modal">
        <h2>Создание нового КТП</h2>
        <form method="post">
            <select name="lesson" required>
                <option value="" selected disabled>Выберите предмет</option>
                <?php
                $res = $conn->query("SELECT DISTINCT `lesson` FROM `workload` WHERE `teacher` = '$currentfullname' AND `school` = '$currentschool'");
                while ($res && $row = $res->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['lesson']) . "'>" . htmlspecialchars($row['lesson']) . "</option>";
                }
                ?>
            </select>
            <select name="group" required>
                <?php for ($i = 1; $i <= 11; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
            <input type="submit" value="Создать">
        </form>
    </div>
    <?php require "../teacherhead.php"; ?>
    <h2>КТП</h2>
    <a href="#ex1" rel="modal:open"><button>Новое КТП</button></a><br><br>
    <h3>Созданные КТП</h3>
    <?php
    $res = $conn->query("SELECT `lesson`, `grade`, `author` FROM `ktps` WHERE `school` = '$currentschool'");
    if ($res && $res->num_rows > 0):
        echo "<section class='ktps'>";
        while ($row = $res->fetch_assoc()):
    ?>
            <div class='ktp'>
                <h3><?= htmlspecialchars($row['lesson']) ?>, <?= htmlspecialchars($row['grade']) ?> класс</h3>
                <p><?= htmlspecialchars($row['author']) ?></p>
                <a href="edit.php?lesson=<?= $row["lesson"] ?>&grade=<?= $row["grade"] ?>" class="edit-icon"><img src="/img/edit.svg"></a>
            </div>
    <?php
        endwhile;
        echo "</section>";
    endif;
    ?>
</body>
</html>
