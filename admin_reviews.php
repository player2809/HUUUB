<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: exit.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Модерация отзывов</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; padding: 30px; }
    .review-box { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
  </style>
</head>
<body>

<div class="container">
  <h2 class="mb-4">💬 Модерация отзывов</h2>

  <?php
  $result = $conn->query("SELECT * FROM reviews ORDER BY created_at DESC");

  if (!$result) {
      echo "<div class='alert alert-danger'>Ошибка запроса: " . $conn->error . "</div>";
      exit;
  }

  while ($row = $result->fetch_assoc()):
  ?>
    <div class="review-box">
      <p><strong><?= htmlspecialchars($row['user_name']) ?></strong>
         <?php if (!empty($row['dress_article'])): ?>
           о платье: <em><?= htmlspecialchars($row['dress_article']) ?></em>
         <?php endif; ?>
      </p>
      <p><?= nl2br(htmlspecialchars($row['text'])) ?></p>
      <p><small>🕒 <?= $row['created_at'] ?></small></p>

      <?php if ($row['status'] == 1): ?>
        <p class="text-success">✅ Отзыв одобрен</p>
      <?php elseif ($row['status'] == 2): ?>
        <p class="text-danger">❌ Отклонён: <?= htmlspecialchars($row['rejection_reason']) ?></p>
      <?php else: ?>
        <form method="post" action="moderate_review.php" class="mt-3">
          <input type="hidden" name="review_id" value="<?= $row['id'] ?>">
          <div class="mb-2">
            <textarea name="rejection_reason" class="form-control" placeholder="Причина отклонения (если отклоняете)" rows="2"></textarea>
          </div>
          <button name="action" value="approve" class="btn btn-success btn-sm">Одобрить</button>
          <button name="action" value="reject" class="btn btn-danger btn-sm">Отклонить</button>
        </form>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>
</div>

</body>
</html>
