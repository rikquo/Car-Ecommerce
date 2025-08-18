<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit();
}

require_once '../config/dbcon.php';

$admin_info = [
    'username' => htmlspecialchars($_SESSION['user_username']),
    'email' => 'admin@revgarage.com',
    'role' => 'Administrator',
    'profile_picture' => '/placeholder.svg?height=80&width=80&text=AD' // Default placeholder
];


try {

    $stmt = $conn->prepare("SELECT email, profile_picture FROM users WHERE user_id = :user_id AND role = 'admin'");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $db_admin_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($db_admin_data) {
        if (!empty($db_admin_data['email'])) {
            $admin_info['email'] = htmlspecialchars($db_admin_data['email']);
        }

        if (!empty($db_admin_data['profile_picture'])) {
            $admin_info['profile_picture'] = htmlspecialchars($db_admin_data['profile_picture']);
        }
    }
} catch (PDOException $e) {
    error_log("Database error fetching admin profile: " . $e->getMessage());
}


$admin_stmt = $conn->prepare("SELECT email, profile_picture FROM users WHERE user_id = :user_id AND role = 'admin'");
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
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Rev Garage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="/admin/css/admin-dashboard.css">
    <link rel="stylesheet" href="/admin/css/adminpfp.css">
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
                    <li class="nav-item">
                        <a href="/admin/settings.php" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="nav-item active">
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
                    <h1 class="page-title">Admin Profile</h1>
                </div>

                <div class="header-right">
                    <div class="header-actions">
                        <button class="action-btn" title="Notifications">
                            <i class="fas fa-bell"></i>

                            <span class="notification-badge">0</span>
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
                <section id="profile-section" class="content-section active">
                    <div class="profile-container">
                        <div class="profile-picture-wrapper">
                            <img src="<?php echo $admin_info['profile_picture']; ?>"
                                alt="Profile Picture"
                                class="profile-picture"
                                onerror="this.src='/placeholder.svg?height=120&width=120&text=AD';">
                        </div>
                        <h2 class="profile-name"><?php echo $admin_info['username']; ?></h2>
                        <p class="profile-role"><?php echo $admin_info['role']; ?></p>

                        <div class="profile-details">
                            <div class="profile-detail-item">
                                <i class="fas fa-id-badge"></i>
                                <span class="profile-detail-label">Username:</span>
                                <span class="profile-detail-value"><?php echo $admin_info['username']; ?></span>
                            </div>
                            <div class="profile-detail-item">
                                <i class="fas fa-envelope"></i>
                                <span class="profile-detail-label">Email:</span>
                                <span class="profile-detail-value"><?php echo $admin_info['email']; ?></span>
                            </div>
                            <div class="profile-detail-item">
                                <i class="fas fa-user-shield"></i>
                                <span class="profile-detail-label">Role:</span>
                                <span class="profile-detail-value"><?php echo $admin_info['role']; ?></span>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="/admin/js/admin-dashboard.js"></script>
</body>

</html>