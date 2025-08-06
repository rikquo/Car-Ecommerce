<?php
session_start();
// Security check: Ensure the user is a logged-in admin.
// if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
//     http_response_code(403); // Forbidden
//     echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
//     exit();
// }

// Include the database connection
// The path might need adjustment based on your file structure.
require_once '../../config/dbcon.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $model = $_POST['model'] ?? '';
    $series = $_POST['series'] ?? '';
    $engine = $_POST['engine'] ?? '';
    $power = $_POST['power'] ?? '';
    $doors = $_POST['doors'] ?? '';
    $mph = $_POST['mph'] ?? '';
    $status = $_POST['status'] ?? 'available';
    $price =    $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';
    $stock =  $_POST['stock'] ?? '';


    $isAssigned = [$model, $engine, $power, $series, $doors, $mph, $status, $price, $description, $stock];
    foreach ($isAssigned as $value) {
        if (empty($value)) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: ../vehicles.php");
            exit();
        }
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {


        // Size check

        $fileTemp = $_FILES['image']['tmp_name']; // php/temp/shoe.png

        $fileName = basename($_FILES['image']['name']); // shoe.png
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // PNG
        $newFileName = uniqid('car_', true) . '.' . $fileExtension; // car_123456789.png
        $filePath = "../../uploads/";

        $newFilePath = $filePath . $newFileName; // ../../uploads/car_model/car_123456789.png
        echo "FIle Here";
        move_uploaded_file($fileTemp, $newFilePath);
    } else {
        echo "Error";
    }

    $query = "INSERT INTO cars (series_id, name, price, image_url, engine, power_hp, doors, acceleration_0_60, description, stock_quantity) 
              VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $status = $stmt->execute([
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
    if ($status) {
        $_SESSION['success'] = "Vehicle added successfully!";
        header("Location: ../vehicles.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to add vehicle.";
        header("Location: ../vehicles.php");
        exit();
    }
} else {
    header("Location: ../vehicles.php");
    exit();
}



// header('Content-Type: application/json');

// // --- 1. Handle File Upload ---
// if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
//     $uploadDir = '../../assets/img/uploads/'; // The directory to store uploaded images
//     // Create the directory if it doesn't exist
//     if (!is_dir($uploadDir)) {
//         mkdir($uploadDir, 0755, true);
//     }

//     // Create a unique filename to prevent overwriting
//     $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
//     $uniqueFilename = uniqid('car_', true) . '.' . $fileExtension;
//     $uploadFile = $uploadDir . $uniqueFilename;

//     if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
//         // File is uploaded successfully. The path to store in DB:
//         $imageUrl = '/assets/img/uploads/' . $uniqueFilename;
//     } else {
//         echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
//         exit();
//     }
// } else {
//     echo json_encode(['status' => 'error', 'message' => 'Image upload failed or no image provided.']);
//     exit();
// }

// // --- 2. Get Data from POST Request ---
// $model = $_POST['model'] ?? '';
// $series_name = $_POST['series'] ?? '';
// $engine = $_POST['engine'] ?? '';
// $power = $_POST['power'] ?? '';
// $doors = $_POST['doors'] ?? '';
// $acceleration = $_POST['0-60mph'] ?? ''; // Note the key name matches the form
// $price = $_POST['price'] ?? 0;
// $description = $_POST['description'] ?? '';
// $stock_quantity = 1; // Defaulting to 1, adjust if you have a stock field in the form

// // --- 3. Get `series_id` from `series_name` ---
// // This is crucial for the foreign key constraint.
// try {
//     $stmt_series = $conn->prepare("SELECT series_id FROM car_series WHERE name = :series_name");
//     $stmt_series->bindParam(':series_name', $series_name);
//     $stmt_series->execute();
//     $series_result = $stmt_series->fetch(PDO::FETCH_ASSOC);

//     if (!$series_result) {
//         // If series doesn't exist, you could either add it or return an error.
//         // For now, let's return an error.
//         echo json_encode(['status' => 'error', 'message' => 'Invalid series specified.']);
//         exit();
//     }
//     $series_id = $series_result['series_id'];

// } catch (PDOException $e) {
//     echo json_encode(['status' => 'error', 'message' => 'Database error finding series: ' . $e->getMessage()]);
//     exit();
// }

// // --- 4. Prepare and Execute SQL INSERT Statement ---
// $sql = "INSERT INTO cars (series_id, name, price, image_url, engine, power_hp, doors, acceleration_0_60, description, stock_quantity) 
//         VALUES (:series_id, :name, :price, :image_url, :engine, :power_hp, :doors, :acceleration, :description, :stock_quantity)";

// try {
//     $stmt = $conn->prepare($sql);

//     // Bind parameters
//     $stmt->bindParam(':series_id', $series_id, PDO::PARAM_INT);
//     $stmt->bindParam(':name', $model);
//     $stmt->bindParam(':price', $price);
//     $stmt->bindParam(':image_url', $imageUrl);
//     $stmt->bindParam(':engine', $engine);
//     $stmt->bindParam(':power_hp', $power);
//     $stmt->bindParam(':doors', $doors);
//     $stmt->bindParam(':acceleration', $acceleration);
//     $stmt->bindParam(':description', $description);
//     $stmt->bindParam(':stock_quantity', $stock_quantity, PDO::PARAM_INT);

//     $stmt->execute();

//     // --- 5. Send Success Response ---
//     echo json_encode([
//         'status' => 'success', 
//         'message' => 'Vehicle added successfully!',
//         'vehicle_id' => $conn->lastInsertId()
//     ]);

// } catch (PDOException $e) {
//     // --- 6. Send Error Response ---
//     // In production, you might want to log this error instead of echoing it.
//     echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
// }
