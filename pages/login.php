<?php
// login.php

// Start a session at the very beginning of the script
session_start();

// --- Database Connection Configuration ---
$host = 'localhost'; // Your database host
$port = '3306'; // Your MySQL port (as specified)
$db_name = 'revgaragedb'; // The database name you created
$username = 'root'; // Default XAMPP username
$password = ''; // Default XAMPP password (empty)

$pdo = null; // Initialize PDO object

try {
    // Create a new PDO instance with the specified port
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8", $username, $password);

    // Set the PDO error mode to exception for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database Connection failed: " . $e->getMessage());
    die("A database connection error occurred. Please try again later.");
}

// --- Handle AJAX POST Requests ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set content type to JSON for AJAX responses
    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
        $data = $_POST;
    }

    $action = $data['action'] ?? '';

    if ($action === 'login') {
        // --- Login Logic ---
        $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $data['password'] ?? '';
        $remember_me = filter_var($data['rememberMe'] ?? '', FILTER_VALIDATE_BOOLEAN);

        $errors = [];
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (empty($password)) {
            $errors[] = "Password is required.";
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => implode(" ", $errors)]);
            exit();
        }

        try {
            $stmt = $pdo->prepare("SELECT user_id, username, email, password, role FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();

                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['logged_in'] = true;

                    if ($remember_me) {
                        $cookie_name = "remember_user";
                        $cookie_value = $user['user_id'] . '|' . hash('sha256', $user['email'] . $user['password'] . $_SERVER['REMOTE_ADDR']);
                        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
                    }

                    // --- Updated: Role-based Redirection URL ---
                    if ($user['role'] === 'admin') {
                        $redirect_url = '/admin/adminhome.php';
                    } else {
                        $redirect_url = 'home.php';
                    }

                    echo json_encode(['success' => true, 'message' => 'Login successful! Redirecting...', 'redirect' => $redirect_url]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'No account found with that email.']);
            }
        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'A database error occurred during login.']);
        }
        exit();

    } elseif ($action === 'signup') {
        // --- Signup Logic (remains unchanged) ---
        $username = filter_var($data['fullname'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $data['password'] ?? '';
        $confirm_password = $data['confirmPassword'] ?? '';
        $agree_terms = filter_var($data['agreeTerms'] ?? '', FILTER_VALIDATE_BOOLEAN);

        $errors = [];
        if (empty($username)) {
            $errors[] = "Full name is required.";
        }
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (empty($password)) {
            $errors[] = "Password is required.";
        } elseif (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }
        if (!$agree_terms) {
            $errors[] = "You must agree to the Terms & Conditions.";
        }

        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => implode(" ", $errors)]);
            exit();
        }

        try {
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email OR username = :username");
            $stmt->execute(['email' => $email, 'username' => $username]);
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'Email or Username already registered.']);
                exit();
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error creating account. Please try again.']);
            }
        } catch (PDOException $e) {
            error_log("Signup Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'A database error occurred during signup.']);
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Rev Garage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="/assets/css/home.css" />
    <link rel="stylesheet" href="/assets/css/login.css" />
</head>

<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollTrigger.min.js" crossorigin="anonymous"></script>

    <?php
    // Conditional inclusion of the navbar based on session
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        if ($_SESSION['user_role'] === 'admin') {
            // Redirect if an admin is on the login page after being logged in
            header('Location: /admin/adminhome.php');
            exit();
        } else {
            // User is logged in, include user-specific navbar
            include('../includes/user_nav.php');
        }
    } else {
        // User is not logged in, include default navbar
        include('../includes/navbar.php');
    }
    ?>

    <main class="auth-main">
        <div class="auth-bg-elements">
            <div class="bg-circle bg-circle-1"></div>
            <div class="bg-circle bg-circle-2"></div>
            <div class="bg-circle bg-circle-3"></div>
            <div class="bg-line bg-line-1"></div>
            <div class="bg-line bg-line-2"></div>
        </div>

        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h1 class="auth-title">Welcome Back</h1>
                    <p class="auth-subtitle">Sign in to your Rev Garage account</p>
                </div>

                <form class="auth-form" id="loginForm">
                    <input type="hidden" name="action" value="login" />
                    <div class="form-group">
                        <input type="email" class="form-input" placeholder="Email Address" required autocomplete="email" name="email" />
                    </div>
                    <div class="form-group password-group">
                        <input type="password" class="form-input" id="loginPassword" placeholder="Password" required autocomplete="current-password" name="password" />
                        <button type="button" class="password-toggle" onclick="togglePassword('loginPassword', this)">
                            <i class="fa-regular fa-eye"></i>
                            <i class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                    <div class="checkbox-group">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="rememberMe" name="rememberMe" />
                            <span class="checkbox-mark"></span>
                        </div>
                        <label for="rememberMe" class="checkbox-label">Remember me</label>
                    </div>
                    <button type="submit" class="auth-submit">Sign In</button>
                    <div class="forgot-password">
                        <a href="#" class="forgot-link">Forgot your password?</a>
                    </div>
                </form>

                <form class="auth-form hidden" id="signupForm">
                    <input type="hidden" name="action" value="signup" />
                    <div class="form-group">
                        <input type="text" class="form-input" placeholder="Full Name" required autocomplete="name" name="fullname" />
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-input" placeholder="Email Address" required autocomplete="email" name="email" />
                    </div>
                    <div class="form-group password-group">
                        <input type="password" class="form-input" id="signupPassword" placeholder="Password (min. 8 characters)" required autocomplete="new-password" name="password" />
                        <button type="button" class="password-toggle" onclick="togglePassword('signupPassword', this)">
                            <i class="fa-regular fa-eye"></i>
                            <i class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                    <div class="form-group password-group">
                        <input type="password" class="form-input" id="confirmPassword" placeholder="Confirm Password" required autocomplete="new-password" name="confirmPassword" />
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', this)">
                            <i class="fa-regular fa-eye"></i>
                            <i class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                    <div class="checkbox-group">
                        <div class="custom-checkbox">
                            <input type="checkbox" id="agreeTerms" required name="agreeTerms" />
                            <span class="checkbox-mark"></span>
                        </div>
                        <label for="agreeTerms" class="checkbox-label">
                            I agree to the <a href="#" style="color: #4caf50;">Terms & Conditions</a>
                        </label>
                    </div>
                    <button type="submit" class="auth-submit">Create Account</button>
                </form>
                <div class="social-login">
                    <div class="social-divider">
                        <span>or continue with</span>
                    </div>
                    <div class="social-buttons">
                        <button type="button" class="social-btn" onclick="handleGoogleLogin()">
                            Google
                        </button>
                        <button type="button" class="social-btn" onclick="handleFacebookLogin()">
                            Facebook
                        </button>
                    </div>
                </div>

                <div class="auth-switch">
                    <p class="switch-text">Don't have an account?</p>
                    <button type="button" class="switch-btn" onclick="switchForm()">
                        Sign Up
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script src="/assets/js/login.js"></script>
</body>

</html>