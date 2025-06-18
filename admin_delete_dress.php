<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.html");
    exit;
}
include 'db.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $conn->query("DELETE FROM dresses WHERE id = $id");
}
header("Location: admin_dresses.php");
exit;
