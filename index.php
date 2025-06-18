<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Luxury Dress Rental - О нас</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f8f9fa; }
    header { background: #333; color: white; padding: 10px 20px; }
    header h1 { margin: 0; }
    nav ul { list-style: none; margin: 0; padding: 0; display: flex; gap: 15px; }
    nav a { color: white; text-decoration: none; }
    .logo-container { position: relative; text-align: center; }
    .logo-image { width: 100%; max-height: 400px; object-fit: cover; }
    .slogan-overlay {
      position: absolute; top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      color: white; font-size: 2rem; background: rgba(0, 0, 0, 0.5); padding: 10px 20px; border-radius: 8px;
    }

    .reviews { padding: 40px 20px; background: #fff; text-align: center; }
    .reviews h2 { font-size: 2em; margin-bottom: 30px; }

    .slider-container {
      position: relative;
      max-width: 600px;
      margin: 0 auto;
      min-height: 180px;
    }

    .review-slide {
      display: none;
      animation: fadeIn 1s ease-in-out;
    }

    .review-slide.active { display: block; }

    .avatar {
      width: 60px; height: 60px;
      background: #6c757d;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 20px;
      margin: 0 auto 10px;
    }

    .review-text {
      font-style: italic;
      margin: 10px 0;
    }

    .date {
      color: #888;
      font-size: 0.9em;
    }

    .slider-buttons {
      margin-top: 20px;
    }

    .slider-buttons button {
      background-color: #333;
      color: white;
      border: none;
      padding: 8px 16px;
      margin: 0 5px;
      cursor: pointer;
      border-radius: 5px;
    }

    .slider-buttons button:hover {
      background-color: #555;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* hero section */
    .hero-section {
      position: relative;
      width: 100%;
      height: 80vh;
      background: url('images/SOFA.png') no-repeat center center / cover;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .hero-overlay {
      background: rgba(0, 0, 0, 0.55);
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: fadeIn 2s ease;
    }

    .hero-title {
      color: #fff;
      font-size: 3rem;
      text-align: center;
      font-family: 'Georgia', serif;
      font-weight: bold;
      text-shadow: 2px 2px 5px rgba(0,0,0,0.7);
      line-height: 1.3;
      animation: slideUp 1.5s ease-out;
    }

    @keyframes slideUp {
      from { transform: translateY(30px); opacity: 0; }
      to   { transform: translateY(0); opacity: 1; }
    }

    @media (max-width: 768px) {
      .hero-title {
        font-size: 2rem;
        padding: 0 20px;
      }
    }
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="hero-section">
  <div class="hero-overlay">
    <h1 class="hero-title">Открой свой<br>бесконечный гардероб</h1>
  </div>
</section>

<section class="reviews">
  <h2>Отзывы наших клиентов</h2>
  <div class="slider-container">
    <?php
    include 'db.php';
    $result = $conn->query("SELECT user_name, text, created_at FROM reviews WHERE status = 'Подтвержден' ORDER BY RAND() LIMIT 5");
    $index = 0;
    while ($row = $result->fetch_assoc()):
      $activeClass = $index === 0 ? 'active' : '';
      $initials = mb_substr($row['user_name'], 0, 1, 'UTF-8');
    ?>
      <div class="review-slide <?= $activeClass ?>">
        <div class="avatar"><?= htmlspecialchars($initials) ?></div>
        <p class="name"><?= htmlspecialchars($row['user_name']) ?></p>
        <p class="review-text">“<?= nl2br(htmlspecialchars($row['text'])) ?>”</p>
        <p class="date"><?= date('d.m.Y', strtotime($row['created_at'])) ?></p>
      </div>
    <?php $index++; endwhile; ?>
  </div>

  <div class="slider-buttons">
    <button onclick="prevSlide()">Предыдущий</button>
    <button onclick="nextSlide()">Следующий</button>
  </div>
</section>

<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.review-slide');

function showSlide(index) {
  slides.forEach((slide, i) => {
    slide.classList.remove('active');
    if (i === index) slide.classList.add('active');
  });
}

function nextSlide() {
  currentSlide = (currentSlide + 1) % slides.length;
  showSlide(currentSlide);
}

function prevSlide() {
  currentSlide = (currentSlide - 1 + slides.length) % slides.length;
  showSlide(currentSlide);
}

setInterval(nextSlide, 7000);
showSlide(currentSlide);
</script>

</body>
</html>
