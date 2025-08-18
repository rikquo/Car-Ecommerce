<?php
session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit();
}
require_once '../../config/dbcon.php';
if (isset($_GET['car_id']) && !empty($_GET['car_id'])) {
    $car_id = $_GET['car_id'];
    $sql = "DELETE FROM cars WHERE car_id = :car_id";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = 'Vehicle deleted successfully!';
        } else {
            $_SESSION['error'] = 'Vehicle not found.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'Invalid request. No vehicle ID provided.';
}
header('Location: ../vehicles.php');
exit();
