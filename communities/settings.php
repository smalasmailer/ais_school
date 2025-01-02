<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

    if (isset($_GET["id"])) {
        $publicid = $_GET["id"];

        $res = $conn->query("SELECT * FROM `communities` WHERE `publicid` = '$publicid'");
        $row = $res->fetch_assoc();
        $name = $row["name"];
        $author = $row["author"];
        $descripion = $row["description"];
        $category = $row["category"];

        $res = $conn->query("SELECT `login` FROM `users` WHERE `fullname` = '$author'");
        $login = $res->fetch_assoc()["login"];

        $isAuthor = false;

        if ($_COOKIE["acclogin"] == $login) {
            $isAuthor = true;
        }

        if(!$isAuthor){
            header("Location: index.php");
            exit();
        }
    }
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["community"], $_POST["description"])){
            $community = $_POST["community"];
            $descripion1 = $_POST["description"];

            $conn->query("UPDATE `communities` SET `name` = '$community', `description` = '$descripion1' WHERE `publicid` = '$publicid'");
            header("Location: " . $_SERVER["PHP_SELF"] . "?id=$publicid");
            exit();
        }
    }
    if(isset($_GET["act"])){
        $act = $_GET["act"];
        if($act == "del"){
            $conn->query("DELETE FROM `communities` WHERE `publicid` = '$publicid'");
            $conn->query("DELETE FROM `communityposts` WHERE `community` = '$publicid'");
            $conn->query("UPDATE `schools` SET `community` = NULL WHERE `community` = '$publicid'");
            header("Location: my.php");
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки сообществ</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <?php require $_SERVER["DOCUMENT_ROOT"] . "/header.php" ?><br>
    <h2>Настройки сообщества</h2><br>
    <h3>Общие сведения</h3><br>
    <p>ID сообщества: <?= $publicid ?><br>Категория сообщества: <?= $category ?></p><br>
    <hr>
    <br>
    <form method="post">
        <label>Название сообщества:<br>
            <input type="text" name="community" placeholder="Название сообщества" value="<?php echo htmlspecialchars($name, ENT_QUOTES) ?>">
        </label><br>
        <label>Описание сообщества:<br><textarea name="description" placeholder="Описание"><?= $descripion ?></textarea></label><br>
        <input type="submit" value="Сохранить">
    </form>
    <br>
    <hr>
    <h2>Привяжите сообщество к школе</h2><br>
    <?php 
        if($schoolcommunity == "не привязано"):
    ?>
        <p>Хотите ли вы привязать сообщество "<?= $name ?>" к школе "<?= $currentschool ?>"?</p><br>
        <button><a href="link.php?community=<?= $publicid ?>">Привязать</a></button>
    <?php else: ?>
        <p>Данное сообщество уже привязано к школе "<?= $currentschool ?>"</p><br>
        <button><a href="unlink.php?community=<?= $publicid ?>">Отвязать</a></button><br><br>
        <h3>Перенос публикаций со "Школьных новостей"</h3>
        <p>В данном случае все новые опубликованные посты через сообщество будут удалены, а старые публикации перенесены.</p><br>
        <button><a href="translate.php?community=<?= $publicid ?>">Перенести</a></button>
    <?php endif; ?>
    <hr>
    <p>Ссылка на сообщество: https://ais-school.ru/group.php?id=<?= $publicid ?></p>
    <hr>
    <p>После нажатия на кнопку вы не сможете вернуть предыдущие публикации или иные сведения о сообществе.</p>
    <a href="?id=<?= $publicid ?>&act=del"><button class="danger">Удалить сообщество</button></a>
</body>
</html>