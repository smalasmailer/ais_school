<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "ОУ"){
        header("Location: /");
        exit();
    }

    if(isset($_GET["test"])){
        $test = $_GET["test"];
    } else{
        header("Location: index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование теста</title>
</head>
<body>
    
</body>
</html>