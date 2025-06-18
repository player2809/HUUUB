<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <title>Luxury Dress Rental - Каталог</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f8f9fa; }
    header { background: #333; color: white; padding: 10px 20px; }
    header h1 { margin: 0; }
    nav ul { list-style: none; margin: 0; padding: 0; display: flex; gap: 15px; }
    nav a { color: white; text-decoration: none; }
    .catalog-item {
      border: 1px solid #ddd;
      padding: 15px;
      margin: 10px;
      text-align: center;
      border-radius: 10px;
      background: #fff;
      cursor: pointer;
    }
    .catalog-item img {
      width: 100%;
      height: auto;
      border-radius: 8px;
    }
    .catalog-item button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      margin-top: 10px;
      cursor: pointer;
      border-radius: 5px;
    }
    .catalog-item button:hover {
      background-color: #0056b3;
    }
    .color-label {
      margin-right: 15px;
      display: inline-block;
    }
    #rentMessage {
      color: red;
      margin-top: 10px;
      min-height: 20px;
    }
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container mt-5">
  <h2>Фильтры товаров</h2>
  <div class="filters">
    <div class="row mb-3">
      <div class="col-md-6">
        <label for="order" class="form-label">Упорядочить:</label>
        <select id="order" class="form-select">
          <option value="asc">по возрастанию</option>
          <option value="desc">по убыванию</option>
        </select>
      </div>
    </div>
    <div class="row mb-3">
      <div class="col-md-6">
        <label for="size" class="form-label">Размер</label>
        <select id="size" class="form-select">
          <option value="">Выберите размер</option>
          <option value="38">38</option>
          <option value="40">40</option>
          <option value="42">42</option>
          <option value="44">44</option>
          <option value="46">46</option>
        </select>
      </div>
    </div>
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label d-block">Цвет</label>
        <div>
          <?php
            $colors = ['красный','синий','зеленый','лавандовый','мятный','телесный','голубой'];
            foreach ($colors as $color) {
              echo "<label class='color-label'><input class='form-check-input' type='checkbox' value='$color'> $color</label>";
            }
          ?>
        </div>
      </div>
    </div>
    <div class="row mb-4">
      <div class="col-md-6">
        <label for="dressType" class="form-label">Вид платья</label>
        <select id="dressType" class="form-select">
          <option value="">Выберите вид платья</option>
          <option value="ball">Бальное</option>
          <option value="evening">Вечернее</option>
          <option value="cocktail">Коктейльное</option>
          <option value="wedding">Свадебное</option>
        </select>
      </div>
    </div>
  </div>

  <section id="catalog">
    <h2>Каталог</h2>
    <div class="row" id="catalogItems"></div>
  </section>
</div>

<!-- Модальное окно выбора даты аренды -->
<div class="modal fade" id="rentModal" tabindex="-1" aria-labelledby="rentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="rentalForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rentModalLabel">Выберите даты аренды</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="rentDressId" name="dress_id" value="">
        <div class="mb-3">
          <label for="date_from" class="form-label">Дата начала</label>
          <input type="date" id="date_from" name="date_from" class="form-control" required min="<?= date('Y-m-d') ?>">
        </div>
        <div class="mb-3">
          <label for="date_to" class="form-label">Дата окончания</label>
          <input type="date" id="date_to" name="date_to" class="form-control" required min="<?= date('Y-m-d') ?>">
        </div>
        <div id="rentMessage"></div>

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
        <a href="login.php" class="btn btn-primary">Войти</a>
        <a href="register.php" class="btn btn-secondary ms-2">Регистрация</a>
      </div>
    </div>
  </div>
</div>

<script>
  const isLoggedIn = <?= $is_logged_in ? 'true' : 'false' ?>;
  let dresses = [];

  fetch('get_dresses.php')
    .then(res => res.json())
    .then(data => {
      dresses = data;
      loadDresses(sortDresses(dresses, 'asc'));
    })
    .catch(err => {
      console.error('Ошибка при загрузке платьев:', err);
    });

  function sortDresses(arr, order) {
    return arr.sort((a, b) => (order === 'asc' ? a.price_per_day - b.price_per_day : b.price_per_day - a.price_per_day));
  }

  function handleReserveClick(event, id) {
    event.stopPropagation();
    if (isLoggedIn) {
      document.getElementById('rentDressId').value = id;
      document.getElementById('rentMessage').textContent = '';
      document.getElementById('date_from').value = '';
      document.getElementById('date_to').value = '';
      document.getElementById('birth_date').value = '';
      document.getElementById('passport_series_number').value = '';
      document.getElementById('passport_issue_date').value = '';
      document.getElementById('passport_issued_by').value = '';
      document.getElementById('reason').value = '';
      const modal = new bootstrap.Modal(document.getElementById('rentModal'));
      modal.show();
    } else {
      const modal = new bootstrap.Modal(document.getElementById('authModal'));
      modal.show();
    }
  }

  function loadDresses(list) {
    const catalog = document.getElementById('catalogItems');
    catalog.innerHTML = '';
    list.forEach(dress => {
      const item = document.createElement('div');
      item.classList.add('col-md-4', 'mb-4');
      item.innerHTML = `
        <div class="catalog-item" onclick="window.location.href='dress.php?id=${dress.id}'">
          <img src="${dress.image_main}" alt="Платье ${dress.id}">
          <p>Размер: ${dress.size}</p>
          <p>Цвет: ${dress.color}</p>
          <p>Цена за сутки: ${dress.price_per_day} руб.</p>
          <button onclick="handleReserveClick(event, ${dress.id})">Зарезервировать</button>
        </div>
      `;
      catalog.appendChild(item);
    });
  }

  function filterDresses() {
    const size = document.getElementById('size').value;
    const type = document.getElementById('dressType').value;
    const order = document.getElementById('order').value;
    const selectedColors = Array.from(document.querySelectorAll('input[type="checkbox"]:checked')).map(cb => cb.value.toLowerCase());

    let filtered = dresses.filter(d => {
      return (!size || d.size === size) &&
             (!type || d.category_name === type) &&
             (selectedColors.length === 0 || selectedColors.includes(d.color.toLowerCase()));
    });

    filtered = sortDresses(filtered, order);
    loadDresses(filtered);
  }

  document.getElementById('size').addEventListener('change', filterDresses);
  document.getElementById('dressType').addEventListener('change', filterDresses);
  document.getElementById('order').addEventListener('change', filterDresses);
  document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.addEventListener('change', filterDresses));

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

    // Проверяем доступность
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
