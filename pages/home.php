  <!-- <?php include('../includes/navbar.php'); ?> -->
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
          <button class="btnmore">
            View More
          </button>
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
          <button class="btnmore">
            View More
          </button>
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
          <button class="btnmore">
            View More
          </button>
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
          <button class="btnmore">
            View More
          </button>
        </div>
      </div>

      <div class="super-sideimgcon2">
        <div class="super-sideimg2 super-leftimg2">
          <img src="/assets/img/765lt.jpg" alt="left car" width="100%">
        </div>
        <div class="super-sideimg2 super-rightimg2">
          <h2>Mclaren 765LT</h2>
          <h3>The McLaren 765LT is an even more extreme version of the 720S, with weight reduction, increased power (755
            horsepower), and enhanced aerodynamics. It's part of McLaren's 'Longtail' series, focusing on track
            performance while maintaining road legality.</h3>
          <button class="btnmore">
            View More
          </button>
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
          <button class="btnmore">
            View More
          </button>
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
            The McLaren P1 is a limited-production hybrid sports car, considered one of the "Holy Trinity" of hypercars alongside the Porsche 918 Spyder and Ferrari LaFerrari. It boasts a 3.8-liter twin-turbo V8 engine combined with a hybrid system, delivering a total output of 916 horsepower. The P1 is known for its advanced aerodynamics, including a Formula 1-inspired Drag Reduction System (DRS). 
          </h3>
          <button class="btnmore">
            View More
          </button>
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
          <button class="btnmore">
            View More
          </button>
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
          <button class="btnmore">
            View More
          </button>
        </div>
      </div>
    </div>
  </section>

<?php include('../includes/footer.php'); ?>

  <script src="/assets/js/home.js"></script>
</body>

</html>
