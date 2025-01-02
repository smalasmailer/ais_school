{% load static %}
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация организации</title>
    <link rel="stylesheet" href="{ % static 'style.css' %}">
</head>
<body>
    <form method="post">
        {% csrf_token %}
        {{ form }}
    </form>
</body>
</html>