<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit();
}

require_once '../config/dbcon.php';

$admin_info = [
    'username' => htmlspecialchars($_SESSION['user_username']),
    'email' => 'admin@revgarage.com', // Default placeholder
    'role' => 'Administrator',
    'profile_picture' => '/placeholder.svg?height=80&width=80&text=AD' // Default placeholder
];

try {
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
} catch (PDOException $e) {
    error_log("Database error fetching admin profile on orders page: " . $e->getMessage());
}


function getStatusBadgeClass($status)
{
    switch (strtolower($status)) {
        case 'pending':
            return 'status-badge pending';
        case 'processing':
            return 'status-badge processing';
        case 'completed':
            return 'status-badge completed';
        case 'cancelled':
            return 'status-badge cancelled';
        default:
            return 'status-badge';
    }
}


function formatCurrency($amount)
{
    return '$' . number_format($amount, 0);
}


$pending_orders = 0; // Initialize to 0
try {
    $pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
} catch (PDOException $e) {
    error_log("Database error fetching pending orders count on orders page: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Rev Garage Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/admin/css/admin-dashboard.css">
    <link rel="stylesheet" href="/admin/css/orders.css">
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
                    <li class="nav-item ">
                        <a href="/admin/users.php" class="nav-link">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li class="nav-item active">
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
                    <li class="nav-item ">
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
                    <h1 class="page-title">Orders Management</h1>
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

            <div class="orders-management">
                <div class="section-header">
                    <div class="header-left">
                        <h2 class="section-title">Order Management</h2>
                        <p class="section-subtitle">Track and manage customer orders</p>
                    </div>
                    <div class="header-actions">
                        <button class="secondary-btn" id="exportOrdersBtn">
                            <i class="fas fa-download"></i>
                            Export Orders
                        </button>
                    </div>
                </div>

                <!-- Order Statistics Cards -->
                <div class="stats-grid">
                    <?php

                    try {
                        $pending_orders_card = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
                        $completed_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Completed'")->fetchColumn();
                        $cancelled_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Cancelled'")->fetchColumn();
                        $total_revenue = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'Completed'")->fetchColumn();
                    } catch (Exception $e) {
                        $pending_orders_card = 0;
                        $completed_orders = 0;
                        $cancelled_orders = 0;
                        $total_revenue = 0;
                    }
                    ?>
                    <div class="stat-card">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value"><?php echo $pending_orders_card; ?></h3>
                            <p class="stat-label">Pending Orders</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon cancelled">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value"><?php echo $cancelled_orders; ?></h3>
                            <p class="stat-label">Cancelled Orders</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon completed">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value"><?php echo $completed_orders; ?></h3>
                            <p class="stat-label">Completed Orders</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value"><?php echo formatCurrency($total_revenue ?: 0); ?></h3>
                            <p class="stat-label">Total Revenue</p>
                        </div>
                    </div>
                </div>

                <div class="controls-section">
                    <div class="search-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search by order ID or customer...">
                        </div>
                    </div>

                    <div class="filter-controls">
                        <select class="filter-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>

                        <select class="filter-select" id="dateFilter">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>

                        <button class="clear-filters-btn" id="clearFiltersBtn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <?php
                // Pagination logic
                $records_per_page = 15;
                $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($current_page - 1) * $records_per_page;

                try {
                    $total_records_sql = "SELECT COUNT(*) FROM orders o 
                        LEFT JOIN users u ON o.user_id = u.user_id";
                    $total_records_result = $conn->query($total_records_sql);
                    $total_records = $total_records_result->fetchColumn();
                    $total_pages = ceil($total_records / $records_per_page);


                    $sql = "SELECT o.order_id, o.user_id, o.total_amount, o.status, o.order_date,
                   u.username, u.email,
                   od.car_id, od.price_at_purchase,
                   c.name as car_name, c.image_url
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.user_id 
            LEFT JOIN order_details od ON o.order_id = od.order_id
            LEFT JOIN cars c ON od.car_id = c.car_id
            ORDER BY o.order_date DESC 
            LIMIT $records_per_page OFFSET $offset";
                    $result = $conn->query($sql);
                } catch (Exception $e) {
                    $total_records = 0;
                    $total_pages = 0;
                    $result = false;
                }
                ?>

                <div class="table-container">
                    <div class="table-header">
                        <div class="table-info">
                            <span class="results-count"><strong id="resultCount"><?php echo $result ? $result->rowCount() : 0; ?></strong> orders</span>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th><span>Order ID</span></th>
                                    <th><span>Customer</span></th>
                                    <th><span>Vehicle</span></th>
                                    <th><span>Amount</span></th>
                                    <th><span>Status</span></th>
                                    <th><span>Order Date</span></th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTableBody">
                                <?php
                                if ($result && $result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<tr class="order-row" data-status="' . htmlspecialchars(strtolower($row["status"])) . '">';

                                        // Order ID 
                                        echo '<td>';
                                        echo '<div class="order-id">';
                                        echo '<span class="order-number">#ORD-' . str_pad($row["order_id"], 3, '0', STR_PAD_LEFT) . '</span>';
                                        echo '</div>';
                                        echo '</td>';

                                        // Customer 
                                        echo '<td>';
                                        echo '<div class="customer-info">';
                                        echo '<span class="customer-name">' . htmlspecialchars($row["username"] ?: 'Unknown Customer') . '</span>';
                                        if ($row["email"]) {
                                            echo '<span class="customer-email">' . htmlspecialchars($row["email"]) . '</span>';
                                        }
                                        echo '</div>';
                                        echo '</td>';

                                        // Vehicle 
                                        echo '<td>';
                                        echo '<div class="vehicle-info">';
                                        if ($row["image_url"]) {
                                            echo '<img src="' . htmlspecialchars($row["image_url"]) . '" alt="' . htmlspecialchars($row["car_name"]) . '" class="vehicle-thumb">';
                                        }
                                        echo '<span class="vehicle-name">' . htmlspecialchars($row["car_name"] ?: 'Unknown Vehicle') . '</span>';
                                        echo '</div>';
                                        echo '</td>';

                                        // Amount 
                                        echo '<td class="amount-cell">' . formatCurrency($row["price_at_purchase"] ?: $row["total_amount"]) . '</td>';

                                        // Status 
                                        echo '<td>';
                                        echo '<span class="' . getStatusBadgeClass($row["status"]) . '">' . ucfirst(htmlspecialchars($row["status"])) . '</span>';
                                        echo '</td>';

                                        // Date 
                                        echo '<td>' . date('M d, Y', strtotime($row["order_date"])) . '</td>';

                                        // Actions 
                                        echo '<td>';
                                        echo '<div class="action-buttons">';


                                        // Edit Status button
                                        $order_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                        echo '<button class="action-btn edit-btn" title="Update Status" onclick="openStatusModal(' . $order_json . ')">';
                                        echo '<i class="fas fa-edit"></i>';
                                        echo '</button>';

                                        // Delete button (only for cancelled orders)
                                        if (strtolower($row["status"]) === 'cancelled') {
                                            echo '<a href="actions/delete_order.php?order_id=' . htmlspecialchars($row["order_id"]) . '" class="action-btn delete-btn" title="Delete" onclick="return confirm(\'Are you sure you want to delete this order?\');">';
                                            echo '<i class="fas fa-trash-alt"></i>';
                                            echo '</a>';
                                        }

                                        echo '</div>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="no-results">No orders found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <div class="table-pagination">
                            <div class="pagination-info">
                                <?php
                                $start_record = $offset + 1;
                                $end_record = min($offset + $records_per_page, $total_records);
                                ?>
                                <span>Showing <?php echo $start_record; ?>-<?php echo $end_record; ?> of <?php echo $total_records; ?> orders</span>
                            </div>
                            <div class="pagination-controls">
                                <?php
                                $prev_disabled = ($current_page <= 1) ? 'disabled' : '';
                                echo '<a class="pagination-btn ' . $prev_disabled . '" href="?page=' . ($current_page - 1) . '"><i class="fas fa-chevron-left"></i></a>';

                                for ($i = 1; $i <= $total_pages; $i++) {
                                    $active_class = ($i == $current_page) ? 'active' : '';
                                    echo '<a class="pagination-btn ' . $active_class . '" href="?page=' . $i . '">' . $i . '</a>';
                                }

                                $next_disabled = ($current_page >= $total_pages) ? 'disabled' : '';
                                echo '<a class="pagination-btn ' . $next_disabled . '" href="?page=' . ($current_page + 1) . '"><i class="fas fa-chevron-right"></i></a>';
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-error" id="alertMessage">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-success" id="alertMessage">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']);
    }
    ?>


    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Order Status</h3>
                <button class="modal-close" type="button">&times;</button>
            </div>
            <div class="modal-body">
                <form id="statusForm" method="POST" action="/admin/actions/update_order.php">
                    <input type="hidden" id="statusOrderId" name="order_id" value="">

                    <div class="form-group">
                        <label>Current Status</label>
                        <input type="text" id="currentStatus" readonly class="readonly-input">
                    </div>

                    <div class="form-group">
                        <label>New Status *</label>
                        <select id="newStatus" name="status" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="modal-close secondary-btn">Cancel</button>
                        <button type="submit" class="primary-btn">
                            <i class="fas fa-save"></i>
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/admin/js/admin-dashboard.js"></script>
    <script src="/admin/js/orders.js"></script>
</body>

</html>