<?php
session_start();

// Проверка, что пользователь авторизован и является админом
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: exit.html");
    exit;
}

include 'db.php'; // Подключение к базе данных

// Добавление новой категории
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_category'])) {
    $name = trim($_POST['new_category']);
    if (!empty($name)) {
        // Проверка, нет ли уже такой категории
        $stmt_check = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt_check->bind_param("s", $name);
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $stmt->close();
        }
        $stmt_check->close();
    }
    header("Location: admin_categories.php");
    exit;
}

// Удаление категории
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Здесь можно добавить проверку, не связана ли категория с платьями

    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_categories.php");
    exit;
}

// Получение всех категорий
$categories_result = $conn->query("SELECT * FROM categories ORDER BY id ASC");
if (!$categories_result) {
    die("Ошибка получения категорий: " . $conn->error);
}

// Получение всех платьев с названием категории
$dresses_result = $conn->query("
    SELECT dresses.id, dresses.article, dresses.name, categories.name AS category_name, dresses.size, dresses.color, dresses.price_per_day
    FROM dresses
LEFT JOIN categories ON dresses.category_id = categories.id
    ORDER BY dresses.id ASC
");
if (!$dresses_result) {
    die("Ошибка получения платьев: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Категории платьев - Админ панель</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="container bg-white p-4 rounded shadow">
    <h2 class="mb-4">📂 Управление категориями платьев</h2>

    <form method="post" class="mb-4">
      <div class="input-group w-50">
        <input type="text" name="new_category" class="form-control" placeholder="Введите новую категорию" required>
        <button class="btn btn-primary">Добавить</button>
      </div>
    </form>

    <h4>Список категорий</h4>
    <table class="table table-bordered align-middle mb-5">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Название категории</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $categories_result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td>
              <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить категорию «<?= htmlspecialchars($row['name']) ?>»?')">Удалить</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <h4>Платья из базы данных</h4>
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Артикул</th>
          <th>Название</th>
          <th>Категория</th>
          <th>Размер</th>
          <th>Цвет</th>
          <th>Цена за день</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($dress = $dresses_result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($dress['id']) ?></td>
            <td><?= htmlspecialchars($dress['article']) ?></td>
            <td><?= htmlspecialchars($dress['name']) ?></td>
            <td><?= htmlspecialchars($dress['category_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($dress['size']) ?></td>
            <td><?= htmlspecialchars($dress['color']) ?></td>
            <td><?= htmlspecialchars($dress['price_per_day']) ?> ₽</td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <a href="admin_panel.php" class="btn btn-secondary mt-3">⬅ Назад</a>
  </div>
</body>
</html>
