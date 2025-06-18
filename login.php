<?php
session_start();
include 'db.php';

$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($login) || empty($password)) {
    echo "<p>Введите логин и пароль.</p><a href='exit.html'>Назад</a>";
    exit;
}

$query = $conn->prepare("SELECT id, name, password, is_admin FROM users WHERE login = ?");
$query->bind_param("s", $login);
$query->execute();
$result = $query->get_result();

if ($user = $result->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['is_admin'] = $user['is_admin'];

        if ($user['is_admin'] == 1) {
            header("Location: admin_panel.php");
        } else {
            header("Location: profile.php");
        }
        exit;
    } else {
        echo "<p>Неверный пароль.</p><a href='exit.php'>Назад</a>";
    }
} else {
    echo "<p>Пользователь не найден.</p><a href='exit.php'>Назад</a>";
}
?>
