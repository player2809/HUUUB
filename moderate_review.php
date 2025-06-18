<?php
file_put_contents("form_debug.log", date("Y-m-d H:i:s") . " — POST: " . print_r($_POST, true) . "\n", FILE_APPEND);

session_start();
include 'db.php';

// Включаем отображение всех ошибок
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Проверка: администратор ли пользователь
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: exit.html");
    exit;
}

// Получение данных из формы
$review_id = (int)($_POST['review_id'] ?? 0);
$action = $_POST['action'] ?? '';
$reason = trim($_POST['rejection_reason'] ?? '');

// Если данные есть
if ($review_id && $action) {
    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE reviews SET status = 'Подтвержден', rejection_reason = NULL WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $review_id);
            if (!$stmt->execute()) {
                file_put_contents("form_debug.log", date("Y-m-d H:i:s") . " — Ошибка выполнения запроса (approve): " . $stmt->error . "\n", FILE_APPEND);
            } else {
                file_put_contents("form_debug.log", date("Y-m-d H:i:s") . " — Одобрено успешно\n", FILE_APPEND);
            }
        } else {
            die("Ошибка подготовки запроса: " . $conn->error);
        }
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE reviews SET status = 'Отклонён', rejection_reason = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $reason, $review_id);
            if (!$stmt->execute()) {
                file_put_contents("form_debug.log", date("Y-m-d H:i:s") . " — Ошибка выполнения запроса (reject): " . $stmt->error . "\n", FILE_APPEND);
            } else {
                file_put_contents("form_debug.log", date("Y-m-d H:i:s") . " — Отклонено успешно\n", FILE_APPEND);
            }
        } else {
            die("Ошибка подготовки запроса: " . $conn->error);
        }
    }
}

// Перенаправление обратно
header("Location: admin_reviews.php");
exit;
