<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: exit.html");
    exit;
}

include 'db.php';

$errors = [];

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

    if ($order_id <= 0) {
        $errors[] = "Некорректный ID заявки.";
    } else {
        if (isset($_POST['confirm'])) {
            // Подтверждаем заявку
            $stmt = $conn->prepare("UPDATE orders SET status = 'Подтверждена' WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            if (!$stmt->execute()) {
                $errors[] = "Ошибка при подтверждении заявки: " . $stmt->error;
            }
            $stmt->close();
        } elseif (isset($_POST['decline'])) {
            $reason = trim($_POST['reason'] ?? '');
            if ($reason === '') {
                $errors[] = "Причина отказа обязательна.";
            } else {
                // Обновляем статус с причиной отказа
                $status_text = "Отклонена: " . $reason;
                $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status_text, $order_id);
                if (!$stmt->execute()) {
                    $errors[] = "Ошибка при отклонении заявки: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }

    if (empty($errors)) {
        // Перезагрузка страницы после успешного действия, чтобы обновить список
        header("Location: admin_update_order.php");
        exit;
    }
}

// Получаем список заявок
$sql = "SELECT 
            o.id,
            o.date_from,
            o.date_to,
            o.total_price,
            o.status,
            u.name AS user_name,
            d.article AS dress_article
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN dresses d ON o.dress_id = d.id
        ORDER BY o.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Управление заявками</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light p-4">
<div class="container bg-white p-4 rounded shadow">
    <h2>📋 Заявки клиентов</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table table-bordered table-striped mt-4 align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Клиент</th>
                    <th>Платье</th>
                    <th>Период</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                        <td><?= htmlspecialchars($row['dress_article']) ?></td>
                        <td><?= htmlspecialchars($row['date_from']) ?> — <?= htmlspecialchars($row['date_to']) ?></td>
                        <td><?= htmlspecialchars($row['total_price']) ?> ₽</td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td style="min-width: 260px;">
                            <?php
                            $statusLower = mb_strtolower(trim($row['status']));
                            if ($statusLower === 'new' || $statusLower === 'новый' || $statusLower === 'новая'):
                            ?>
                                <!-- Форма подтверждения -->
                                <form method="post" style="display:inline-block; margin-right: 5px;">
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="confirm" class="btn btn-success btn-sm">✅ Подтвердить</button>
                                </form>

                                <!-- Форма отклонения -->
                                <form method="post" style="display:inline-flex; gap:5px; align-items:center;">
                                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                    <input type="text" name="reason" class="form-control form-control-sm" placeholder="Причина отказа" required>
                                    <button type="submit" name="decline" class="btn btn-danger btn-sm">❌ Отклонить</button>
                                </form>
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="mt-4">Заявок пока нет.</p>
    <?php endif; ?>

    <a href="admin_panel.php" class="btn btn-secondary mt-3">← Назад в админ-панель</a>
</div>
</body>
</html>
