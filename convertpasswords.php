<?php
    require "config.php";

    $res = $conn->query("SELECT `login`, `password` FROM users");
    while($row = $res->fetch_assoc()){
        $login = $row["login"];
        $password = $row["password"];

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("UPDATE `users` SET `password` = ? WHERE login = ?");
        $stmt->bind_param("ss", $hashed_password, $login);
        $stmt->execute();
    }