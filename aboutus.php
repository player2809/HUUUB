<!DOCTYPE html> 
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Luxury Dress Rental - Где нас найти?</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
      color: #333;
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
      padding: 20px;
    }

    h1, h2 {
      text-align: center;
    }

    section {
      padding: 20px;
      text-align: center;
    }

    .image-frame {
      width: 100%;
      max-width: 900px;
      height: auto;
      border: 5px solid #ddd;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      background-color: #f8f8f8;
    }

    .image-frame img {
      width: 100%;
      height: auto;
      object-fit: cover;
    }

    @media (max-width: 768px) {
      .image-frame img {
        max-height: 400px;
      }
    }

    @media (max-width: 320px) {
      .image-frame img {
        max-height: 300px;
      }
    }
  </style>
</head>
<body>

  <?php include 'header.php'; ?>

  <section>
    <h2>Где нас найти?</h2>
    <p><strong>Адрес:</strong> г. Ижевск, ул. Удмуртская, д. 255</p>
    <p><strong>Телефон:</strong> +7 (900) 123-45-67</p>
    <p><strong>Email:</strong> info@luxurydress.com</p>
  </section>

  <div class="container">
    <div class="image-frame">
      <img src="images/map.png" alt="Карта с местоположением">
    </div>
  </div>

</body>
</html>
