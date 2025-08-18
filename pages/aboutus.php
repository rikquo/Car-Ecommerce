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
  <title>About Us - Rev Garage</title>
  <link rel="stylesheet" href="/assets/css/home.css" />
  <link rel="stylesheet" href="/assets/css/aboutus.css" />
</head>

<body>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollTrigger.min.js"
    crossorigin="anonymous"></script>

  <main class="about-main">
    <section class="about-hero">
      <div class="about-hero-content">
        <div class="about-hero-left">
          <h1 class="about-title">About Us</h1>
          <p class="about-subtitle">Discover the passion and precision behind Rev Garage, where automotive excellence meets uncompromising performance.</p>
          <div class="breadcrumb">
            <a href="/pages/home.php">Home</a>
            <span>→</span>
            <span>About Us</span>
          </div>
        </div>
        <div class="about-hero-right">
          <div class="hero-circle">
            <img src="/assets/img/w1(3).jpg?height=400&width=400" alt="McLaren Heritage" />
          </div>
        </div>
      </div>
    </section>

    <!-- Who We Are Section -->
    <section class="who-we-are">
      <div class="who-we-are-content">
        <div class="who-we-are-left">
          <img src="/assets/img/720swallpaper.jpg?height=500&width=600" alt="McLaren Team" />
        </div>
        <div class="who-we-are-right">
          <h3 class="section-subtitle">Who We Are</h3>
          <h2 class="section-title">A Legacy Of Racing Excellence</h2>
          <p class="section-description">
            At Rev Garage, we embody the spirit of McLaren's racing heritage. Founded on the principles of innovation, performance, and precision, we bring you the finest collection of McLaren supercars and hypercars.
          </p>
          <div class="features-list">
            <div class="feature-item">
              <span class="feature-icon">⟢</span>
              <span>Racing DNA in every vehicle</span>
            </div>
            <div class="feature-item">
              <span class="feature-icon">⚡︎</span>
              <span>Cutting-edge technology</span>
            </div>
            <div class="feature-item">
              <span class="feature-icon">🔧</span>
              <span>Expert craftsmanship</span>
            </div>
            <div class="feature-item">
              <span class="feature-icon">☆</span>
              <span>Uncompromising quality</span>
            </div>
          </div>
          <a href="/pages/models.php"><button class="cta-button">Explore Our Collection</button></a>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
      <div class="stats-container">
        <div class="stats-left">
          <h3 class="stats-subtitle">About Us</h3>
          <h2 class="stats-title">McLaren Excellence For Everyone</h2>
          <p class="stats-description">
            With decades of experience in automotive excellence, we've established ourselves as the premier destination for McLaren enthusiasts and collectors worldwide.
          </p>
        </div>
        <div class="stats-right">
          <div class="stats-grid">
            <div class="stat-box">
              <span class="stat-number">500+</span>
              <span class="stat-label">Happy Customers</span>
            </div>
            <div class="stat-box">
              <span class="stat-number">15</span>
              <span class="stat-label">Years of Experience</span>
            </div>
            <div class="stat-box">
              <span class="stat-number">3</span>
              <span class="stat-label">Series Available</span>
            </div>
            <div class="stat-box">
              <span class="stat-number">24/7</span>
              <span class="stat-label">Customer Support</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="cta-overlay">
        <div class="cta-content">
          <h2 class="cta-title">Let's Drive Excellence Together</h2>
          <p class="cta-description">
            Experience the thrill of McLaren's engineering mastery. From Sport Series to Ultimate Series, find your perfect match.
          </p>
          <a href="/pages/models.php"><button class="cta-main-button">View Our Models</button></a>
        </div>
      </div>
    </section>
  </main>

  <?php include('../includes/footer.php'); ?>

  <script src="/assets/js/aboutus.js"></script>
  <?php include 'chatbot.php'; ?>
</body>

</html>