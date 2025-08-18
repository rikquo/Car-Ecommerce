<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /pages/login.php');
    exit();
}

require_once '../config/dbcon.php';

$user_id = $_SESSION['user_id'];

// Get user's orders with order details
try {
    $orders_stmt = $conn->prepare("
        SELECT o.*, 
               COUNT(od.order_detail_id) as item_count,
               GROUP_CONCAT(c.name SEPARATOR ', ') as car_names
        FROM orders o 
        LEFT JOIN order_details od ON o.order_id = od.order_id
        LEFT JOIN cars c ON od.car_id = c.car_id
        WHERE o.user_id = ?
        GROUP BY o.order_id
        ORDER BY o.order_date DESC
    ");
    $orders_stmt->execute([$user_id]);
    $orders = $orders_stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error loading orders: " . $e->getMessage();
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Rev Garage</title>
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="stylesheet" href="/assets/css/orders.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Added complete navbar with user profile dropdown -->
    <header>
        <nav class="navbar">
            <div class="header">
                <div class="logo"><img src="/assets/img/logo.png" width="150px" /></div>
                <div class="navigation">
                    <a href="/pages/home.php" class="nav-link">Home</a>
                    <a href="/pages/models.php" class="nav-link">Models</a>
                    <a href="/pages/aboutus.php" class="nav-link">About Us</a>
                    <a href="/pages/contactus.php" class="nav-link">Contact Us</a>
                    <a href="/pages/garage.php" class="nav-link">Garage</a>
                    <a href="/pages/wishlist.php" class="nav-link">Wishlist</a>


                    <div class="user-profile">
                        <?php
                        // Database connection to get current user's profile picture
                        $host = 'localhost';
                        $port = '3306';
                        $db_name = 'revgaragedb';
                        $username = 'root';
                        $password = '';

                        try {
                            $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8", $username, $password);
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            // Get user's current profile picture from database
                            $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE user_id = :user_id");
                            $stmt->execute(['user_id' => $_SESSION['user_id']]);
                            $user_data = $stmt->fetch();

                            $nav_avatar_url = ($user_data && $user_data['profile_picture'])
                                ? $user_data['profile_picture']
                                : "/placeholder.svg?height=40&width=40&text=" . substr($_SESSION['user_username'], 0, 1);
                        } catch (PDOException $e) {
                            // Fallback to placeholder if database query fails
                            $nav_avatar_url = "/placeholder.svg?height=40&width=40&text=" . substr($_SESSION['user_username'], 0, 1);
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($nav_avatar_url); ?>"
                            alt="Profile" class="profile-picture" onclick="toggleProfileDropdown()">

                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="dropdown-header">
                                <h4><?php echo htmlspecialchars($_SESSION['user_username']); ?></h4>
                                <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                            </div>
                            <a href="/pages/userpfp.php" class="dropdown-item">My Profile</a>
                            <a href="/pages/garage.php" class="dropdown-item">My Garage</a>
                            <a href="/pages/wishlist.php" class="dropdown-item">My Wishlist</a>
                            <a href="/pages/orders.php" class="dropdown-item">Order History</a>
                            <div class="dropdown-divider"></div>
                            <a href="/pages/logout.php" class="dropdown-item logout-item">Sign Out</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="orders-main">
        <section class="orders-hero">
            <div class="orders-hero-content">
                <h1 class="orders-title">Order History</h1>
                <p class="orders-subtitle">Track your McLaren purchases and order status</p>
            </div>
        </section>

        <section class="orders-content">
            <div class="orders-container">
                <?php if (isset($error_message)): ?>
                    <div class="error-message">
                        <p><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                <?php elseif (empty($orders)): ?>
                    <div class="no-orders">

                        <h2>No Orders Yet</h2>
                        <p>You haven't placed any orders yet. Start exploring our McLaren collection!</p>
                        <a href="/pages/models.php" class="browse-btn">Browse Models</a>
                    </div>
                <?php else: ?>
                    <div class="orders-list">
                        <?php foreach ($orders as $order): ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-info">
                                        <h3 class="order-number">Order #<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></h3>
                                        <p class="order-date"><?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
                                    </div>
                                    <div class="order-status">
                                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="order-details">
                                    <div class="order-summary">
                                        <div class="summary-item">
                                            <span class="label">Items:</span>
                                            <span class="value"><?php echo $order['item_count']; ?> car(s)</span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="label">Models:</span>
                                            <span class="value"><?php echo htmlspecialchars($order['car_names']); ?></span>
                                        </div>
                                        <div class="summary-item">
                                            <span class="label">Payment:</span>
                                            <span class="value"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                                        </div>
                                        <?php if ($order['financing_option']): ?>
                                            <div class="summary-item">
                                                <span class="label">Financing:</span>
                                                <span class="value"><?php echo $order['financing_option']; ?> months</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="order-total">
                                        <span class="total-label">Total:</span>
                                        <span class="total-amount">$<?php echo number_format($order['total_amount'], 2); ?></span>
                                    </div>
                                </div>

                                <div class="order-actions">
                                    <a href="/pages/order_details.php?order_id=<?php echo $order['order_id']; ?>" class="view-details-btn">
                                        View Details
                                    </a>
                                    <?php if ($order['status'] === 'Pending'): ?>
                                        <button class="cancel-order-btn" onclick="cancelOrder(<?php echo $order['order_id']; ?>)">
                                            Cancel Order
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const profile = document.querySelector('.user-profile');
            const dropdown = document.getElementById('profileDropdown');

            if (profile && !profile.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order?')) {
                // Create a form to submit the cancellation
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/pages/cancel_order.php';

                const orderIdInput = document.createElement('input');
                orderIdInput.type = 'hidden';
                orderIdInput.name = 'order_id';
                orderIdInput.value = orderId;

                form.appendChild(orderIdInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    <?php include 'chatbot.php'; ?>
</body>

</html>