<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /pages/login.php');
    exit();
}

require_once '../config/dbcon.php';
require_once 'stock_manager.php';

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle order cancellation
if (isset($_POST['cancel_order']) && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];

    try {
        // Verify the order belongs to the user and is cancellable
        $check_stmt = $conn->prepare("SELECT status FROM orders WHERE order_id = ? AND user_id = ?");
        $check_stmt->execute([$order_id, $user_id]);
        $order = $check_stmt->fetch();

        if (!$order) {
            $error = "Order not found or you don't have permission to cancel it.";
        } elseif ($order['status'] !== 'Pending') {
            $error = "Only pending orders can be cancelled.";
        } else {
            $conn->beginTransaction();

            if (!restoreStockForOrder($order_id, $conn)) {
                throw new Exception("Failed to restore stock quantities");
            }

            // Update order status to cancelled
            $cancel_stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE order_id = ? AND user_id = ?");
            $cancel_stmt->execute([$order_id, $user_id]);

            $conn->commit();
            $message = "Order has been successfully cancelled and stock has been restored.";
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $error = "Error cancelling order: " . $e->getMessage();
    } catch (PDOException $e) {
        $conn->rollBack();
        $error = "Error cancelling order. Please try again.";
    }
}

// Redirect back to orders page with message
if ($message) {
    header('Location: /pages/orders.php?message=' . urlencode($message));
} elseif ($error) {
    header('Location: /pages/orders.php?error=' . urlencode($error));
} else {
    header('Location: /pages/orders.php');
}
exit();
