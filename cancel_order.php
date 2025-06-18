<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: exit.html");
    exit;
}

include 'db.php';

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Проверка: принадлежит ли заявка пользователю и можно ли отменить
$stmt = $conn->prepare("SELECT status, date_from FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();

if (!$order = $res->fetch_assoc()) {
    die("Заявка не найдена или не принадлежит вам.");
}

if ($order['status'] !== 'Новый' || strtotime($order['date_from']) <= time()) {
    die("Заявку уже нельзя отменить.");
}

// Обновление статуса
$stmt = $conn->prepare("UPDATE orders SET status = 'Отменена' WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

header("Location: profile.php");
exit;
?>
