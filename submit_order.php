<?php 
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: exit.html");
    exit;
}

$user_id = $_SESSION['user_id'];

$dress_id = $_POST['dress_id'] ?? '';
$date_from = $_POST['date_from'] ?? '';
$date_to = $_POST['date_to'] ?? '';
$birth_date = $_POST['birth_date'] ?? '';
$passport_series_number = preg_replace('/[^0-9]/', '', $_POST['passport_series_number'] ?? '');
$passport_issue_date = $_POST['passport_issue_date'] ?? '';
$passport_issued_by = trim($_POST['passport_issued_by'] ?? '');

$errors = [];

// Проверки
if (!$dress_id) $errors[] = "Не выбрано платье.";
if (!$date_from || !$date_to) $errors[] = "Укажите даты аренды.";
if (!$birth_date) $errors[] = "Укажите дату рождения.";
if (!$passport_series_number || strlen($passport_series_number) < 10) $errors[] = "Некорректный паспорт.";
if (!$passport_issue_date) $errors[] = "Укажите дату выдачи.";
if (!$passport_issued_by) $errors[] = "Укажите, кем выдан паспорт.";

$start = strtotime($date_from);
$end = strtotime($date_to);
$days = ceil(($end - $start) / (60 * 60 * 24));
if ($days < 1) $errors[] = "Срок аренды должен быть минимум 1 день.";

// Получение цены
$stmt = $conn->prepare("SELECT price_per_day FROM dresses WHERE id = ?");
$stmt->bind_param("i", $dress_id);
$stmt->execute();
$res = $stmt->get_result();

if (!$dress = $res->fetch_assoc()) {
    $errors[] = "Платье не найдено.";
} else {
    $rate = (float)$dress['price_per_day'];
}

if ($days === 2) $discount = 5;
elseif ($days >= 3 && $days <= 4) $discount = 10;
elseif ($days >= 5 && $days <= 6) $discount = 15;
elseif ($days >= 7) $discount = 20;
else $discount = 0;

$total = $rate * $days * (1 - $discount / 100);

// Повторная отправка
$stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND dress_id = ? AND date_from = ? AND date_to = ?");
$stmt->bind_param("iiss", $user_id, $dress_id, $date_from, $date_to);
$stmt->execute();
$stmt->bind_result($exists);
$stmt->fetch();
$stmt->close();

if ($exists > 0) {
    echo "<p style='color:orange;'>Вы уже оформляли заявку на это платье в эти даты.</p>";
    echo "<p><a href='profile.php'>Перейти в личный кабинет</a></p>";
    exit;
}

if ($errors) {
    echo "<h3 style='color:darkred;'>Ошибки:</h3><ul style='color:red;'>";
    foreach ($errors as $err) echo "<li>$err</li>";
    echo "</ul><p><a href='javascript:history.back()'>⬅ Назад</a></p>";
    exit;
}

// Сохраняем
$stmt = $conn->prepare("INSERT INTO orders 
    (user_id, dress_id, date_from, date_to, total_price, birth_date, passport_series_number, passport_issue_date, passport_issued_by)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iissdssss", $user_id, $dress_id, $date_from, $date_to, $total, $birth_date, $passport_series_number, $passport_issue_date, $passport_issued_by);

if ($stmt->execute()) {
    header("Location: profile.php?success=1");
    exit;
} else {
    echo "<p style='color:red;'>Ошибка: {$stmt->error}</p><p><a href='javascript:history.back()'>⬅ Назад</a></p>";
}
?>
