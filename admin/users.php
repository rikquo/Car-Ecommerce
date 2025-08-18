<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit();
}

require_once '../config/dbcon.php';
$host = 'localhost';
$dbname = 'revgaragedb';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


$admin_info = [
    'username' => htmlspecialchars($_SESSION['user_username']),
    'email' => 'admin@revgarage.com',
    'role' => 'Administrator',
    'profile_picture' => '/placeholder.svg?height=80&width=80&text=AD' // Default placeholder
];


try {

    $admin_stmt = $pdo->prepare("SELECT email, profile_picture FROM users WHERE user_id = :user_id AND role = 'admin'");
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
} catch (PDOException $e) {
    error_log("Database error fetching admin profile on users page: " . $e->getMessage());
}


$pending_orders = 0;
try {

    $pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
} catch (PDOException $e) {
    error_log("Database error fetching pending orders count on users page: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    switch ($_POST['action']) {
        case 'add_user':
            try {
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $role = $_POST['role'];


                $profile_picture = null;
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
                    $upload_dir = '../uploads/profiles/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
                    $filename = 'profile_' . uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;

                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                        $profile_picture = $upload_path;
                    }
                }

                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, profile_picture) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $password, $role, $profile_picture]);

                echo json_encode(['success' => true, 'message' => 'User added successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;

        case 'update_user':
            try {
                $user_id = $_POST['user_id'];
                $username = $_POST['username'];
                $email = $_POST['email'];
                $role = $_POST['role'];


                $profile_picture_sql = "";
                $params = [$username, $email, $role];

                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
                    $upload_dir = '../uploads/profiles/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
                    $filename = 'profile_' . uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $filename;

                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                        $profile_picture_sql = ", profile_picture = ?";
                        $params[] = $upload_path;
                    }
                }


                if (!empty($_POST['password'])) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $profile_picture_sql .= ", password = ?";
                    $params[] = $password;
                }

                $params[] = $user_id;

                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ?" . $profile_picture_sql . " WHERE user_id = ?");
                $stmt->execute($params);

                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;

        case 'delete_user':
            try {
                $user_id = $_POST['user_id'];


                if ($user_id == $_SESSION['user_id']) {
                    echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
                    exit;
                }

                $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                $stmt->execute([$user_id]);

                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;

        case 'get_user':
            try {
                $user_id = $_POST['user_id'];
                $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {

                    unset($user['password']);
                    echo json_encode(['success' => true, 'user' => $user]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'User not found']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
    }
}


$search = isset($_GET['search']) ? $_GET['search'] : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';

$sql = "SELECT user_id, username, email, role, created_at, profile_picture FROM users WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (username LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($role_filter) {
    $sql .= " AND role = ?";
    $params[] = $role_filter;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

try {
    $loyal_users = $pdo->query("
        SELECT u.user_id, u.username, u.email, u.role, u.profile_picture, 
               COUNT(o.order_id) as order_count, SUM(o.total_amount) as total_spent
        FROM users u
        LEFT JOIN orders o ON u.user_id = o.user_id
        WHERE u.role != 'admin'
        GROUP BY u.user_id
        ORDER BY order_count DESC, total_spent DESC
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching loyal users: " . $e->getMessage());
    $loyal_users = [];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Rev Garage Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/admin/css/admin-dashboard.css">
    <link rel="stylesheet" href="/admin/css/users.css">
</head>

<body>
    <div class="auth-bg-elements">
        <div class="bg-circle bg-circle-1"></div>
        <div class="bg-circle bg-circle-2"></div>
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
                    <li class="nav-item active">
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
                    <li class="nav-item">
                        <a href="/admin/adminpfp.php" class="nav-link">
                            <i class="fas fa-user-circle"></i>
                            <span>Profile</span>
                        </a>
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
                    <h1 class="page-title">User Management</h1>
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
                <div class="users-header">
                    <div class="users-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <select class="filter-select" id="roleFilter">
                            <option value="">All Roles</option>
                            <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="user" <?php echo $role_filter === 'user' ? 'selected' : ''; ?>>User</option>
                        </select>
                    </div>
                    <button class="add-user-btn" onclick="openAddUserModal()">
                        <i class="fas fa-plus"></i>
                        Add New User
                    </button>
                </div>

                <div class="users-table">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <img src="<?php echo $user['profile_picture'] ? htmlspecialchars($user['profile_picture']) : '/placeholder.svg?height=40&width=40&text=' . substr($user['username'], 0, 1); ?>"
                                                alt="<?php echo htmlspecialchars($user['username']); ?>" class="user-avatar">
                                            <span><?php echo htmlspecialchars($user['username']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="role-badge <?php echo $user['role']; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view" onclick="viewUser(<?php echo $user['user_id']; ?>)" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn edit" onclick="editUser(<?php echo $user['user_id']; ?>)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                                                <button class="action-btn delete" onclick="deleteUser(<?php echo $user['user_id']; ?>)" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="loyal-users-section">
                    <div class="loyal-users-header">
                        <h2 class="loyal-users-title">Top 10 Loyal Customers</h2>
                    </div>
                    <div class="loyal-users-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>User</th>
                                    <th>Orders</th>
                                    <th>Total Spent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($loyal_users as $index => $user): ?>
                                    <tr>
                                        <td class="loyal-user-rank">#<?php echo $index + 1; ?></td>
                                        <td>
                                            <div class="loyal-user-info">
                                                <img src="<?php echo $user['profile_picture'] ? htmlspecialchars($user['profile_picture']) : '/placeholder.svg?height=40&width=40&text=' . substr($user['username'], 0, 1); ?>"
                                                    alt="<?php echo htmlspecialchars($user['username']); ?>" class="user-avatar">
                                                <div>
                                                    <div><?php echo htmlspecialchars($user['username']); ?></div>
                                                    <div style="font-size: 0.8rem; color: rgba(255,255,255,0.6);"><?php echo htmlspecialchars($user['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="loyal-user-stat-value"><?php echo $user['order_count'] ?? 0; ?></td>
                                        <td class="loyal-user-stat-value">$<?php echo number_format($user['total_spent'] ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($loyal_users)): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; color: rgba(255,255,255,0.6); padding: 1.5rem;">No loyal users data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>


    <div id="userModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Add New User</h2>
                <button class="modal-close" onclick="closeUserModal()">&times;</button>
            </div>
            <form id="userForm" enctype="multipart/form-data">
                <input type="hidden" id="userId" name="user_id">
                <input type="hidden" id="formAction" name="action" value="add_user">

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password">
                    <small id="passwordHelp" style="color: rgba(255,255,255,0.6); font-size: 0.8rem;"></small>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="profilePicture">Profile Picture</label>
                    <input type="file" id="profilePicture" name="profile_picture" accept="image/*">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeUserModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>


    <div id="viewUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">User Details</h2>
                <button class="modal-close" onclick="closeViewUserModal()">&times;</button>
            </div>
            <div id="userDetails">

            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeViewUserModal()">Close</button>
            </div>
        </div>
    </div>
    <script src="/admin/js/users.js"></script>


</body>

</html>