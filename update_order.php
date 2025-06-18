<?php  
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header("Location: exit.html");
    exit;
}

include 'db.php';

if (!isset($_POST['id'], $_POST['action'])) {
    header("Location: admin_update_order.php");
    exit;
}

$id = intval($_POST['id']);
$action = $_POST['action'];

if ($action === 'confirm') {
    $stmt = $conn->prepare("UPDATE orders SET status = 'Подтвержден' WHERE id = ?");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
} elseif ($action === 'cancel') {
    if (!isset($_POST['reason']) || trim($_POST['reason']) === '') {
        // Можно добавить вывод ошибки или редирект с сообщением об ошибке
        header("Location: admin_update_order.php?error=empty_reason");
        exit;
    }
    $reason = trim($_POST['reason']);
    $stmt = $conn->prepare("UPDATE orders SET status = 'Отменен', reason = ? WHERE id = ?");
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }
    $stmt->bind_param("si", $reason, $id);
    $stmt->execute();
}

header("Location: admin_update_order.php");
exit;
?>
