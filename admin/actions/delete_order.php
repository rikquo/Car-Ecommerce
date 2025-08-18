<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit();
}

require_once '../../config/dbcon.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    try {

        $check_sql = "SELECT status FROM orders WHERE order_id = :order_id";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bindParam(':order_id', $order_id);
        $check_stmt->execute();
        $order = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            $_SESSION['error'] = 'Order not found.';
        } elseif ($order['status'] !== 'Cancelled') {
            $_SESSION['error'] = 'Only cancelled orders can be deleted.';
        } else {

            $delete_logs_sql = "DELETE FROM order_logs WHERE order_id = :order_id";
            $delete_logs_stmt = $conn->prepare($delete_logs_sql);
            $delete_logs_stmt->bindParam(':order_id', $order_id);
            $delete_logs_stmt->execute();

            $delete_sql = "DELETE FROM orders WHERE order_id = :order_id";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bindParam(':order_id', $order_id);

            if ($delete_stmt->execute()) {
                $_SESSION['message'] = 'Order deleted successfully.';
            } else {
                $_SESSION['error'] = 'Failed to delete order.';
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'Order ID not provided.';
}

header('Location: /admin/orders.php');
exit();
