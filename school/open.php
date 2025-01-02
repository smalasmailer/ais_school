<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if($currentrole != "Учитель" && $currentrole != "Директор" && $currentrole != "Завуч"){
        header("Location: /");
        exit();
    }
    if(isset($_GET["period"])){
        $period = $_GET["period"];
    } else{
        header("Location: teacher.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Открытие журнала</title>
    <link rel="stylesheet" href="teacherstyle.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php
        require "teacherhead.php";
    ?><br>
    <a href="teacher.php"><button>В учительскую</button></a>
    <section class="journalselect">
        <h2><?php echo $period;?>-й период</h2>
        <a href="open.php?period=1">1-й период</a>
        <a href="open.php?period=2">2-й период</a>
        <a href="open.php?period=3">3-й период</a>
        <a href="open.php?period=4">4-й период</a>
        <br>
            <div class="class-list">
            <h3>Классы</h3>

            <?php
            $query = "SELECT DISTINCT `group` FROM `workload` WHERE `teacher` = '$currentfullname'";
            $result = $conn->query($query);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<button class="class-button" data-group="' . $row['group'] . '">' . $row['group'] . '</button>';
                }
            } else {
                echo "<p>У вас нет классов.</p>";
            }
            ?>

        </div>
        <div class="openjournal">
            <h3>Предметы</h3>
            <p>Выберите класс в списке слева</p>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            $('.class-button').on('click', function() {
                var group = $(this).data('group');
            
                $.ajax({
                    url: 'get_lessons.php',
                    type: 'POST',
                    data: { group: group, period: <?php echo $period; ?> },
                    success: function(response) {
                        $('.openjournal').html(response);
                    },
                    error: function() {
                        alert('Ошибка загрузки предметов.');
                    }
                });
            });
        });
    </script>
</body>
</html>