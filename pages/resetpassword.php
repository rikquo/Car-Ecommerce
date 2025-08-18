<?php
// resetpassword.php
session_start();

// Database Connection Configuration
$host = 'localhost';
$port = '3306';
$db_name = 'revgaragedb';
$username = 'root';
$password = '';

$pdo = null;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database Connection failed: " . $e->getMessage());
    die("A database connection error occurred. Please try again later.");
}

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
$message = '';
$message_type = '';
$valid_token = false;
$user_data = null;
$token_data = null;

// Validate token
if (!empty($token) && !empty($email)) {
    try {
        // Get the token for this specific email
        $stmt = $pdo->prepare("
            SELECT prt.*, u.username, u.email 
            FROM password_reset_tokens prt 
            JOIN users u ON prt.user_id = u.user_id 
            WHERE prt.expires_at > NOW()
            AND prt.email = :email
            ORDER BY prt.expires_at DESC
            LIMIT 1
        ");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $token_data = $stmt->fetch();

        if ($token_data && password_verify($token, $token_data['token'])) {
            $valid_token = true;
            $user_data = $token_data;
        } else {
            $message = "Invalid or expired reset token. Please request a new password reset.";
            $message_type = "error";
        }
    } catch (PDOException $e) {
        error_log("Token Validation Error: " . $e->getMessage());
        $message = "A database error occurred. Please try again later.";
        $message_type = "error";
    }
} else {
    $message = "Invalid reset link. Please use the link from your email.";
    $message_type = "error";
}

// Handle password reset form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    if (empty($new_password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        try {
            // Update user password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
            $update_stmt->bindParam(':password', $hashed_password);
            $update_stmt->bindParam(':user_id', $user_data['user_id']);
            $update_stmt->execute();

            // Delete all tokens for this user
            $delete_all_stmt = $pdo->prepare("DELETE FROM password_reset_tokens WHERE user_id = :user_id");
            $delete_all_stmt->bindParam(':user_id', $user_data['user_id']);
            $delete_all_stmt->execute();

            $message = "Password reset successful! You can now log in with your new password.";
            $message_type = "success";
            $valid_token = false;
        } catch (PDOException $e) {
            error_log("Password Update Error: " . $e->getMessage());
            $message = "A database error occurred. Please try again later.";
            $message_type = "error";
        }
    } else {
        $message = implode(" ", $errors);
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<!-- Rest of your HTML remains the same -->


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Rev Garage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="stylesheet" href="/assets/css/login.css">
    <style>
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .password-requirements {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            font-size: 14px;
        }

        .password-requirements h4 {
            margin: 0 0 10px 0;
            color: #495057;
        }

        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
            color: #6c757d;
        }

        .password-requirements li {
            margin: 5px 0;
        }

        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px;
        }

        .password-toggle:hover {
            color: #4caf50;
        }

        .login-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #4caf50;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
            margin-top: 15px;
        }

        .login-link:hover {
            background: #45a049;
            color: white;
        }
    </style>
</head>

<body>
    <?php include('../includes/navbar.php'); ?>

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
                    <h1 class="auth-title">Reset Password</h1>
                    <?php if ($valid_token && $user_data): ?>
                        <p class="auth-subtitle">Create a new password for <?php echo htmlspecialchars($user_data['email']); ?></p>
                    <?php else: ?>
                        <p class="auth-subtitle">Set up your new password</p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                        <?php if ($message_type === 'success'): ?>
                            <div style="margin-top: 15px;">
                                <a href="login.php" class="login-link">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Go to Login
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($valid_token): ?>
                    <form class="auth-form" method="POST">
                        <div class="password-requirements">
                            <h4>Password Requirements:</h4>
                            <ul>
                                <li>At least 8 characters long</li>
                                <li>Mix of letters and numbers recommended</li>
                                <li>Avoid using personal information</li>
                            </ul>
                        </div>

                        <div class="form-group password-group">
                            <input
                                type="password"
                                class="form-input"
                                id="password"
                                name="password"
                                placeholder="New Password"
                                required
                                autocomplete="new-password"
                                minlength="8">
                            <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>

                        <div class="form-group password-group">
                            <input
                                type="password"
                                class="form-input"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="Confirm New Password"
                                required
                                autocomplete="new-password"
                                minlength="8">
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', this)">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>

                        <button type="submit" class="auth-submit">
                            <i class="fas fa-key"></i>
                            Reset Password
                        </button>
                    </form>
                <?php elseif (empty($message) || $message_type === 'error'): ?>
                    <div style="text-align: center; padding: 20px;">
                        <p>Need a new reset link?</p>
                        <a href="forgetpassword.php" class="login-link">
                            <i class="fas fa-envelope"></i>
                            Request Password Reset
                        </a>
                    </div>
                <?php endif; ?>

                <div class="auth-switch">
                    <p class="switch-text">Remember your password?</p>
                    <a href="login.php" class="switch-btn" style="text-decoration: none;">
                        Sign In
                    </a>
                </div>
            </div>
        </div>
    </main>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fa-regular fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fa-regular fa-eye';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');

            function validatePasswords() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }

            if (password && confirmPassword) {
                password.addEventListener('input', validatePasswords);
                confirmPassword.addEventListener('input', validatePasswords);
            }
        });
    </script>
</body>

</html>