<?php
session_start();
require_once '../config/dbcon.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /pages/login.php?redirect=' . urlencode($_SERVER['HTTP_REFERER']));
    exit();
}

// Check if car_id is provided
if (!isset($_POST['car_id']) || empty($_POST['car_id'])) {
    header('Location: /pages/models.php?error=invalid_car');
    exit();
}

$car_id = (int)$_POST['car_id'];
$user_id = $_SESSION['user_id'];

try {
    // Check if car exists
    $car_check = $conn->prepare("SELECT car_id, name FROM cars WHERE car_id = ?");
    $car_check->execute([$car_id]);
    $car = $car_check->fetch();

    if (!$car) {
        header('Location: /pages/models.php?error=car_not_found');
        exit();
    }

    // Check if car is already in garage
    $garage_check = $conn->prepare("SELECT garage_item_id FROM garage_items WHERE user_id = ? AND car_id = ?");
    $garage_check->execute([$user_id, $car_id]);

    if ($garage_check->fetch()) {
        header('Location: /pages/models.php?message=already_in_garage');
        exit();
    }

    // Add car to garage
    $add_stmt = $conn->prepare("INSERT INTO garage_items (user_id, car_id, added_at) VALUES (?, ?, NOW())");
    $add_stmt->execute([$user_id, $car_id]);

    header('Location: /pages/models.php?message=added_to_garage&car_name=' . urlencode($car['name']));
    exit();
} catch (PDOException $e) {
    error_log("Add to garage error: " . $e->getMessage());
    header('Location: /pages/models.php?error=database_error');
    exit();
}
