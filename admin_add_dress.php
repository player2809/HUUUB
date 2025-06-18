<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: exit.html");
    exit;
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article = $_POST['article'];
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $price = $_POST['price_per_day'];

    $stmt = $conn->prepare("INSERT INTO dresses (article, name, category_id, size, color, price_per_day) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissi", $article, $name, $category_id, $size, $color, $price);

    if ($stmt->execute()) {
        header("Location: admin_dresses.php");
        exit;
    } else {
        $error = "Ошибка добавления: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Добавить платье</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light p-4">
<div class="container bg-white p-4 rounded shadow">
    <h2>➕ Добавление нового платья</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Артикул:</label>
            <input type="text" name="article" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Название:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Категория:</label>
            <select name="category_id" class="form-select" required>
                <option value="">Выберите категорию</option>
                <?php
                $cats = $conn->query("SELECT id, name FROM categories");
                while ($cat = $cats->fetch_assoc()):
                ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Размер:</label>
            <input type="text" name="size" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Цвет:</label>
            <input type="text" name="color" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Цена за день:</label>
            <input type="number" name="price_per_day" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="admin_dresses.php" class="btn btn-secondary">Назад</a>
    </form>
</div>
</body>
</html>
