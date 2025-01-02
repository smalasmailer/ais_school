<?php
require "../config.php";
require $_SERVER["DOCUMENT_ROOT"] . "/Parsedown.php";

$parse = new Parsedown();
$parse->setMarkupEscaped(true);

// Проверка прав доступа
if (!in_array($currentrole, ["Директор", "Завуч", "Учитель"])) {
    header("Location: ../index.html");
    exit();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$period = isset($_GET['period']) ? $_GET['period'] : null;

// Получение количества уроков
$res = $conn->query("SELECT COUNT(`id`) AS lesson_count FROM `{$currentschool}_calls`");
$row = $res->fetch_assoc();
$lessons = $row["lesson_count"] ?? 0;

// Получение уроков без оценок за прошлые даты
$no_marks_query = $conn->prepare("
    SELECT `dayid`, `date`, `lessonname`, `groupname`, `period`
    FROM `timetable`
    WHERE `teacher` = ? AND `school` = ? AND `date` < CURDATE()
    AND NOT EXISTS (
        SELECT 1 FROM `marks`
        WHERE `timetable`.`dayid` = `marks`.`dayid`
        AND `timetable`.`date` = `marks`.`date`
        AND `timetable`.`lessonname` = `marks`.`lessonname`
        AND `timetable`.`groupname` = `marks`.`groupname`
    )
");
$no_marks_query->bind_param('ss', $currentfullname, $currentschool);
$no_marks_query->execute();
$no_marks_result = $no_marks_query->get_result();

// Получение уроков без ДЗ за прошлые даты
$no_homework_query = $conn->prepare("
    SELECT `dayid`, `date`, `lessonname`, `groupname`, `period`
    FROM `timetable`
    WHERE `teacher` = ? AND `school` = ? AND `date` < CURDATE() AND `homework` = ''
");
$no_homework_query->bind_param('ss', $currentfullname, $currentschool);
$no_homework_query->execute();
$no_homework_result = $no_homework_query->get_result();

// Получение уроков без тем за прошлые даты
$no_topic_query = $conn->prepare("
    SELECT `dayid`, `date`, `lessonname`, `groupname`, `period`
    FROM `timetable`
    WHERE `teacher` = ? AND `school` = ? AND `date` < CURDATE() AND `lessontopic` = ''
");
$no_topic_query->bind_param('ss', $currentfullname, $currentschool);
$no_topic_query->execute();
$no_topic_result = $no_topic_query->get_result();

// Общий запрос для всех уроков до текущей даты
$total_lessons_query = $conn->prepare("
    SELECT COUNT(*) as total_count
    FROM `timetable`
    WHERE `teacher` = ? AND `school` = ? AND `date` < CURDATE()
");
$total_lessons_query->bind_param('ss', $currentfullname, $currentschool);
$total_lessons_query->execute();
$total_lessons_result = $total_lessons_query->get_result();
$total_lessons = $total_lessons_result->fetch_assoc()['total_count'];

// Общий запрос для всех уроков до текущей даты
$total_lessons_query = $conn->prepare("
    SELECT COUNT(*) as total_count
    FROM `timetable`
    WHERE `teacher` = ? AND `school` = ? AND `date` < CURDATE()
");
$total_lessons_query->bind_param('ss', $currentfullname, $currentschool);
$total_lessons_query->execute();
$total_lessons_result = $total_lessons_query->get_result();
$total_lessons = $total_lessons_result->fetch_assoc()['total_count'];

// Подсчет количества уроков без оценок, без тем и без домашнего задания
$no_marks_count1 = $no_marks_result->num_rows;
$no_homework_count1 = $no_homework_result->num_rows;
$no_topic_count1 = $no_topic_result->num_rows;

// Вычисление процента заполнения для оценок, тем и домашнего задания
$marks_filled_percentage = round(($total_lessons > 0) ? (1 - $no_marks_count1 / $total_lessons) * 100 : 0, 2);
$homework_filled_percentage = round(($total_lessons > 0) ? (1 - $no_homework_count1 / $total_lessons) * 100 : 0, 2);
$topic_filled_percentage = round(($total_lessons > 0) ? (1 - $no_topic_count1 / $total_lessons) * 100 : 0, 2);

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

    // Если текущий период не найден, используем ближайший
    if (!$currentPeriod && $closestPeriod) {
        $currentPeriod = $closestPeriod;
    }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабинет учителя</title>
    <link rel="stylesheet" href="teacherstyle.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
    <style>
        .info-cards {
            display: flex;
            align-items: center;
            height: auto;
        }
        .row {
            display: flex;
            justify-content: space-between;
            width: 100%;
            max-width: 800px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php require "teacherhead.php"; ?>

    <div class="padding: 10px; background-color: darkred; color: white;">
        <h2><a href="/school/itogi/">Подводим итоги года с АИС "Школой"</a></h2>
    </div>

    <section class="teacherdesk">
        <div class="container">

            <div class="main-content">
                <!-- Карточки с информацией о пропущенных уроках -->
                <section class="info-cards">
                    <div class="card <?php if($no_marks_result->num_rows>0){
                            echo "nomarks";
                        } else{
                            echo "marks";
                        }?>">
                        <h3>Уроки без оценок</h3>
                        <div class="modal" id="nomarksm">
                            <table>
                                <tr><th>Урок</th><th>Предмет</th><th>Класс</th><th>Открыть</th></tr>
                                <?php while ($row = $no_marks_result->fetch_assoc()): 
                                    $formdate = date("d.m.y", strtotime($row["date"]));
                                ?>
                                    <tr>
                                        <td><?= $formdate ?> (<?= $row["dayid"] ?>-й урок)</td>
                                        <td><?= $row["lessonname"] ?></td>
                                        <td><?= $row["groupname"] ?></td>
                                        <td><a href="journal/allmarks.php?lesson=<?= $row["lessonname"] ?>&group=<?= $row["groupname"] ?>&period=<?= $row["period"] ?>"><button>Открыть</button></a></td>
                                    </tr>                                    
                                <?php endwhile; ?>
                            </table>
                        </div>
                        <?php if ($no_marks_result->num_rows > 0): ?>
                            <button style="background-color: darkred"><a href="#nomarksm" rel="modal:open">Заполните журнал!</a></button>
                        <?php else: ?>
                            <p>Все оценки выставлены.</p>
                        <?php endif; ?>
                        <p>Процент заполняемости: <?php echo $marks_filled_percentage; ?>%</p>
                    </div>

                    <div class="card <?php if($no_homework_result->num_rows>0){
                            echo "nomarks";
                        } else{
                            echo "marks";
                        }?>">
                        <h3>Уроки без ДЗ</h3>
                        <?php if ($no_homework_result->num_rows > 0): ?>
                            <div class="modal" id="nohw">
                                <table>
                                    <tr><th>Урок</th><th>Предмет</th><th>Класс</th><th>Открыть</th></tr>
                                    <?php while ($row = $no_homework_result->fetch_assoc()): 
                                        $formdate = date("d.m.y", strtotime($row["date"]));
                                    ?>
                                        <tr>
                                            <td><?= $formdate ?> (<?= $row["dayid"] ?>-й урок)</td>
                                            <td><?= $row["lessonname"] ?></td>
                                            <td><?= $row["groupname"] ?></td>
                                            <td><a href="journal/allmarks.php?lesson=<?= $row["lessonname"] ?>&group=<?= $row["groupname"] ?>&period=<?= $row["period"] ?>"><button>Открыть</button></a></td>
                                        </tr>                                    
                                    <?php endwhile; ?>
                                </table>
                            </div>
                            <button style="background-color: darkred"><a href="#nohw" rel="modal:open">Заполните ДЗ!</a></button>
                        <?php else: ?>
                            <p>Все ДЗ указаны.</p>
                        <?php endif; ?>
                        <p>Процент заполняемости: <?php echo $homework_filled_percentage; ?>%</p>
                    </div>

                    <div class="card <?php if($no_topic_result->num_rows>0){
                            echo "nomarks";
                        } else{
                            echo "marks";
                        }?>">
                        <h3>Уроки без тем</h3>
                        <?php if ($no_topic_result->num_rows > 0): ?>
                            <div class="modal" id="notopics">
                                <table>
                                    <tr><th>Урок</th><th>Предмет</th><th>Класс</th><th>Открыть</th></tr>
                                    <?php while ($row = $no_topic_result->fetch_assoc()): 
                                        $formdate = date("d.m.y", strtotime($row["date"]));
                                    ?>
                                        <tr>
                                            <td><?= $formdate ?> (<?= $row["dayid"] ?>-й урок)</td>
                                            <td><?= $row["lessonname"] ?></td>
                                            <td><?= $row["groupname"] ?></td>
                                            <td><a href="journal/allmarks.php?lesson=<?= $row["lessonname"] ?>&group=<?= $row["groupname"] ?>&period=<?= $row["period"] ?>"><button>Открыть</button></a></td>
                                        </tr>                                    
                                    <?php endwhile; ?>
                                </table>
                            </div>
                            <button style="background-color: darkred"><a href="#notopics" rel="modal:open">Заполните темы!</a></button>
                        <?php else: ?>
                            <p>Все темы указаны.</p>
                        <?php endif; ?>
                        <p>Процент заполняемости: <?php echo $topic_filled_percentage; ?>%</p>
                    </div>
                </section>
                <section class="journalselect">
                    <h2><?php echo htmlspecialchars($currentPeriod["period_number"]);?>-й период</h2>
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
                <br>
                <h2>Новости</h2>
                <section class="posts">
                    <?php
                        if($schoolcommunity == "не привязано"){
                            $res = $conn->query("SELECT * FROM `posts` WHERE `school` = '$currentschool' ORDER BY `id` DESC");
                            if($res->num_rows>0){
                                while($row = $res->fetch_assoc()){
                                    echo "<center><div class='post'>";
                                    echo "<h3>$row[header]<br><span style='font-style: italic;'>$row[author]</span></h3>";
                                    echo "<p>$row[text]</p>";
                                    if($row["author"] == $currentfullname){
                                        echo "<a href='delpost.php?id=$row[header]'>Удалить публикацию</a>";
                                    }
                                    echo "</div><br></center>";
                                }
                            } else{
                                echo "Публикаций нет, но вы можете добавить!";
                            }
                        } else{
                            echo "<a href='/group.php?id=$schoolcommunity'>Школьное сообщество</a>";
                            $res = $conn->query("SELECT * FROM `communityposts` WHERE `community` = '$schoolcommunity' ORDER BY `id` DESC");
                            if($res->num_rows>0){
                                while($row = $res->fetch_assoc()){
                                    echo "<center><div class='post'>";
                                    $htmltext = $parse->text($row["text"]);
                                    echo "<p>$htmltext</p>";
                                    echo "<p><span style='color: gray;'>Написал: $row[author]</span></p>";
                                    echo "</div>";
                                }
                            } else{
                                echo "Публикаций нет.";
                            }
                        }
                    ?>
                </section>
            </div>

            <div class="sidebar">
                <h3>Меню</h3><br>
                <div class="schoolname">
                    <h3><a href="/profile/school.php?school=<?php echo urlencode($currentschool); ?>" style="text-decoration: none; color: black;"><?php echo $currentschool; ?></a></h3>
                </div><br>
                <ul>
                    <?php if(!empty($currentgroupname)): ?>
                    <li><a href="klruk/">Классное руководство</a></li>
                    <?php endif; ?>
                    <li><a href="newpost.php">Новая запись</a></li>
                    <li><a href="report/">Отчёты</a></li>
                    <?php if($currentrole == "Директор"): ?>
                        <li><a href="admin.php">Администрирование</a></li>
                    <?php elseif($currentrole == "Завуч"): ?>
                        <li><a href="zavuch.php">АРМ Завуч</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            $('.class-button').on('click', function() {
                var group = $(this).data('group');
            
                $.ajax({
                    url: 'get_lessons.php',
                    type: 'POST',
                    data: { group: group },
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