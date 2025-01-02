<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Директор" && $currentrole != "Учитель" && $currentrole != "Завуч" && $currentrole != "Ученик"){
        header("Location: " . $_SERVER["DOCUMENT_ROOT"]);
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Почта</title>
</head>
<body>
    <?php
        if($currentrole != "Ученик"){
            require "../teacherhead.php";
        }
    ?>
    
</body>
</html>