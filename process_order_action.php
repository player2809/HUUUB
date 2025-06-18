<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: exit.html");
    exit;
}

include 'db.php';

$order_id = (int)($_POST['order_id'] ?? 0);
$action = $_POST['action'] ?? '';
$rejection_reason = trim($_POST['rejection_reason'] ?? '');

if ($action === 'confirm') {
    $stmt = $conn->prepare("UPDATE orders SET status = 'Подтверждена', rejection_reason = NULL WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
} elseif ($action === 'reject' && $rejection_reason !== '') {
    $stmt = $conn->prepare("UPDATE orders SET status = 'Отклонена', rejection_reason = ? WHERE id = ?");
    $stmt->bind_param("si", $rejection_reason, $order_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: admin_update_order.php");
exit;
