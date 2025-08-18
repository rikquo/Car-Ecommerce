<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /pages/login.php');
    exit();
}

// Check if order_id is provided
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header('Location: /pages/orders.php');
    exit();
}

require_once '../config/dbcon.php';

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Get order details
try {
    $order_stmt = $conn->prepare("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.user_id WHERE o.order_id = ? AND o.user_id = ?");
    $order_stmt->execute([$order_id, $user_id]);
    $order = $order_stmt->fetch();

    if (!$order) {
        header('Location: /pages/orders.php?error=order_not_found');
        exit();
    }

    // Get order items
    $items_stmt = $conn->prepare("SELECT od.*, c.name, c.image_url, c.engine, c.power_hp, c.doors, c.acceleration_0_60 FROM order_details od JOIN cars c ON od.car_id = c.car_id WHERE od.order_id = ?");
    $items_stmt->execute([$order_id]);
    $order_items = $items_stmt->fetchAll();
} catch (PDOException $e) {
    header('Location: /pages/orders.php?error=database_error');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Rev Garage</title>
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="stylesheet" href="/assets/css/order_success.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .user-profile {
            position: relative;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 20px;
        }

        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            object-fit: cover;
        }

        .profile-picture:hover {
            border-color: rgba(255, 255, 255, 0.6);
            transform: scale(1.05);
        }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: rgba(43, 53, 67, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 15px 0;
            width: 220px;
            max-width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 9999;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .profile-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 10px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 10px;
        }

        .dropdown-header h4 {
            color: #f0f0f0;
            margin: 0;
            font-family: "Montserrat", sans-serif;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-header p {
            color: rgba(255, 255, 255, 0.6);
            margin: 5px 0 0 0;
            font-family: "Montserrat", sans-serif;
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-item {
            display: block;
            padding: 10px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-family: "Montserrat", sans-serif;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #f0f0f0;
        }

        .dropdown-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 10px 0;
        }

        .logout-item {
            color: rgba(255, 82, 82, 0.8);
        }

        .logout-item:hover {
            background: rgba(255, 82, 82, 0.1);
            color: #ff5252;
        }

        @media (max-width: 768px) {
            .profile-dropdown {
                width: 200px;
                max-width: 200px;
                right: -10px;
            }
        }
    </style>
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
                    <!-- <input type="text" placeholder="Search" class="searchbox" /> -->

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
                        <img src="<?php echo htmlspecialchars($nav_avatar_url); ?>" alt="Profile" class="profile-picture" onclick="toggleProfileDropdown()">

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

    <main class="success-main">
        <section class="success-hero">
            <div class="success-hero-content">
                <h1 class="success-title">Order Details</h1>
                <p class="success-subtitle">Complete information about your McLaren order</p>
                <div class="order-number">
                    Order #<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?>
                </div>
            </div>
        </section>

        <section class="order-details">
            <div class="details-container">
                <div class="order-info">
                    <h2>Order Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Order Date:</label>
                            <span><?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Customer:</label>
                            <span><?php echo htmlspecialchars($order['full_name']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Email:</label>
                            <span><?php echo htmlspecialchars($order['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Phone:</label>
                            <span><?php echo htmlspecialchars($order['phone_number']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Address:</label>
                            <span><?php echo htmlspecialchars($order['address']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Payment Method:</label>
                            <span><?php echo htmlspecialchars($order['payment_method']); ?></span>
                        </div>
                        <?php if ($order['financing_option']): ?>
                            <div class="info-item">
                                <label>Financing:</label>
                                <span><?php echo $order['financing_option']; ?> months</span>
                            </div>
                        <?php endif; ?>
                        <div class="info-item">
                            <label>Status:</label>
                            <span class="status-badge"><?php echo htmlspecialchars($order['status']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="ordered-items">
                    <h2>Ordered Items</h2>
                    <?php foreach ($order_items as $item): ?>
                        <div class="ordered-item">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <div class="item-specs">
                                    <span><?php echo htmlspecialchars($item['engine']); ?></span>
                                    <span><?php echo $item['power_hp']; ?> HP</span>
                                    <span><?php echo htmlspecialchars($item['doors']); ?></span>
                                    <span>0-60: <?php echo htmlspecialchars($item['acceleration_0_60']); ?></span>
                                </div>
                                <span class="item-price">$<?php echo number_format($item['price_at_purchase'], 2); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="order-total">
                        <h3>Total: $<?php echo number_format($order['total_amount'], 2); ?></h3>
                    </div>
                </div>

                <div class="action-buttons">

                    <a href="/pages/generate_receipt.php?order_id=<?php echo $order['order_id']; ?>" class="download-receipt-btn" target="_blank"> Download Receipt</a>
                    <a href="/pages/orders.php" class="continue-btn">Back to Orders</a>
                    <a href="/pages/models.php" class="home-btn">Continue Shopping</a>
                </div>
            </div>
        </section>
    </main>

    <script>
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('show');
        }

        document.addEventListener('click', function(event) {
            const profile = document.querySelector('.user-profile');
            const dropdown = document.getElementById('profileDropdown');

            if (profile && !profile.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
    </script>
    <?php include 'chatbot.php'; ?>
</body>

</html>