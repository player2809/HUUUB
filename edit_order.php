<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: exit.html");
    exit;
}
include 'db.php';

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT o.*, d.price_per_day 
                        FROM orders o 
                        JOIN dresses d ON d.article = o.dress_article 
                        WHERE o.id = ? AND o.user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Заявка не найдена.");
}

$article = htmlspecialchars($order['dress_article']);
$price = (float)$order['price_per_day'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Редактирование заявки</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; padding: 30px; }
    .form-container {
      max-width: 600px; margin: auto; background: white;
      padding: 25px; border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; margin-bottom: 20px; }
    .price-result { font-size: 18px; font-weight: bold; text-align: center; margin-top: 15px; }
  </style>
</head>
<body>

<div class="form-container">
  <h2>Редактирование заявки №<?= $order_id ?></h2>
  <form method="post" action="submit_edit.php">
    <input type="hidden" name="id" value="<?= $order_id ?>">
    <input type="hidden" id="price_per_day" value="<?= $price ?>">

    <label>Платье:</label>
    <input type="text" class="form-control" value="<?= $article ?>" disabled>

    <label class="mt-3">Дата начала:</label>
    <input type="date" name="date_from" id="date_from" class="form-control" value="<?= $order['date_from'] ?>" required>

    <label class="mt-2">Дата окончания:</label>
    <input type="date" name="date_to" id="date_to" class="form-control" value="<?= $order['date_to'] ?>" required>

    <div class="price-result" id="priceResult">Итог: <?= $order['total_price'] ?> ₽</div>

    <button type="submit" class="btn btn-primary w-100 mt-3">Сохранить</button>
  </form>
</div>

<script>
function calculatePrice() {
  const dateFrom = new Date(document.getElementById('date_from').value);
  const dateTo = new Date(document.getElementById('date_to').value);
  const price = parseFloat(document.getElementById('price_per_day').value);
  const result = document.getElementById('priceResult');
  const days = Math.ceil((dateTo - dateFrom) / (1000 * 60 * 60 * 24));
  let discount = 0;

  if (isNaN(days) || days < 1) {
    result.textContent = "Итог: 0 ₽";
    return;
  }

  if (days === 2) discount = 5;
  else if (days >= 3 && days <= 4) discount = 10;
  else if (days >= 5 && days <= 6) discount = 15;
  else if (days >= 7) discount = 20;

  const total = price * days * (1 - discount / 100);
  result.textContent = `Итог: ${total.toFixed(0)} ₽ (${days} дн., скидка ${discount}%)`;
}

document.getElementById('date_from').addEventListener('change', calculatePrice);
document.getElementById('date_to').addEventListener('change', calculatePrice);
</script>

</body>
</html>
