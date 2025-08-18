<?php
session_start();

// Security check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: ../vehicles.php");
    exit();
}

require_once '../../config/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $car_id = intval($_POST['car_id'] ?? 0);
        $model = trim($_POST['model'] ?? '');
        $series = intval($_POST['series'] ?? 0);
        $engine = trim($_POST['engine'] ?? '');
        $power = intval($_POST['power'] ?? 0);
        $doors = trim($_POST['doors'] ?? '');
        $mph = trim($_POST['mph'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $stock = intval($_POST['stock'] ?? 0);

        // Validation
        if ($car_id <= 0) {
            $_SESSION['error'] = "Invalid vehicle ID.";
            header("Location: ../vehicles.php");
            exit();
        }

        if (empty($model) || $series <= 0 || empty($engine) || $power <= 0 || $price <= 0) {
            $_SESSION['error'] = "Please fill in all required fields.";
            header("Location: ../vehicles.php");
            exit();
        }

        // Handle image upload (optional for edit)
        $imageUpdate = "";
        $imageParams = [];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTemp = $_FILES['image']['tmp_name'];
            $fileName = basename($_FILES['image']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($fileExtension, $allowedExtensions)) {
                $_SESSION['error'] = "Invalid image format.";
                header("Location: ../vehicles.php");
                exit();
            }

            if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $_SESSION['error'] = "Image file is too large.";
                header("Location: ../vehicles.php");
                exit();
            }

            $newFileName = uniqid('car_', true) . '.' . $fileExtension;
            $uploadDir = "../../uploads/";
            $newFilePath = $uploadDir . $newFileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($fileTemp, $newFilePath)) {
                $imageUpdate = ", image_url = ?";
                $imageParams[] = $newFilePath;
            }
        }

        // Update query
        $query = "UPDATE cars SET series_id = ?, name = ?, price = ?, engine = ?, power_hp = ?, doors = ?, acceleration_0_60 = ?, description = ?, stock_quantity = ?" . $imageUpdate . " WHERE car_id = ?";

        $params = [$series, $model, $price, $engine, $power, $doors, $mph, $description, $stock];
        $params = array_merge($params, $imageParams);
        $params[] = $car_id;

        $stmt = $conn->prepare($query);
        $result = $stmt->execute($params);

        if ($result) {
            $_SESSION['message'] = "Vehicle updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update vehicle.";
        }
    } catch (Exception $e) {
        error_log("Error in update_vehicle.php: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred while updating the vehicle.";
    }
}

header("Location: ../vehicles.php");
exit();
