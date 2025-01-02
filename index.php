<?php
    require $_SERVER["DOCUMENT_ROOT"] . "/config.php";
    if(isset($_COOKIE["acclogin"])){
        header("Location: /login/loginsuccess.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>АИС "Школа"</title>
    <meta name="description" content="АИС «Школа» - это совокупность инструментов для образовательных организаций и органов управления. Школы могут следить за успеваемостью учащиехся, а органы управления контроллировать сами образовательные учреждения. Ученики же могут следить за своей успеваемостью">
    <link rel="stylesheet" href="main.css">
    <link rel="shortcut icon" href="/img/logo.png" type="image/png">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://yastatic.net/s3/passport-sdk/autofill/v1/sdk-suggest-with-polyfills-latest.js"></script>
    <script>
        YaAuthSuggest.init(
              {
                 client_id: 'abfbff2112fa4d03acd14f505258cd5f',
                 response_type: 'token',
                 redirect_uri: 'https://ais-school.ru/login/yandex_callback.php'
              },
              'ais-school.ru'
           )
           .then(({
              handler
           }) => handler())
           .then(data => console.log('Сообщение с токеном', data))
           .catch(error => console.log('Обработка ошибки', error));
    </script>
    <meta name="mailru-domain" content="FfKvIxzbhYwNJjy2" />
    <link rel="shortcut icon" href="img/logo.png" type="image/png">
    <style>
        .important{
            padding: 30px;
            background-color: darkred;
            text-align: center;
        }
        .important h2{
            color: white;
        }
        .important p{
            color: white;
        }
        .important a{
            color: lightblue;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-buttons">
            <a href="schoolai.html"><button class="header-button">Школа AI</button></a>
            <a href="rules.html"><button class="header-button">Пользовательское соглашение</button></a>
        </div>
    </div><br>
    <div class="important">
        <h2>Система приостановила свою работу</h2>
        <p>Подробности можно узнать <a href="close.html">здесь</a></p>
    </div>
    <h1>АИС "Школа"</h1>
    <center><p>- это российская система для ОО и ОУ. Школы смогут следить за успеваемостью учащиехся организации. Учащиеся смогут получать информацию об оценках, расписании и новостях школ в режиме 24/7. ОУ же контроллируй ОО (доступными для использования, видят статистику по школе и пр.)</p></center>
    <br>
    <!--<div class="content">
        <div class="info">
            <h2>ОБНОВЛЕНИЕ 2.1</h2>
            <p>- Добавление модуля КТП</p>
            <p>- Добавление модуля для проведения пед. аттестаций</p>
            <p>- Добавление модуля тестов</p>
            <p>- Интеграция ИИ с системой (Школа AI)</p>
        </div>
    </div>
    <br>-->
    <h1>Школам</h1>
    <div class="content">
        <div class="info">
            <h2>Журнал для учителя</h2>
            <p>Журнал - главный инструмент учителя для ведения учёта, оценки и анализа учебного процесса.</p>
        </div>
        <table class="subject-table">
            <tr>
                <th>УЧЕНИК</th>
                <th>09.09</th>
                <th>10.09</th>
                <th>СР. БАЛЛ</th>
                <th>1 ЧЕТВ.</th>
            </tr>
            <tr>
                <td>Иванов Иван Иванович</td>
                <td style="background-color: lightgreen;">5</td>
                <td style="background-color: lightgreen;">4</td>
                <td>4.5</td>
                <td style="background-color: lightgreen;">5</td>
            </tr>
            <tr>
                <td>Щукин Алексей Сергеевич</td>
                <td style="background-color: orange;">3</td>
                <td style="background-color: lightgreen;">4</td>
                <td>3.5</td>
                <td style="background-color: lightgreen;">4</td>
            </tr>
        </table>
    </div>
    <div class="content">
        <table class="subject-table">
            <tr>
                <th>№</th>
                <th>Учитель</th>
                <th>Урок</th>
                <th>ДЗ</th>
                <th>Тема</th>
                <th>Оценка</th>
            </tr>
            <tr>
                <td>1</td>
                <td>Иванов И. И.</td>
                <td>ОДНКНР</td>
                <td>§3 читать; №1</td>
                <td>Семейные ценности</td>
                <td style="background-color:lightgreen;">4</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Петров И. И.</td>
                <td>Информатика</td>
                <td>§2 читать; отвечать на вопросы</td>
                <td>Управление компьютером</td>
                <td style="background-color:lightgreen;">5</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Андрианов К. А.</td>
                <td>Русский язык</td>
                <td>Готовиться к входному диктанту</td>
                <td>Входной диктант</td>
                <td style="background-color:orange;">3/3</td>
            </tr>
        </table>
        <div class="info">
            <h2>Дневник для ученика</h2>
            <p>Дневник - лицо ученика, где хранятся сведения о темах, ДЗ, учителях и оценках. Вместо того, чтобы хранить все в бумажном дневнике, можно пользоваться электронным!</p>
        </div>
    </div>
    <h1>Органам управления</h1>
    <div class="content">
        <div class="info">
            <h2>Проведение пед. аттестаций</h2>
            <p>Воспользуйтесь бесплатным встроенным сервисом АИС "Тесты" для создания и проведения тестов по пед. аттестации</p>
        </div>
        <div class="lines">
            <h3>№1. ЧЕМ ЗАНИМАЕТСЯ ДЕЛОПРОИЗВОДСТВО?</h3>
            <div class="line">
                Разработка документов и их утверждение
            </div><br>
            <div class="line">
                Выдача зарплат сотрудникам и<br>подготовка отчётов по ЭЖД
            </div><br>
            <div class="line">
                Хранение документации, передача<br>документов в архив
            </div>
        </div>
    </div>
    <div class="content">
        <div class="form">
            <form method="post">
                <input type="text" placeholder="Наименование орг."><br>
                <input type="text" placeholder="ФИО директора"><br>
                <input type="submit" value="Открыть">
            </form>
        </div>
        <div class="info">
            <h2>Регистрация школ в АИС</h2>
            <p>Выдайте доступ к системе школе за 2 минуты</p>
        </div>
    </div>
    <div class="content">
        <div class="info">
            <h2>Статистика успеваемости по школе</h2>
            <p>Наблюдайте за средним баллом и количеством пропусков по школе</p>
        </div>
        <div class="stats">
            <p>Пропусков по <font color='gray'>болезни</font>: 25</p>
            <p>Пропусков по <font color='red'>неуваж. причине</font>: 3</p>
            <p>Пропусков по <font color='darkgreen'>уваж. причине</font>: 5</p>
            <hr>
            <p>Ср. балл по школе: <font color='darkgreen'>4.0</font></p>
        </div>
    </div>
</body>
</html>