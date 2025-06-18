<!-- header.php -->
<header>
  <h1>Love Stories Dress Rental</h1>
  <nav>
    <ul>
      <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">О нас</a></li>
      <li><a href="rentaldress.php" class="<?= basename($_SERVER['PHP_SELF']) === 'rentaldress.php' ? 'active' : '' ?>">Каталог</a></li>
      <li><a href="conditionofrental.php" class="<?= basename($_SERVER['PHP_SELF']) === 'conditionofrental.php' ? 'active' : '' ?>">Условия проката</a></li>
      <li><a href="aboutus.php" class="<?= basename($_SERVER['PHP_SELF']) === 'aboutus.php' ? 'active' : '' ?>">Где нас найти?</a></li>
      <li><a href="registration.php" class="<?= basename($_SERVER['PHP_SELF']) === 'registration.php' ? 'active' : '' ?>">Регистрация</a></li>
      <li><a href="exit.php" class="<?= basename($_SERVER['PHP_SELF']) === 'exit.php' ? 'active' : '' ?>">Вход</a></li>
      <li><a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>">Личный кабинет</a></li>
    </ul>
  </nav>
</header>
