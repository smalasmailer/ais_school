<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    require 'Parsedown.php';  // Подключаем Parsedown

    $Parsedown = new Parsedown();  // Создаем экземпляр Parsedown
    $Parsedown->setMarkupEscaped(true);

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
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $isAuthor) {
        if (isset($_POST["text"])) {
            $res = $conn->query("SELECT `id` FROM `communityposts` WHERE `community` = '$publicid'");
            $id = $res->num_rows+1;

            $conn->query("INSERT INTO `communityposts`(`id`, `text`, `author`, `community`) VALUES($id, '$_POST[text]', '$currentfullname', '$publicid')");
            header("Location: group.php?id=$publicid");
            exit();
        }
    }
    if(isset($_GET["act"]) && $isAuthor){
        $act = $_GET["act"];

        if($act == "del"){
            if(isset($_GET["postid"])){
                $conn->query("DELETE FROM `communityposts` WHERE `id` = '$_GET[postid]' AND `community` = '$publicid'");
                header("Location: group.php?id=$publicid");
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сообщество</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body, html { height: 70%; }
        .community { display: flex; width: 80%; height: 100%; justify-content: center; align-items: center; margin: 0 auto; padding: 0; box-sizing: border-box; }
        .posts { width: 70%; height: 100%; margin-right: 15px; }
        .info { width: 20%; height: 100%; background-color: lightblue; }

        /* Контейнер для кнопок и текстового поля */
        .input-container { display: flex; flex-direction: column; align-items: flex-start; }

        .format-buttons {
            display: none;
            flex-direction: row;
            margin-bottom: 5px;
        }

        .format-buttons button { 
            margin-right: 3px; 
            padding: 5px;
            width: 35px;
        }

        /* Текстовое поле */
        .newpost textarea {
            resize: vertical;
            height: 100px;
            min-width: 95%;
        }

        .newpost input[type="submit"] { margin: 0; width: 95%; min-width: 95%; }

        .post { padding: 5px; background-color: #E5E5E5; text-align: left; border-radius: 5px; padding-left: 20px; }
    </style>
    <meta name="description" content="<?= $descripion ?>">
</head>
<body>
    <?php require "header.php"; ?>
    <br>
    <section class="community">
        <div class="posts">
            <h1>Публикации</h1>
            <?php if ($isAuthor): ?>
            <div class="newpost">
                <p>Вы можете использовать <a href="https://ru.wikipedia.org/wiki/Markdown">форматирование Markdown</a> и <a href="https://ru.wikipedia.org/wiki/HTML#Структура_HTML-документа">HTML</a></p>
                <form method="post">
                    <input type="hidden" name="publicid" value="<?= $publicid ?>">
                    <div class="input-container">
                        <div class="format-buttons" id="format-buttons">
                            <button type="button" onclick="insertMarkdown('# ')" title="Заголовок 1" onmousedown="preventHide()">З1</button>
                            <button type="button" onclick="insertMarkdown('## ')" title="Заголовок 2" onmousedown="preventHide()">З2</button>
                            <button type="button" onclick="insertMarkdown('### ')" title="Заголовок 3" onmousedown="preventHide()">З3</button>
                            <button type="button" onclick="insertMarkdown('#### ')" title="Заголовок 4" onmousedown="preventHide()">З4</button>
                            <button type="button" onclick="insertMarkdown('**', '**')" title="Жирный текст" onmousedown="preventHide()">Ж</button>
                            <button type="button" onclick="insertMarkdown('*', '*')" title="Курсив" onmousedown="preventHide()">К</button>
                            <button type="button" onclick="insertMarkdown('~~', '~~')" title="Зачеркнутый текст" onmousedown="preventHide()">Зч</button>
                        </div>
                        <textarea name="text" placeholder="Напишите о чём-нибудь..." onfocus="showButtons()" onblur="hideButtons()"></textarea>
                    </div>
                    <input type="submit" value="Опубликовать">
                </form>
            </div>
            <?php endif; ?>

            <!-- Отображение существующих постов -->
            <?php
                $res = $conn->query("SELECT `id`, `text`, `author` FROM `communityposts` WHERE `community` = '$publicid' ORDER BY `id` DESC");
                if ($res->num_rows > 0) {
                    echo "<br>";
                    while ($row = $res->fetch_assoc()) {
                        $textHtml = $Parsedown->text($row["text"]);  // Конвертируем Markdown в HTML
                        echo "<div class='post'>";
                        echo "<p>$textHtml</p><br>";
                        echo "<p><span style='color: gray;'>Написал: $row[author]</span></p>";
                        if($isAuthor){
                            echo "<a href='?id=$publicid&act=del&postid=$row[id]'><button>Удалить</button></a>";
                        }
                        echo "</div><br>";
                    }
                }
            ?>
        </div>
        <div class="info">
            <h1><?= $name ?></h1>
            <h3><?= $category ?></h3>
            <p><?= $descripion ?></p>
            <p><a href="profile/public.php?profile=<?= $login ?>"><button><?= $author ?></button></a></p>
            <?php if ($isAuthor): ?>
                <br><p><a href="communities/settings.php?id=<?= $publicid ?>"><button>Настройки сообщества</button></a></p>
            <?php endif; ?>
        </div>
    </section>

    <script>
        function insertMarkdown(prefix, suffix = '') {
            const textarea = document.querySelector('textarea[name="text"]');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;

            // Insert the markdown syntax around the selected text
            textarea.value = text.slice(0, start) + prefix + text.slice(start, end) + suffix + text.slice(end);
            textarea.focus();
            textarea.selectionEnd = end + prefix.length + suffix.length;
        }

        function showButtons() {
            document.getElementById('format-buttons').style.display = 'flex';
        }

        function hideButtons() {
            const activeElement = document.activeElement;
            if (activeElement.tagName !== 'TEXTAREA' && !activeElement.closest('.format-buttons')) {
                document.getElementById('format-buttons').style.display = 'none';
            }
        }

        function preventHide() {
            event.preventDefault();
        }

        document.addEventListener('click', function(event) {
            const target = event.target;
            if (!target.closest('.format-buttons') && target.tagName !== 'TEXTAREA') {
                hideButtons();
            }
        });
    </script>
</body>
</html>
