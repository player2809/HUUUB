<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: exit.html");
    exit;
}

include 'db.php';

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM dresses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$dress = $stmt->get_result()->fetch_assoc();

if (!$dress) {
    die("Платье не найдено.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article = $_POST['article'];
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $price = $_POST['price_per_day'];

    $update = $conn->prepare("UPDATE dresses SET article = ?, name = ?, category_id = ?, size = ?, color = ?, price_per_day = ? WHERE id = ?");
    $update->bind_param("ssissii", $article, $name, $category_id, $size, $color, $price, $id);

    if ($update->execute()) {
        header("Location: admin_dresses.php");
        exit;
    } else {
        $error = "Ошибка обновления: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Редактировать платье</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light p-4">
<div class="container bg-white p-4 rounded shadow">
    <h2>✏️ Редактировать платье</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Артикул:</label>
            <input type="text" name="article" class="form-control" value="<?= htmlspecialchars($dress['article']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Название:</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($dress['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Категория:</label>
            <select name="category_id" class="form-select" required>
                <option value="">Выберите категорию</option>
                <?php
                $cats = $conn->query("SELECT id, name FROM categories");
                while ($cat = $cats->fetch_assoc()):
                    $selected = ($cat['id'] == $dress['category_id']) ? 'selected' : '';
                ?>
                    <option value="<?= $cat['id'] ?>" <?= $selected ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Размер:</label>
            <input type="text" name="size" class="form-control" value="<?= htmlspecialchars($dress['size']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Цвет:</label>
            <input type="text" name="color" class="form-control" value="<?= htmlspecialchars($dress['color']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Цена за день:</label>
            <input type="number" name="price_per_day" class="form-control" value="<?= htmlspecialchars($dress['price_per_day']) ?>" required>
        </div>

        <button type="submit" class="btn btn-success">Обновить</button>
        <a href="admin_dresses.php" class="btn btn-secondary">Назад</a>
    </form>
</div>
</body>
</html>
