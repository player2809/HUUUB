<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: exit.html");
    exit;
}
include 'db.php';

$id = $_POST['id'];
$action = $_POST['action'];

if ($action === 'approve') {
    $stmt = $conn->prepare("UPDATE reviews SET status = 'Подтвержден' WHERE id = ?");
    $stmt->bind_param("i", $id);
} elseif ($action === 'reject') {
    $reason = $_POST['reason'];
    $stmt = $conn->prepare("UPDATE reviews SET status = 'Отменен', reason = ? WHERE id = ?");
    $stmt->bind_param("si", $reason, $id);
}
$stmt->execute();
header("Location: admin_reviews.php");
exit;
?>
