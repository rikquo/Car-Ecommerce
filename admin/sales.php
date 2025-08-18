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
    error_log("Database error fetching admin profile on sales page: " . $e->getMessage());
}

//  format currency
function formatCurrency($amount)
{
    return '$' . number_format($amount, 0);
}

function calculateCommission($amount)
{
    return $amount * 0.05;
}

// notification
$pending_orders = 0;
try {
    $pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
} catch (PDOException $e) {
    error_log("Database error fetching pending orders count on sales page: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Management - Rev Garage Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/admin/css/admin-dashboard.css">
    <link rel="stylesheet" href="/admin/css/sales.css">
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
                    <li class="nav-item active">
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
                    <h1 class="page-title">Sales Management</h1>
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

            <div class="sales-management">
                <div class="section-header">
                    <div class="header-left">
                        <h2 class="section-title">Sales Dashboard</h2>
                        <p class="section-subtitle">Track completed sales and revenue performance</p>
                    </div>
                    <div class="header-actions">
                        <button class="secondary-btn" id="exportSalesBtn">
                            <i class="fas fa-download"></i>
                            Export Sales Report
                        </button>

                    </div>
                </div>


                <div class="stats-grid">
                    <?php

                    try {
                        $total_sales = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Completed'")->fetchColumn();
                        $total_revenue = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'Completed'")->fetchColumn();
                        $avg_sale_value = $total_sales > 0 ? $total_revenue / $total_sales : 0;

                        // Today's sales
                        $today_sales = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Completed' AND DATE(order_date) = CURDATE()")->fetchColumn();
                        $today_revenue = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'Completed' AND DATE(order_date) = CURDATE()")->fetchColumn();

                        // This months sales
                        $month_sales = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Completed' AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())")->fetchColumn();
                        $month_revenue = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'Completed' AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())")->fetchColumn();

                        // Total commission
                        $total_commission = calculateCommission($total_revenue ?: 0);
                    } catch (Exception $e) {
                        $total_sales = 0;
                        $total_revenue = 0;
                        $avg_sale_value = 0;
                        $today_sales = 0;
                        $today_revenue = 0;
                        $month_sales = 0;
                        $month_revenue = 0;
                        $total_commission = 0;
                    }
                    ?>
                    <div class="stat-card">
                        <div class="stat-icon total-sales">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value"><?php echo $total_sales; ?></h3>
                            <p class="stat-label">Total Sales</p>
                            <span class="stat-change positive">All Time</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon total-revenue">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value"><?php echo formatCurrency($total_revenue ?: 0); ?></h3>
                            <p class="stat-label">Total Revenue</p>
                            <span class="stat-change positive">All Time</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon avg-sale">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value"><?php echo formatCurrency($avg_sale_value); ?></h3>
                            <p class="stat-label">Average Sale Value</p>
                            <span class="stat-change neutral">Per Transaction</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon monthly-sales">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-value"><?php echo $month_sales; ?></h3>
                            <p class="stat-label">This Month Sales</p>
                            <span class="stat-change positive"><?php echo formatCurrency($month_revenue ?: 0); ?></span>
                        </div>
                    </div>
                </div>


                <div class="quick-stats">
                    <div class="quick-stat-item">
                        <div class="quick-stat-icon today">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="quick-stat-content">
                            <span class="quick-stat-value"><?php echo $today_sales; ?></span>
                            <span class="quick-stat-label">Today's Sales</span>
                            <span class="quick-stat-amount"><?php echo formatCurrency($today_revenue ?: 0); ?></span>
                        </div>
                    </div>

                    <div class="quick-stat-item">
                        <div class="quick-stat-icon commission">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="quick-stat-content">
                            <span class="quick-stat-value"><?php echo formatCurrency($total_commission); ?></span>
                            <span class="quick-stat-label">Total Commission</span>
                            <span class="quick-stat-amount">5% of Revenue</span>
                        </div>
                    </div>

                    <div class="quick-stat-item">
                        <div class="quick-stat-icon conversion">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="quick-stat-content">
                            <?php
                            $total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
                            $conversion_rate = $total_orders > 0 ? ($total_sales / $total_orders) * 100 : 0;
                            ?>
                            <span class="quick-stat-value"><?php echo number_format($conversion_rate, 1); ?>%</span>
                            <span class="quick-stat-label">Conversion Rate</span>
                            <span class="quick-stat-amount"><?php echo $total_sales; ?>/<?php echo $total_orders; ?> Orders</span>
                        </div>
                    </div>
                </div>

                <div class="controls-section">
                    <div class="search-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search by sale ID or customer...">
                        </div>
                    </div>

                    <div class="filter-controls">
                        <select class="filter-select" id="periodFilter">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                        </select>

                        <select class="filter-select" id="seriesFilter">
                            <option value="">All Series</option>
                            <option value="1">Sport Series</option>
                            <option value="2">Super Series</option>
                            <option value="3">Ultimate Series</option>
                        </select>

                        <select class="filter-select" id="amountFilter">
                            <option value="">All Amounts</option>
                            <option value="0-200000">Under $200K</option>
                            <option value="200000-500000">$200K - $500K</option>
                            <option value="500000-1000000">$500K - $1M</option>
                            <option value="1000000+">Over $1M</option>
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
                        LEFT JOIN users u ON o.user_id = u.user_id 
                        WHERE o.status = 'Completed'";
                    $total_records_result = $conn->query($total_records_sql);
                    $total_records = $total_records_result->fetchColumn();
                    $total_pages = ceil($total_records / $records_per_page);


                    $sql = "SELECT o.order_id, o.user_id, o.total_amount, o.status, o.order_date,
                           u.username, u.email,
                           od.car_id, od.price_at_purchase,
                           c.name as car_name, c.image_url, c.series_id,
                           cs.name as series_name
                    FROM orders o 
                    LEFT JOIN users u ON o.user_id = u.user_id 
                    LEFT JOIN order_details od ON o.order_id = od.order_id
                    LEFT JOIN cars c ON od.car_id = c.car_id
                    LEFT JOIN car_series cs ON c.series_id = cs.series_id
                    WHERE o.status = 'Completed'
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
                            <span class="results-count"><strong id="resultCount"><?php echo $result ? $result->rowCount() : 0; ?></strong> sales</span>
                        </div>

                    </div>

                    <div class="table-wrapper">
                        <table class="sales-table">
                            <thead>
                                <tr>

                                    <th><span>Sale ID</span></th>
                                    <th><span>Customer</span></th>
                                    <th><span>Vehicle</span></th>
                                    <th><span>Series</span></th>
                                    <th><span>Sale Amount</span></th>
                                    <th><span>Commission</span></th>
                                    <th><span>Sale Date</span></th>

                                </tr>
                            </thead>
                            <tbody id="salesTableBody">
                                <?php
                                if ($result && $result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $sale_amount = $row["price_at_purchase"] ?: $row["total_amount"];
                                        $commission = calculateCommission($sale_amount);

                                        echo '<tr class="sale-row" data-series-id="' . htmlspecialchars($row["series_id"]) . '" data-amount="' . $sale_amount . '">';
                                        // Sale ID 
                                        echo '<td>';
                                        echo '<div class="sale-id">';
                                        echo '<span class="sale-number">#SALE-' . str_pad($row["order_id"], 3, '0', STR_PAD_LEFT) . '</span>';
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

                                        // Series 
                                        echo '<td>';
                                        echo '<span class="series-badge series-' . $row["series_id"] . '">' . htmlspecialchars($row["series_name"] ?: 'Unknown') . '</span>';
                                        echo '</td>';

                                        // Sale Amount
                                        echo '<td class="amount-cell">' . formatCurrency($sale_amount) . '</td>';

                                        // Commission
                                        echo '<td class="commission-cell">' . formatCurrency($commission) . '</td>';

                                        // Date
                                        echo '<td>' . date('M d, Y', strtotime($row["order_date"])) . '</td>';


                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="9" class="no-results">No sales found</td></tr>';
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
                                <span>Showing <?php echo $start_record; ?>-<?php echo $end_record; ?> of <?php echo $total_records; ?> sales</span>
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
    <script src="/admin/js/admin-dashboard.js"></script>
    <script src="/admin/js/sales.js"></script>
</body>

</html>