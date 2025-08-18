<?php
session_start();
header('Content-Type: application/json');

$response = ["success" => false, "message" => ""];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $response['message'] = "You must be logged in to submit a review.";
        echo json_encode($response);
        exit();
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "revgaragedb";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        $response['message'] = "Database connection failed.";
        echo json_encode($response);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $car_id = $_POST['car_id'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $review_text = $_POST['review_text'] ?? null;
    $is_approved = 0; // Reviews are not approved by default

    if ($car_id && $rating && $review_text) {
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, car_id, rating, review_text, is_approved) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisi", $user_id, $car_id, $rating, $review_text, $is_approved);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Review submitted successfully! It is pending approval.";
        } else {
            $response['message'] = "Error submitting review: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['message'] = "All fields are required.";
    }

    $conn->close();
} else {
    $response['message'] = "Invalid request method.";
}

echo json_encode($response);
