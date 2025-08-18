<?php
// check_auth.php - Include this at the top of protected pages

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection (adjust these values to match your setup)
$host = 'localhost';
$port = '3306';
$db_name = 'revgaragedb';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database Connection failed: " . $e->getMessage());
    die("A database connection error occurred.");
}

// Function to check remember me cookie and auto-login
function checkRememberMe($pdo)
{
    if (isset($_COOKIE['remember_user']) && !isset($_SESSION['logged_in'])) {
        $cookie_data = $_COOKIE['remember_user'];
        $parts = explode('|', $cookie_data);

        if (count($parts) === 2) {
            $user_id = $parts[0];
            $token = $parts[1];

            try {
                $stmt = $pdo->prepare("SELECT user_id, username, email, password, role FROM users WHERE user_id = :user_id");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();

                if ($stmt->rowCount() === 1) {
                    $user = $stmt->fetch();
                    $expected_token = hash('sha256', $user['email'] . $user['password'] . $_SERVER['REMOTE_ADDR']);

                    if (hash_equals($expected_token, $token)) {
                        // Valid remember me token, log the user in
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_username'] = $user['username'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['logged_in'] = true;
                        $_SESSION['remembered'] = true;

                        // Refresh the cookie for another 30 days
                        setcookie('remember_user', $cookie_data, time() + (86400 * 30), "/", "", false, true);

                        return true;
                    }
                }
            } catch (PDOException $e) {
                error_log("Remember Me Error: " . $e->getMessage());
            }
        }

        // Invalid cookie, remove it
        setcookie('remember_user', '', time() - 3600, "/");
    }
    return false;
}

// Check for remember me cookie
checkRememberMe($pdo);

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page
    header('Location: /pages/login.php');
    exit();
}
