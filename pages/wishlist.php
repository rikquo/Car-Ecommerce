<?php
session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /pages/login.php');
    exit();
}

include('../includes/user_nav.php');
require_once '../config/dbcon.php';
require_once '../config/functions.php';


if ($_POST && isset($_POST['action']) && $_POST['action'] === 'remove_from_wishlist' && isset($_POST['car_id'])) {
    $car_id = (int)$_POST['car_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND car_id = ?");
    $stmt->execute([$user_id, $car_id]);
    $message = "Car removed from wishlist!";
}

$user_id = $_SESSION['user_id'];
$wishlist_query = "
    SELECT c.*, w.created_at as added_date 
    FROM wishlist w 
    JOIN cars c ON w.car_id = c.car_id 
    WHERE w.user_id = ? 
    ORDER BY w.created_at DESC
";
$stmt = $conn->prepare($wishlist_query);
$stmt->execute([$user_id]);
$wishlist_cars = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Wishlist - Rev Garage</title>
    <link rel="stylesheet" href="/assets/css/home.css" />
    <link rel="stylesheet" href="/assets/css/wishlist.css" />
</head>

<body>
    <?php if (isset($message)): ?>
        <div class="wishlist-notification" id="wishlistNotification">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <main class="wishlist-main">
        <div class="wishlist-header">
            <h1 class="wishlist-title">My Wishlist</h1>
            <p class="wishlist-subtitle">Your dream cars collection</p>
        </div>

        <?php if (empty($wishlist_cars)): ?>
            <div class="empty-wishlist">
                <div class="empty-icon">♡</div>
                <h2>Your wishlist is empty</h2>
                <p>Start adding your dream cars to your wishlist!</p>
                <a href="/pages/models.php" class="browse-cars-btn">Browse Cars</a>
            </div>
        <?php else: ?>
            <div class="wishlist-grid">
                <?php foreach ($wishlist_cars as $car): ?>
                    <div class="wishlist-card">
                        <div class="car-image">
                            <img src="<?php echo $car["image_url"] ?>" alt="<?php echo $car["name"] ?>" />
                            <div class="added-date">
                                Added: <?php echo date('M j, Y', strtotime($car['added_date'])); ?>
                            </div>
                        </div>
                        <div class="car-info">
                            <h3 class="car-name"><?php echo $car["name"] ?></h3>
                            <div class="car-price"><?php echo $car["price"] ?></div>
                            <div class="car-specs">
                                <div class="spec-item">
                                    <span class="spec-label">Engine:</span>
                                    <span class="spec-value"><?php echo $car["engine"] ?></span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">Power:</span>
                                    <span class="spec-value"><?php echo $car["power_hp"] ?> hp</span>
                                </div>
                                <div class="spec-item">
                                    <span class="spec-label">0-60 mph:</span>
                                    <span class="spec-value"><?php echo $car["acceleration_0_60"] ?></span>
                                </div>
                            </div>
                            <div class="card-actions">
                                <button class="details-btn" onclick="window.location.href='/pages/models.php#car-details-modal-<?php echo $car['car_id']; ?>'">
                                    View Details
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="car_id" value="<?php echo $car['car_id']; ?>">
                                    <input type="hidden" name="action" value="remove_from_wishlist">
                                    <button type="submit" class="remove-btn" onclick="return confirm('Remove this car from your wishlist?')">
                                        <span class="heart-icon">♥</span> Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="wishlist-summary">
                <h3>Wishlist Summary</h3>
                <p>You have <?php echo count($wishlist_cars); ?> car<?php echo count($wishlist_cars) !== 1 ? 's' : ''; ?> in your wishlist</p>
                <a href="/pages/models.php" class="browse-more-btn">Browse More Cars</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include('../includes/footer.php'); ?>

    <script>
        const notification = document.getElementById('wishlistNotification');
        if (notification) {
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 300);
            }, 3000);
        }
    </script>
    <?php include 'chatbot.php'; ?>
</body>

</html>