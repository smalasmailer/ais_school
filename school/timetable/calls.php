<?php
require "../../config.php";
require "../schoolhead.php";

if (isset($_GET["delete"])){
    $id = $_GET["delete"];
    if ($id){
        $conn->query("DROP TABLE `{$currentschool}_calls`;");
        header("Location: calls.php");
    }
}

echo "<link rel='stylesheet' href='calls.css'>";

if($currentrole != "Директор" && $currentrole != "Администратор"){
    header("Location: ../index.php");
    exit();
}

if(isset($_POST["maxlessons"])){
    $maxlessons = $_POST["maxlessons"];

    if ($maxlessons < 1){
        die("Количество уроков должно быть больше 1!");
    }

    echo "<form method='post' action='editcalls/create.php'>";
    echo "<input type='hidden' value='$maxlessons' name='maxlessons'>";

    for($i = 1; $i <= $maxlessons; $i++) {
        echo "$i урок: от: <input type='time' name='from_$i' required>; до: <input type='time' name='to_$i' required><br>";
    }
    echo "<input type='submit' value='Подтвердить'>";
    echo "</form>";
} else {
    $res = $conn->query("SHOW TABLES LIKE '{$currentschool}_calls';");
    
    if ($res && $res->num_rows == 0) {
        echo "<h2>Расписание звонков еще не было создано!</h2>";
        echo "<form method='post'>";
        echo "<input type='number' placeholder='Укажите макс. кол-во уроков' name='maxlessons' required><br>";
        echo "<input type='submit' value='Далее'>";
        echo "</form>";
    } else {
        echo "<h2>Расписание звонков создано:</h2>";
        $res = $conn->query("SELECT * FROM `{$currentschool}_calls`");

        if ($res) {
            echo "<center>";
            echo "<table border='1'>";
        
            echo "<tr>";
            echo "<th>№</th><th>Начало</th><th>Конец</th>";
            echo "</tr>";

            while ($row = $res->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $key => $cell) {
                    // Предполагаем, что в базе данных время хранится в формате "H:i:s"
                    if ($key === 'from' || $key === 'to') { // замените 'time_column_name' на реальное название вашего поля с временем
                        $cell = date("H:i", strtotime($cell));
                    }
                    echo "<td>{$cell}</td>";
                }
                echo "</tr>";
            }
        
            echo "</table>";
            echo "</center>";
            echo "<a href='?delete=1'>Удалить расписание звонков</a>";

        } else {
            echo "Ошибка выполнения запроса: " . $conn->error;
        }
    }
}
?>