<?php 
session_start();

// Проверка: вошел ли пользователь и является ли админом
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: exit.php");
    exit;
}

$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Админ-панель</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; padding: 30px; }
    .container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
    .list-group-item { font-size: 18px; padding: 15px; }
  </style>
</head>
<body>

<div class="container">
  <h2 class="mb-4">👑 Административная панель</h2>
  <p>Добро пожаловать, <strong><?= htmlspecialchars($user_name) ?></strong>!</p>

  <div class="list-group mb-4">
    <a href="admin_update_order.php" class="list-group-item list-group-item-action">📋 Управление заявками</a>
    <a href="admin_dresses.php" class="list-group-item list-group-item-action">👗 Управление платьями</a>
    <a href="admin_reviews.php" class="list-group-item list-group-item-action">💬 Модерация отзывов</a>

    <a href="admin_categories.php" class="list-group-item list-group-item-action">📂 Категории платьев</a>
  </div>

  <div>
    <a href="logout.php" class="btn btn-outline-danger">🚪 Выйти из аккаунта</a>
  </div>
</div>

</body>
</html>
