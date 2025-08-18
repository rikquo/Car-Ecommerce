<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit();
}

require_once '../../config/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? '';
    $new_status = $_POST['status'] ?? '';
    $notes = $_POST['notes'] ?? '';

    // Validate input
    if (empty($order_id) || empty($new_status)) {
        $_SESSION['error'] = 'Order ID and status are required.';
        header('Location: /admin/orders.php');
        exit();
    }

    // Validate status - updated for your database ENUM
    $valid_statuses = ['Pending', 'Completed', 'Cancelled'];
    if (!in_array($new_status, $valid_statuses)) {
        $_SESSION['error'] = 'Invalid status selected.';
        header('Location: /admin/orders.php');
        exit();
    }

    try {
        // Update order status
        $sql = "UPDATE orders SET status = :status WHERE order_id = :order_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':status', $new_status);
        $stmt->bindParam(':order_id', $order_id);

        if ($stmt->execute()) {
            // Log the status change if notes were provided
            if (!empty($notes)) {
                $log_sql = "INSERT INTO order_logs (order_id, action, notes, created_at) VALUES (:order_id, :action, :notes, NOW())";
                $log_stmt = $conn->prepare($log_sql);
                $action = "Status changed to " . $new_status;
                $log_stmt->bindParam(':order_id', $order_id);
                $log_stmt->bindParam(':action', $action);
                $log_stmt->bindParam(':notes', $notes);
                $log_stmt->execute();
            }

            $_SESSION['message'] = 'Order status updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update order status.';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'Invalid request method.';
}

header('Location: /admin/orders.php');
exit();
