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

    require_once '../config/dbcon.php';
    require_once '../config/functions.php';
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
      <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollTrigger.min.js"
          crossorigin="anonymous"></script>


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

          <!-- Filter Section -->
          <section class="filter-section">
              <div class="filter-container">
                  <h3 class="filter-title">Filter by Price Range</h3>
                  <div class="filter-options">
                      <button class="filter-btn active" data-filter="all">All Models</button>
                      <button class="filter-btn" data-filter="under-200k">Under $200K</button>
                      <button class="filter-btn" data-filter="200k-500k">$200K - $500K</button>
                      <button class="filter-btn" data-filter="500k-1m">$500K - $1M</button>
                      <button class="filter-btn" data-filter="above-1m">Above $1M</button>
                  </div>
              </div>
          </section>

          <!-- Sport Series -->
          <section id="sport-series" class="series-section">
              <div class="series-header">
                  <h2 class="series-title">Sport Series</h2>
                  <p class="series-description">The perfect introduction to McLaren's world of supercars, combining everyday usability with track-ready performance.</p>
              </div>

              <div class="cars-grid">
                  <?php
                    $sportSeriesCars = getCarSeries(1); // Assuming 1 is the ID for Sport Series
                    foreach ($sportSeriesCars as $car) {

                    ?>

                      <div class="car-card">
                          <div class="car-image">

                              <img src="<?php echo $car["image_url"] ?>" alt="<?php $car["name"] ?>" />
                          </div>
                          <div class="car-info">
                              <h3 class="car-name"><?php echo $car["name"] ?></h3>
                              <div class="car-price"><?php echo $car["price"] ?></div>
                              <div class="car-specs">
                                  <div class="spec-item">
                                      <span class="spec-label">Engine: </span>
                                      <span class="spec-value"><?php echo $car["engine"] ?></span>
                                  </div>
                                  <div class="spec-item">
                                      <span class="spec-label">Power:</span>
                                      <span class="spec-value"><?php echo $car["power_hp"] ?></span>
                                  </div>
                                  <div class="spec-item">
                                      <span class="spec-label">Doors:</span>
                                      <span class="spec-value"><?php echo $car["doors"] ?></span>
                                  </div>
                                  <div class="spec-item">
                                      <span class="spec-label">0-60 mph:</span>
                                      <span class="spec-value"><?php echo $car["acceleration_0_60"] ?></span>
                                  </div>
                              </div>
                              <a href="570s.php"><button class="details-btn">More Details</button></a>
                          </div>
                      </div>

                  <?php
                    }

                    ?>


                  <!-- <div class="car-card">
          <div class="car-image">
            <img src="img/540c.jpg" alt="McLaren 540C" />
          </div>
          <div class="car-info">
            <h3 class="car-name">McLaren 540C</h3>
            <div class="car-price">$165,000</div>
            <div class="car-specs">
              <div class="spec-item">
                <span class="spec-label">Engine:</span>
                <span class="spec-value">3.8L Twin-Turbo V8</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Power:</span>
                <span class="spec-value">533 HP</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Doors:</span>
                <span class="spec-value">2-Door</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">0-60 mph:</span>
                <span class="spec-value">3.4s</span>
              </div>
            </div>
            <a href="540c.php"><button class="details-btn">More Details</button></a>
          </div>
        </div> -->
                  <!-- 
        <div class="car-card">
          <div class="car-image">
            <img src="img/540c.jpg" alt="McLaren 540C" />
          </div>
          <div class="car-info">
            <h3 class="car-name">McLaren 540C</h3>
            <div class="car-price">$165,000</div>
            <div class="car-specs">
              <div class="spec-item">
                <span class="spec-label">Engine:</span>
                <span class="spec-value">3.8L Twin-Turbo V8</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Power:</span>
                <span class="spec-value">533 HP</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Doors:</span>
                <span class="spec-value">2-Door</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">0-60 mph:</span>
                <span class="spec-value">3.4s</span>
              </div>
            </div>
            <a href="540c.php"><button class="details-btn">More Details</button></a>
          </div>
        </div>
        <div class="car-card">
          <div class="car-image">
            <img src="img/540c.jpg" alt="McLaren 540C" />
          </div>
          <div class="car-info">
            <h3 class="car-name">McLaren 540C</h3>
            <div class="car-price">$165,000</div>
            <div class="car-specs">
              <div class="spec-item">
                <span class="spec-label">Engine:</span>
                <span class="spec-value">3.8L Twin-Turbo V8</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Power:</span>
                <span class="spec-value">533 HP</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Doors:</span>
                <span class="spec-value">2-Door</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">0-60 mph:</span>
                <span class="spec-value">3.4s</span>
              </div>
            </div>
            <a href="540c.php"><button class="details-btn">More Details</button></a>
          </div>
        </div>
        

        <div class="car-card">
          <div class="car-image">
            <img src="img/600Lt.jpg" alt="McLaren 600LT" />
          </div>
          <div class="car-info">
            <h3 class="car-name">McLaren 600LT</h3>
            <div class="car-price">$240,000</div>
            <div class="car-specs">
              <div class="spec-item">
                <span class="spec-label">Engine:</span>
                <span class="spec-value">3.8L Twin-Turbo V8</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Power:</span>
                <span class="spec-value">592 HP</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">Doors:</span>
                <span class="spec-value">2-Door</span>
              </div>
              <div class="spec-item">
                <span class="spec-label">0-60 mph:</span>
                <span class="spec-value">2.9s</span>
              </div>
            </div>
            <a href="600lt.php"><button class="details-btn">More Details</button></a>
          </div>
        </div> -->
              </div>
          </section>

          <!-- Super Series -->
          <section id="super-series" class="series-section">
              <div class="series-header">
                  <h2 class="series-title">Super Series</h2>
                  <p class="series-description">The pinnacle of McLaren's engineering excellence, delivering uncompromising performance and cutting-edge technology.</p>
              </div>

              <div class="cars-grid">
                  <?php
                    $sportSeriesCars = getCarSeries(2); // Assuming 1 is the ID for Sport Series
                    foreach ($sportSeriesCars as $car) {

                    ?>

                      <div class="car-card">
                          <div class="car-image">

                              <img src="<?php echo $car["image_url"] ?>" alt="<?php $car["name"] ?>" />
                          </div>
                          <div class="car-info">
                              <h3 class="car-name"><?php echo $car["name"] ?></h3>
                              <div class="car-price"><?php echo $car["price"] ?></div>
                              <div class="car-specs">
                                  <div class="spec-item">
                                      <span class="spec-label">Engine: </span>
                                      <span class="spec-value"><?php echo $car["engine"] ?></span>
                                  </div>
                                  <div class="spec-item">
                                      <span class="spec-label">Power:</span>
                                      <span class="spec-value"><?php echo $car["power_hp"] ?></span>
                                  </div>
                                  <div class="spec-item">
                                      <span class="spec-label">Doors:</span>
                                      <span class="spec-value"><?php echo $car["doors"] ?></span>
                                  </div>
                                  <div class="spec-item">
                                      <span class="spec-label">0-60 mph:</span>
                                      <span class="spec-value"><?php echo $car["acceleration_0_60"] ?></span>
                                  </div>
                              </div>
                              <a href="570s.php"><button class="details-btn">More Details</button></a>
                          </div>
                      </div>

                  <?php
                    }

                    ?>

                  <!-- <div class="car-card">
                      <div class="car-image">
                          <img src="/assets/img/720s.jpg" alt="McLaren 720S" />
                      </div>
                      <div class="car-info">
                          <h3 class="car-name">McLaren 720S</h3>
                          <div class="car-price">$310,000</div>
                          <div class="car-specs">
                              <div class="spec-item">
                                  <span class="spec-label">Engine:</span>
                                  <span class="spec-value">4.0L Twin-Turbo V8</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Power:</span>
                                  <span class="spec-value">710 HP</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Doors:</span>
                                  <span class="spec-value">2-Door</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">0-60 mph:</span>
                                  <span class="spec-value">2.8s</span>
                              </div>
                          </div>
                          <a href="720s.php"><button class="details-btn">More Details</button></a>
                      </div>
                  </div>

                  <div class="car-card">
                      <div class="car-image">
                          <img src="/assets/img/765lt.jpg" alt="McLaren 765LT" />
                      </div>
                      <div class="car-info">
                          <h3 class="car-name">McLaren 765LT</h3>
                          <div class="car-price">$385,000</div>
                          <div class="car-specs">
                              <div class="spec-item">
                                  <span class="spec-label">Engine:</span>
                                  <span class="spec-value">4.0L Twin-Turbo V8</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Power:</span>
                                  <span class="spec-value">755 HP</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Doors:</span>
                                  <span class="spec-value">2-Door</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">0-60 mph:</span>
                                  <span class="spec-value">2.7s</span>
                              </div>
                          </div>
                          <a href="765LT.php"><button class="details-btn">More Details</button></a>
                      </div>
                  </div>

                  <div class="car-card">
                      <div class="car-image">
                          <img src="/assets/img/675LT spider .jpg" alt="McLaren 675LT Spider" />
                      </div>
                      <div class="car-info">
                          <h3 class="car-name">McLaren 675LT Spider</h3>
                          <div class="car-price">$275,000</div>
                          <div class="car-specs">
                              <div class="spec-item">
                                  <span class="spec-label">Engine:</span>
                                  <span class="spec-value">3.8L Twin-Turbo V8</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Power:</span>
                                  <span class="spec-value">666 HP</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Doors:</span>
                                  <span class="spec-value">2-Door Convertible</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">0-60 mph:</span>
                                  <span class="spec-value">2.9s</span>
                              </div>
                          </div>
                          <a href="675LT.php"><button class="details-btn">More Details</button></a>
                      </div> -->
                  <!-- </div> -->
              </div>
          </section>

          <!-- Ultimate Series -->
          <section id="ultimate-series" class="series-section">
              <div class="series-header">
                  <h2 class="series-title">Ultimate Series</h2>
                  <p class="series-description">The most exclusive and extreme McLaren machines, representing the absolute pinnacle of automotive engineering and performance.</p>
              </div>

              <div class="cars-grid">
                  <?php
                    $sportSeriesCars = getCarSeries(3); // Assuming 1 is the ID for Sport Series
                    foreach ($sportSeriesCars as $car) {

                    ?>

                      <div class="car-card">
                          <div class="car-image">

                              <img src="<?php echo $car["image_url"] ?>" alt="<?php $car["name"] ?>" />
                          </div>
                          <div class="car-info">
                              <h3 class="car-name"><?php echo $car["name"] ?></h3>
                              <div class="car-price"><?php echo $car["price"] ?></div>
                              <div class="car-specs">
                                  <div class="spec-item">
                                      <span class="spec-label">Engine: </span>
                                      <span class="spec-value"><?php echo $car["engine"] ?></span>
                                  </div>
                                  <div class="spec-item">
                                      <span class="spec-label">Power:</span>
                                      <span class="spec-value"><?php echo $car["power_hp"] ?></span>
                                  </div>
                                  <div class="spec-item">
                                      <span class="spec-label">Doors:</span>
                                      <span class="spec-value"><?php echo $car["doors"] ?></span>
                                  </div>
                                  <div class="spec-item">
                                      <span class="spec-label">0-60 mph:</span>
                                      <span class="spec-value"><?php echo $car["acceleration_0_60"] ?></span>
                                  </div>
                              </div>
                              <a href="570s.php"><button class="details-btn">More Details</button></a>
                          </div>
                      </div>

                  <?php
                    }

                    ?>
                  <!-- <div class="car-card">
                      <div class="car-image">
                          <img src="/assets/img/P1.jpg" alt="McLaren P1" />
                      </div>
                      <div class="car-info">
                          <h3 class="car-name">McLaren P1</h3>
                          <div class="car-price">$1,350,000</div>
                          <div class="car-specs">
                              <div class="spec-item">
                                  <span class="spec-label">Engine:</span>
                                  <span class="spec-value">3.8L Twin-Turbo V8 + Electric</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Power:</span>
                                  <span class="spec-value">916 HP</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Doors:</span>
                                  <span class="spec-value">2-Door</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">0-60 mph:</span>
                                  <span class="spec-value">2.8s</span>
                              </div>
                          </div>
                          <a href="p1.php"><button class="details-btn">More Details</button></a>
                      </div>
                  </div>

                  <div class="car-card">
                      <div class="car-image">
                          <img src="/assets/img/senna.jpg" alt="McLaren Senna" />
                      </div>
                      <div class="car-info">
                          <h3 class="car-name">McLaren Senna</h3>
                          <div class="car-price">$958,000</div>
                          <div class="car-specs">
                              <div class="spec-item">
                                  <span class="spec-label">Engine:</span>
                                  <span class="spec-value">4.0L Twin-Turbo V8</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Power:</span>
                                  <span class="spec-value">789 HP</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Doors:</span>
                                  <span class="spec-value">2-Door</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">0-60 mph:</span>
                                  <span class="spec-value">2.7s</span>
                              </div>
                          </div>
                          <a href="senna.php"><button class="details-btn">More Details</button></a>
                      </div> -->
                  <!-- </div>

                  <div class="car-card">
                      <div class="car-image">
                          <img src="/assets/img/speedtail.jpg" alt="McLaren Speedtail" />
                      </div>
                      <div class="car-info">
                          <h3 class="car-name">McLaren Speedtail</h3>
                          <div class="car-price">$2,250,000</div>
                          <div class="car-specs">
                              <div class="spec-item">
                                  <span class="spec-label">Engine:</span>
                                  <span class="spec-value">4.0L Twin-Turbo V8 + Electric</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Power:</span>
                                  <span class="spec-value">1,035 HP</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Doors:</span>
                                  <span class="spec-value">3-Seat Configuration</span>
                              </div>
                              <div class="spec-item">
                                  <span class="spec-label">Top Speed:</span>
                                  <span class="spec-value">250 mph</span>
                              </div>
                          </div>
                          <a href="speedtail.php"><button class="details-btn">More Details</button></a>
                      </div>
                  </div> -->
              </div>
          </section>
      </main>

      <?php include('../includes/footer.php'); ?>

      <script src="/assets/js/models.js"></script>
  </body>

  </html>