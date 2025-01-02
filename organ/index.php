<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ОУ</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .settingschool{
            margin: 0;
            padding: 0;
            display: flex;
            justify-content:center;
        }
        a{
            text-decoration: none;
            color:black;
        }
        .element:hover{
            background-color:lightgray;
        }
        .element{
            max-width:120px;
            margin-right:15px;
        }
    </style>
</head>
<body>
    <?php
        require "organheader.php";
    ?>
    <h2>Орган управления</h2>
    <center>
        <a href="tools/orgs.php"><button>Школы</button></a>
        <a href="tests/"><button>Педагогическая аттестация</button></a>
    </center>
</body>
</html>