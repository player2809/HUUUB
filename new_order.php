<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: exit.html");
    exit;
}
include 'db.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Оформление заявки</title>
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
    .error-message { color: red; margin-top: 10px; display: none; }
  </style>
</head>
<body>

<div class="form-container">
  <h2>Оформление заявки</h2>
  <form id="orderForm" method="post" action="submit_order.php" novalidate>
    <label>Выберите платье:</label>
    <select name="dress_id" class="form-select" id="dress_id" required>
      <option value="">Выберите...</option>
      <?php
        $result = $conn->query("SELECT id, article, name, price_per_day FROM dresses ORDER BY name");
        while ($row = $result->fetch_assoc()) {
            $id = (int)$row['id'];
            $article = htmlspecialchars($row['article']);
            $name = htmlspecialchars($row['name']);
            $price = (int)$row['price_per_day'];
            echo "<option value='$id' data-price='$price'>$article — $name ($price ₽/день)</option>";
        }
      ?>
    </select>

    <label class="mt-3">Дата начала:</label>
    <input type="date" name="date_from" id="date_from" class="form-control" required>

    <label class="mt-2">Дата окончания:</label>
    <input type="date" name="date_to" id="date_to" class="form-control" required>

    <div class="price-result" id="priceResult">Итог: 0 ₽</div>

    <hr>

    <label class="mt-3">Дата рождения:</label>
    <input type="date" name="birth_date" id="birth_date" class="form-control" required>

    <label class="mt-3">Паспорт: серия и номер</label>
    <input type="text" name="passport_series_number" id="passport_series_number" class="form-control" placeholder="Например: 1234 567890" required>

    <label class="mt-3">Дата выдачи паспорта:</label>
    <input type="date" name="passport_issue_date" id="passport_issue_date" class="form-control" required>

    <label class="mt-3">Кем выдан паспорт:</label>
    <input type="text" name="passport_issued_by" id="passport_issued_by" class="form-control" required>

    <div class="error-message" id="errorMessage"></div>

    <button type="submit" class="btn btn-primary w-100 mt-4">Отправить заявку</button>
  </form>
</div>

<script>
function calculatePrice() {
  const select = document.getElementById('dress_id');
  const dateFrom = new Date(document.getElementById('date_from').value);
  const dateTo = new Date(document.getElementById('date_to').value);
  const result = document.getElementById('priceResult');

  const days = Math.ceil((dateTo - dateFrom) / (1000 * 60 * 60 * 24));
  if (isNaN(days) || days < 1 || !select.value) {
    result.textContent = 'Итог: 0 ₽';
    return;
  }

  const pricePerDay = parseFloat(select.selectedOptions[0].dataset.price);
  let discount = 0;
  if (days === 2) discount = 5;
  else if (days >= 3 && days <= 4) discount = 10;
  else if (days >= 5 && days <= 6) discount = 15;
  else if (days >= 7) discount = 20;

  const total = pricePerDay * days * (1 - discount / 100);
  result.textContent = `Итог: ${total.toFixed(0)} ₽ (${days} дн., скидка ${discount}%)`;
}

document.getElementById('dress_id').addEventListener('change', calculatePrice);
document.getElementById('date_from').addEventListener('change', calculatePrice);
document.getElementById('date_to').addEventListener('change', calculatePrice);

// Проверка перед отправкой
document.getElementById('orderForm').addEventListener('submit', function(event) {
  event.preventDefault();

  const form = event.target;
  const errorDiv = document.getElementById('errorMessage');
  let errorMsg = '';

  ['dress_id', 'date_from', 'date_to', 'birth_date', 'passport_series_number', 'passport_issue_date', 'passport_issued_by'].forEach(id => {
    const val = document.getElementById(id).value.trim();
    if (!val) errorMsg += `Поле ${id} обязательно.<br>`;
  });

  if (errorMsg) {
    errorDiv.innerHTML = errorMsg;
    errorDiv.style.display = 'block';
    return;
  }

  errorDiv.style.display = 'none';
  form.submit();
});
</script>

</body>
</html>
