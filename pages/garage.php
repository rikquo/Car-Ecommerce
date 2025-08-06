  <?php
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
   if ($_SESSION['user_role'] === 'admin' && !isset($_GET['noredirect'])) {
    header('Location: /admin/adminhome.php');
    exit();
} else {
        // User is logged in, include the user-specific navbar
        include('../includes/user_nav.php');
    }
} else {
    // User is not logged in, include the default navbar
    include('../includes/navbar.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Garage Collection - Rev Garage</title>
  <link rel="stylesheet" href="/assets/css/garage.css" />
</head>

<body>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollTrigger.min.js"
    crossorigin="anonymous"></script>

  <main class="garage-main">
    <!-- Hero Section -->
    <section class="garage-hero">
      <h1 class="garage-title">Your Garage Collection</h1>
      <p class="garage-subtitle">
        Review your selected McLaren vehicles and proceed with your exclusive automotive investment.
      </p>
    </section>

    <!-- Main Content -->
    <div class="collection-container" id="collectionContainer">
      <!-- Selected Cars Section -->
      <div class="selected-cars">
        <div class="section-header">
          <h2 class="section-title">Selected Vehicles (<span id="carCount">2</span>)</h2>
          <button class="clear-all-btn" onclick="clearAllCars()">Clear All</button>
        </div>

        <div id="carsList">
          <!-- Car Item 1 -->
          <div class="car-item" data-car-id="1" data-price="310000">
            <div class="car-image-small">
              <img src="/placeholder.svg?height=100&width=150" alt="McLaren 720S" />
            </div>
            <div class="car-details">
              <h3 class="car-name-small">McLaren 720S</h3>
              <div class="car-specs-small">
                <span class="spec-small">4.0L Twin-Turbo V8</span>
                <span class="spec-small">710 HP</span>
                <span class="spec-small">0-60: 2.8s</span>
              </div>
              <div class="car-actions">
                <span class="car-price-small">$310,000</span>
                <button class="remove-btn" onclick="removeCar(1)">Remove</button>
              </div>
            </div>
          </div>

          <!-- Car Item 2 -->
          <div class="car-item" data-car-id="2" data-price="215000">
            <div class="car-image-small">
              <img src="/placeholder.svg?height=100&width=150" alt="McLaren 570S" />
            </div>
            <div class="car-details">
              <h3 class="car-name-small">McLaren 570S</h3>
              <div class="car-specs-small">
                <span class="spec-small">3.8L Twin-Turbo V8</span>
                <span class="spec-small">562 HP</span>
                <span class="spec-small">0-60: 3.2s</span>
              </div>
              <div class="car-actions">
                <span class="car-price-small">$215,000</span>
                <button class="remove-btn" onclick="removeCar(2)">Remove</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State (hidden by default) -->
        <div class="empty-garage" id="emptyState" style="display: none;">
          <div class="empty-icon">🏎️</div>
          <h2 class="empty-title">Your Garage is Empty</h2>
          <p class="empty-description">
            Start building your dream collection by exploring our exceptional range of McLaren supercars.
          </p>
          <a href="models.php">
            <button class="browse-btn">Browse Our Collection</button>
          </a>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="order-summary" id="orderSummary">
        <h2 class="summary-title">Investment Summary</h2>
        
        <div class="summary-row">
          <span class="summary-label">Subtotal (<span id="summaryCarCount">2</span> vehicles)</span>
          <span class="summary-value" id="subtotalAmount">$525,000</span>
        </div>
        
        <div class="summary-row">
          <span class="summary-label">Processing & Documentation</span>
          <span class="summary-value">$2,500</span>
        </div>
        
        <div class="summary-row total-row">
          <span class="total-label">Total Investment</span>
          <span class="total-value" id="totalAmount">$527,500</span>
        </div>

        <div class="action-buttons">
          <button class="primary-action-btn" onclick="proceedToConsultation()">
            Proceed to Consultation
          </button>
          <button class="secondary-action-btn" onclick="requestCustomQuote()">
            Request Custom Quote
          </button>
        </div>

        <!-- Financing Options -->
        <div class="financing-section">
          <h3 class="financing-title">Financing Options</h3>
          <div class="financing-options">
            <div class="financing-option">
              <span class="financing-label">60 months @ 3.9% APR</span>
              <span class="financing-value" id="financing60">$9,759/mo</span>
            </div>
            <div class="financing-option">
              <span class="financing-label">72 months @ 4.2% APR</span>
              <span class="financing-value" id="financing72">$8,335/mo</span>
            </div>
            <div class="financing-option">
              <span class="financing-label">84 months @ 4.5% APR</span>
              <span class="financing-value" id="financing84">$7,280/mo</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

 <?php include('../includes/footer.php'); ?>

  <script src="/assets/js/garage.js"></script>
</body>

</html>
