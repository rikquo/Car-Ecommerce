<?php
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ($_SESSION['user_role'] === 'admin' && !isset($_GET['noredirect'])) {
        header('Location: /admin/adminhome.php');
        exit();
    } else {
        include('../includes/user_nav.php');
    }
} else {
    include('../includes/navbar.php');
}
require_once '../config/dbcon.php';
require_once '../config/functions.php';

// Handle wishlist actions
if ($_POST && isset($_POST['action']) && isset($_POST['car_id'])) {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $car_id = (int)$_POST['car_id'];
        $user_id = $_SESSION['user_id'];

        if ($_POST['action'] === 'add_to_wishlist') {
            $check_stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND car_id = ?");
            $check_stmt->execute([$user_id, $car_id]);

            if (!$check_stmt->fetch()) {
                $stmt = $conn->prepare("INSERT INTO wishlist (user_id, car_id, created_at) VALUES (?, ?, NOW())");
                $stmt->execute([$user_id, $car_id]);
                $wishlist_message = "Car added to wishlist!";
            } else {
                $wishlist_message = "Car is already in your wishlist!";
            }
        } elseif ($_POST['action'] === 'remove_from_wishlist') {
            $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND car_id = ?");
            $stmt->execute([$user_id, $car_id]);
            $wishlist_message = "Car removed from wishlist!";
        }
    } else {
        $wishlist_message = "Please log in to use wishlist!";
    }
}

// Handle garage actions
if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case 'added_to_garage':
            $car_name = isset($_GET['car_name']) ? htmlspecialchars($_GET['car_name']) : 'Car';
            $wishlist_message = $car_name . " added to garage!";
            break;
        case 'already_in_garage':
            $wishlist_message = "Car is already in your garage!";
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'invalid_car':
            $wishlist_message = "Invalid car selection.";
            break;
        case 'car_not_found':
            $wishlist_message = "Car not found.";
            break;
        case 'database_error':
            $wishlist_message = "Database error occurred.";
            break;
    }
}

// Get user's wishlist items if logged in
$user_wishlist = [];
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $user_id = $_SESSION['user_id'];
    $wishlist_stmt = $conn->prepare("SELECT car_id FROM wishlist WHERE user_id = ?");
    $wishlist_stmt->execute([$user_id]);
    while ($row = $wishlist_stmt->fetch()) {
        $user_wishlist[] = $row['car_id'];
    }
}

// Get search and filter parameters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$price_filter = isset($_GET['price_filter']) ? $_GET['price_filter'] : 'all';

// Function to filter cars by price range
function filterCarsByPrice($cars, $price_filter)
{
    if ($price_filter === 'all') {
        return $cars;
    }

    return array_filter($cars, function ($car) use ($price_filter) {
        // Remove currency symbols and convert to number
        $price = (float) preg_replace('/[^0-9.]/', '', $car['price']);

        switch ($price_filter) {
            case 'under-200k':
                return $price < 200000;
            case '200k-500k':
                return $price >= 200000 && $price <= 500000;
            case '500k-1m':
                return $price > 500000 && $price <= 1000000;
            case 'above-1m':
                return $price > 1000000;
            default:
                return true;
        }
    });
}

// Function to filter cars by search query
function filterCarsBySearch($cars, $search_query)
{
    if (empty($search_query)) {
        return $cars;
    }

    $search_query = strtolower($search_query);

    return array_filter($cars, function ($car) use ($search_query) {
        $searchable_text = strtolower($car['name'] . ' ' . $car['engine'] . ' ' . $car['description']);
        return strpos($searchable_text, $search_query) !== false;
    });
}

// Retrieve all car series and cars
$car_series_1 = getCarSeries(1); // Sport Series
$car_series_2 = getCarSeries(2); // Super Series
$car_series_3 = getCarSeries(3); // Ultimate Series

// Apply filters to each series
$car_series_1 = filterCarsByPrice(filterCarsBySearch($car_series_1, $search_query), $price_filter);
$car_series_2 = filterCarsByPrice(filterCarsBySearch($car_series_2, $search_query), $price_filter);
$car_series_3 = filterCarsByPrice(filterCarsBySearch($car_series_3, $search_query), $price_filter);

$all_cars = array_merge($car_series_1, $car_series_2, $car_series_3);

// Count results for display
$total_results = count($all_cars);

function getStockStatus($quantity)
{
    if ($quantity <= 0) {
        return ['class' => 'out-of-stock', 'text' => 'Out of Stock'];
    } elseif ($quantity <= 10) {
        return ['class' => 'low-stock', 'text' => $quantity . ' in stock'];
    } else {
        return ['class' => '', 'text' => $quantity . ' in stock'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Models - Rev Garage</title>
    <link rel="stylesheet" href="/assets/css/home.css" />
    <link rel="stylesheet" href="/assets/css/models.css" />
</head>

<body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollTrigger.min.js" crossorigin="anonymous"></script>

    <?php if (isset($wishlist_message)): ?>
        <div class="wishlist-notification" id="wishlistNotification">
            <?php echo htmlspecialchars($wishlist_message); ?>
        </div>
    <?php endif; ?>

    <main class="models-main">

        <section class="models-hero">
            <div class="models-hero-content">
                <h1 class="models-title">Models</h1>
                <p class="models-subtitle">Discover our complete collection of McLaren supercars, from the accessible Sport Series to the ultimate track-focused machines.</p>
                <div class="breadcrumb">
                    <a href="/pages/home.php">Home</a>
                    <span>→</span>
                    <span>Models</span>
                </div>
            </div>
        </section>

        <!-- Enhanced Filter and Search Section -->
        <section class="filter-section">
            <div class="filter-container">
                <h3 class="filter-title">Find Your Perfect McLaren</h3>

                <!-- Search Bar -->
                <form method="GET" class="search-form">
                    <div class="search-container">
                        <input
                            type="text"
                            name="search"
                            class="search-input"
                            placeholder="Search by model name, engine, or description..."
                            value="<?php echo htmlspecialchars($search_query); ?>" />
                        <button type="submit" class="search-btn">
                            <span class="search-icon">Search</span>
                        </button>
                    </div>
                    <!-- Preserve current price filter when searching -->
                    <input type="hidden" name="price_filter" value="<?php echo htmlspecialchars($price_filter); ?>" />
                </form>

                <!-- Price Filter -->
                <div class="filter-options">
                    <form method="GET" class="filter-form">
                        <!-- Preserve search query when filtering -->
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>" />

                        <button type="submit" name="price_filter" value="all" class="filter-btn <?php echo $price_filter === 'all' ? 'active' : ''; ?>">
                            All Models
                        </button>
                        <button type="submit" name="price_filter" value="under-200k" class="filter-btn <?php echo $price_filter === 'under-200k' ? 'active' : ''; ?>">
                            Under $200K
                        </button>
                        <button type="submit" name="price_filter" value="200k-500k" class="filter-btn <?php echo $price_filter === '200k-500k' ? 'active' : ''; ?>">
                            $200K - $500K
                        </button>
                        <button type="submit" name="price_filter" value="500k-1m" class="filter-btn <?php echo $price_filter === '500k-1m' ? 'active' : ''; ?>">
                            $500K - $1M
                        </button>
                        <button type="submit" name="price_filter" value="above-1m" class="filter-btn <?php echo $price_filter === 'above-1m' ? 'active' : ''; ?>">
                            Above $1M
                        </button>
                    </form>
                </div>

                <!-- Results Summary -->
                <div class="results-summary">
                    <?php if (!empty($search_query) || $price_filter !== 'all'): ?>
                        <p class="results-text">
                            Showing <?php echo $total_results; ?> result<?php echo $total_results !== 1 ? 's' : ''; ?>
                            <?php if (!empty($search_query)): ?>
                                for "<?php echo htmlspecialchars($search_query); ?>"
                            <?php endif; ?>
                            <?php if ($price_filter !== 'all'): ?>
                                in <?php
                                    $filter_labels = [
                                        'under-200k' => 'Under $200K',
                                        '200k-500k' => '$200K - $500K',
                                        '500k-1m' => '$500K - $1M',
                                        'above-1m' => 'Above $1M'
                                    ];
                                    echo $filter_labels[$price_filter] ?? $price_filter;
                                    ?> range
                            <?php endif; ?>
                        </p>
                        <?php if (!empty($search_query) || $price_filter !== 'all'): ?>
                            <a href="models.php" class="clear-filters-btn">Clear All Filters</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="results-text">Showing all <?php echo $total_results; ?> models</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <main class="models-main">
            <?php if (count($car_series_1) > 0): ?>
                <section id="sport-series" class="series-section">
                    <div class="series-header">
                        <h2 class="series-title">Sport Series</h2>
                        <p class="series-description">The perfect introduction to McLaren's world of supercars, combining everyday usability with track-ready performance.</p>
                    </div>
                    <div class="cars-grid">
                        <?php foreach ($car_series_1 as $car): ?>
                            <?php $stockStatus = getStockStatus($car["stock_quantity"]); ?>
                            <div class="car-card">
                                <div class="car-image">
                                    <img src="<?php echo $car["image_url"] ?>" alt="<?php echo $car["name"] ?>" data-car-id="<?php echo $car["car_id"] ?>" />
                                </div>
                                <div class="car-info">
                                    <h3 class="car-name"><?php echo $car["name"] ?></h3>
                                    <div class="car-price"><?php echo $car["price"] ?></div>
                                    <div class="car-specs">
                                        <div class="spec-item"><span class="spec-label">Engine: </span><span class="spec-value"><?php echo $car["engine"] ?></span></div>
                                        <div class="spec-item"><span class="spec-label">Power:</span><span class="spec-value"><?php echo $car["power_hp"] ?></span></div>
                                        <div class="spec-item"><span class="spec-label">Doors:</span><span class="spec-value"><?php echo $car["doors"] ?></span></div>
                                        <div class="spec-item">
                                            <span class="spec-label">0-60 mph:</span>
                                            <span class="spec-value"><?php echo $car["acceleration_0_60"] ?></span>
                                            <div class="stock-indicator">
                                                <span class="stock-count <?php echo $stockStatus['class'] ?>"><?php echo $stockStatus['text'] ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="details-btn" data-target="#car-details-modal-<?php echo $car["car_id"] ?>">More Details</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (count($car_series_2) > 0): ?>
                <section id="super-series" class="series-section">
                    <div class="series-header">
                        <h2 class="series-title">Super Series</h2>
                        <p class="series-description">The pinnacle of McLaren's engineering excellence, delivering uncompromising performance and cutting-edge technology.</p>
                    </div>
                    <div class="cars-grid">
                        <?php foreach ($car_series_2 as $car): ?>
                            <?php $stockStatus = getStockStatus($car["stock_quantity"]); ?>
                            <div class="car-card">
                                <div class="car-image">
                                    <img src="<?php echo $car["image_url"] ?>" alt="<?php echo $car["name"] ?>" data-car-id="<?php echo $car["car_id"] ?>" />
                                </div>
                                <div class="car-info">
                                    <h3 class="car-name"><?php echo $car["name"] ?></h3>
                                    <div class="car-price"><?php echo $car["price"] ?></div>
                                    <div class="car-specs">
                                        <div class="spec-item"><span class="spec-label">Engine: </span><span class="spec-value"><?php echo $car["engine"] ?></span></div>
                                        <div class="spec-item"><span class="spec-label">Power:</span><span class="spec-value"><?php echo $car["power_hp"] ?></span></div>
                                        <div class="spec-item"><span class="spec-label">Doors:</span><span class="spec-value"><?php echo $car["doors"] ?></span></div>
                                        <div class="spec-item">
                                            <span class="spec-label">0-60 mph:</span>
                                            <span class="spec-value"><?php echo $car["acceleration_0_60"] ?></span>
                                            <div class="stock-indicator">
                                                <span class="stock-count <?php echo $stockStatus['class'] ?>"><?php echo $stockStatus['text'] ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="details-btn" data-target="#car-details-modal-<?php echo $car["car_id"] ?>">More Details</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (count($car_series_3) > 0): ?>
                <section id="ultimate-series" class="series-section">
                    <div class="series-header">
                        <h2 class="series-title">Ultimate Series</h2>
                        <p class="series-description">The most exclusive and extreme McLaren machines, representing the absolute pinnacle of automotive engineering and performance.</p>
                    </div>
                    <div class="cars-grid">
                        <?php foreach ($car_series_3 as $car): ?>
                            <?php $stockStatus = getStockStatus($car["stock_quantity"]); ?>
                            <div class="car-card">
                                <div class="car-image">
                                    <img src="<?php echo $car["image_url"] ?>" alt="<?php echo $car["name"] ?>" data-car-id="<?php echo $car["car_id"] ?>" />
                                </div>
                                <div class="car-info">
                                    <h3 class="car-name"><?php echo $car["name"] ?></h3>
                                    <div class="car-price"><?php echo $car["price"] ?></div>
                                    <div class="car-specs">
                                        <div class="spec-item"><span class="spec-label">Engine: </span><span class="spec-value"><?php echo $car["engine"] ?></span></div>
                                        <div class="spec-item"><span class="spec-label">Power:</span><span class="spec-value"><?php echo $car["power_hp"] ?></span></div>
                                        <div class="spec-item"><span class="spec-label">Doors:</span><span class="spec-value"><?php echo $car["doors"] ?></span></div>
                                        <div class="spec-item">
                                            <span class="spec-label">0-60 mph:</span>
                                            <span class="spec-value"><?php echo $car["acceleration_0_60"] ?></span>
                                            <div class="stock-indicator">
                                                <span class="stock-count <?php echo $stockStatus['class'] ?>"><?php echo $stockStatus['text'] ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="details-btn" data-target="#car-details-modal-<?php echo $car["car_id"] ?>">More Details</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- No Results Message -->
            <?php if ($total_results === 0): ?>
                <section class="no-results-section">
                    <div class="no-results-content">
                        <h2 class="no-results-title">No McLaren Models Found</h2>
                        <p class="no-results-text">
                            <?php if (!empty($search_query)): ?>
                                We couldn't find any McLaren models matching "<?php echo htmlspecialchars($search_query); ?>".
                            <?php else: ?>
                                No McLaren models found in the selected price range.
                            <?php endif; ?>
                        </p>
                        <p class="no-results-suggestion">Try adjusting your search terms or price filter to see more results.</p>
                        <a href="models.php" class="reset-search-btn">View All Models</a>
                    </div>
                </section>
            <?php endif; ?>
        </main>

        <?php foreach ($all_cars as $car): ?>
            <div id="car-details-modal-<?php echo $car["car_id"] ?>" class="modal">
                <div class="modal-content">
                    <span class="close-btn">&times;</span>
                    <div class="modal-content-container">
                        <img src="<?php echo $car["image_url"] ?>" alt="<?php echo $car["name"] ?>" class="modal-car-image" />
                        <h2><?php echo $car["name"] ?></h2>
                        <div class="modal-specs">
                            <p><strong>Price:</strong> <?php echo number_format($car["price"], 2) ?></p>
                            <p><strong>Engine:</strong> <?php echo $car["engine"] ?></p>
                            <p><strong>Power:</strong> <?php echo $car["power_hp"] ?> hp</p>
                            <p><strong>Doors:</strong> <?php echo $car["doors"] ?></p>
                            <p><strong>0-60 mph:</strong> <?php echo $car["acceleration_0_60"] ?></p>
                        </div>
                        <p class="modal-description"><?php echo $car["description"] ?></p>
                        <div class="modal-buttons">
                            <button class="add-to-garage-btn">Add to Garage</button>

                            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="car_id" value="<?php echo $car['car_id']; ?>">
                                    <?php if (in_array($car['car_id'], $user_wishlist)): ?>
                                        <input type="hidden" name="action" value="remove_from_wishlist">
                                        <button type="submit" class="wishlist-btn active">
                                            <span class="heart-icon">♥</span>
                                        </button>
                                    <?php else: ?>
                                        <input type="hidden" name="action" value="add_to_wishlist">
                                        <button type="submit" class="wishlist-btn">
                                            <span class="heart-icon">♡</span>
                                        </button>
                                    <?php endif; ?>
                                </form>
                            <?php else: ?>
                                <button class="wishlist-btn" onclick="alert('Please log in to use wishlist!')">
                                    <span class="heart-icon">♡</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php include('../includes/footer.php'); ?>
        <script src="/assets/js/models.js"></script>
        <?php include 'chatbot.php'; ?>
</body>

</html>