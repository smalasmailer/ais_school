<?php
require "../../config.php";

if (isset($_GET["group"]) && isset($_GET["lesson"]) && isset($_GET["period"])) {
    $group = $_GET["group"];
    $lesson = $_GET["lesson"];
    $period = $_GET["period"];
} else {
    header("Location: ../index.php");
    exit();
}

// Получаем даты всех уроков из таблицы timetable
$stmt = $conn->prepare("
    SELECT dayid, date, type
    FROM timetable
    WHERE lessonname = ? AND groupname = ? AND school = ? AND period = ?
    ORDER BY date, dayid ASC
");
$stmt->bind_param("sssi", $lesson, $group, $currentschool, $period);
$stmt->execute();
$marksRes = $stmt->get_result();
$dates = $marksRes->fetch_all(MYSQLI_ASSOC);

// Получаем список студентов
$stmt = $conn->prepare("SELECT fullname FROM students WHERE groupname = ? AND school = ? ORDER BY fullname ASC");
$stmt->bind_param("ss", $group, $currentschool);
$stmt->execute();
$studentsRes = $stmt->get_result();
$students = $studentsRes->fetch_all(MYSQLI_ASSOC);

// Получаем оценки для всех студентов
$stmt = $conn->prepare("SELECT studentname, dayid, date, mark, typemark FROM marks WHERE lessonname = ? AND school = ? AND groupname = ? ORDER BY date ASC, dayid, studentname ASC");
$stmt->bind_param("sss", $lesson, $currentschool, $group);
$stmt->execute();
$marksRes = $stmt->get_result();
$marks = [];
while ($row = $marksRes->fetch_assoc()) {
    $marks[$row['studentname']][$row['dayid']][$row['date']][$row["typemark"]] = $row['mark'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все оценки по <?php echo htmlspecialchars($lesson); ?> - <?php echo htmlspecialchars($group); ?></title>
    <link rel="stylesheet" href="/style.css">
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    padding: 20px;
    background-color: #f9f9f9;
    overflow-x: hidden;
    margin: 0;
}

.markstable {
    max-width: 100%;
    overflow-x: auto;
    border: 1px solid #ddd;
    font-size: 8px !important;
    display: block;
    margin: 0;
    height: auto;
    overflow-y: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    white-space: nowrap;
    height: min-content;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
    font-weight: bolder;
}

td {
    padding: 0;
}

th {
    background-color: #f2f2f2;
    position: sticky;
    top: 0;
    z-index: 1;
    font-size: 14px;
}
input {
    text-align: center;
    border: none;
    background-color: transparent;
    font-size: auto;
    color: black;
    margin: 0;
    padding: 0;
    line-height: 40px;
}

.mark-input, .totalmark-input {
    width: 40px;
    font-size: 12px;
    margin: 2px;
    height: 25px;
}
.totalmark-input{
    width: 60px;
}

.mark-input:hover, .totalmark-input:hover {
    cursor: pointer; /* Обеспечит курсор для ввода */
}

button {
    padding: 10px 20px;
    background-color: darkblue;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background-color: #0048bb;
    -webkit-box-shadow: 8px 8px 24px -11px rgba(66, 68, 90, 1);
    -moz-box-shadow: 8px 8px 24px -11px rgba(66, 68, 90, 1);
    box-shadow: 8px 8px 24px -11px rgba(66, 68, 90, 1);
}

button:active {
    background-color: #0048bb;
    box-shadow: 4px 4px 10px rgba(66, 68, 90, 0.5);
}

button:focus {
    outline: 2px solid #8cc646;
}

.journalsect {
    display: flex;
    position: relative;
    height: calc(100vh - 60px);
    overflow: hidden;
}

.ktp {
    width: 40%;
    overflow-y: scroll;
    height: auto;
}

.ktp-input {
    width: 100%;
    padding: 5px;
    font-size: 12px;
    border-radius: 5px;
}

.ktp table {
    width: 100%;
    padding: 15px;
    height: auto;
}

.journalmenu {
    display: flex;
    padding: 5px;
}

.tooltip {
    position: absolute;
    display: none;
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 5px;
    border-radius: 4px;
    z-index: 1000;
}

.sticky-cell {
    position: sticky;
    left: 0;
    z-index: 10;
    background-color: #f4f4f4;
    min-height: 40px;
}

input, textarea {
    padding: 12px;
}

.scroll-container {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 20px;
    background-color: transparent;
    z-index: 100;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
}

.scroll-bar {
    width: 100%;
    height: 100%;
    background-color: #888;
}

.scroll-container::-webkit-scrollbar {
    display: block;
}

.scroll-container::-webkit-scrollbar-thumb {
    background: #555;
}

.scroll-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

tr:hover {
    background-color: rgba(173, 216, 230, 0.5);
}
/* Цветной режим */
.color-mode .mark-input[value='5'] { background-color: darkgreen; }
.color-mode .mark-input[value='4'] { background-color: green; }
.color-mode .mark-input[value='3'] { background-color: orange; }
.color-mode .mark-input[value='2'] { background-color: red; }
.color-mode .mark-input[value='1'] { background-color: darkred; }

/* Черно-белый режим */
.black-and-white-mode .mark-input { background-color: white; }

</style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        function applyColor(inputField) {
            var value = inputField.val();
            var bgColor, fgColor, style;

            // Устанавливаем цвета для одиночных оценок
            if (value === "5") {
                bgColor = 'darkgreen';
                fgColor = 'white';
            } else if (value === "4") {
                bgColor = 'green';
                fgColor = 'white';
            } else if (value === "3") {
                bgColor = 'orange';
                fgColor = 'white';
            } else if (value === "2") {
                bgColor = 'red';
                fgColor = 'white';
            } else if (value === "1") {
                bgColor = 'darkred';
                fgColor = 'white';
            } else if (value === "б" || value === "н" || value === "п" || value === ".") {
                bgColor = 'gray';
                fgColor = 'white';
                style = 'bold';
            } else if (value.includes('/')) { // Обработка дробных оценок
                var parts = value.split('/');
                var firstPart = parseFloat(parts[0]);
                var secondPart = parseFloat(parts[1]);
                
                // Установка цвета фона в зависимости от значений дроби
                if (firstPart === 5) {
                    bgColor = 'darkgreen'; // Зеленый для 5
                } else if (firstPart === 4) {
                    bgColor = 'green'; // Зеленый для 4
                } else if (firstPart === 3) {
                    bgColor = 'orange'; // Оранжевый для 3
                } else if (firstPart === 2) {
                    bgColor = 'red'; // Красный для 2
                } else if (firstPart === 1) {
                    bgColor = 'darkred'; // Темно-красный для 1
                } else {
                    bgColor = 'white'; // Белый фон по умолчанию
                }

                // Устанавливаем цвет текста для дробной части
                fgColor = 'white'; // Черный текст для дробных оценок
            } else {
                bgColor = '';
                fgColor = '';
            }

            inputField.css({
                'background-color': bgColor,
                'color': fgColor,
                'padding': 0
            });
            if (style) {
                inputField.css({
                    'font-weight': style,
                });
            }
        }

        $('.mark-input').each(function() {
            applyColor($(this));
        });

        function updateAverage() {
            $('.student-row').each(function() {
                var total = 0;
                var count = 0;

                $(this).find('.mark-input').each(function() {
                    var value = $(this).val();

                    // Пропускаем пустые значения
                    if (value) {
                        // Обработка дробных оценок
                        var parts = value.split('/');
                        if (parts.length === 2) {
                            // Преобразуем дробную оценку в две отдельные оценки
                            total += parseFloat(parts[0]);
                            total += parseFloat(parts[1]);
                            count += 2; // Увеличиваем счетчик на 2
                        } else if ($.isNumeric(value)) {
                            total += parseFloat(value);
                            count++;
                        }
                    }
                });

                var average = count > 0 ? (total / count).toFixed(2) : '0.00';
                $(this).find('.average-cell').text(average);
            });
        }

        $('.mark-input').on('blur', function() {
            var inputField = $(this);
            var newValue = inputField.val();
            var studentName = inputField.data('student');
            var dayid = inputField.data('dayid');
            var date = inputField.data('date');
            var type = inputField.data('type');

            var validMarkPattern = /^([1-5]\/[1-5]|[1-5]|[нпб.]\/[1-5]|[нпб.])$/; // Допускаем дробь вида X/X или одиночные оценки

            // Позволяем пустое значение
            if (newValue !== "" && !validMarkPattern.test(newValue)) {
                alert("Некорректная оценка! Используйте формат X/X или одиночную оценку.");
                return;
            }

            console.log('Sending data:', {
                group: '<?php echo $group; ?>',
                lesson: '<?php echo $lesson; ?>',
                dayid: dayid,
                student: studentName,
                mark: newValue,
                type: type,
                period: '<?php echo $period; ?>',
                date: date
            });

            $.ajax({
                url: 'save_allmark_data.php',
                type: 'POST',
                data: {
                    group: '<?php echo $group; ?>',
                    lesson: '<?php echo $lesson; ?>',
                    dayid: dayid,
                    student: studentName,
                    mark: newValue,
                    period: '<?php echo $period; ?>',
                    lessonType: type,
                    date: date
                },
                success: function(response) {
                    console.log(response);
                    inputField.prop('readonly', true);
                },
                error: function() {
                    alert('Ошибка при сохранении оценки.');
                }
            });
        });

        $('.mark-input').on('input', function() {
            applyColor($(this));
            updateAverage();
        });

        $('.mark-input').on('focus', function() {
            $(this).prop('readonly', false);
        });

        // Сохранение позиции прокрутки
        const saveScrollPosition = () => {
            const scrollPosition = $('.markstable').scrollTop(); // Замените на правильный селектор для вашей таблицы
            localStorage.setItem('journalScrollPosition', scrollPosition);
        };

        // Восстановление позиции прокрутки
        const restoreScrollPosition = () => {
            const savedPosition = localStorage.getItem('journalScrollPosition');
            if (savedPosition) {
                $('.markstable').scrollTop(savedPosition); // Замените на правильный селектор для вашей таблицы
            }
        };

        $(document).ready(function () {
            $('.ktp-input').on('change', function () {
                // Извлечение значений из текущей строки
                var lessonId = $(this).closest('tr').find('input[name="lessonId[]"]').val(); // Обновлено
                var date = $(this).closest('tr').find('input[name="date[]"]').val(); // Обновлено
                var group = $('textarea[name="group"]').val();
                var lesson = $('textarea[name="lesson"]').val();
                var period = $('textarea[name="period"]').val();
                var theme = $(this).closest('tr').find('textarea[name="theme[]"]').val();
                var homework = $(this).closest('tr').find('textarea[name="homework[]"]').val();

                // Вывод данных в консоль для отладки
                console.log('Sending data:', {
                    lessonId: lessonId,
                    date: date,
                    group: group,
                    lesson: lesson,
                    period: period,
                    theme: theme,
                    homework: homework
                });

                // Отправка AJAX-запроса
                $.ajax({
                    url: 'save_ktp.php',
                    type: 'POST',
                    data: {
                        lessonId: lessonId,
                        date: date,
                        group: group,
                        lesson: lesson,
                        period: period,
                        theme: theme,
                        homework: homework
                    },
                    success: function (response) {
                        console.log('Changes saved successfully', response);
                    },
                    error: function (xhr, status, error) {
                        console.error('Failed to save changes:', error);
                    }
                });
            });

            $('#toggleKTP').on('click', function() {
                const ktpBlock = $('.ktp');
                const journal = $(document.getElementsByClassName("markstable")); // Оборачиваем в jQuery
                const isVisible = ktpBlock.is(':visible');

                if (isVisible) {
                    saveScrollPosition(); // Сохраняем позицию перед скрытием
                    ktpBlock.hide(); // Скрывает блок .ktp
                    journal.css('width', '100%'); // Устанавливает ширину на 100%
                    $(this).text('Показать KTП'); // Изменяет текст кнопки
                } else {
                    ktpBlock.show(); // Показывает блок .ktp
                    journal.css('width', '60%'); // Устанавливает ширину на 60%
                    $(this).text('Скрыть KTП'); // Изменяет текст кнопки
                    restoreScrollPosition(); // Восстанавливаем позицию при открытии
                }

                
            });
        });
updateAverage();
    });
</script>
</head>
<body>
    <h1>Журнал: <?php echo htmlspecialchars($lesson); ?> (<?php echo htmlspecialchars($group); ?>)</h1>
    <div class="journalmenu">
        <a href="topics.php?lesson=<?php echo $lesson; ?>&period=<?php echo $period; ?>&group=<?php echo $group; ?>"><button>Календарно-тематическое планирование</button></a><br><br>
        <button id="toggleKTP" style="margin-left: auto;margin-right:5px;">Скрыть КТП</button>
        <a href="../teacher.php"><button>В учительскую</button></a>
        <button id="toggle-color-mode" style="margin-left: 5px;">Ч/Б режим</button>
    </div>
    <center><div class="journalmenu">
        <div class="grade-buttons" style="margin-right: auto;">
            <!-- Кнопки для выставления оценок -->
            <button class="grade-button" data-grade="5">5</button>
            <button class="grade-button" data-grade="4">4</button>
            <button class="grade-button" data-grade="3">3</button>
            <button class="grade-button" data-grade="2">2</button>
            <button class="grade-button" data-grade="1">1</button>
            <button class="grade-button" data-grade="н">н</button>
            <button class="grade-button" data-grade="п">п</button>
            <button class="grade-button" data-grade="б">б</button>
            <button class="grade-button" data-grade=".">.</button>
        </div>
        
    </div>
    <section class="journalsect">
        
        <div class="markstable" id="journal-table">
            <table>
                <thead>
                    <!-- Строка с месяцем -->
                    <tr>
                        <th rowspan=3 class="sticky-cell">Список учеников</th>
                        <?php
                        $currentMonth = '';
                        $monthDates = []; // Массив для хранения количества уроков в каждом месяце

                        // Массив для перевода месяцев на русский
                        $monthsRU = [
                            'January' => 'Январь', 
                            'February' => 'Февраль', 
                            'March' => 'Март', 
                            'April' => 'Апрель', 
                            'May' => 'Май', 
                            'June' => 'Июнь', 
                            'July' => 'Июль', 
                            'August' => 'Август', 
                            'September' => 'Сентябрь',
                            'October' => 'Октябрь', 
                            'November' => 'Ноябрь', 
                            'December' => 'Декабрь'
                        ];
                    
                        // Считаем количество уроков на каждый месяц
                        foreach ($dates as $row) {
                            $month = date('F', strtotime($row['date'])); // Получаем месяц на английском
                            if (!isset($monthDates[$month])) {
                                $monthDates[$month] = 1;
                            } else {
                                $monthDates[$month]++;
                            }
                        }
                    
                        // Выводим ячейки с названиями месяцев
                        foreach ($monthDates as $month => $lessonsInMonth) {
                            echo "<th colspan='$lessonsInMonth'>" . $monthsRU[$month] . "</th>"; // Используем массив для перевода
                        }
                        ?>
                        <th rowspan=3>Ср. балл</th>
                        <th rowspan=3>Итог<br><?php echo $period; ?> пер.</th>
                    </tr>

                    <tr>
    <!-- Строка с числами -->
    <?php
        $previousDate = null;
        $typesArray = []; // массив для хранения типов для каждой уникальной даты и dayid
        foreach ($dates as $dateRow):
            $formattedDate = date('d', strtotime($dateRow['date']));
            $dayid = $dateRow['dayid'];
            $type = $dateRow['type'];
            $originalDate = $dateRow['date'];

            $res1 = $conn->query("SELECT `type` FROM `timetable` WHERE `dayid` = $dayid AND `date` = '$originalDate' AND `groupname` = '$group' AND `lessonname` = '$lesson' AND `teacher` = '$currentfullname' AND `period` = $period AND `school` = '$currentschool'");
            $types = $res1->num_rows;
            
            echo "<th><a href='journal.php?group=$group&lesson=$lesson&date=$originalDate&idless=$dayid&period=$period'>$formattedDate</a></th>";
            endforeach;
            ?>
            </tr>
            <tr>
                <!-- Строка с типами -->
                <?php
                    foreach ($dates as $dateRow):
                        $type = $dateRow['type'];

                        echo "<th>$type</th>";
                    endforeach;
                ?>
            </tr>
                </thead>
                <tbody>
    <?php
        $totalMarks = [];
        $query = "SELECT mark, student, lesson, groupname, period 
                  FROM totalmarks 
                  WHERE lesson = '$lesson' 
                  AND groupname = '$group' 
                  AND period = '$period' 
                  AND school = '$currentschool'";
        $result = mysqli_query($conn, $query);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $totalMarks[$row['student']] = $row['mark'];
        }
    ?>
    <?php foreach ($students as $row): ?>
        <?php $student = $row['fullname']; ?>
        <tr class="student-row">
            <?php
                $showStudent = explode(" ", $student);
            ?>
            <td class="sticky-cell"><?php echo htmlspecialchars($showStudent[0]) . " " . htmlspecialchars($showStudent[1]); ?></td> <!-- Добавлен класс sticky-cell -->
            <?php
            $marksArray = []; // Для хранения оценок текущего студента
            foreach ($dates as $dateRow) {
                $dayid = $dateRow['dayid'];
                $originalDate = $dateRow['date'];
                $type = $dateRow["type"]; // Получаем тип для конкретной даты
                $formattedDate = date('d', strtotime($originalDate));
            
                // Если оценка существует для данного типа, выводим её
                $mark = isset($marks[$student][$dayid][$originalDate][$type]) 
                    ? $marks[$student][$dayid][$originalDate][$type] 
                    : '';
            
                echo "<td><input type='text' class='mark-input' value='" . htmlspecialchars($mark) . "' 
                    data-student='" . htmlspecialchars($student) . "' 
                    data-dayid='" . htmlspecialchars($dayid) . "' 
                    data-date='" . htmlspecialchars($originalDate) . "'
                    data-type='" . htmlspecialchars($type) ."' 
                    title='" . htmlspecialchars($formattedDate) . "'></td>";
            }
            
                
                // (Ваш существующий код для подсчета среднего)
            ?>
            <td class="average-cell"><?php echo is_numeric($averageMark) ? number_format($averageMark, 2) : $averageMark; ?></td>
            <td class="totalmark">
            <input type="text" class="totalmark-input" 
                   value="<?php echo isset($totalMarks[$student]) ? htmlspecialchars($totalMarks[$student]) : ''; ?>" 
                   data-student="<?php echo htmlspecialchars($student); ?>"
                   data-group="<?php echo htmlspecialchars($group); ?>"
                   data-lesson="<?php echo htmlspecialchars($lesson); ?>"
                   data-period="<?php echo htmlspecialchars($period); ?>">
        </td>
        </tr>
    <?php endforeach; 
    ?>
</tbody>

            </table>
        </div>
        <div class="ktp">
            <form id="ktp-form" action="save_ktp.php" method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Тема</th>
                            <th>Д/З к уроку</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dates as $dateRow): ?>
                            <?php
                            $res = $conn->query("SELECT `lessontopic`, `homework` FROM `timetable` WHERE `groupname` = '$group' AND `lessonname` = '$lesson' AND `date` = '{$dateRow['date']}' AND `dayid` = {$dateRow['dayid']} AND `period` = '$period' ORDER BY `date`, `dayid` ASC");
                            $ktpRow = $res->fetch_assoc();
                            $theme = $ktpRow ? $ktpRow['lessontopic'] : '';
                            $homework = $ktpRow ? $ktpRow['homework'] : '';
                            $types = $res->num_rows;
                            ?>
                            <tr>
                                <input type="hidden" name="lessonId[]" value="<?php echo htmlspecialchars($dateRow['dayid']); ?>">
                                <input type="hidden" name="date[]" value="<?php echo htmlspecialchars($dateRow['date']); ?>">
                                <td><?php echo date('d.m.Y', strtotime($dateRow['date'])); ?><br><?php echo $dateRow["dayid"]; ?>-й урок</td>
                                <td>
                                    <textarea name="theme[]" class="ktp-input"><?php echo htmlspecialchars($theme); ?></textarea>
                                </td>
                                <td>
                                    <textarea name="homework[]" class="ktp-input"><?php echo htmlspecialchars($homework); ?></textarea>
                                </td>
                            </tr>
                        
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <input type="hidden" name="group" value="<?php echo htmlspecialchars($group); ?>">
                <input type="hidden" name="lesson" value="<?php echo htmlspecialchars($lesson); ?>">
                <input type="hidden" name="period" value="<?php echo htmlspecialchars($period); ?>">
            </form>
        </div></center>
    </section>
    <script>
        $('.totalmark-input').on('blur', function() {
            var inputField = $(this);
            var mark = inputField.val();
            var student = inputField.data('student');
            var group = inputField.data('group');
            var lesson = inputField.data('lesson');
            var period = inputField.data('period');

            var validMarkPattern = /^(|[1-5]|н\/а|осв)$/;

            // Позволяем пустое значение
            if (mark !== "" && !validMarkPattern.test(mark)) {
                alert("Некорректная оценка! Используйте формат X/X или одиночную оценку.");
                return;
            }

            $.ajax({
                url: 'save_totalmark_data.php',
                type: 'POST',
                data: {
                    mark: mark,
                    student: student,
                    group: group,
                    lesson: lesson,
                    period: period
                },
                success: function(response) {
                    console.log(response);
                    inputField.prop('readonly', true);
                },
                error: function() {
                    alert('Ошибка при сохранении оценки.');
                }
            });
        });
    /*let lastActiveInput = null; // Переменная для хранения последнего активного инпута

    // Функция для сохранения оценки в базе данных
    function saveMark(student, dayid, date, mark, lesson, group, period) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "save_mark.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                console.log(xhr.responseText);
            } else {
                console.error('Ошибка сохранения:', xhr.statusText);
            }
        }
    };
    xhr.send(`student=${encodeURIComponent(student)}&dayid=${encodeURIComponent(dayid)}&date=${encodeURIComponent(date)}&mark=${encodeURIComponent(mark)}&lesson=${encodeURIComponent(lesson)}&group=${encodeURIComponent(group)}&period=${encodeURIComponent(period)}`);
}


    document.querySelectorAll('.grade-button').forEach(button => {
    button.addEventListener('click', function() {
        const grade = this.getAttribute('data-grade');

        if (lastActiveInput) {
            lastActiveInput.value = grade; // Вставляем оценку в последнюю активную ячейку
            const student = lastActiveInput.getAttribute('data-student');
            const dayid = lastActiveInput.getAttribute('data-dayid');
            const date = lastActiveInput.getAttribute('data-date');
            const lesson = "<?php echo $lesson; ?>"; // Замените на ваше значение
            const group = "<?php echo $group; ?>"; // Замените на ваше значение
            const period = <?php echo htmlspecialchars($period); ?>; // Замените на ваше значение
            saveMark(student, dayid, date, grade, lesson, group, period); // Сохраняем оценку
        }
    });
});*/

window.addEventListener("load", function() {
    document.getElementById("toggle-color-mode").addEventListener("click", function() {
        const journalTable = document.getElementById("journal-table");
        const isColorMode = journalTable.classList.contains("color-mode");
        const button = document.getElementById("toggle-color-mode");
        const isBWMode = button.innerText === "Ч/Б режим";
        
        // Переключаем режим
        if (isBWMode) {
            // Логика для включения цветного режима
            button.innerText = "Цветной режим";
        } else {
            // Логика для включения Ч/Б режима
            button.innerText = "Ч/Б режим";
        }

        // Переключаем режим
        journalTable.classList.toggle("color-mode");
        journalTable.classList.toggle("black-and-white-mode");

        // Получаем все ячейки с оценками
        const markCells = journalTable.querySelectorAll(".mark-input");

        markCells.forEach(cell => {
            const value = cell.value;

            // Устанавливаем цвет фона в зависимости от режима
            if (isColorMode) {
                cell.style.backgroundColor = "#f9f9f9"; // Черно-белый режим
                cell.style.color = "black";
            } else {
                switch (value) {
                    case '5':
                        cell.style.backgroundColor = "darkgreen";
                        cell.style.color = "white";
                        break;
                    case '4':
                        cell.style.backgroundColor = "green";
                        cell.style.color = "white";
                        break;
                    case '3':
                        cell.style.backgroundColor = "orange";
                        cell.style.color = "white";
                        break;
                    case '2':
                        cell.style.backgroundColor = "red";
                        cell.style.color = "white";
                        break;
                    case '1':
                        cell.style.backgroundColor = "darkred";
                        cell.style.color = "white";
                        break;
                    case 'н':
                        cell.style.backgroundColor = "gray";
                        cell.style.color = "white";
                        cell.style.fontweight = "bold";
                        break;
                    case 'б':
                        cell.style.backgroundColor = "gray";
                        cell.style.color = "white";
                        cell.style.fontweight = "bold";
                        break;
                    case '.':
                        cell.style.backgroundColor = "gray";
                        cell.style.color = "white";
                        cell.style.fontweight = "bold";
                        break;
                    case 'п':
                        cell.style.backgroundColor = "gray";
                        cell.style.color = "white";
                        cell.style.fontweight = "bold";
                        break;
                    default:
                        cell.style.backgroundColor = "#f9f9f9"; // Для пустых и других значений
                }
            }
        });
    });
});
    // Обработка клика по инпутам оценок
    document.querySelectorAll('.mark-input').forEach(input => {
        input.addEventListener('click', function() {
            lastActiveInput = this; // Сохраняем ссылку на активный инпут
        });

        // Сохранение при вводе с клавиатуры с задержкой
        input.addEventListener('input', function() {
            const mark = this.value;
            const student = this.getAttribute('data-student');
            const dayid = this.getAttribute('data-dayid');
            const date = this.getAttribute('data-date');
            saveMark(student, dayid, date, mark); // Сохраняем при вводе
        });

        // Сохранение при потере фокуса
        input.addEventListener('blur', function() {
            const mark = this.value;
            const student = this.getAttribute('data-student');
            const dayid = this.getAttribute('data-dayid');
            const date = this.getAttribute('data-date');
            saveMark(student, dayid, date, mark); // Сохраняем при потере фокуса
        });
    });
</script>
<script>
        document.querySelectorAll('.mark-input').forEach(input => {
            input.addEventListener('input', () => {
                // Обновление среднего балла при изменении оценок
                const row = input.closest('tr');
                const averageCell = row.querySelector('.average-cell');
                const marks = [...row.querySelectorAll('.mark-input')].map(input => input.value);
                const marksArray = marks.map(mark => {
                    const parts = mark.split('/');
                    return parts.length === 2 ? parseFloat(parts[0]) / parseFloat(parts[1]) : parseFloat(mark);
                }).filter(Boolean); // Убираем пустые значения
            
                const averageMark = marksArray.length ? (marksArray.reduce((a, b) => a + b) / marksArray.length).toFixed(2) : '0.00';
                averageCell.textContent = averageMark;
            });
        });
    </script>
</div>
</body>
</html>