<?php  
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: exit.html");
    exit;
}

include 'db.php';

$user_id = $_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['user_name']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Luxury Dress Rental - Личный кабинет</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f8f9fa; }
    .container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 0 20px rgba(0,0,0,0.1); margin: 30px auto; max-width: 1000px; }
    .btn-link { text-decoration: none; margin-right: 10px; }
    table th, table td { vertical-align: middle; }
    img.thumb { width: 60px; border-radius: 5px; }
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
  <h2 class="mb-4">👤 Личный кабинет, <?= $user_name ?></h2>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Заявка успешно оформлена!</div>
  <?php endif; ?>

  <p><a href="new_order.php" class="btn btn-success">➕ Оформить новую заявку</a></p>

  <?php
  $sql = "
  SELECT 
    o.id, o.date_from, o.date_to, o.total_price, o.status,
    d.article, d.name, d.image_main AS image
  FROM orders o
  JOIN dresses d ON o.dress_id = d.id
  WHERE o.user_id = ? ORDER BY o.id DESC";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0): ?>
    <h4 class="mt-4">Ваши заявки:</h4>
    <table class="table table-bordered table-striped mt-3">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Платье</th>
          <th>Срок</th>
          <th>Фото</th>
          <th>Сумма</th>
          <th>Статус</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): 
          $canEdit = (strtotime($row['date_from']) > time()) && ($row['status'] === 'Новая'); ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['article']) ?> — <?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['date_from'] ?> — <?= $row['date_to'] ?></td>
            <td><img src="<?= htmlspecialchars($row['image']) ?>" alt="Фото" class="thumb"></td>
            <td><?= $row['total_price'] ?> ₽</td>
            <td><?= $row['status'] ?></td>
            <td>
              <?php if ($canEdit): ?>
                <a href="edit_order.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">✏️</a>
                <a href="cancel_order.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Вы уверены, что хотите отменить заявку?')">❌</a>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="mt-4">У вас пока нет заявок.</p>
  <?php endif; ?>

  <hr>
  <a href="logout.php" class="btn btn-outline-secondary">🚪 Выйти из аккаунта</a>
  <hr>

  <h4 class="mt-5">📝 Оставить отзыв</h4>
  <form action="review.php" method="post">
    <div class="mb-3">
      <label for="reviewText" class="form-label">Ваш отзыв:</label>
      <textarea name="review_text" id="reviewText" class="form-control" rows="4" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Отправить отзыв</button>
  </form>

  <hr>
  <h4 class="mt-5">📋 Ваши отзывы</h4>

  <?php
  $review_stmt = $conn->prepare("SELECT text, created_at, status FROM reviews WHERE user_id = ? ORDER BY created_at DESC");
  $review_stmt->bind_param("i", $user_id);
  $review_stmt->execute();
  $review_result = $review_stmt->get_result();

  if ($review_result->num_rows > 0): ?>
    <ul class="list-group mt-3">
      <?php while ($review = $review_result->fetch_assoc()): ?>
        <li class="list-group-item">
          <p class="mb-1"><?= htmlspecialchars($review['text']) ?></p>
          <small class="text-muted">🕒 <?= $review['created_at'] ?> | Статус: <strong><?= $review['status'] ?></strong></small>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p class="text-muted">Вы пока не оставляли отзывов.</p>
  <?php endif; ?>
</div>

</body>
</html>
