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
    'profile_picture' => '/placeholder.svg?height=80&width=80&text=AD'
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
    error_log("Database error fetching admin profile on vehicles page: " . $e->getMessage());
}

function getSeriesName($series_id)
{
    switch ($series_id) {
        case 1:
            return 'Sport Series';
        case 2:
            return 'Super Series';
        case 3:
            return 'Ultimate Series';
        default:
            return 'Unknown Series';
    }
}

$pending_orders = 0; // Initialize to 0
try {
    $pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
} catch (PDOException $e) {
    error_log("Database error fetching pending orders count on vehicles page: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Management - Rev Garage Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/admin/css/admin-dashboard.css">
    <link rel="stylesheet" href="/admin/css/vehicles.css">
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
                    <li class="nav-item active">
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
                    <h1 class="page-title">Vehicle Management</h1>
                </div>

                <div class="header-right">
                    <div class="header-actions">
                        <button class="action-btn" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge"><?php echo $pending_orders; ?></span>
                        </button>
                        <!-- Messages and Theme Toggle buttons removed -->
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

            <div class="vehicle-management">
                <div class="section-header">
                    <div class="header-left">
                        <h2 class="section-title">Vehicle Inventory</h2>
                        <p class="section-subtitle">Manage your McLaren collection</p>
                    </div>
                    <div class="header-actions">
                        <button class="primary-btn" id="addVehicleBtn">
                            <i class="fas fa-plus"></i>
                            Add Vehicle
                        </button>
                    </div>
                </div>

                <div class="controls-section">
                    <div class="search-controls">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search by model...">
                        </div>
                    </div>

                    <div class="filter-controls">
                        <select class="filter-select" id="seriesFilter">
                            <option value="">All Series</option>
                            <option value="1">Sport Series</option>
                            <option value="2">Super Series</option>
                            <option value="3">Ultimate Series</option>
                        </select>

                        <button class="clear-filters-btn" id="clearFiltersBtn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <?php
                // Pagination logic
                $records_per_page = 10;
                $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($current_page - 1) * $records_per_page;

                $total_records_sql = "SELECT COUNT(*) FROM cars";
                $total_records_result = $conn->query($total_records_sql);
                $total_records = $total_records_result->fetchColumn();
                $total_pages = ceil($total_records / $records_per_page);

                $sql = "SELECT car_id, name, series_id, engine, power_hp, doors, acceleration_0_60, price, description, stock_quantity, image_url FROM cars LIMIT $records_per_page OFFSET $offset";
                $result = $conn->query($sql);
                ?>

                <div class="table-container">
                    <div class="table-header">
                        <div class="table-info">
                            <span class="results-count"><strong id="resultCount"><?php echo $result->rowCount(); ?></strong> vehicles</span>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <table class="vehicles-table">
                            <thead>
                                <tr>
                                    <th><span>Image</span></th>
                                    <th><span>Model</span></th>
                                    <th><span>Series</span></th>
                                    <th><span>Price</span></th>
                                    <th><span>Power (HP)</span></th>
                                    <th><span>0-60 mph</span></th>
                                    <th><span>Stock</span></th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="vehiclesTableBody">
                                <?php
                                if ($result && $result->rowCount() > 0) {
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<tr class="vehicle-row" data-series-id="' . htmlspecialchars($row["series_id"]) . '">';

                                        // Image Column
                                        echo '<td>';
                                        echo '<div class="vehicle-image">';
                                        echo '<img src="' . htmlspecialchars($row["image_url"]) . '" alt="' . htmlspecialchars($row["name"]) . '" class="table-image">';
                                        echo '</div>';
                                        echo '</td>';

                                        // Model Column
                                        echo '<td>';
                                        echo '<div class="vehicle-info">';
                                        echo '<span class="vehicle-name">' . htmlspecialchars($row["name"]) . '</span>';
                                        echo '</div>';
                                        echo '</td>';

                                        // Series Column
                                        $series_name = getSeriesName($row["series_id"]);
                                        echo '<td><span class="series-badge" data-series-id="' . htmlspecialchars($row["series_id"]) . '">' . htmlspecialchars($series_name) . '</span></td>';

                                        // Price Column
                                        echo '<td class="price-cell">$' . htmlspecialchars(number_format($row["price"], 0)) . '</td>';

                                        // Power Column
                                        echo '<td>' . htmlspecialchars($row["power_hp"]) . ' HP</td>';

                                        // Acceleration Column
                                        echo '<td>' . htmlspecialchars($row["acceleration_0_60"]) . '</td>';

                                        // Stock Column
                                        echo '<td>' . htmlspecialchars($row["stock_quantity"]) . '</td>';

                                        // Actions Column
                                        echo '<td>';
                                        echo '<div class="action-buttons">';

                                        // Edit button with vehicle data
                                        $vehicle_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                        echo '<button class="action-btn edit-btn" title="Edit" onclick="openEditModal(' . $vehicle_json . ')">';
                                        echo '<i class="fas fa-edit"></i>';
                                        echo '</button>';

                                        // Delete button
                                        echo '<a href="actions/delete_vehicles.php?car_id=' . htmlspecialchars($row["car_id"]) . '" class="action-btn delete-btn" title="Delete" onclick="return confirm(\'Are you sure you want to delete this vehicle?\');">';
                                        echo '<i class="fas fa-trash-alt"></i>';
                                        echo '</a>';

                                        echo '</div>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="8">0 results found</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-pagination">
                        <div class="pagination-info">
                            <?php
                            $start_record = $offset + 1;
                            $end_record = min($offset + $records_per_page, $total_records);
                            ?>
                            <span>Showing <?php echo $start_record; ?>-<?php echo $end_record; ?> of <?php echo $total_records; ?> vehicles</span>
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

    <!-- Modal -->
    <div id="vehicleModal" class="modal">
        <div class="modal-content large-modal">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Vehicle</h3>
                <button class="modal-close" type="button">&times;</button>
            </div>
            <div class="modal-body">
                <form id="vehicleForm" method="POST" action="actions/add_vehicles.php" enctype="multipart/form-data">
                    <input type="hidden" id="vehicleId" name="car_id" value="">
                    <input type="hidden" id="formAction" name="action" value="add">

                    <div class="tab-content active" id="basic-tab">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Image *</label>
                                <input type="file" id="vehicleImage" name="image" accept="image/*" required>
                                <div id="currentImage" style="display: none;">
                                    <p>Current image:</p>
                                    <img id="currentImagePreview" src="/placeholder.svg" alt="Current vehicle image" style="max-width: 200px; max-height: 150px;">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Model Name *</label>
                                <input type="text" id="vehicleModel" name="model" required placeholder="e.g., McLaren 720S">
                            </div>
                            <div class="form-group">
                                <label>Series *</label>
                                <select id="vehicleSeries" name="series" required>
                                    <option value="">Select Series</option>
                                    <option value="1">Sport Series</option>
                                    <option value="2">Super Series</option>
                                    <option value="3">Ultimate Series</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Engine *</label>
                                <input type="text" id="vehicleEngine" name="engine" required placeholder="V8, Hybrid, etc.">
                            </div>
                            <div class="form-group">
                                <label>Power (HP) *</label>
                                <input type="number" id="vehiclePower" name="power" required placeholder="e.g., 710">
                            </div>
                            <div class="form-group">
                                <label>Doors *</label>
                                <input type="text" id="vehicleDoors" name="doors" placeholder="e.g., 2-Doors">
                            </div>
                            <div class="form-group">
                                <label>0-60mph *</label>
                                <input type="text" id="vehicleMph" name="mph" placeholder="e.g., 2.8s">
                            </div>
                            <div class="form-group">
                                <label>Price *</label>
                                <input type="number" id="vehiclePrice" name="price" step="0.01" placeholder="e.g., 342000">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea id="vehicleDescription" name="description" rows="4" placeholder="Vehicle description and key features..."></textarea>
                            </div>
                            <div class="form-group">
                                <label>Stock Quantity *</label>
                                <input type="number" id="vehicleStock" name="stock" required min="0" placeholder="5">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="modal-close secondary-btn">Cancel</button>
                            <button type="submit" class="primary-btn">
                                <i class="fas fa-save"></i>
                                <span id="submitButtonText">Save Vehicle</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/admin/js/admin-dashboard.js"></script>
    <script src="/admin/js/vehicles.js"></script>
</body>

</html>