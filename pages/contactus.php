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
  <title>Contact Us - Rev Garage</title>
  <link rel="stylesheet" href="/assets/css/home.css" />
  <link rel="stylesheet" href="/assets/css/contactus.css" />
</head>

<body>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollTrigger.min.js"
    crossorigin="anonymous"></script>

  <main class="contact-main">
    <div class="contact-bg-text">CONTACT</div>

    <div class="gdcolor"></div>

    <div class="geometric-bg">
      <div class="geo-line geo-line-1"></div>
      <div class="geo-line geo-line-2"></div>
      <div class="geo-line geo-line-3"></div>
      <div class="geo-circle geo-circle-1"></div>
      <div class="geo-circle geo-circle-2"></div>

      <div class="blur-circle"></div>
      <div class="blur-circle-2"></div>
      <div class="blur-circle-3"></div>
    </div>

    <div class="contact-container">
      <!-- Left Side - Contact Info -->
      <div class="contact-left">
        <div class="contact-badge">
          <span class="badge-icon">📍</span>
          <span>Contact</span>
        </div>

        <h1 class="contact-title">Get in touch</h1>
        <p class="contact-subtitle">
          Ready to experience McLaren excellence? Have questions about our collection or need assistance with your purchase?
        </p>

        <div class="contact-cards">
          <div class="contact-card">
            <div class="card-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <polyline points="22,6 12,13 2,6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
            <div class="card-content">
              <h3>Email us</h3>
              <p>info@revgarage.com</p>
            </div>
            <div class="card-arrow">→</div>
          </div>

          <div class="contact-card">
            <div class="card-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 16.92V19.92C22 20.52 21.39 21 20.77 21C9.39 21 0 11.61 0 0.23C0 -0.39 0.48 -1 1.08 -1H4.08C4.68 -1 5.08 -0.39 5.08 0.23C5.08 3.23 5.78 6.23 7.08 8.93C7.38 9.53 7.18 10.23 6.68 10.63L4.68 12.23C6.28 15.83 9.17 18.72 12.77 20.32L14.37 18.32C14.77 17.82 15.47 17.62 16.07 17.92C18.77 19.22 21.77 19.92 24.77 19.92C25.39 19.92 26 20.4 26 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
            <div class="card-content">
              <h3>Call us</h3>
              <p>+95 9 123 456 789</p>
            </div>
            <div class="card-arrow">→</div>
          </div>

          <div class="contact-card">
            <div class="card-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21 10C21 17 12 23 12 23S3 17 3 10C3 5.03 7.03 1 12 1S21 5.03 21 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </div>
            <div class="card-content">
              <h3>Our location</h3>
              <p>Yangon, Myanmar<br>Southeast Asia</p>
            </div>
            <div class="card-arrow">→</div>
          </div>
        </div>
      </div>

      <!-- Right Side - Contact Form -->
      <div class="contact-right">
        <form class="contact-form" id="contactForm">
          <div class="form-group">
            <input type="text" id="name" name="name" placeholder="Name" required>
          </div>

          <div class="form-group">
            <input type="email" id="email" name="email" placeholder="Email" required>
          </div>

          <div class="form-group">
            <input type="tel" id="phone" name="phone" placeholder="Phone Number">
          </div>

          <div class="form-group">
            <select id="interest" name="interest" required>
              <option value="">Select your interest</option>
              <option value="sport-series">Sport Series</option>
              <option value="super-series">Super Series</option>
              <option value="ultimate-series">Ultimate Series</option>
              <option value="general-inquiry">General Inquiry</option>
              <option value="test-drive">Test Drive</option>
              <option value="service">Service & Support</option>
            </select>
          </div>

          <div class="form-group">
            <textarea id="message" name="message" placeholder="Message" rows="6" required></textarea>
          </div>

          <button type="submit" class="submit-btn">
            <span>Send Message</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              <polygon points="22,2 15,22 11,13 2,9 22,2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </form>
      </div>
    </div>

    <!-- Added FAQ Section -->
    <div class="faq-section">
      <div class="faq-container">
        <h2 class="faq-title">Frequently Asked Questions</h2>
        <div class="faq-items">
          <div class="faq-item">
            <div class="faq-question">
              <h3>How can I schedule a test drive?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>You can schedule a test drive by calling us at +95 9 123 456 789, emailing info@revgarage.com, or using the contact form above. Select "Test Drive" from the interest dropdown and we'll get back to you within 24 hours.</p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">
              <h3>What McLaren models do you have available?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>We carry the complete McLaren lineup including Sport Series, Super Series, and Ultimate Series models. Our inventory changes regularly, so please contact us for current availability and pricing.</p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">
              <h3>Do you offer financing options?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>Yes, we work with various financial institutions to provide competitive financing options. Our finance team can help you find the best solution for your needs. Contact us to discuss your requirements.</p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">
              <h3>What services does your service center provide?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>Our certified McLaren technicians provide comprehensive services including routine maintenance, repairs, performance upgrades, and warranty work. We use only genuine McLaren parts and offer 24/7 roadside assistance.</p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question">
              <h3>What are your showroom hours?</h3>
              <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
              <p>Monday - Friday: 9:00 AM - 7:00 PM<br>Saturday: 9:00 AM - 6:00 PM<br>Sunday: 10:00 AM - 5:00 PM<br><br>We recommend calling ahead to ensure availability for test drives and consultations.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Additional Info Section -->
    <div class="additional-info">
      <div class="info-container">
        <div class="info-item">
          <h3>Showroom Hours</h3>
          <p>Monday - Friday: 9:00 AM - 7:00 PM<br>
            Saturday: 9:00 AM - 6:00 PM<br>
            Sunday: 10:00 AM - 5:00 PM</p>
        </div>
        <div class="info-item">
          <h3>Service Center</h3>
          <p>Expert McLaren technicians<br>
            Genuine parts & accessories<br>
            24/7 roadside assistance</p>
        </div>
        <div class="info-item">
          <h3>Test Drives</h3>
          <p>Experience McLaren performance<br>
            Professional guidance<br>
            Book your appointment today</p>
        </div>
      </div>
    </div>
  </main>

  <?php include('../includes/footer.php'); ?>

  <script src="/assets/js/contactus.js"></script>
  <?php include 'chatbot.php'; ?>
</body>

</html>