<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: exit.html");
    exit;
}

include 'db.php';

$user_id = $_SESSION['user_id'];
$order_id = intval($_POST['order_id']);
$article = $_POST['dress_article'] ?? '';
$date_from = $_POST['date_from'] ?? '';
$date_to = $_POST['date_to'] ?? '';

$errors = [];

// Проверка дат
$start = strtotime($date_from);
$end = strtotime($date_to);
$days = ceil(($end - $start) / (60 * 60 * 24));
if ($days < 1) $errors[] = "Минимальный срок проката — 1 день.";

// Получение ставки
$stmt = $conn->prepare("SELECT price_per_day FROM dresses WHERE article = ?");
$stmt->bind_param("s", $article);
$stmt->execute();
$res = $stmt->get_result();
if (!$dress = $res->fetch_assoc()) {
    $errors[] = "Платье не найдено.";
} else {
    $rate = $dress['price_per_day'];
}

// Скидка
if ($days == 2) $discount = 5;
elseif ($days >= 3 && $days <= 4) $discount = 10;
elseif ($days >= 5 && $days <= 6) $discount = 15;
elseif ($days >= 7) $discount = 20;
else $discount = 0;

$total = $rate * $days * (1 - $discount / 100);

// Проверка права на редактирование
$check = $conn->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ? AND status = 'Новый'");
$check->bind_param("ii", $order_id, $user_id);
$check->execute();
$res = $check->get_result();
if (!$res->fetch_assoc()) {
    $errors[] = "Редактирование недоступно.";
}

if (count($errors)) {
    foreach ($errors as $err) echo "<p>$err</p>";
    echo "<p><a href='profile.php'>Назад</a></p>";
    exit;
}

// Обновление
$update = $conn->prepare("UPDATE orders SET dress_article = ?, date_from = ?, date_to = ?, total_price = ? WHERE id = ?");
$update->bind_param("sssdi", $article, $date_from, $date_to, $total, $order_id);
$update->execute();

echo "<p>Заявка успешно обновлена.</p><a href='profile.php'>Вернуться в кабинет</a>";
