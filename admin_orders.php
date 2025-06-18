<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: exit.html");
    exit;
}

include 'db.php';

$status_filter = $_GET['status'] ?? '';

$query = "SELECT o.*, u.name, u.surname FROM orders o JOIN users u ON o.user_id = u.id";
if ($status_filter) {
    $query .= " WHERE o.status = ?";
}
$query .= " ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
if ($status_filter) {
    $stmt->bind_param("s", $status_filter);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Заявки - Админ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">

<div class="container bg-white p-4 rounded shadow">
  <h2>📋 Заявки</h2>

  <form method="get" class="mb-3">
    <label for="status" class="form-label">Фильтр по статусу:</label>
    <select name="status" id="status" class="form-select w-25 d-inline" onchange="this.form.submit()">
      <option value="">Все</option>
      <option value="Новый" <?= $status_filter == 'Новый' ? 'selected' : '' ?>>Новые</option>
      <option value="Подтвержден" <?= $status_filter == 'Подтвержден' ? 'selected' : '' ?>>Подтвержденные</option>
      <option value="Отменен" <?= $status_filter == 'Отменен' ? 'selected' : '' ?>>Отмененные</option>
    </select>
  </form>

  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th>Дата</th>
        <th>Клиент</th>
        <th>Артикул</th>
        <th>Срок</th>
        <th>Сумма</th>
        <th>Статус</th>
        <th>Действия</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['created_at']) ?></td>
        <td><?= htmlspecialchars($row['surname']) . ' ' . htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['dress_article']) ?></td>
        <td><?= htmlspecialchars($row['date_from']) ?> — <?= htmlspecialchars($row['date_to']) ?></td>
        <td><?= htmlspecialchars($row['total_price']) ?> ₽</td>
        <td><?= htmlspecialchars($row['status']) ?></td>
        <td>
          <?php if ($row['status'] === 'Новый'): ?>
            <form action="admin_update_order.php" method="post" class="d-inline">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <input type="hidden" name="action" value="confirm">
              <button class="btn btn-success btn-sm">✅ Подтвердить</button>
            </form>
            <button class="btn btn-danger btn-sm" onclick="cancelPrompt(<?= $row['id'] ?>)">❌ Отменить</button>
          <?php else: ?>
            <span class="text-muted">—</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script>
function cancelPrompt(id) {
  const reason = prompt("Укажите причину отмены:");
  if (reason) {
    const form = document.createElement('form');
    form.method = 'post';
    form.action = 'admin_update_order.php';

    form.innerHTML = `
      <input type="hidden" name="id" value="${id}">
      <input type="hidden" name="action" value="cancel">
      <input type="hidden" name="reason" value="${reason}">
    `;

    document.body.appendChild(form);
    form.submit();
  }
}
</script>

</body>
</html>
