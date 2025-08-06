<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /pages/login.php');
    exit();
}

// Database connection
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
    die("A database connection error occurred. Please try again later.");
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    $user_id = $_SESSION['user_id'];
    
    if ($action === 'update_profile') {
        $new_username = filter_var($_POST['username'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $new_email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        
        $errors = [];
        
        if (empty($new_username)) {
            $errors[] = "Username is required.";
        }
        if (empty($new_email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        
        if (empty($errors)) {
            try {
                // Check if email or username already exists (excluding current user)
                $stmt = $pdo->prepare("SELECT user_id FROM users WHERE (email = :email OR username = :username) AND user_id != :user_id");
                $stmt->execute(['email' => $new_email, 'username' => $new_username, 'user_id' => $user_id]);
                
                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => false, 'message' => 'Email or Username already exists.']);
                    exit();
                }
                
                // Update user information
                $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE user_id = :user_id");
                $stmt->execute(['username' => $new_username, 'email' => $new_email, 'user_id' => $user_id]);
                
                // Update session variables
                $_SESSION['user_username'] = $new_username;
                $_SESSION['user_email'] = $new_email;
                
                echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
            } catch (PDOException $e) {
                error_log("Profile Update Error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error updating profile.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => implode(" ", $errors)]);
        }
        exit();
    }
    
    if ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (empty($current_password)) {
            $errors[] = "Current password is required.";
        }
        if (empty($new_password)) {
            $errors[] = "New password is required.";
        } elseif (strlen($new_password) < 8) {
            $errors[] = "New password must be at least 8 characters long.";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match.";
        }
        
        if (empty($errors)) {
            try {
                // Verify current password
                $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $user_id]);
                $user = $stmt->fetch();
                
                if (!password_verify($current_password, $user['password'])) {
                    echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
                    exit();
                }
                
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
                $stmt->execute(['password' => $hashed_password, 'user_id' => $user_id]);
                
                echo json_encode(['success' => true, 'message' => 'Password changed successfully!']);
            } catch (PDOException $e) {
                error_log("Password Change Error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error changing password.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => implode(" ", $errors)]);
        }
        exit();
    }
}

// Get user information
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        session_destroy();
        header('Location: /pages/login.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("User Fetch Error: " . $e->getMessage());
    die("Error loading user information.");
}

// Get user statistics
try {
    // Get garage count
    $stmt = $pdo->prepare("SELECT COUNT(*) as garage_count FROM garage WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $garage_count = $stmt->fetch()['garage_count'];
    
    // Get order count
    $stmt = $pdo->prepare("SELECT COUNT(*) as order_count FROM orders WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $order_count = $stmt->fetch()['order_count'];
    
} catch (PDOException $e) {
    $garage_count = 0;
    $order_count = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Profile - Rev Garage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="/assets/css/home.css" />
    <link rel="stylesheet" href="/assets/css/profile.css" />
</head>

<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollTrigger.min.js" crossorigin="anonymous"></script>

    <?php include('../includes/user_nav.php'); ?>

    <main class="profile-main">
        <!-- Background Elements -->
        <div class="profile-bg-elements">
            <div class="bg-circle bg-circle-1"></div>
            <div class="bg-circle bg-circle-2"></div>
            <div class="bg-circle bg-circle-3"></div>
            <div class="bg-line bg-line-1"></div>
            <div class="bg-line bg-line-2"></div>
        </div>

        <div class="profile-container">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="/placeholder.svg?height=120&width=120&text=<?php echo substr($user['username'], 0, 1); ?>" alt="Profile Picture" />
                    <button class="avatar-edit-btn">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                <div class="profile-info">
                    <h1 class="profile-name"><?php echo htmlspecialchars($user['username']); ?></h1>
                    <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
                    <p class="profile-joined">Member since <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
                </div>
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $garage_count; ?></span>
                        <span class="stat-label">Cars in Garage</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $order_count; ?></span>
                        <span class="stat-label">Orders Placed</span>
                    </div>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="profile-content">
                <!-- Profile Settings -->
                <div class="profile-section">
                    <div class="section-header">
                        <h2 class="section-title">Profile Information</h2>
                        <p class="section-description">Update your account information and preferences</p>
                    </div>

                    <form class="profile-form" id="profileForm">
                        <input type="hidden" name="action" value="update_profile" />
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" id="username" name="username" class="form-input" 
                                       value="<?php echo htmlspecialchars($user['username']); ?>" required />
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" name="email" class="form-input" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required />
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Password Change -->
                <div class="profile-section">
                    <div class="section-header">
                        <h2 class="section-title">Change Password</h2>
                        <p class="section-description">Update your password to keep your account secure</p>
                    </div>

                    <form class="profile-form" id="passwordForm">
                        <input type="hidden" name="action" value="change_password" />
                        
                        <div class="form-group">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="password-input-group">
                                <input type="password" id="current_password" name="current_password" class="form-input" required />
                                <button type="button" class="password-toggle" onclick="togglePassword('current_password', this)">
                                    <i class="fa-regular fa-eye"></i>
                                    <i class="fa-regular fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="password-input-group">
                                    <input type="password" id="new_password" name="new_password" class="form-input" 
                                           placeholder="Min. 8 characters" required />
                                    <button type="button" class="password-toggle" onclick="togglePassword('new_password', this)">
                                        <i class="fa-regular fa-eye"></i>
                                        <i class="fa-regular fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <div class="password-input-group">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" required />
                                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', this)">
                                        <i class="fa-regular fa-eye"></i>
                                        <i class="fa-regular fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-secondary">
                                <i class="fas fa-lock"></i>
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quick Actions -->
                <div class="profile-section">
                    <div class="section-header">
                        <h2 class="section-title">Quick Actions</h2>
                        <p class="section-description">Access your account features quickly</p>
                    </div>

                    <div class="quick-actions">
                        <a href="/pages/garage.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="action-content">
                                <h3>My Garage</h3>
                                <p>View and manage your selected vehicles</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>

                        <a href="/pages/models.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="action-content">
                                <h3>Browse Models</h3>
                                <p>Explore our McLaren collection</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>

                        <a href="/pages/contactus.php" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="action-content">
                                <h3>Support</h3>
                                <p>Get help with your account or orders</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('../includes/footer.php'); ?>

    <script src="/assets/js/profile.js"></script>
</body>

</html>