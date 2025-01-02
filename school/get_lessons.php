<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php"; // Подключение к базе данных

if (isset($_POST['group'])) {
    $group = $_POST['group'];
    if(isset($_POST["period"])){
        $period = $_POST["period"];
    } else{
        // Текущая дата
    $currentDate = new DateTime('now');
    
    // Определение смещения недели
    $week_offset = (int)($_GET['week_offset'] ?? 0);
    
    // Устанавливаем начало и конец недели с учетом смещения
    $startOfWeek = (clone $currentDate)->modify("Monday this week")->modify("$week_offset week");
    $endOfWeek = (clone $startOfWeek)->modify("+6 days");
    
    // Запрос периодов для определения текущего периода
    $periods_query = "SELECT * FROM `{$currentschool}_periods`";
    $periods_res = $conn->query($periods_query);
    $currentPeriod = null;
    $closestPeriod = null;
    $smallestDifference = null;

    while ($period = $periods_res->fetch_assoc()) {
        foreach ($period as $key => $value) {
            if (strpos($key, 'from') !== false && $value) {
                $fromDate = new DateTime($value);
                $toDateKey = str_replace('from', 'to', $key);
                $toDate = new DateTime($period[$toDateKey]);
                
                // Проверка, попадает ли текущая дата в период
                if ($currentDate >= $fromDate && $currentDate <= $toDate) {
                    $currentPeriod = [
                        'from' => $fromDate->format('d.m.Y'),
                        'to' => $toDate->format('d.m.Y'),
                        'period_number' => (int)filter_var($key, FILTER_SANITIZE_NUMBER_INT)
                    ];
                    break 2;
                }
                
                // Выбор ближайшего периода, если текущая дата не попадает ни в один из них
                $difference = abs($currentDate->getTimestamp() - $fromDate->getTimestamp());
                if ($smallestDifference === null || $difference < $smallestDifference) {
                    $smallestDifference = $difference;
                    $closestPeriod = [
                        'from' => $fromDate->format('d.m.Y'),
                        'to' => $toDate->format('d.m.Y'),
                        'period_number' => (int)filter_var($key, FILTER_SANITIZE_NUMBER_INT)
                    ];
                }
            }
        }
    }
    if (!$currentPeriod && $closestPeriod) {
        $currentPeriod = $closestPeriod;
    }
}
    // Если текущий период не найден, используем ближайший
    

    // Получаем список предметов для указанного класса
    $query = "SELECT `lesson` FROM `workload` WHERE `group` = '$group' AND `teacher` = '$currentfullname'";
    $result = $conn->query($query);

    echo "<h3>Предметы у $group</h3>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<p>$row[lesson]";
            if (isset($_POST["period"])) {
                echo "<a href='journal/allmarks.php?group={$group}&lesson={$row["lesson"]}&period={$period}'><button style='padding: 1px;margin-left: 3px;'>Журнал</button></a>";
                echo "<a href='journal/topics.php?group={$group}&lesson={$row["lesson"]}&period={$period}'><button style='padding: 1px;margin-left: 3px;background-color: darkblue;'>КТП</button></a>";
            } else {
                echo "<a href='journal/allmarks.php?group={$group}&lesson={$row["lesson"]}&period={$currentPeriod["period_number"]}'><button style='padding: 1px;margin-left: 3px;'>Журнал</button></a>";
                echo "<a href='journal/topics.php?group={$group}&lesson={$row["lesson"]}&period={$currentPeriod["period_number"]}'><button style='padding: 1px;margin-left: 3px;background-color: darkblue;'>КТП</button></a>";
            }
            echo "</p>";
        }
    } else {
        echo "Нет предметов для этого класса.";
    }
}
?>