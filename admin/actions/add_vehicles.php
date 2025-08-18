<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access.";
    header("Location: ../vehicles.php");
    exit();
}

require_once '../../config/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {

        $model = trim($_POST['model'] ?? '');
        $series = intval($_POST['series'] ?? 0);
        $engine = trim($_POST['engine'] ?? '');
        $power = intval($_POST['power'] ?? 0);
        $doors = trim($_POST['doors'] ?? '');
        $mph = trim($_POST['mph'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $stock = intval($_POST['stock'] ?? 0);


        if (empty($model)) {
            $_SESSION['error'] = "Model name is required.";
            header("Location: ../vehicles.php");
            exit();
        }

        if ($series <= 0 || $series > 3) {
            $_SESSION['error'] = "Please select a valid series.";
            header("Location: ../vehicles.php");
            exit();
        }

        if (empty($engine)) {
            $_SESSION['error'] = "Engine information is required.";
            header("Location: ../vehicles.php");
            exit();
        }

        if ($power <= 0) {
            $_SESSION['error'] = "Power must be greater than 0.";
            header("Location: ../vehicles.php");
            exit();
        }

        if ($price <= 0) {
            $_SESSION['error'] = "Price must be greater than 0.";
            header("Location: ../vehicles.php");
            exit();
        }

        if ($stock < 0) {
            $_SESSION['error'] = "Stock quantity cannot be negative.";
            header("Location: ../vehicles.php");
            exit();
        }


        $newFilePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTemp = $_FILES['image']['tmp_name'];
            $fileName = basename($_FILES['image']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));


            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($fileExtension, $allowedExtensions)) {
                $_SESSION['error'] = "Invalid image format. Please use JPG, PNG, GIF, or WebP.";
                header("Location: ../vehicles.php");
                exit();
            }


            if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $_SESSION['error'] = "Image file is too large. Maximum size is 5MB.";
                header("Location: ../vehicles.php");
                exit();
            }


            $newFileName = uniqid('car_', true) . '.' . $fileExtension;


            $uploadDir = "../../uploads/";


            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $_SESSION['error'] = "Failed to create upload directory.";
                    header("Location: ../vehicles.php");
                    exit();
                }
            }

            $newFilePath = $uploadDir . $newFileName;


            if (!move_uploaded_file($fileTemp, $newFilePath)) {
                $_SESSION['error'] = "Failed to upload image.";
                header("Location: ../vehicles.php");
                exit();
            }
        } else {

            switch ($_FILES['image']['error']) {
                case UPLOAD_ERR_NO_FILE:
                    $_SESSION['error'] = "No image file was uploaded.";
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $_SESSION['error'] = "Image file is too large.";
                    break;
                default:
                    $_SESSION['error'] = "Image upload failed.";
                    break;
            }
            header("Location: ../vehicles.php");
            exit();
        }


        $query = "INSERT INTO cars (series_id, name, price, image_url, engine, power_hp, doors, acceleration_0_60, description, stock_quantity) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            $series,
            $model,
            $price,
            $newFilePath,
            $engine,
            $power,
            $doors,
            $mph,
            $description,
            $stock
        ]);

        if ($result) {
            $_SESSION['message'] = "Vehicle added successfully!";
            header("Location: ../vehicles.php");
            exit();
        } else {

            if (file_exists($newFilePath)) {
                unlink($newFilePath);
            }
            $_SESSION['error'] = "Failed to add vehicle to database.";
            header("Location: ../vehicles.php");
            exit();
        }
    } catch (PDOException $e) {

        if (isset($newFilePath) && file_exists($newFilePath)) {
            unlink($newFilePath);
        }

        error_log("Database error in add_vehicles.php: " . $e->getMessage());
        $_SESSION['error'] = "Database error occurred while adding vehicle.";
        header("Location: ../vehicles.php");
        exit();
    } catch (Exception $e) {

        if (isset($newFilePath) && file_exists($newFilePath)) {
            unlink($newFilePath);
        }

        error_log("Error in add_vehicles.php: " . $e->getMessage());
        $_SESSION['error'] = "An unexpected error occurred.";
        header("Location: ../vehicles.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../vehicles.php");
    exit();
}
