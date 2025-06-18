<?php
$host = 'localhost';
$user = 'root';
$pass = 'root';
$db   = '6';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Ошибка подключения к БД: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>
