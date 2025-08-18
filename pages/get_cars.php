<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "revgaragedb";

$response = array();

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Fetch all cars for the dropdown
    $query = "SELECT car_id, name, series FROM cars ORDER BY series, name";
    $result = $conn->query($query);

    $cars = array();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cars[] = $row;
        }
    }

    $response['success'] = true;
    $response['cars'] = $cars;

    $conn->close();
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    $response['cars'] = array();
}

echo json_encode($response);
