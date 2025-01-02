<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Директор" && $currentrole != "Завуч"){
        header("Location: /");
        exit();      
    }
    if (isset($_GET["community"])) {
        $publicid = $_GET["community"];

        $res = $conn->query("SELECT `login` FROM `users` WHERE `fullname` = '$currentfullname'");
        $login = $res->fetch_assoc()["login"];

        $isAuthor = false;

        if ($_COOKIE["acclogin"] == $login) {
            $isAuthor = true;
        }

        if(!$isAuthor){
            header("Location: index.php");
            exit();
        }

        // Подготовка запроса для вставки в communityposts
        $stmt_insert = $conn->prepare("INSERT INTO `communityposts` (`id`, `text`, `author`, `community`) VALUES (?, ?, ?, ?)");

        // Подготовка запроса для выборки из posts
        $stmt_select = $conn->prepare("SELECT * FROM `posts` WHERE `school` = ?");
        $stmt_select->bind_param("s", $currentschool);
        $stmt_select->execute();

        $res = $stmt_select->get_result();

        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                // Формируем текст
                $text = "# " . $row['header'] . "\n" . $row['text'];

                // Вставляем данные в communityposts
                $stmt_insert->bind_param("isss", $row['id'], $text, $row['author'], $publicid);
                $stmt_insert->execute();
            }
        }

        // Закрываем подготовленные выражения
        $stmt_select->close();
        $stmt_insert->close();

        $conn->query("DELETE FROM `posts` WHERE `school` = '$currentschool'");

        header("Location: settings.php?id=$publicid");
        exit();
    }
?>