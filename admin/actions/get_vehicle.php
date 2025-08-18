<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Include database connection
require_once '../../config/dbcon.php';

// Check if car_id is provided
if (!isset($_GET['car_id']) || empty($_GET['car_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vehicle ID is required']);
    exit();
}

$car_id = filter_input(INPUT_GET, 'car_id', FILTER_SANITIZE_NUMBER_INT);

try {
    // Fetch vehicle data
    $query = "SELECT car_id, name, series_id, engine, power_hp, doors, acceleration_0_60, price, description, stock_quantity, image_url FROM cars WHERE car_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$car_id]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vehicle) {
        // Map database fields to expected frontend fields
        $vehicle['power'] = $vehicle['power_hp'];
        $vehicle['mph'] = $vehicle['acceleration_0_60'];

        echo json_encode([
            'success' => true,
            'vehicle' => $vehicle
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Vehicle not found'
        ]);
    }
} catch (PDOException $e) {
    error_log("Database error in get_vehicle.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
