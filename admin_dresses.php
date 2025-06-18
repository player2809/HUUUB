<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: exit.html");
    exit;
}

include 'db.php';

// Получаем все платья с названиями категорий по category_id
$dresses_result = $conn->query("
    SELECT 
        dresses.id, 
        dresses.article, 
        dresses.name, 
        categories.name AS category_name, 
        dresses.size, 
        dresses.color, 
        dresses.price_per_day
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
    <meta charset="UTF-8" />
    <title>Управление платьями - Админ панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light p-4">
<div class="container bg-white p-4 rounded shadow">
    <h2 class="mb-4">👗 Управление платьями</h2>

    <a href="admin_add_dress.php" class="btn btn-primary mb-3">➕ Добавить новое платье</a>

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
                <th>Действия</th>
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
                    <td>
                        <a href="admin_edit_dress.php?id=<?= $dress['id'] ?>" class="btn btn-sm btn-warning">Редактировать</a>
                        <a href="admin_delete_dress.php?id=<?= $dress['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить платье «<?= htmlspecialchars(addslashes($dress['name'])) ?>»?')">Удалить</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="admin_panel.php" class="btn btn-secondary mt-3">⬅ Назад</a>
</div>
</body>
</html>
