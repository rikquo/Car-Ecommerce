<?php

/**
 * Stock Management Functions
 * Handles stock updates when orders are placed
 */

require_once '../config/dbcon.php';

/**
 * Update stock quantity for a car when an order is placed
 * @param int $car_id - The ID of the car
 * @param int $quantity - The quantity to decrease (default 1)
 * @param PDO $conn - Database connection
 * @return bool - True if successful, false otherwise
 */
function updateCarStock($car_id, $quantity = 1, $conn)
{
    try {
        // First check current stock
        $check_stmt = $conn->prepare("SELECT stock_quantity FROM cars WHERE car_id = ?");
        $check_stmt->execute([$car_id]);
        $current_stock = $check_stmt->fetchColumn();

        if ($current_stock === false) {
            error_log("Car ID $car_id not found");
            return false;
        }

        if ($current_stock < $quantity) {
            error_log("Insufficient stock for car ID $car_id. Current: $current_stock, Requested: $quantity");
            return false;
        }

        // Update stock quantity
        $update_stmt = $conn->prepare("UPDATE cars SET stock_quantity = stock_quantity - ? WHERE car_id = ?");
        $result = $update_stmt->execute([$quantity, $car_id]);

        if ($result) {
            error_log("Stock updated for car ID $car_id. Decreased by $quantity");
            return true;
        } else {
            error_log("Failed to update stock for car ID $car_id");
            return false;
        }
    } catch (PDOException $e) {
        error_log("Database error in updateCarStock: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if sufficient stock is available for a car
 * @param int $car_id - The ID of the car
 * @param int $quantity - The quantity to check (default 1)
 * @param PDO $conn - Database connection
 * @return bool - True if sufficient stock, false otherwise
 */
function checkStockAvailability($car_id, $quantity = 1, $conn)
{
    try {
        $stmt = $conn->prepare("SELECT stock_quantity FROM cars WHERE car_id = ?");
        $stmt->execute([$car_id]);
        $current_stock = $stmt->fetchColumn();

        return ($current_stock !== false && $current_stock >= $quantity);
    } catch (PDOException $e) {
        error_log("Database error in checkStockAvailability: " . $e->getMessage());
        return false;
    }
}

/**
 * Get current stock quantity for a car
 * @param int $car_id - The ID of the car
 * @param PDO $conn - Database connection
 * @return int|false - Stock quantity or false if not found
 */
function getCurrentStock($car_id, $conn)
{
    try {
        $stmt = $conn->prepare("SELECT stock_quantity FROM cars WHERE car_id = ?");
        $stmt->execute([$car_id]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Database error in getCurrentStock: " . $e->getMessage());
        return false;
    }
}

/**
 * Restore stock when an order is cancelled
 * @param int $order_id - The order ID to restore stock for
 * @param PDO $conn - Database connection
 * @return bool - True if successful, false otherwise
 */
function restoreStockForOrder($order_id, $conn)
{
    try {
        // Get all items in the cancelled order
        $stmt = $conn->prepare("SELECT car_id FROM order_details WHERE order_id = ?");
        $stmt->execute([$order_id]);
        $order_items = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $success = true;
        foreach ($order_items as $car_id) {
            // Restore stock by adding 1 back
            $update_stmt = $conn->prepare("UPDATE cars SET stock_quantity = stock_quantity + 1 WHERE car_id = ?");
            if (!$update_stmt->execute([$car_id])) {
                $success = false;
                error_log("Failed to restore stock for car ID $car_id in order $order_id");
            }
        }

        return $success;
    } catch (PDOException $e) {
        error_log("Database error in restoreStockForOrder: " . $e->getMessage());
        return false;
    }
}
