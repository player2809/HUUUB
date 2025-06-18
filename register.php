<?php
include 'db.php';

function input($key) {
    return htmlspecialchars(trim($_POST[$key] ?? ''));
}

$name         = input('name');
$surname      = input('surname');
$patronymic   = input('patronymic');
$login        = input('login');
$email        = input('email');
$tel          = input('tel');
$password     = $_POST['password'] ?? '';
$password_repeat = $_POST['password_repeat'] ?? '';
$rules        = isset($_POST['rules']);

$errors = [];

if (!$rules) $errors[] = "Необходимо согласие с правилами.";
if ($password !== $password_repeat) $errors[] = "Пароли не совпадают.";

if (strlen($password) < 6) $errors[] = "Пароль должен содержать минимум 6 символов.";

if (!preg_match("/^[А-Яа-яЁё -]+$/u", $name)) $errors[] = "Имя содержит недопустимые символы.";
if (!preg_match("/^[А-Яа-яЁё -]+$/u", $surname)) $errors[] = "Фамилия содержит недопустимые символы.";
if (!empty($patronymic) && !preg_match("/^[А-Яа-яЁё -]*$/u", $patronymic)) $errors[] = "Отчество содержит недопустимые символы.";
if (!preg_match("/^[A-Za-z0-9-]+$/", $login)) $errors[] = "Логин содержит недопустимые символы.";
if (!preg_match("/^\+?\d{11,12}$/", $tel)) $errors[] = "Телефон в неверном формате.";

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Некорректный email.";

if (count($errors)) {
    foreach ($errors as $err) echo "<p>$err</p>";
    echo '<p><a href="registration.html">Вернуться к регистрации</a></p>';
    exit;
}

// Проверка на уникальность
$check = $conn->prepare("SELECT id FROM users WHERE login = ? OR email = ? OR tel = ?");
$check->bind_param("sss", $login, $email, $tel);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo "<p>Пользователь с таким логином, email или телефоном уже существует.</p>";
    echo '<p><a href="registration.html">Вернуться</a></p>';
    exit;
}
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (name, surname, patronymic, login, email, tel, password)
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $name, $surname, $patronymic, $login, $email, $tel, $password_hash);

if ($stmt->execute()) {
    echo "<p>Регистрация прошла успешно. <a href='exit.php'>Войти</a></p>";
} else {
    echo "<p>Ошибка регистрации: " . $conn->error . "</p>";
}
?>
