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
    // Total vehicles
    $stmt = $conn->query("SELECT COUNT(*) as total_vehicles FROM cars");
    $total_vehicles = $stmt->fetch()['total_vehicles'];

    // Total customers
    $stmt = $conn->query("SELECT COUNT(*) as total_customers FROM users WHERE role = 'user'");
    $total_customers = $stmt->fetch()['total_customers'];

    // Total revenue
    $stmt = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total_revenue FROM orders WHERE status = 'Completed'");
    $total_revenue = $stmt->fetch()['total_revenue'];

    // Pending orders
    $stmt = $conn->query("SELECT COUNT(*) as pending_orders FROM orders WHERE status = 'Pending'");
    $pending_orders = $stmt->fetch()['pending_orders'];

    // Monthly sales  for chart (last 6 months)
    $stmt = $conn->query("
        SELECT 
            DATE_FORMAT(order_date, '%Y-%m') as month,
            COALESCE(SUM(total_amount), 0) as revenue
        FROM orders 
        WHERE order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        AND status = 'Completed'
        GROUP BY DATE_FORMAT(order_date, '%Y-%m')
        ORDER BY month ASC
    ");
    $monthly_sales = $stmt->fetchAll();

    // Vehicle categories 
    $stmt = $conn->query("
        SELECT 
            cs.name as series_name,
            COUNT(c.car_id) as count
        FROM car_series cs
        LEFT JOIN cars c ON cs.series_id = c.series_id
        GROUP BY cs.series_id, cs.name
        ORDER BY cs.series_id
    ");
    $vehicle_categories = $stmt->fetchAll();

    // Recent orders
    $stmt = $conn->query("
        SELECT 
            o.order_id,
            u.username as customer_name,
            c.name as vehicle_name,
            o.total_amount,
            o.status,
            o.order_date
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        JOIN order_details od ON o.order_id = od.order_id
        JOIN cars c ON od.car_id = c.car_id
        ORDER BY o.order_date DESC
        LIMIT 10
    ");
    $recent_orders = $stmt->fetchAll();

    // Top selling 
    $stmt = $conn->query("
        SELECT 
            c.name as vehicle_name,
            c.image_url,
            COUNT(od.car_id) as sales_count,
            SUM(od.price_at_purchase) as total_revenue
        FROM cars c
        JOIN order_details od ON c.car_id = od.car_id
        JOIN orders o ON od.order_id = o.order_id
        WHERE o.status = 'Completed'
        GROUP BY c.car_id, c.name, c.image_url
        ORDER BY sales_count DESC
        LIMIT 5
    ");
    $top_vehicles = $stmt->fetchAll();


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
    // Set default values if database query fails
    $total_vehicles = 0;
    $total_customers = 0;
    $total_revenue = 0;
    $pending_orders = 0;
    $monthly_sales = [];
    $vehicle_categories = [];
    $recent_orders = [];
    $top_vehicles = [];
    error_log("Database error: " . $e->getMessage());
}

$sales_labels = [];
$sales_data = [];

// last 6 months
for ($i = 5; $i >= 0; $i--) {
    $month_key = date('Y-m', strtotime("-$i months"));
    $month_name = date('M', strtotime("-$i months"));
    $sales_labels[] = $month_name;

    $found = false;
    foreach ($monthly_sales as $sale) {
        if ($sale['month'] == $month_key) {
            $sales_data[] = (float)$sale['revenue'];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $sales_data[] = 0;
    }
}

//  category data
$category_labels = [];
$category_data = [];
foreach ($vehicle_categories as $category) {
    $category_labels[] = $category['series_name'];
    $category_data[] = (int)$category['count'];
}


if (empty($category_labels)) {
    $category_labels = ['Sports Series', 'Super Series', 'Ultimate Series'];
    $category_data = [0, 0, 0];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Rev Garage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/admin/css/admin-dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <li class="nav-item active">
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
                    <h1 class="page-title">Dashboard Overview</h1>
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
                <section id="dashboard-section" class="content-section active">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value"><?php echo number_format($total_vehicles); ?></h3>
                                <p class="stat-label">Total Vehicles</p>
                                <span class="stat-change positive">Active inventory</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value"><?php echo number_format($total_customers); ?></h3>
                                <p class="stat-label">Total Customers</p>
                                <span class="stat-change positive">Registered users</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value">$<?php echo number_format($total_revenue, 0); ?></h3>
                                <p class="stat-label">Total Revenue</p>
                                <span class="stat-change positive">Completed orders</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value"><?php echo number_format($pending_orders); ?></h3>
                                <p class="stat-label">Pending Orders</p>
                                <span class="stat-change <?php echo $pending_orders > 0 ? 'negative' : 'positive'; ?>">
                                    <?php echo $pending_orders > 0 ? 'Needs attention' : 'All processed'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="charts-row">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3>Sales Overview (Last 6 Months)</h3>
                                <div class="chart-controls">
                                    <button class="chart-btn active">Monthly</button>
                                    <button class="chart-btn">Yearly</button>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>

                        <div class="chart-card">
                            <div class="chart-header">
                                <h3>Vehicle Categories</h3>
                                <div class="chart-info">
                                    <span class="revenue-amount"><?php echo number_format($total_vehicles); ?></span>
                                    <span class="revenue-period">Total Vehicles</span>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="activity-section">
                        <div class="recent-orders">
                            <div class="section-header">
                                <h3>Recent Orders</h3>
                                <a href="/admin/orders.php" class="view-all-btn">View All</a>
                            </div>
                            <div class="orders-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Vehicle</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recent_orders)): ?>
                                            <tr>
                                                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                                    No orders found. Orders will appear here once customers start purchasing vehicles.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <tr>
                                                    <td>#ORD-<?php echo str_pad($order['order_id'], 3, '0', STR_PAD_LEFT); ?></td>
                                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($order['vehicle_name']); ?></td>
                                                    <td>$<?php echo number_format($order['total_amount'], 0); ?></td>
                                                    <td>
                                                        <span class="status-badge <?php echo strtolower($order['status']); ?>">
                                                            <?php echo $order['status']; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="top-vehicles">
                            <div class="section-header">
                                <h3>Top Selling Vehicles</h3>
                            </div>
                            <div class="vehicles-list">
                                <?php if (empty($top_vehicles)): ?>
                                    <div style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                        <i class="fas fa-chart-line" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                                        <p>No sales data available yet.</p>
                                        <p style="font-size: 0.875rem;">Top selling vehicles will appear here once orders are completed.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($top_vehicles as $vehicle): ?>
                                        <div class="vehicle-item">
                                            <img src="<?php echo htmlspecialchars($vehicle['image_url']); ?>"
                                                alt="<?php echo htmlspecialchars($vehicle['vehicle_name']); ?>"
                                                class="vehicle-thumb"
                                                onerror="this.src='/placeholder.svg?height=60&width=80&text=<?php echo urlencode(substr($vehicle['vehicle_name'], 0, 1)); ?>'">
                                            <div class="vehicle-info">
                                                <h4><?php echo htmlspecialchars($vehicle['vehicle_name']); ?></h4>
                                                <p><?php echo $vehicle['sales_count']; ?> sold this period</p>
                                            </div>
                                            <div class="vehicle-revenue">
                                                $<?php echo $vehicle['total_revenue'] >= 1000000 ? number_format($vehicle['total_revenue'] / 1000000, 1) . 'M' : number_format($vehicle['total_revenue'] / 1000, 0) . 'K'; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>


    <script>
        // Chart data from PHP
        window.chartData = {
            sales: {
                labels: <?php echo json_encode($sales_labels); ?>,
                data: <?php echo json_encode($sales_data); ?>
            },
            categories: {
                labels: <?php echo json_encode($category_labels); ?>,
                data: <?php echo json_encode($category_data); ?>
            }
        };


        console.log('Chart Data:', window.chartData);
    </script>
    <script src="/admin/js/admin-dashboard.js"></script>
</body>

</html>