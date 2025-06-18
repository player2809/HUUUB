<!-- Модальное окно авторизации -->
<div class="modal fade" id="authModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Требуется авторизация</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Для бронирования необходимо войти в аккаунт.</p>
        <a href="exit.php" class="btn btn-primary">Войти</a>
        <a href="registration.php" class="btn btn-outline-secondary">Зарегистрироваться</a>
      </div>
    </div>
  </div>
</div>

<!-- Модальное окно выбора даты -->
<div class="modal fade" id="rentModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <form id="rentalForm">
        <div class="modal-header">
          <h5 class="modal-title">Выбор даты аренды</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="rentDressId" name="rentDressId">
          <div class="mb-3">
            <label for="date_from" class="form-label">С:</label>
            <input type="date" id="date_from" name="date_from" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="date_to" class="form-label">По:</label>
            <input type="date" id="date_to" name="date_to" class="form-control" required>
          </div>
          <p id="rentMessage" class="text-danger mt-2"></p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Проверить и перейти к брони</button>
        </div>
      </form>
    </div>
  </div>
</div>
