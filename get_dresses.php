<?php
include 'db.php';

$result = $conn->query("SELECT d.id, d.article, d.name, d.size, d.color, d.image_main, d.price_per_day, c.name AS category_name 
                        FROM dresses d 
                        LEFT JOIN categories c ON d.category_id = c.id");

$dresses = [];
while ($row = $result->fetch_assoc()) {
    $dresses[] = $row;
}

header('Content-Type: application/json');
echo json_encode($dresses, JSON_UNESCAPED_UNICODE);
?>
