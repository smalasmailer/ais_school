<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "ОУ"){
        header("Location: /");
        exit();
    }
    $accepted = null;
    if(isset($_GET["accepted"])){
        if($_GET["accepted"]){
            $accepted = true;
        }
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["testname"], $_POST["rate"], $_POST["creditfrom"])){
            $testname = $_POST["testname"];
            $rate = $_POST["rate"];
            $creditfrom = (int)$_POST["creditfrom"];
            $createdate = date("Y-m-d", time());
            $author = $organname;
            $ispublish = 0;

            $stmt = $conn->prepare("INSERT INTO `organtests`(`testname`, `author`, `rate`, `ispublish`, `fromcredit`, `createdate`) VALUES(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssiis", $testname, $author, $rate, $ispublish, $creditfrom, $createdate);
            $stmt->execute();
            $stmt->close();

            header("Location: index.php?accepted=1");
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Педагогическая аттестация</title>
    <link rel="stylesheet" href="/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
    <style>
        .tests{
            display: flex;
            flex-wrap: wrap;
            justify-content: start;
        }
        .test{
            background-color: darkblue;
            border-radius: 15px;
            padding: 20px;
            color: white;
            margin: 15px;
        }
    </style>
</head>
<body>
    <div class="modal" id="create">
        <h2>Создание теста</h2>
        <form method="post">
            <input type="text" name="testname" placeholder="Название теста" required><br>
            <select name="rate" required>
                <option value="" selected disabled>Система оценивания</option>
                <option value="пятибальная">Пятибальная (2-5)</option>
                <option value="зачет">Зачет (зч/нз)</option>
                <option value="проценты">Проценты (0-100%)</option>
                <option value="стобальная">Стобальная</option>
            </select><br>
            Зачет с <input type="number" name="creditfrom" id="" min="0" max="100" style="width: 100px;">%<br>
            <input type="submit" value="Создать">
        </form>
    </div>
    <?php if(!isset($accepted)): ?>
        <h2>Ваш орган <?= $organname ?>?</h2>
        <button><a href="/logout.php">Нет. Выйти из аккаунта</a></button>
        <button><a href="?accepted=1">Да. Продолжить</a></button>
        <p>Если вы находитесь не под тем органом, на котором надо организовать пед. аттестацию, то ответственность за проделанные действия будете вы.</p>
    <?php else: ?>
        <?php require "../organheader.php"; ?>
        <h2>Педагогическая аттестация</h2>
        <a href="#create" rel="modal:open"><button>Создать тест</button></a><br>
        <?php
            $res = $conn->query("SELECT * FROM `organtests` WHERE `author` = '$organname'");
            if (!$res) {
                die("Ошибка выполнения запроса: " . $conn->error);
            }
            
            if ($res->num_rows > 0) {
                echo "<section class='tests'>";
                while ($row = $res->fetch_assoc()) {
                    $id = preg_replace("/[^a-zA-Z0-9]/", "", $row["testname"]);
                    $formdate = date("d.m.Y", strtotime($row["createdate"]));
                    $publishstatus = $row["ispublish"] ? "опубликован" : "черновик";
            
                    echo "<div class='modal' id='about-$id'>";
                    echo "<h2>{$row['testname']}</h2>";
                    echo "<p>Система оценивания: {$row['rate']}</p>";
                    echo "<p>Статус: $publishstatus</p>";
                    echo "<p>Зачет при: {$row['fromcredit']}%</p>";
                    echo "<p>Дата создания: $formdate</p>";
                    echo "</div>";
            
                    echo "<div class='test'>";
                    echo "<h3>{$row['testname']}</h3>";
                    echo "<button><a href='#about-$id' rel='modal:open'>Сведения</a></button><br>";
                    echo "<button><a href='edit.php?test={$row['testname']}'>Редактировать</a></button>";
                    echo "</div>";
                }
                echo "</section>";
            } else {
                echo "<p>Тесты не найдены.</p>";
            }
            
        ?>
    <?php endif; ?>
</body>
</html>