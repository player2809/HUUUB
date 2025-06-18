<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
    exit;
}

require_once 'db.php'; // Подключение к базе (должно содержать $conn - объект mysqli)

// Получаем данные из POST
$user_id = $_SESSION['user_id'];
$dress_id = $_POST['dress_id'] ?? null;
$date_from = $_POST['date_from'] ?? null;
$date_to = $_POST['date_to'] ?? null;
$birth_date = $_POST['birth_date'] ?? null;
$passport_series_number = trim($_POST['passport_series_number'] ?? '');
$passport_issue_date = $_POST['passport_issue_date'] ?? null;
$passport_issued_by = trim($_POST['passport_issued_by'] ?? '');
$reason = trim($_POST['reason'] ?? '');

// Проверка обязательных полей
if (!$dress_id || !$date_from || !$date_to || !$birth_date || !$passport_series_number || !$passport_issue_date || !$passport_issued_by || !$reason) {
    echo json_encode(['success' => false, 'error' => 'Заполните все обязательные поля']);
    exit;
}

// Проверка формата дат (простейшая)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_to)) {
    echo json_encode(['success' => false, 'error' => 'Неверный формат даты']);
    exit;
}

// Проверка логики дат
if ($date_from > $date_to) {
    echo json_encode(['success' => false, 'error' => 'Дата начала аренды не может быть позже даты окончания']);
    exit;
}

// Дополнительные проверки можно добавить здесь...

// Проверка доступности платья на указанные даты (повторная для безопасности)
$sqlCheck = "SELECT COUNT(*) AS cnt FROM orders WHERE dress_id = ? AND status IN ('new', 'confirmed') AND NOT (date_to < ? OR date_from > ?)";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param('iss', $dress_id, $date_from, $date_to);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
$row = $resultCheck->fetch_assoc();
if ($row['cnt'] > 0) {
    echo json_encode(['success' => false, 'error' => 'Платье недоступно на выбранные даты']);
    exit;
}

// Вставка заявки в таблицу orders
$sqlInsert = "INSERT INTO orders 
    (user_id, dress_id, date_from, date_to, birth_date, passport_series_number, passport_issue_date, passport_issued_by, reason, status, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'new', NOW())";

$stmt = $conn->prepare($sqlInsert);
$stmt->bind_param('iisssssss', $user_id, $dress_id, $date_from, $date_to, $birth_date, $passport_series_number, $passport_issue_date, $passport_issued_by, $reason);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка при сохранении заявки']);
}
