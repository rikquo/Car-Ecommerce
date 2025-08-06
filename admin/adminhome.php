<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit();
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
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <!-- <i class="fas fa-car-side"></i> -->
                    <span>Rev Garage</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-item active">
                        <a href="adminhome.php" class="nav-link" data-section="dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="vehicles.php" class="nav-link" data-section="vehicles">
                            <i class="fas fa-car"></i>
                            <span>Vehicles</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#customers" class="nav-link" data-section="customers">
                            <i class="fas fa-users"></i>
                            <span>Customers</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#orders" class="nav-link" data-section="orders">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#analytics" class="nav-link" data-section="analytics">
                            <i class="fas fa-chart-line"></i>
                            <span>Analytics</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#settings" class="nav-link" data-section="settings">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <img src="/placeholder.svg?height=40&width=40&text=A" alt="Admin" class="user-avatar">
                    <div class="user-details">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_username']); ?></span>
                        <span class="user-role">Administrator</span>
                    </div>
                </div>
                <a href="/pages/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
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
                            <span class="notification-badge">3</span>
                        </button>
                        <button class="action-btn" title="Messages">
                            <i class="fas fa-envelope"></i>
                        </button>
                        <button class="action-btn theme-toggle" title="Toggle Theme">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>
                    
                    <div class="user-profile">
                        <img src="/placeholder.svg?height=40&width=40&text=A" alt="Admin" class="profile-avatar">
                        <span class="profile-name"><?php echo htmlspecialchars($_SESSION['user_username']); ?></span>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="content-area">
                <!-- Dashboard Section -->
                <section id="dashboard-section" class="content-section active">
                    <!-- Stats Cards -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value">127</h3>
                                <p class="stat-label">Total Vehicles</p>
                                <span class="stat-change positive">+12% from last month</span>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value">2,847</h3>
                                <p class="stat-label">Total Customers</p>
                                <span class="stat-change positive">+8% from last month</span>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value">$2.4M</h3>
                                <p class="stat-label">Total Revenue</p>
                                <span class="stat-change positive">+15% from last month</span>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-value">89</h3>
                                <p class="stat-label">Pending Orders</p>
                                <span class="stat-change negative">-3% from last month</span>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="charts-row">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3>Sales Overview</h3>
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
                                    <span class="revenue-amount">$847K</span>
                                    <span class="revenue-period">This Month</span>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="activity-section">
                        <div class="recent-orders">
                            <div class="section-header">
                                <h3>Recent Orders</h3>
                                <a href="#orders" class="view-all-btn">View All</a>
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
                                        <tr>
                                            <td>#ORD-001</td>
                                            <td>John Smith</td>
                                            <td>McLaren 720S</td>
                                            <td>$310,000</td>
                                            <td><span class="status-badge pending">Pending</span></td>
                                            <td>2024-01-15</td>
                                        </tr>
                                        <tr>
                                            <td>#ORD-002</td>
                                            <td>Sarah Johnson</td>
                                            <td>McLaren P1</td>
                                            <td>$1,350,000</td>
                                            <td><span class="status-badge completed">Completed</span></td>
                                            <td>2024-01-14</td>
                                        </tr>
                                        <tr>
                                            <td>#ORD-003</td>
                                            <td>Mike Wilson</td>
                                            <td>McLaren 570S</td>
                                            <td>$215,000</td>
                                            <td><span class="status-badge processing">Processing</span></td>
                                            <td>2024-01-13</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="top-vehicles">
                            <div class="section-header">
                                <h3>Top Selling Vehicles</h3>
                            </div>
                            <div class="vehicles-list">
                                <div class="vehicle-item">
                                    <img src="/placeholder.svg?height=60&width=80" alt="McLaren 720S" class="vehicle-thumb">
                                    <div class="vehicle-info">
                                        <h4>McLaren 720S</h4>
                                        <p>23 sold this month</p>
                                    </div>
                                    <div class="vehicle-revenue">$7.13M</div>
                                </div>
                                <div class="vehicle-item">
                                    <img src="/placeholder.svg?height=60&width=80" alt="McLaren 570S" class="vehicle-thumb">
                                    <div class="vehicle-info">
                                        <h4>McLaren 570S</h4>
                                        <p>18 sold this month</p>
                                    </div>
                                    <div class="vehicle-revenue">$3.87M</div>
                                </div>
                                <div class="vehicle-item">
                                    <img src="/placeholder.svg?height=60&width=80" alt="McLaren P1" class="vehicle-thumb">
                                    <div class="vehicle-info">
                                        <h4>McLaren P1</h4>
                                        <p>5 sold this month</p>
                                    </div>
                                    <div class="vehicle-revenue">$6.75M</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Vehicles Section -->
                <section id="vehicles-section" class="content-section">
                    <div class="section-header">
                        <h2>Vehicle Management</h2>
                        <button class="primary-btn" id="addVehicleBtn">
                            <i class="fas fa-plus"></i>
                            Add New Vehicle
                        </button>
                    </div>
                    
                    <div class="table-container">
                        <div class="table-controls">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search vehicles...">
                            </div>
                            <div class="filter-controls">
                                <select class="filter-select">
                                    <option value="">All Categories</option>
                                    <option value="sport">Sport Series</option>
                                    <option value="super">Super Series</option>
                                    <option value="ultimate">Ultimate Series</option>
                                </select>
                                <select class="filter-select">
                                    <option value="">All Status</option>
                                    <option value="available">Available</option>
                                    <option value="sold">Sold</option>
                                    <option value="reserved">Reserved</option>
                                </select>
                            </div>
                        </div>
                        
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Model</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="/placeholder.svg?height=50&width=70" alt="720S" class="table-image"></td>
                                    <td>McLaren 720S</td>
                                    <td>Super Series</td>
                                    <td>$310,000</td>
                                    <td><span class="status-badge available">Available</span></td>
                                    <td>5</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><img src="/placeholder.svg?height=50&width=70" alt="570S" class="table-image"></td>
                                    <td>McLaren 570S</td>
                                    <td>Sport Series</td>
                                    <td>$215,000</td>
                                    <td><span class="status-badge available">Available</span></td>
                                    <td>8</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><img src="/placeholder.svg?height=50&width=70" alt="P1" class="table-image"></td>
                                    <td>McLaren P1</td>
                                    <td>Ultimate Series</td>
                                    <td>$1,350,000</td>
                                    <td><span class="status-badge reserved">Reserved</span></td>
                                    <td>2</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Customers Section -->
                <section id="customers-section" class="content-section">
                    <div class="section-header">
                        <h2>Customer Management</h2>
                        <button class="primary-btn" id="addCustomerBtn">
                            <i class="fas fa-plus"></i>
                            Add New Customer
                        </button>
                    </div>
                    
                    <div class="table-container">
                        <div class="table-controls">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search customers...">
                            </div>
                        </div>
                        
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Orders</th>
                                    <th>Total Spent</th>
                                    <th>Join Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="customer-info">
                                            <img src="/placeholder.svg?height=40&width=40&text=JS" alt="John Smith" class="customer-avatar">
                                            <span>John Smith</span>
                                        </div>
                                    </td>
                                    <td>john.smith@email.com</td>
                                    <td>+1 (555) 123-4567</td>
                                    <td>3</td>
                                    <td>$825,000</td>
                                    <td>2023-06-15</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="customer-info">
                                            <img src="/placeholder.svg?height=40&width=40&text=SJ" alt="Sarah Johnson" class="customer-avatar">
                                            <span>Sarah Johnson</span>
                                        </div>
                                    </td>
                                    <td>sarah.johnson@email.com</td>
                                    <td>+1 (555) 987-6543</td>
                                    <td>1</td>
                                    <td>$1,350,000</td>
                                    <td>2023-08-22</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn delete" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Orders Section -->
                <section id="orders-section" class="content-section">
                    <div class="section-header">
                        <h2>Order Management</h2>
                        <div class="header-actions">
                            <button class="secondary-btn">
                                <i class="fas fa-download"></i>
                                Export
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <div class="table-controls">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search orders...">
                            </div>
                            <div class="filter-controls">
                                <select class="filter-select">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Vehicle</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#ORD-001</td>
                                    <td>John Smith</td>
                                    <td>McLaren 720S</td>
                                    <td>$310,000</td>
                                    <td><span class="status-badge pending">Pending</span></td>
                                    <td>2024-01-15</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#ORD-002</td>
                                    <td>Sarah Johnson</td>
                                    <td>McLaren P1</td>
                                    <td>$1,350,000</td>
                                    <td><span class="status-badge completed">Completed</span></td>
                                    <td>2024-01-14</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Analytics Section -->
                <section id="analytics-section" class="content-section">
                    <div class="section-header">
                        <h2>Analytics & Reports</h2>
                        <div class="date-range-picker">
                            <input type="date" class="date-input">
                            <span>to</span>
                            <input type="date" class="date-input">
                        </div>
                    </div>
                    
                    <div class="analytics-grid">
                        <div class="analytics-card">
                            <div class="card-header">
                                <h3>Revenue Trends</h3>
                                <div class="card-actions">
                                    <button class="card-btn">Week</button>
                                    <button class="card-btn active">Month</button>
                                    <button class="card-btn">Year</button>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="analytics-card">
                            <div class="card-header">
                                <h3>Customer Growth</h3>
                            </div>
                            <div class="chart-container">
                                <canvas id="customerChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="analytics-card">
                            <div class="card-header">
                                <h3>Sales by Category</h3>
                            </div>
                            <div class="chart-container">
                                <canvas id="salesByCategoryChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="analytics-card">
                            <div class="card-header">
                                <h3>Performance Metrics</h3>
                            </div>
                            <div class="metrics-list">
                                <div class="metric-item">
                                    <span class="metric-label">Conversion Rate</span>
                                    <span class="metric-value">12.5%</span>
                                    <span class="metric-change positive">+2.1%</span>
                                </div>
                                <div class="metric-item">
                                    <span class="metric-label">Average Order Value</span>
                                    <span class="metric-value">$485K</span>
                                    <span class="metric-change positive">+8.3%</span>
                                </div>
                                <div class="metric-item">
                                    <span class="metric-label">Customer Lifetime Value</span>
                                    <span class="metric-value">$1.2M</span>
                                    <span class="metric-change positive">+15.7%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Settings Section -->
                <section id="settings-section" class="content-section">
                    <div class="section-header">
                        <h2>Settings</h2>
                    </div>
                    
                    <div class="settings-grid">
                        <div class="settings-card">
                            <div class="settings-header">
                                <h3>General Settings</h3>
                            </div>
                            <div class="settings-content">
                                <div class="setting-item">
                                    <label>Site Name</label>
                                    <input type="text" value="Rev Garage" class="setting-input">
                                </div>
                                <div class="setting-item">
                                    <label>Contact Email</label>
                                    <input type="email" value="admin@revgarage.com" class="setting-input">
                                </div>
                                <div class="setting-item">
                                    <label>Currency</label>
                                    <select class="setting-select">
                                        <option value="USD">USD ($)</option>
                                        <option value="EUR">EUR (€)</option>
                                        <option value="GBP">GBP (£)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-card">
                            <div class="settings-header">
                                <h3>Notification Settings</h3>
                            </div>
                            <div class="settings-content">
                                <div class="setting-item">
                                    <div class="setting-toggle">
                                        <input type="checkbox" id="emailNotifications" checked>
                                        <label for="emailNotifications">Email Notifications</label>
                                    </div>
                                </div>
                                <div class="setting-item">
                                    <div class="setting-toggle">
                                        <input type="checkbox" id="orderAlerts" checked>
                                        <label for="orderAlerts">Order Alerts</label>
                                    </div>
                                </div>
                                <div class="setting-item">
                                    <div class="setting-toggle">
                                        <input type="checkbox" id="inventoryAlerts">
                                        <label for="inventoryAlerts">Low Inventory Alerts</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="settings-actions">
                        <button class="primary-btn">Save Changes</button>
                        <button class="secondary-btn">Reset to Default</button>
                    </div>
                </section>
            </div>
        </main>
    </div>


    <script src="admin/js/admin-dashboard.js"></script>
</body>
</html>
