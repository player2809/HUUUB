<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: exit.html");
    exit;
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$review_text = trim($_POST['review_text'] ?? '');

if ($review_text === '') {
    echo "Отзыв не может быть пустым. <a href='profile.php'>Назад</a>";
    exit;
}

$stmt = $conn->prepare("INSERT INTO reviews (user_id, user_name, text) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $user_name, $review_text);

if ($stmt->execute()) {
    echo "✅ Отзыв отправлен на модерацию. <a href='profile.php'>Вернуться</a>";
} else {
    echo "Ошибка при отправке отзыва.";
}
?>
