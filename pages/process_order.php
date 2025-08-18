<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /pages/login.php');
    exit();
}

require_once '../config/dbcon.php';
require_once '../includes/stock_manager.php';

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/garage.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get form data
$full_name = trim($_POST['full_name'] ?? '');
$address = trim($_POST['address'] ?? '');
$phone_number = trim($_POST['phone_number'] ?? '');
$payment_method = $_POST['payment_method'] ?? '';
$financing_option = !empty($_POST['financing_option']) ? (int)$_POST['financing_option'] : null;
$car_ids = $_POST['car_ids'] ?? []; // Array of car IDs to purchase

// Validate required fields
if (empty($full_name) || empty($address) || empty($phone_number) || empty($payment_method) || empty($car_ids)) {
    $_SESSION['error_message'] = "Please fill in all required fields.";
    header('Location: /pages/checkout.php');
    exit();
}

// Validate car IDs
if (!is_array($car_ids)) {
    $car_ids = [$car_ids];
}

try {
    // Start transaction
    $conn->beginTransaction();

    $total_amount = 0;
    $car_details = [];

    foreach ($car_ids as $car_id) {
        $car_id = (int)$car_id;

        // Check if car exists and get details
        $car_stmt = $conn->prepare("SELECT car_id, name, price, stock_quantity FROM cars WHERE car_id = ?");
        $car_stmt->execute([$car_id]);
        $car = $car_stmt->fetch();

        if (!$car) {
            throw new Exception("Car with ID $car_id not found.");
        }

        if (!checkStockAvailability($car_id, 1, $conn)) {
            throw new Exception("Sorry, {$car['name']} is currently out of stock.");
        }

        $car_details[] = $car;
        $total_amount += $car['price'];
    }

    // Create the order
    $order_stmt = $conn->prepare("
        INSERT INTO orders (user_id, full_name, address, phone_number, payment_method, financing_option, total_amount, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')
    ");

    $order_stmt->execute([
        $user_id,
        $full_name,
        $address,
        $phone_number,
        $payment_method,
        $financing_option,
        $total_amount
    ]);

    $order_id = $conn->lastInsertId();

    // Add order details and update stock
    $detail_stmt = $conn->prepare("
        INSERT INTO order_details (order_id, car_id, price_at_purchase) 
        VALUES (?, ?, ?)
    ");

    foreach ($car_details as $car) {
        // Add order detail
        $detail_stmt->execute([$order_id, $car['car_id'], $car['price']]);

        if (!updateCarStock($car['car_id'], 1, $conn)) {
            throw new Exception("Failed to update stock for {$car['name']}.");
        }
    }

    // Commit transaction
    $conn->commit();

    // Set success message
    $_SESSION['success_message'] = "Order placed successfully!";

    // Redirect to order success page
    header("Location: /pages/order_success.php?order_id=$order_id");
    exit();
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();

    // Set error message
    $_SESSION['error_message'] = "Order failed: " . $e->getMessage();

    // Redirect back to checkout
    header('Location: /pages/checkout.php');
    exit();
} catch (PDOException $e) {
    // Rollback transaction on database error
    $conn->rollback();

    // Log the error
    error_log("Database error in process_order.php: " . $e->getMessage());

    // Set generic error message
    $_SESSION['error_message'] = "A database error occurred. Please try again.";

    // Redirect back to checkout
    header('Location: /pages/checkout.php');
    exit();
}
