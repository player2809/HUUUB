<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    echo "ID платья не указан.";
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("
    SELECT d.*, c.name AS category_name 
    FROM dresses d 
    LEFT JOIN categories c ON d.category_id = c.id 
    WHERE d.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$dress = $result->fetch_assoc()) {
    echo "Платье не найдено.";
    exit;
}

$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($dress['name']) ?> — Описание</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .dress-images img {
      max-width: 150px;
      margin-right: 10px;
      border-radius: 5px;
      cursor: pointer;
      border: 1px solid #ccc;
    }
    .dress-images img:hover {
      border-color: #007bff;
    }
    .main-image {
      max-width: 400px;
      margin-bottom: 20px;
      border-radius: 8px;
      border: 1px solid #ddd;
    }
    .calculator {
      max-width: 400px;
      margin-top: 30px;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background: #f8f9fa;
    }
    /* Кнопка в стиле хедера */
    .btn-reserve {
      background-color: #333;
      color: white;
      border: none;
      padding: 10px 25px;
      border-radius: 5px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .btn-reserve:hover {
      background-color: #555;
    }
    #totalCost {
      font-weight: bold;
      font-size: 1.2em;
      margin-top: 5px;
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <h1><?= htmlspecialchars($dress['name']) ?></h1>
  <p><strong>Артикул:</strong> <?= htmlspecialchars($dress['article']) ?></p>

  <!-- Главное фото -->
  <img src="<?= htmlspecialchars($dress['image_main']) ?>" alt="Главное фото платья" class="main-image img-fluid" id="mainImage" />

  <!-- Дополнительные фото -->
  <div class="dress-images mb-4">
    <?php if ($dress['image_1']): ?>
      <img src="<?= htmlspecialchars($dress['image_1']) ?>" alt="Фото 1" onclick="document.getElementById('mainImage').src=this.src" />
    <?php endif; ?>
    <?php if ($dress['image_2']): ?>
      <img src="<?= htmlspecialchars($dress['image_2']) ?>" alt="Фото 2" onclick="document.getElementById('mainImage').src=this.src" />
    <?php endif; ?>
  </div>

  <!-- Характеристики -->
  <ul>
    <li><strong>Категория:</strong> <?= htmlspecialchars($dress['category_name']) ?></li>
    <li><strong>Размер:</strong> <?= htmlspecialchars($dress['size']) ?></li>
    <li><strong>Цвет:</strong> <?= htmlspecialchars($dress['color']) ?></li>
    <li><strong>Силуэт:</strong> <?= htmlspecialchars($dress['silhouette']) ?></li>
    <li><strong>Длина:</strong> <?= htmlspecialchars($dress['length']) ?></li>
    <li><strong>Материал:</strong> <?= htmlspecialchars($dress['material']) ?></li>
    <li><strong>Тариф за сутки:</strong> <?= htmlspecialchars($dress['price_per_day']) ?> ₽</li>
    <li><strong>Залог:</strong> <?= htmlspecialchars($dress['deposit']) ?> ₽</li>
  </ul>

  <!-- Описание -->
  <p><?= nl2br(htmlspecialchars($dress['description'])) ?></p>

  <!-- Калькулятор стоимости проката -->
  <div class="calculator">
    <h4>Калькулятор стоимости проката</h4>
    <form id="calcForm" onsubmit="return false;">
      <div class="mb-3">
        <label for="startDate" class="form-label">Дата начала проката</label>
        <input type="date" id="startDate" class="form-control" required min="<?= date('Y-m-d') ?>" />
      </div>
      <div class="mb-3">
        <label for="endDate" class="form-label">Дата окончания проката</label>
        <input type="date" id="endDate" class="form-control" required min="<?= date('Y-m-d') ?>" />
      </div>
      <div class="mb-3">
        <label class="form-label">Тариф за сутки:</label>
        <div><?= htmlspecialchars($dress['price_per_day']) ?> ₽</div>
      </div>
      <div class="mb-3">
        <label class="form-label">Итоговая стоимость:</label>
        <div id="totalCost">0 ₽</div>
      </div>
      <button type="button" class="btn-reserve" id="reserveBtn" disabled>Зарезервировать</button>
    </form>
  </div>

  <a href="rentaldress.php" class="btn btn-secondary mt-4">← Назад в каталог</a>
</div>

<!-- Модальное окно выбора даты аренды -->
<div class="modal fade" id="rentModal" tabindex="-1" aria-labelledby="rentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="rentalForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rentModalLabel">Оформление бронирования</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="rentDressId" name="dress_id" value="<?= $dress['id'] ?>">
        <div class="mb-3">
          <label for="date_from" class="form-label">Дата начала</label>
          <input type="date" id="date_from" name="date_from" class="form-control" required min="<?= date('Y-m-d') ?>">
        </div>
        <div class="mb-3">
          <label for="date_to" class="form-label">Дата окончания</label>
          <input type="date" id="date_to" name="date_to" class="form-control" required min="<?= date('Y-m-d') ?>">
        </div>
        <div id="rentMessage" class="text-danger mb-3"></div>

        <div class="mb-3">
          <label for="birth_date" class="form-label">Дата рождения</label>
          <input type="date" id="birth_date" name="birth_date" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="passport_series_number" class="form-label">Серия и номер паспорта</label>
          <input type="text" id="passport_series_number" name="passport_series_number" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="passport_issue_date" class="form-label">Дата выдачи паспорта</label>
          <input type="date" id="passport_issue_date" name="passport_issue_date" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="passport_issued_by" class="form-label">Кем выдан</label>
          <input type="text" id="passport_issued_by" name="passport_issued_by" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="reason" class="form-label">Причина аренды</label>
          <textarea id="reason" name="reason" class="form-control" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Зарезервировать</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
      </div>
    </form>
  </div>
</div> 

<!-- Модальное окно входа/регистрации -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="authModalLabel">Пожалуйста, войдите или зарегистрируйтесь</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">
        <p>Для бронирования платья необходимо авторизоваться.</p>
        <a href="exit.php" class="btn btn-primary">Войти</a>
        <a href="register.php" class="btn btn-secondary ms-2">Регистрация</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const isLoggedIn = <?= $is_logged_in ? 'true' : 'false' ?>;
  const pricePerDay = <?= (int)$dress['price_per_day'] ?>;

  const startDateInput = document.getElementById('startDate');
  const endDateInput = document.getElementById('endDate');
  const totalCostDiv = document.getElementById('totalCost');
  const reserveBtn = document.getElementById('reserveBtn');

  // Функция для подсчёта скидки по таблице
  function getDiscount(days) {
    if (days >= 7) return 20;
    if (days >= 5) return 15;
    if (days >= 3) return 10;
    if (days >= 2) return 5;
    return 0;
  }

  function calculateCost() {
    const start = new Date(startDateInput.value);
    const end = new Date(endDateInput.value);
    if (!startDateInput.value || !endDateInput.value) {
      totalCostDiv.textContent = '0 ₽';
      reserveBtn.disabled = true;
      return;
    }
    if (end < start) {
      totalCostDiv.textContent = 'Дата окончания не может быть раньше даты начала';
      reserveBtn.disabled = true;
      return;
    }
    const timeDiff = end - start;
    const days = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) + 1; // включая первый день

    const discount = getDiscount(days);
    const cost = days * pricePerDay * (1 - discount / 100);

    totalCostDiv.textContent = `${cost.toFixed(2)} ₽ (скидка ${discount}%)`;
    reserveBtn.disabled = false;
  }

  startDateInput.addEventListener('change', calculateCost);
  endDateInput.addEventListener('change', calculateCost);

  reserveBtn.addEventListener('click', () => {
    if (!isLoggedIn) {
      const authModal = new bootstrap.Modal(document.getElementById('authModal'));
      authModal.show();
      return;
    }
    // Заполняем форму модального окна
    document.getElementById('date_from').value = startDateInput.value;
    document.getElementById('date_to').value = endDateInput.value;
    document.getElementById('rentDressId').value = <?= $dress['id'] ?>;

    // Сбрасываем сообщения и поля
    document.getElementById('rentMessage').textContent = '';
    document.getElementById('birth_date').value = '';
    document.getElementById('passport_series_number').value = '';
    document.getElementById('passport_issue_date').value = '';
    document.getElementById('passport_issued_by').value = '';
    document.getElementById('reason').value = '';

    const rentModal = new bootstrap.Modal(document.getElementById('rentModal'));
    rentModal.show();
  });

  // Обработка отправки формы бронирования
  document.getElementById('rentalForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const id = document.getElementById('rentDressId').value;
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    const birthDate = document.getElementById('birth_date').value;
    const passportNumber = document.getElementById('passport_series_number').value.trim();
    const passportIssueDate = document.getElementById('passport_issue_date').value;
    const passportIssuedBy = document.getElementById('passport_issued_by').value.trim();
    const reason = document.getElementById('reason').value.trim();

    if (!dateFrom || !dateTo) {
      document.getElementById('rentMessage').textContent = 'Пожалуйста, выберите обе даты.';
      return;
    }
    if (!birthDate || !passportNumber || !passportIssueDate || !passportIssuedBy || !reason) {
      document.getElementById('rentMessage').textContent = 'Пожалуйста, заполните все обязательные поля.';
      return;
    }

    fetch('check_availability.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `dress_id=${encodeURIComponent(id)}&date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.available) {
        // Создаем заказ
        fetch('create_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            dress_id: id,
            date_from: dateFrom,
            date_to: dateTo,
            birth_date: birthDate,
            passport_series_number: passportNumber,
            passport_issue_date: passportIssueDate,
            passport_issued_by: passportIssuedBy,
            reason: reason
          })
        })
        .then(res => res.json())
        .then(resData => {
          if (resData.success) {
            window.location.href = 'profile.php';
          } else {
            document.getElementById('rentMessage').textContent = 'Ошибка при создании заявки: ' + (resData.error || '');
          }
        })
        .catch(() => {
          document.getElementById('rentMessage').textContent = 'Ошибка при создании заявки. Попробуйте позже.';
        });
      } else {
        document.getElementById('rentMessage').textContent = 'Платье недоступно на выбранные даты. Попробуйте другие.';
      }
    })
    .catch(() => {
      document.getElementById('rentMessage').textContent = 'Ошибка при проверке. Попробуйте позже.';
    });
  });
</script>

</body>
</html>
