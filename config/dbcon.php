<?php
$server = "localhost";
$user = "root";
$password = "";
$database = "revgaragedb";
$port = 3306;
$dsn = "mysql:host=$server;port=$port; dbname=$database";
try {
    $conn = new PDO($dsn, $user, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // echo "Connection has been made";
} catch (PDOException $e) {
    echo $e->getMessage();
}
