<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /pages/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

require_once '../config/dbcon.php';
require_once '../includes/stock_manager.php';

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

try {
    $user_stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
    $user_stmt->execute([$user_id]);
    $user_info = $user_stmt->fetch();
} catch (PDOException $e) {
    $error = "Error loading user information.";
    $user_info = ['username' => '', 'email' => ''];
}

try {
    $garage_stmt = $conn->prepare("
        SELECT g.garage_item_id, c.car_id, c.name, c.price, c.image_url
        FROM garage_items g 
        JOIN cars c ON g.car_id = c.car_id 
        WHERE g.user_id = ? 
        ORDER BY g.added_at DESC
    ");
    $garage_stmt->execute([$user_id]);
    $garage_items = $garage_stmt->fetchAll();

    if (empty($garage_items)) {
        header('Location: /pages/garage.php?error=empty_garage');
        exit();
    }

    $total = 0;
    foreach ($garage_items as $item) {
        $total += $item['price'];
    }
} catch (PDOException $e) {
    $error = "Error loading garage items.";
    $garage_items = [];
    $total = 0;
}

//  form submission
if ($_POST && isset($_POST['place_order'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $payment_method = $_POST['payment_method'] ?? '';
    $financing_option = !empty($_POST['financing_option']) ? (int)$_POST['financing_option'] : null;

    // Validation
    $errors = [];
    if (empty($full_name)) $errors[] = "Full name is required.";
    if (empty($address)) $errors[] = "Address is required.";
    if (empty($phone_number)) $errors[] = "Phone number is required.";
    if (empty($payment_method)) $errors[] = "Payment method is required.";

    if (empty($errors)) {
        foreach ($garage_items as $item) {
            if (!checkStockAvailability($item['car_id'], 1, $conn)) {
                $errors[] = "Sorry, " . htmlspecialchars($item['name']) . " is currently out of stock.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            foreach ($garage_items as $item) {
                if (!updateCarStock($item['car_id'], 1, $conn)) {
                    throw new Exception("Failed to update stock for " . $item['name']);
                }
            }

            // Create order
            $order_stmt = $conn->prepare("
                INSERT INTO orders (user_id, full_name, address, phone_number, payment_method, financing_option, total_amount, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')
            ");
            $order_stmt->execute([$user_id, $full_name, $address, $phone_number, $payment_method, $financing_option, $total]);
            $order_id = $conn->lastInsertId();

            // Add order details
            $detail_stmt = $conn->prepare("INSERT INTO order_details (order_id, car_id, price_at_purchase) VALUES (?, ?, ?)");
            foreach ($garage_items as $item) {
                $detail_stmt->execute([$order_id, $item['car_id'], $item['price']]);
            }

            // Clear garage
            $clear_stmt = $conn->prepare("DELETE FROM garage_items WHERE user_id = ?");
            $clear_stmt->execute([$user_id]);

            $conn->commit();

            header('Location: /pages/order_success.php?order_id=' . $order_id);
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $error = "Error processing order: " . $e->getMessage();
        } catch (PDOException $e) {
            $conn->rollBack();
            $error = "Error processing order. Please try again.";
        }
    } else {
        $error = implode(' ', $errors);
    }
}

include('../includes/user_nav.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Rev Garage</title>
    <link rel="stylesheet" href="/assets/css/home.css">
    <link rel="stylesheet" href="/assets/css/checkout.css">
</head>

<body>
    <?php if ($error): ?>
        <div class="notification error" id="notification">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <main class="checkout-main">
        <section class="checkout-hero">
            <div class="checkout-hero-content">
                <h1 class="checkout-title">Checkout</h1>
                <p class="checkout-subtitle">Complete your McLaren purchase</p>
                <div class="breadcrumb">
                    <a href="/pages/home.php">Home</a>
                    <span>→</span>
                    <a href="/pages/garage.php">Garage</a>
                    <span>→</span>
                    <span>Checkout</span>
                </div>
            </div>
        </section>

        <section class="checkout-content">
            <div class="checkout-container">

                <div class="order-summary">
                    <h2>Order Summary</h2>
                    <div class="summary-items">
                        <?php foreach ($garage_items as $item): ?>
                            <div class="summary-item">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                    alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div class="item-info">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <span class="item-price">$<?php echo number_format($item['price'], 2); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="summary-total">
                        <h3>Total: $<?php echo number_format($total, 2); ?></h3>
                        <p><?php echo count($garage_items); ?> car<?php echo count($garage_items) > 1 ? 's' : ''; ?></p>
                    </div>
                </div>


                <div class="checkout-form-container">
                    <form method="POST" class="checkout-form">
                        <h2>Billing Information</h2>

                        <div class="form-group">
                            <label for="full_name">Full Name *</label>
                            <input type="text" id="full_name" name="full_name"
                                value="<?php echo htmlspecialchars($user_info['username'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="address">Address *</label>
                            <textarea id="address" name="address" rows="3" required
                                placeholder="Enter your full address"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="phone_number">Phone Number *</label>
                            <input type="tel" id="phone_number" name="phone_number" required
                                placeholder="+1 (555) 123-4567">
                        </div>

                        <h2>Payment Method</h2>
                        <div class="payment-methods">
                            <div class="payment-option">
                                <input type="radio" id="kbz_pay" name="payment_method" value="KBZ Pay" required>
                                <label for="kbz_pay">
                                    <span class="payment-icon">💳</span>
                                    KBZ Pay
                                </label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" id="visa" name="payment_method" value="VISA" required>
                                <label for="visa">
                                    <span class="payment-icon">💳</span>
                                    VISA
                                </label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" id="paypal" name="payment_method" value="PayPal" required>
                                <label for="paypal">
                                    <span class="payment-icon">🅿️</span>
                                    PayPal
                                </label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" id="mastercard" name="payment_method" value="MasterCard" required>
                                <label for="mastercard">
                                    <span class="payment-icon">💳</span>
                                    MasterCard
                                </label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" id="bank_transfer" name="payment_method" value="Bank Transfer" required>
                                <label for="bank_transfer">
                                    <span class="payment-icon">🏦</span>
                                    Bank Transfer
                                </label>
                            </div>
                        </div>

                        <h2>Financing Options</h2>
                        <div class="financing-options">
                            <div class="financing-option">
                                <input type="radio" id="full_payment" name="financing_option" value="" checked>
                                <label for="full_payment">
                                    <strong>Full Payment</strong>
                                    <span>Pay the full amount now</span>
                                </label>
                            </div>

                            <div class="financing-option">
                                <input type="radio" id="financing_12" name="financing_option" value="12">
                                <label for="financing_12">
                                    <strong>12 Months Financing</strong>
                                    <span>$<?php echo number_format($total / 12, 2); ?>/month</span>
                                </label>
                            </div>

                            <div class="financing-option">
                                <input type="radio" id="financing_24" name="financing_option" value="24">
                                <label for="financing_24">
                                    <strong>24 Months Financing</strong>
                                    <span>$<?php echo number_format($total / 24, 2); ?>/month</span>
                                </label>
                            </div>

                            <div class="financing-option">
                                <input type="radio" id="financing_36" name="financing_option" value="36">
                                <label for="financing_36">
                                    <strong>36 Months Financing</strong>
                                    <span>$<?php echo number_format($total / 36, 2); ?>/month</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="/pages/garage.php" class="back-btn">Back to Garage</a>
                            <button type="submit" name="place_order" class="place-order-btn">
                                Place Order - $<?php echo number_format($total, 2); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php include('../includes/footer.php'); ?>

    <script>
        const notification = document.getElementById('notification');
        if (notification) {
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }
    </script>

    <?php include 'chatbot.php'; ?>
</body>

</html>