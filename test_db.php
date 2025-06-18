<?php
$conn = new mysqli('localhost', 'root', 'root', '6');
if ($conn->connect_error) {
    die("❌ Ошибка подключения: " . $conn->connect_error);
}
echo "✅ Успешное подключение!";
?>
