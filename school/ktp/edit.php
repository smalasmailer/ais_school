<?php
require $_SERVER["DOCUMENT_ROOT"] . "/config.php";

// Проверка авторизации и роли пользователя
if (!in_array($currentrole, ["Учитель", "Завуч", "Директор"])) {
    http_response_code(403);
    die("Нет доступа");
}

// Проверяем входные данные
if (!isset($_GET['lesson'], $_GET['grade'])) {
    http_response_code(400);
    die("Неверные данные");
} else{
    $lesson = $_GET["lesson"];
    $grade = $_GET["grade"];

    $stmt = $conn->prepare("SELECT `lessonid`, `topic`, `homework` FROM `ktp` WHERE `ktplesson` = ? AND `ktpgrade` = ? AND `author` = ? AND `school` = ?");
    $stmt->bind_param("ssss", $lesson, $grade, $currentfullname, $currentschool);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>КТП <?= $lesson ?>, <?= $grade ?></title>
    <link rel="stylesheet" href="/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        input {
            width: 100%;
            display: block; /* Заставим input вести себя как блок, чтобы он занимал всю строку */
        }

        table {
            width: 80%;
            table-layout: fixed; /* Устанавливаем фиксированную разметку для таблицы */
        }

        table td {
            padding: 0; /* Убедитесь, что padding не мешает растягивать элементы */
        }

        table th {
            padding: 5px;
        }
    </style>
</head>
<body>
    <?php require "../teacherhead.php"; ?>
    <center>
        <h1>КТП для урока "<?= htmlspecialchars($lesson) ?>" класса "<?= htmlspecialchars($grade) ?>"</h1>
        <button class="addRowButton">Добавить строку</button>
        <table id="lessonTable">
            <thead>
                <tr>
                    <th style="width: 2%;">№</th>
                    <th style="width: 49%;">Тема урока</th>
                    <th style="width: 49%;">Д/З к уроку</th>
                    <th style="width: 10%;">Действие</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $rowNum = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-lessonid="<?= htmlspecialchars($row['lessonid']) ?>">
                            <td><?= $rowNum++ ?></td>
                            <td>
                                <input class="ktpInput" type="text" name="topic" value="<?= htmlspecialchars($row['topic']) ?>">
                            </td>
                            <td>
                                <input class="ktpInput" type="text" name="homework" value="<?= htmlspecialchars($row['homework']) ?>">
                            </td>
                            <td>
                                <button class="deleteRowButton">Удалить</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Нет данных для отображения.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button class="addRowButton">Добавить строку</button>
    </center>


    <script>
        $(document).ready(function() {
            // Добавление новой строки
            $(".addRowButton").click(function() {
                $.ajax({
                    url: "add_lesson.php",
                    type: "POST",
                    data: {
                        lesson: "<?= htmlspecialchars($lesson) ?>",
                        grade: "<?= htmlspecialchars($grade) ?>"
                    },
                    success: function(response) {
                        const lastRowNum = $("#lessonTable tbody tr").length + 1;

                        // Удаляем строку "Нет данных для отображения", если она существует
                        $("#lessonTable tbody tr td[colspan='4']").parent().remove();

                        // Добавляем новую строку
                        $("#lessonTable tbody").append(`
                            <tr>
                                <td>${lastRowNum}</td>
                                <td><input type="text" name="topic" value="Укажите тему"></td>
                                <td><input type="text" name="homework" value="Укажите ДЗ"></td>
                                <td style='width: 10%;'><button class="deleteRowButton">Удалить</button></td>
                            </tr>
                        `);
                    },
                    error: function() {
                        alert("Ошибка при добавлении строки.");
                    }
                });
            });

            $(document).on("click", ".deleteRowButton", function () {
                const row = $(this).closest("tr");
                const lessonId = row.data("lessonid");

                if (lessonId) {
                    // Отправка запроса на удаление записи
                    $.ajax({
                        url: "delete_lesson.php",
                        type: "POST",
                        data: { 
                            lessonId: lessonId,
                            lesson: "<?= htmlspecialchars($lesson) ?>",
                            grade: "<?= htmlspecialchars($grade) ?>",
                            author: "<?= htmlspecialchars($currentfullname) ?>"
                        },
                        success: function (response) {
                            row.remove();
                            checkEmptyTable();
                        },
                        error: function () {
                            alert("Ошибка при удалении записи.");
                        }
                    });
                } else {
                    // Удаляем только строку, без обращения к серверу (если ID отсутствует)
                    row.remove();
                    checkEmptyTable();
                }
            });

            function checkEmptyTable() {
                if ($("#lessonTable tbody tr").length === 0) {
                    $("#lessonTable tbody").append(`
                        <tr>
                            <td colspan="4">Нет данных для отображения.</td>
                        </tr>
                    `);
                }
            }

            // Автоматическое сохранение изменений
            $(document).on('input', 'input[name="topic"], input[name="homework"]', function() {
                const row = $(this).closest('tr');
                const topic = row.find('input[name="topic"]').val().trim();
                const homework = row.find('input[name="homework"]').val().trim();
                const lessonId = row.data('lessonid');

                $.ajax({
                    url: "update_lesson.php",
                    type: "POST",
                    data: {
                        lessonId: lessonId,
                        topic: topic,
                        homework: homework,
                        lesson: "<?= $lesson ?>",
                        grade: "<?= $grade ?>",
                        author: "<?= $currentfullname ?>"
                    },
                    success: function(response) {
                        console.log(response); // Выводим сообщение о статусе операции
                    },
                    error: function() {
                        alert("Ошибка при обновлении записи.");
                    }
                });
            });
        });
    </script>
</body>
</html>
