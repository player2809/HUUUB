<?php
header('Content-Type: application/json');
include 'db.php';

$dress_id = $_POST['dress_id'] ?? '';
$date_from = $_POST['date_from'] ?? '';
$date_to = $_POST['date_to'] ?? '';

if (!$dress_id || !$date_from || !$date_to) {
    echo json_encode(['available' => false, 'error' => 'Некорректные данные']);
    exit;
}

// Проверка наличия пересекающихся заказов по dress_id
$stmt = $conn->prepare("
    SELECT id FROM orders 
    WHERE dress_id = ?
    AND NOT (date_to < ? OR date_from > ?)
");
if (!$stmt) {
    echo json_encode(['available' => false, 'error' => 'Ошибка подготовки запроса']);
    exit;
}
$stmt->bind_param("iss", $dress_id, $date_from, $date_to);
$stmt->execute();
$res = $stmt->get_result();

if ($res === false) {
    echo json_encode(['available' => false, 'error' => 'Ошибка выполнения запроса']);
    exit;
}

if ($res->num_rows > 0) {
    echo json_encode(['available' => false]);
} else {
    echo json_encode(['available' => true]);
}
exit;
