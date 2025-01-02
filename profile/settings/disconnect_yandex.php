<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

    $conn->query("UPDATE `users` SET `yid` = NULL WHERE `fullname` = '$currentfullname' AND `login` = '$acclogin'");
    header("Location: index.php");
    exit();
?>