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
    $order_stmt = $conn->prepare("
        SELECT o.*, u.username, u.email
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        WHERE o.order_id = ? AND o.user_id = ?
    ");
    $order_stmt->execute([$order_id, $user_id]);
    $order = $order_stmt->fetch();

    if (!$order) {
        header('Location: /pages/orders.php?error=order_not_found');
        exit();
    }

    // Get order items
    $items_stmt = $conn->prepare("
        SELECT od.*, c.name, c.engine, c.power_hp, c.doors, c.acceleration_0_60
        FROM order_details od 
        JOIN cars c ON od.car_id = c.car_id 
        WHERE od.order_id = ?
    ");
    $items_stmt->execute([$order_id]);
    $order_items = $items_stmt->fetchAll();
} catch (PDOException $e) {
    header('Location: /pages/orders.php?error=database_error');
    exit();
}

// Set headers for PDF download
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="RevGarage_Receipt_' . str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) . '.html"');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Order #<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #ffffff;
            color: #333;
            line-height: 1.6;
            padding: 40px;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }

        .receipt-header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .company-logo {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
            letter-spacing: 2px;
        }

        .company-info {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .receipt-title {
            background: #4caf50;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .receipt-body {
            padding: 30px;
        }

        .order-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .info-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.2rem;
            border-bottom: 2px solid #4caf50;
            padding-bottom: 5px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .items-section {
            margin-top: 30px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .items-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        .items-table tr:hover {
            background: #f8f9fa;
        }

        .total-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #4caf50;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            font-size: 1.2rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .total-amount {
            color: #4caf50;
            font-size: 1.5rem;
        }

        .receipt-footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }

        .thank-you {
            font-size: 1.1rem;
            color: #4caf50;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .print-info {
            margin-top: 20px;
            padding: 15px;
            background: #e8f5e8;
            border-radius: 5px;
            font-size: 0.9rem;
            color: #2e7d32;
        }

        @media print {
            body {
                padding: 0;
            }

            .print-info {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="company-logo">REV GARAGE</div>
            <div class="company-info">
                Premium McLaren Dealership<br>
                123 Speed Street, Racing City, RC 12345<br>
                Phone: (555) 123-FAST | Email: sales@revgarage.com
            </div>
        </div>

        <div class="receipt-title">
            OFFICIAL RECEIPT
        </div>

        <div class="receipt-body">
            <div class="order-info">
                <div class="info-section">
                    <h3>Order Details</h3>
                    <div class="info-item">
                        <span class="info-label">Order Number:</span>
                        <span class="info-value">#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Order Date:</span>
                        <span class="info-value"><?php echo date('F j, Y g:i A', strtotime($order['order_date'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['status']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                    </div>
                    <?php if ($order['financing_option']): ?>
                        <div class="info-item">
                            <span class="info-label">Financing:</span>
                            <span class="info-value"><?php echo $order['financing_option']; ?> months</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="info-section">
                    <h3>Customer Information</h3>
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['full_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['phone_number']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Address:</span>
                        <span class="info-value"><?php echo htmlspecialchars($order['address']); ?></span>
                    </div>
                </div>
            </div>

            <div class="items-section">
                <h3>Purchased Items</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>McLaren Model</th>
                            <th>Engine</th>
                            <th>Power</th>
                            <th>0-60 mph</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['engine']); ?></td>
                                <td><?php echo $item['power_hp']; ?> HP</td>
                                <td><?php echo htmlspecialchars($item['acceleration_0_60']); ?></td>
                                <td>$<?php echo number_format($item['price_at_purchase'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="total-section">
                <div class="total-row">
                    <span>Total Amount:</span>
                    <span class="total-amount">$<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>

        <div class="receipt-footer">
            <div class="thank-you">Thank you for choosing Rev Garage!</div>
            <p>This receipt serves as proof of purchase for your McLaren order.</p>
            <p>For any questions or concerns, please contact our customer service team.</p>

            <div class="print-info">
                <strong>Receipt generated on:</strong> <?php echo date('F j, Y g:i A'); ?><br>
                <strong>Note:</strong> This is an official receipt from Rev Garage. Please keep this for your records.
            </div>
        </div>
    </div>

    <script>
        // Auto-print functionality for downloaded receipt
        window.onload = function() {
            // Small delay to ensure content is fully loaded
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>

</html>