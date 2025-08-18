<?php
// forgetpassword.php
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

// Include PHPMailer
require_once '../src/PHPMailer.php';
require_once '../src/SMTP.php';
require_once '../src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

    if (empty($email)) {
        $message = "Email is required.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $message_type = "error";
    } else {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT user_id, username, email FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();

                // Generate secure token
                $token = bin2hex(random_bytes(32));
                $hashed_token = password_hash($token, PASSWORD_DEFAULT);
                $expires_at = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now

                // Delete any existing tokens for this user
                $delete_stmt = $pdo->prepare("DELETE FROM password_reset_tokens WHERE user_id = :user_id");
                $delete_stmt->bindParam(':user_id', $user['user_id']);
                $delete_stmt->execute();

                // Insert new hashed token
                $insert_stmt = $pdo->prepare("INSERT INTO password_reset_tokens (user_id, email, token, expires_at) VALUES (:user_id, :email, :token, :expires_at)");
                $insert_stmt->bindParam(':user_id', $user['user_id']);
                $insert_stmt->bindParam(':email', $email);
                $insert_stmt->bindParam(':token', $hashed_token);
                $insert_stmt->bindParam(':expires_at', $expires_at);
                $insert_stmt->execute();

                // Send email
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'yukiriko83@gmail.com';
                    $mail->Password   = 'uyup pene lies cuyl';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // Recipients
                    $mail->setFrom('yukiriko83@gmail.com', 'Rev Garage');
                    $mail->addAddress($email, $user['username']);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request - Rev Garage';

                    // Include both token and email in the reset link
                    $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/pages/resetpassword.php?token=" . urlencode($token) . "&email=" . urlencode($email);

                    $mail->Body = "
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .header { background: linear-gradient(135deg, #4caf50, #45a049); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                            .button { display: inline-block; background: #4caf50; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <h1>Rev Garage</h1>
                                <h2>Password Reset Request</h2>
                            </div>
                            <div class='content'>
                                <p>Hello " . htmlspecialchars($user['username']) . ",</p>
                                <p>We received a request to reset your password for your Rev Garage account.</p>
                                <p>Click the button below to reset your password:</p>
                                <p style='text-align: center;'>
                                    <a href='" . $reset_link . "' class='button'>Reset Password</a>
                                </p>
                                <p>Or copy and paste this link into your browser:</p>
                                <p style='word-break: break-all; background: #eee; padding: 10px; border-radius: 5px;'>" . $reset_link . "</p>
                                <p><strong>This link will expire in 1 hour.</strong></p>
                                <p>If you didn't request this password reset, please ignore this email. Your password will remain unchanged.</p>
                                <p>Best regards,<br>The Rev Garage Team</p>
                            </div>
                            <div class='footer'>
                                <p>This is an automated message. Please do not reply to this email.</p>
                            </div>
                        </div>
                    </body>
                    </html>";

                    $mail->send();
                    $message = "Password reset instructions have been sent to your email address.";
                    $message_type = "success";
                } catch (Exception $e) {
                    error_log("Email Error: " . $mail->ErrorInfo);
                    $message = "Failed to send reset email. Please try again later.";
                    $message_type = "error";
                }
            } else {
                $message = "If an account with that email exists, password reset instructions have been sent.";
                $message_type = "success";
            }
        } catch (PDOException $e) {
            error_log("Password Reset Error: " . $e->getMessage());
            $message = "A database error occurred. Please try again later.";
            $message_type = "error";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Rev Garage</title>
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

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4caf50;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #45a049;
        }

        .form-description {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
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
                    <a href="login.php" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                        Back to Login
                    </a>
                    <h1 class="auth-title">Forgot Password?</h1>
                    <p class="auth-subtitle">No worries, we'll send you reset instructions</p>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form class="auth-form" method="POST">
                    <p class="form-description">
                        Enter your email address and we'll send you a link to reset your password.
                    </p>

                    <div class="form-group">
                        <input
                            type="email"
                            class="form-input"
                            name="email"
                            placeholder="Enter your email address"
                            required
                            autocomplete="email"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <button type="submit" class="auth-submit">
                        <i class="fas fa-paper-plane"></i>
                        Send Reset Instructions
                    </button>
                </form>

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
        // Auto-hide success messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('.message.success');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.opacity = '0';
                    successMessage.style.transition = 'opacity 0.5s ease';
                    setTimeout(function() {
                        successMessage.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });
    </script>
</body>

</html>