<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "revgaragedb";
$conn = new mysqli($servername, $username, $password, $dbname);

$db_error = false;
$review_message = "";
$message_type = "";

if ($conn->connect_error) {
  $db_error = true;
  $review_message = "Database connection failed: " . $conn->connect_error;
  $message_type = "error";
} else {

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
      $review_message = "You must be logged in to submit a review.";
      $message_type = "error";
    } else {
      $user_id = $_SESSION['user_id'];
      $car_id = $_POST['car_id'] ?? null;
      $rating = $_POST['rating'] ?? null;
      $review_title = htmlspecialchars(trim($_POST['review_title'] ?? ''));
      $review_text = htmlspecialchars(trim($_POST['review_text'] ?? ''));
      $is_approved = 0;

      if (empty($car_id) || empty($rating) || empty($review_title) || empty($review_text) || !is_numeric($rating) || $rating < 1 || $rating > 5) {
        $review_message = "All fields (Car, Rating, Title, Review) are required and rating must be between 1 and 5.";
        $message_type = "error";
      } else {
        // insert review
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, car_id, rating, review_title, review_text, is_approved) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
          $stmt->bind_param("iiissi", $user_id, $car_id, $rating, $review_title, $review_text, $is_approved);
          if ($stmt->execute()) {
            $review_message = "Review submitted successfully! It is pending administrator approval.";
            $message_type = "success";
          } else {
            $review_message = "Error submitting review: " . $stmt->error;
            $message_type = "error";
          }
          $stmt->close();
        } else {
          $review_message = "Database prepare statement failed: " . $conn->error;
          $message_type = "error";
        }
      }
    }
  }


  $review_query = "SELECT r.*, u.username, u.profile_picture, c.name as car_name
                     FROM reviews r
                     JOIN users u ON r.user_id = u.user_id
                     LEFT JOIN cars c ON r.car_id = c.car_id
                     WHERE r.is_approved = 1
                     ORDER BY r.created_at DESC
                     LIMIT 6";
  $review_result = $conn->query($review_query);
  $cars_query = "SELECT car_id, name, series_id FROM cars ORDER BY name ASC";
  $cars_result = $conn->query($cars_query);
  $available_cars = [];
  if ($cars_result) {
    while ($row = $cars_result->fetch_assoc()) {
      $available_cars[] = $row;
    }
  }
}

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rev Garage</title>
  <link rel="stylesheet" href="/assets/css/home.css" />
</head>

<body>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollTrigger.min.js"
    crossorigin="anonymous"></script>

  <section id="home">
    <div class="container1">
      <div class="hero-wrapper">
        <h1 class="rev-title">Rev Garage</h1>
        <h3 class="rev">"Built for Speed. Driven by Passion."</h3>
        <div class="overlay">
          <img class="image" src="/assets/img/maclaren.jpg" alt="maclaren" height="100%" width="100%" />
        </div>
      </div>
    </div>

    <div class="container2">
      <section class="video-section">
        <video class="scroll-video" src="/assets/img/The Mclaren 600LT.mp4" autoplay muted loop playsinline></video>
      </section>
    </div>
  </section>

  <section id="sport-series">
    <div class="txtSport"> Sport Series </div>

    <div class="container3">
      <div class="sideimgcon">
        <div class="sideimg leftimg">
          <img src="/assets/img/570s.jpg" alt="left car" width="100%">
        </div>
        <div class="sideimg rightimg">
          <h2>Mclaren 570S</h2>
          <h3>The McLaren 570S is a sports car produced by McLaren Automotive from 2015 to 2021. It is part of the brand's
            Sports Series and is known for its mid-engine, rear-wheel-drive layout, carbon fiber chassis, and powerful
            twin-turbo V8 engine.</h3>
          <a href="/pages/models.php"> <button class="btnmore">
              View More
            </button>
          </a>
        </div>
      </div>

      <div class="sideimgcon2">
        <div class="sideimg2 leftimg2">
          <img src="/assets/img/540c.jpg" alt="left car" width="100%">
        </div>
        <div class="sideimg2 rightimg2">
          <h2>Mclaren 540C</h2>
          <h3>The McLaren 540C is considered an entry-level sports car within the McLaren lineup, designed to introduce
            the brand to a wider audience. It's a mid-engine sports car featuring a 3.8-liter twin-turbocharged V8 engine
            producing 540 horsepower. </h3>
          <a href="/pages/models.php"> <button class="btnmore">
              View More
            </button>
          </a>
        </div>
      </div>

      <div class="sideimgcon3">
        <div class="sideimg3 leftimg3">
          <img src="/assets/img/600Lt.jpg" alt="left car" width="100%">
        </div>
        <div class="sideimg3 rightimg3">
          <h2>Mclaren 600LT</h2>
          <h3>The McLaren 600LT is a high-performance, track-focused supercar known for its extreme light weight, powerful
            engine, and engaging driving experience. It's the fourth chapter in McLaren's "Longtail" story, emphasizing a
            focus on reduced weight, increased power, and enhanced driver engagement. </h3>
          <a href="/pages/models.php"> <button class="btnmore">
              View More
            </button>
          </a>
        </div>
      </div>
    </div>
  </section>

  <div class="container4">
    <section class="video-section2">
      <video class="scroll-video2" src="/assets/img/720s vid.mp4" width="100%" autoplay muted loop playsinline></video>
    </section>
  </div>

  <section id="super-series">
    <div class="txtSuper"> Super Series </div>

    <div class="container5">
      <div class="super-sideimgcon">
        <div class="super-sideimg super-leftimg">
          <img src="/assets/img/720s.jpg" alt="left car" width="100%">
        </div>
        <div class="super-sideimg super-rightimg">
          <h2>Mclaren 720S</h2>
          <h3>The "720S" refers to the McLaren 720S, a high-performance supercar produced by McLaren Automotive. It is
            known for its sleek, aerodynamic design and powerful engine. The "720" in its name signifies the metric
            horsepower output of its twin-turbocharged V8 engine. </h3>
          <a href="/pages/models.php"> <button class="btnmore">
              View More
            </button>
          </a>
        </div>
      </div>

      <div class="super-sideimgcon2">
        <div class="super-sideimg2 super-leftimg2">
          <img src="/assets/img/765lt.jpg" alt="left car" width="100%">
        </div>
        <div class="super-sideimg2 super-rightimg2">
          <h2>Mclaren 765LT</h2>
          <h3>The McLaren 765LT pushes the boundaries of performance and engagement. With extreme aerodynamics, reduced
            weight, and brutal power delivery, it’s a Longtail born for the track but capable on the road. It’s McLaren at
            its most intense.</h3>
          <a href="/pages/models.php"> <button class="btnmore">
              View More
            </button>
          </a>
        </div>
      </div>

      <div class="super-sideimgcon3">
        <div class="super-sideimg3 super-leftimg3">
          <img src="/assets/img/675LT spider .jpg" alt="left car" width="100%">
        </div>
        <div class="super-sideimg3 super-rightimg3">
          <h2>Mclaren 675LT</h2>
          <h3>The McLaren 675LT Spider is a high-performance, track-focused convertible version of the 675LT. It's known
            for its lightweight construction, powerful engine, and engaging driving experience. Essentially, it takes the
            675LT coupe and adds a retractable hardtop for open-top driving.</h3>
          <a href="/pages/models.php"> <button class="btnmore">
              View More
            </button>
          </a>
        </div>
      </div>
    </div>
  </section>

  <div class="container6">
    <section class="video-section3">
      <video class="scroll-video3" src="/assets/img/McLaren P1.mp4" width="100%" autoplay muted loop playsinline></video>
    </section>
  </div>

  <section id="ultimate-series">
    <div class="txtUltimate"> Ultimate Series </div>

    <div class="container7">
      <div class="ulti-sideimgcon">
        <div class="ulti-sideimg ulti-leftimg">
          <img src="/assets/img/P1.jpg" alt="left car" width="100%">
        </div>
        <div class="ulti-sideimg ulti-rightimg">
          <h2>Mclaren P1</h2>
          <h3>
            The McLaren P1 is a limited-production hybrid sports car, considered one of the "Holy Trinity" of hypercars alongside the Porsche 918 Spyder and Ferrari LaFerrari. It boasts a 3.8-liter twin-turbo V8 engine combined with a hybrid system, delivering a total output of 916 horsepower. The P1 is known for its advanced aerodynamics, including a Formula 1-1inspired Drag Reduction System (DRS).
          </h3>
          <a href="/pages/models.php"> <button class="btnmore">
              View More
            </button>
          </a>
        </div>
      </div>

      <div class="ulti-sideimgcon2">
        <div class="ulti-sideimg2 ulti-leftimg2">
          <img src="/assets/img/senna.jpg" alt="left car" width="100%">
        </div>
        <div class="ulti-sideimg2 ulti-rightimg2">
          <h2>Mclaren Senna</h2>
          <h3>
            The McLaren Senna is a track-focused hypercar named after legendary Formula 1 driver Ayrton Senna. It features a 4.0-liter twin-turbocharged V8 engine producing 789 horsepower, making it the most powerful McLaren road car at its launch. The Senna is designed for ultimate performance with extreme aerodynamics and lightweight construction, weighing just 1,198 kg.
          </h3>
          <a href="/pages/models.php"> <button class="btnmore">
              View More
            </button>
          </a>
        </div>
      </div>

      <div class="ulti-sideimgcon3">
        <div class="ulti-sideimg3 ulti-leftimg3">
          <img src="/assets/img/speedtail.jpg" alt="left car" width="100%">
        </div>
        <div class="ulti-sideimg3 ulti-rightimg3">
          <h2>Mclaren SpeedTail</h2>
          <h3>
            The McLaren Speedtail is a limited-production, three-seat hypercar known for its speed and aerodynamic design. It's the fastest McLaren ever, reaching a top speed of 250 mph. It's also the first Hyper-GT from McLaren, blending high speed with luxurious features.
          </h3>
          <a href="/pages/models.php"> <button class="btnmore">
              View More
            </button>
          </a>
        </div>
      </div>
    </div>
  </section>

  <section id="reviews">
    <div class="txtReviews">Customer Reviews</div>

    <div class="container8">
      <div class="reviews-grid">
        <?php
        if (!$db_error && $review_result && $review_result->num_rows > 0) {

          while ($review = $review_result->fetch_assoc()) {

            $stars = str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']);

            $profile_pic = !empty($review['profile_picture']) ? $review['profile_picture'] : '/assets/img/default-avatar.png';


            $car_info = "";
            if ($review['is_verified_purchase'] == 1 && $review['car_name']) {
              $car_info = htmlspecialchars($review['car_name']) . ' Owner (Verified)';
            } elseif ($review['car_name']) {
              $car_info = htmlspecialchars($review['car_name']) . ' Owner';
            } else {
              $car_info = 'Rev Garage Customer';
            }

            echo '<div class="review-card">';
            echo '<div class="review-stars"><span>' . $stars . '</span></div>';
            echo '<p class="review-text">"' . htmlspecialchars($review['review_text']) . '"</p>';
            echo '<div class="review-author">';
            echo '<img src="' . htmlspecialchars($profile_pic) . '" alt="Customer" class="review-avatar" onerror="this.src=\'/assets/img/default-avatar.png\'">';
            echo '<div class="review-info">';
            echo '<h4>' . htmlspecialchars($review['username']) . '</h4>';
            echo '<span>' . $car_info . '</span>';
            echo '</div></div></div>';
          }
        } else {

        ?>
          <div class="review-card">
            <div class="review-stars">
              <span>★★★★★</span>
            </div>
            <p class="review-text">"Absolutely incredible experience! The McLaren 720S exceeded all my expectations. The team at Rev Garage made the entire process seamless."</p>
            <div class="review-author">
              <img src="/assets/img/default-avatar.png" alt="Customer" class="review-avatar">
              <div class="review-info">
                <h4>Michael Chen</h4>
                <span>McLaren 720S Owner</span>
              </div>
            </div>
          </div>

          <div class="review-card">
            <div class="review-stars">
              <span>★★★★★</span>
            </div>
            <p class="review-text">"The P1 is a masterpiece! Rev Garage's expertise and passion for McLaren is unmatched. Couldn't be happier with my purchase."</p>
            <div class="review-author">
              <img src="/assets/img/default-avatar.png" alt="Customer" class="review-avatar">
              <div class="review-info">
                <h4>Sarah Johnson</h4>
                <span>McLaren P1 Owner</span>
              </div>
            </div>
          </div>

          <div class="review-card">
            <div class="review-stars">
              <span>★★★★★</span>
            </div>
            <p class="review-text">"From browsing to delivery, Rev Garage provided exceptional service. My 570S is everything I dreamed of and more!"</p>
            <div class="review-author">
              <img src="/assets/img/default-avatar.png" alt="Customer" class="review-avatar">
              <div class="review-info">
                <h4>David Rodriguez</h4>
                <span>McLaren 570S Owner</span>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>

      <div class="review-cta">
        <h3>Share Your Experience</h3>
        <p>Own a McLaren from Rev Garage? We'd love to hear about your experience!</p>
        <button class="btnmore review-btn" onclick="openReviewModal()">Write a Review</button>
      </div>
    </div>
  </section>

  <div id="reviewModal" class="review-modal">
    <div class="review-modal-content">
      <div class="review-modal-header">
        <h2>Write a Review</h2>
        <span class="close-button" onclick="closeReviewModal()">&times;</span>
      </div>
      <form id="reviewForm" action="home.php" method="POST" class="review-form">
        <input type="hidden" name="submit_review" value="1">
        <input type="hidden" name="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">

        <?php if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) { ?>
          <p class="login-message">Please <a href="/pages/lo">log in</a> to submit a review.</p>
        <?php } else { ?>
          <div class="form-group">
            <label for="carSelect">McLaren Model:</label>
            <select name="car_id" id="carSelect" class="form-control" required>
              <option value="">Select a McLaren model...</option>
              <?php
              $cars_by_series = [];
              foreach ($available_cars as $car) {
                echo '<option value="' . htmlspecialchars($car['car_id']) . '">' . htmlspecialchars($car['name']) . '</option>';
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label for="rating">Your Rating:</label>
            <div class="star-rating" id="starRating">
              <span class="star" data-value="1">&#9733;</span>
              <span class="star" data-value="2">&#9733;</span>
              <span class="star" data-value="3">&#9733;</span>
              <span class="star" data-value="4">&#9733;</span>
              <span class="star" data-value="5">&#9733;</span>
            </div>
            <input type="hidden" name="rating" id="ratingInput" required>
          </div>

          <div class="form-group">
            <label for="review_title">Review Title (Max 50 chars):</label>
            <input type="text" name="review_title" id="review_title" class="form-control" maxlength="50" placeholder="e.g., Amazing Car, Great Service!" required>
            <div class="char-count" id="titleCharCount">0/50</div>
          </div>

          <div class="form-group">
            <label for="review_text">Your Review (Max 500 chars):</label>
            <textarea name="review_text" id="review_text_area" class="form-control" rows="5" maxlength="500" placeholder="Share your experience..." required></textarea>
            <div class="char-count" id="textCharCount">0/500</div>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="closeReviewModal()">Cancel</button>
            <button type="submit" class="btn-submit" id="submitReviewBtn">Submit Review</button>
          </div>
        <?php } ?>
      </form>
      <?php if (!empty($review_message)) { ?>
        <div class="review-message <?php echo $message_type; ?>">
          <?php echo $review_message; ?>
        </div>
      <?php } ?>
    </div>
  </div>


  <?php
  if (!$db_error) {
    $conn->close();
  }
  ?>

  <?php include('../includes/footer.php'); ?>

  <script src="/assets/js/home.js"></script>
  <?php include 'chatbot.php'; ?>
</body>

</html>