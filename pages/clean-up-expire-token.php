<?php
$host = 'localhost';
$port = '3306';
$db_name = 'revgaragedb';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Delete expired tokens
    $stmt = $pdo->prepare("DELETE FROM password_reset_tokens WHERE expires_at < NOW()");
    $stmt->execute();

    $deleted_count = $stmt->rowCount();
    echo "Cleaned up {$deleted_count} expired password reset tokens.\n";
} catch (PDOException $e) {
    error_log("Token Cleanup Error: " . $e->getMessage());
    echo "Error cleaning up tokens: " . $e->getMessage() . "\n";
}
