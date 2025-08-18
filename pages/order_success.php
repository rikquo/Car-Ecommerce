<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /pages/login.php');
    exit();
}


if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header('Location: /pages/garage.php');
    exit();
}

require_once '../config/dbcon.php';

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Get order details
try {
    $order_stmt = $conn->prepare("
        SELECT o.*, u.username, u.email
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        WHERE o.order_id = ? AND o.user_id = ?
    ");
    $order_stmt->execute([$order_id, $user_id]);
    $order = $order_stmt->fetch();

    if (!$order) {
        header('Location: /pages/garage.php?error=order_not_found');
        exit();
    }

    // Get order items
    $items_stmt = $conn->prepare("
        SELECT od.*, c.name, c.image_url
        FROM order_details od 
        JOIN cars c ON od.car_id = c.car_id 
        WHERE od.order_id = ?
    ");
    $items_stmt->execute([$order_id]);
    $order_items = $items_stmt->fetchAll();
} catch (PDOException $e) {
    header('Location: /pages/garage.php?error=database_error');
    exit();
}

include('../includes/user_nav.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Rev Garage</title>
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="stylesheet" href="/assets/css/order_success.css">
</head>

<body>
    <main class="success-main">
        <section class="success-hero">
            <div class="success-hero-content">
                <h1 class="success-title">Order Confirmed!</h1>
                <p class="success-subtitle">Thank you for your purchase. Your McLaren order has been successfully placed.</p>
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
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <span class="item-price">$<?php echo number_format($item['price_at_purchase'], 2); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="order-total">
                        <h3>Total: $<?php echo number_format($order['total_amount'], 2); ?></h3>
                    </div>
                </div>

                <div class="next-steps">
                    <h2>What's Next?</h2>
                    <div class="steps">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Order Processing</h4>
                                <p>We'll review your order and contact you within 24 hours to confirm details.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Documentation</h4>
                                <p>Our team will prepare all necessary paperwork and financing documents.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Delivery Coordination</h4>
                                <p>We'll schedule delivery or pickup of your McLaren at your convenience.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h4>Payment Confirmation</h4>
                                <p>We'll contact you in 24 hours along with the dealership around you to confirm the order.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <!-- Added download receipt button -->
                    <a href="/pages/generate_receipt.php?order_id=<?php echo $order['order_id']; ?>" class="download-receipt-btn" target="_blank">
                        Download Receipt
                    </a>
                    <a href="/pages/models.php" class="continue-btn">Continue Shopping</a>
                    <a href="/pages/home.php" class="home-btn">Back to Home</a>
                </div>
            </div>
        </section>
    </main>

    <?php include('../includes/footer.php'); ?>
    <?php include 'chatbot.php'; ?>
</body>

</html>