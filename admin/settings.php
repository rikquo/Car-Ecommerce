<?php
session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pages/login.php'); // Redirect to login page if not authorized
    exit();
}

require_once '../config/dbcon.php';


$admin_info = [
    'user_id' => $_SESSION['user_id'],
    'username' => htmlspecialchars($_SESSION['user_username']),
    'email' => 'admin@revgarage.com',
    'role' => 'Administrator',
    'profile_picture' => '/placeholder.svg?height=80&width=80&text=AD' // Default placeholder
];

try {
    $admin_stmt = $conn->prepare("SELECT email, profile_picture, password FROM users WHERE user_id = :user_id AND role = 'admin'");
    $admin_stmt->bindParam(':user_id', $_SESSION['user_id']);
    $admin_stmt->execute();
    $db_admin_data = $admin_stmt->fetch(PDO::FETCH_ASSOC);

    if ($db_admin_data) {
        if (!empty($db_admin_data['email'])) {
            $admin_info['email'] = htmlspecialchars($db_admin_data['email']);
        }
        if (!empty($db_admin_data['profile_picture'])) {
            $admin_info['profile_picture'] = htmlspecialchars($db_admin_data['profile_picture']);
        }
        $admin_info['hashed_password'] = $db_admin_data['password'];
    }
} catch (PDOException $e) {
    error_log("Database error fetching admin profile on settings page: " . $e->getMessage());
}

$pending_orders = 0;
try {
    $pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
} catch (PDOException $e) {
    error_log("Database error fetching pending orders count on settings page: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $admin_info['user_id'];

    if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_new_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_new_password = $_POST['confirm_new_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
            $_SESSION['error'] = 'Please fill all password fields.';
        } elseif (!password_verify($current_password, $admin_info['hashed_password'])) {
            $_SESSION['error'] = 'Current password is incorrect.';
        } elseif ($new_password !== $confirm_new_password) {
            $_SESSION['error'] = 'New password and confirm password do not match.';
        } elseif (strlen($new_password) < 6) {
            $_SESSION['error'] = 'New password must be at least 6 characters long.';
        } else {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            try {
                $stmt = $conn->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
                $stmt->bindParam(':password', $hashed_new_password);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
                $_SESSION['message'] = 'Password updated successfully!';
                $admin_info['hashed_password'] = $hashed_new_password;
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Error updating password: ' . $e->getMessage();
            }
        }
        header('Location: settings.php');
        exit();
    }

    //profile picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/profiles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
            try {

                $stmt = $conn->prepare("UPDATE users SET profile_picture = :profile_picture WHERE user_id = :user_id");
                $stmt->bindParam(':profile_picture', $upload_path);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
                $_SESSION['message'] = 'Profile picture updated successfully!';
                $admin_info['profile_picture'] = $upload_path;
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Error updating profile picture in database: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Error uploading profile picture. Check file permissions.';
        }
        header('Location: settings.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Rev Garage Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/admin/css/admin-dashboard.css">
    <link rel="stylesheet" href="/admin/css/settings.css">

</head>

<body>
    <div class="auth-bg-elements">
        <div class="bg-circle bg-circle-1"></div>
        <div class="bg-circle bg-circle-2"></div>
        <div class="bg-circle bg-circle-3"></div>
        <div class="bg-line bg-line-1"></div>
        <div class="bg-line bg-line-2"></div>
    </div>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span>Rev Garage</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-item">
                        <a href="/admin/adminhome.php" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/vehicles.php" class="nav-link">
                            <i class="fas fa-car"></i>
                            <span>Vehicles</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/users.php" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/orders.php" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/sales.php" class="nav-link">
                            <i class="fas fa-chart-line"></i>
                            <span>Sales</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="/admin/settings.php" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/adminpfp.php" class="nav-link">
                            <i class="fas fa-user-circle"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <img src="<?php echo $admin_info['profile_picture']; ?>" alt="Admin" class="user-avatar" onerror="this.src='/placeholder.svg?height=40&width=40&text=A';">
                    <div class="user-details">
                        <span class="user-name"><?php echo $admin_info['username']; ?></span>
                        <span class="user-role"><?php echo $admin_info['role']; ?></span>
                    </div>
                </div>
                <a href="/pages/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="header">
                <div class="header-left">
                    <button class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">Settings</h1>
                </div>

                <div class="header-right">
                    <div class="header-actions">
                        <button class="action-btn" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge"><?php echo $pending_orders; ?></span>
                        </button>
                    </div>

                    <div class="user-profile">
                        <img src="<?php echo $admin_info['profile_picture']; ?>"
                            alt="Admin Profile Picture"
                            class="profile-avatar"
                            onerror="this.src='/placeholder.svg?height=40&width=40&text=A';">
                        <span class="profile-name"><?php echo $admin_info['username']; ?></span>
                    </div>
                </div>
            </header>

            <div class="content-area">
                <section id="settings-section" class="content-section active">
                    <div class="settings-container">
                        <?php

                        if (isset($_SESSION['message'])) {
                            echo '<div class="alert alert-success" id="alertMessage">' . $_SESSION['message'] . '</div>';
                            unset($_SESSION['message']);
                        }
                        if (isset($_SESSION['error'])) {
                            echo '<div class="alert alert-error" id="alertMessage">' . $_SESSION['error'] . '</div>';
                            unset($_SESSION['error']);
                        }
                        ?>

                        <div class="settings-section">
                            <h2><i class="fas fa-lock"></i> Change Password</h2>
                            <form method="POST" action="settings.php" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_new_password">Confirm New Password</label>
                                    <input type="password" id="confirm_new_password" name="confirm_new_password" required>
                                </div>
                                <button type="submit" class="btn-submit">Update Password</button>
                            </form>
                        </div>

                        <div class="settings-section">
                            <h2><i class="fas fa-camera"></i> Change Profile Picture</h2>
                            <form method="POST" action="settings.php" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="profile_picture_upload">Upload New Profile Picture</label>
                                    <input type="file" id="profile_picture_upload" name="profile_picture" accept="image/*">
                                    <div class="current-profile-picture">
                                        <p>Current Profile Picture:</p>
                                        <img src="<?php echo $admin_info['profile_picture']; ?>"
                                            alt="Current Profile Picture"
                                            onerror="this.src='/placeholder.svg?height=150&width=150&text=AD';">
                                    </div>
                                </div>
                                <button type="submit" class="btn-submit">Update Picture</button>
                            </form>
                        </div>

                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="/admin/js/admin-dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alertMessage = document.getElementById('alertMessage');
            if (alertMessage) {
                setTimeout(() => {
                    alertMessage.style.opacity = '0';
                    setTimeout(() => alertMessage.remove(), 500);
                }, 5000); // 5 seconds
            }
        });
    </script>
</body>

</html>