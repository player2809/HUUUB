<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Love Stories Dress Rental – Вход</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ваш CSS без изменений (или как выше) */
        /* ... */
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <section> 
        <h2>Вход</h2>
        <form action="login.php" method="POST" class="registration-form">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login" required pattern="[A-Za-z0-9-]+" title="Только латиница, цифры и тире">

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required minlength="6">

            <div class="button-group">
                <button type="submit">Войти</button>
                <a href="logout.php" class="button-exit">Выход</a>
            </div>
        </form>
    </section>

</body>
</html>
