<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Регистрация</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #f8f9fa;
      color: #333;
    }

    section {
      padding: 20px;
    }

    .registration-form {
      max-width: 400px;
      margin: 20px auto;
      background: #f9f9f9;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .registration-form label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .registration-form input[type="text"],
    .registration-form input[type="email"],
    .registration-form input[type="tel"],
    .registration-form input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 2px solid #007BFF;
      border-radius: 5px;
    }

    .registration-form input:focus {
      border-color: #0056b3;
    }

    .registration-form input[type="checkbox"] {
      margin-right: 10px;
    }

    .rules-container {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      font-size: 14px;
    }

    .button-group {
      display: flex;
      justify-content: space-between;
      gap: 15px;
    }

    .registration-form button,
    .button-exit {
      flex: 1;
      padding: 10px;
      background: #222;
      color: #fff;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s, transform 0.3s;
      text-align: center;
    }

    .registration-form button:hover,
    .button-exit:hover {
      background: #444;
      transform: scale(1.05);
    }

    .button-exit {
      background: #ff0000;
    }

    @media (max-width: 768px) {
      header {
        text-align: center;
      }
      nav ul {
        flex-direction: column;
        align-items: center;
      }
      nav ul li {
        margin: 10px 0;
      }
      .registration-form {
        max-width: 300px;
      }
    }
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<section>
  <h2>Регистрация</h2>
  <form action="register.php" method="POST" class="registration-form">
    <label for="name">Имя:</label>
    <input type="text" id="name" name="name" required pattern="[А-Яа-яЁё -]+" title="Только кириллица, пробелы и тире">

    <label for="surname">Фамилия:</label>
    <input type="text" id="surname" name="surname" required pattern="[А-Яа-яЁё -]+" title="Только кириллица, пробелы и тире">

    <label for="patronymic">Отчество (необязательно):</label>
    <input type="text" id="patronymic" name="patronymic" pattern="[А-Яа-яЁё -]*" title="Только кириллица, пробелы и тире">

    <label for="login">Логин:</label>
    <input type="text" id="login" name="login" required pattern="[A-Za-z0-9-]+" title="Только латиница, цифры и тире">

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="tel">Телефон:</label>
    <input type="tel" id="tel" name="tel" required pattern="\+?\d{11,12}" title="Введите телефон в формате +71234567890">

    <label for="password">Пароль:</label>
    <input type="password" id="password" name="password" required minlength="6">

    <label for="password_repeat">Повторите пароль:</label>
    <input type="password" id="password_repeat" name="password_repeat" required minlength="6">

    <div class="rules-container">
      <input type="checkbox" name="rules" id="rules" required>
      <label for="rules">Согласен с правилами</label>
    </div>

    <div class="button-group">
      <button type="submit">Зарегистрироваться</button>
      <a href="logout.php" class="button-exit">Выход</a>
    </div>
  </form>
</section>

</body>
</html>
