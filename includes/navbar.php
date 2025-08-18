<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    .hamburger {
      display: none;
      flex-direction: column;
      cursor: pointer;
      padding: 10px;
      margin-right: 20px;
    }

    .hamburger span {
      width: 25px;
      height: 3px;
      background-color: white;
      margin: 3px 0;
      transition: 0.3s;
      border-radius: 2px;
    }

    .hamburger.active span:nth-child(1) {
      transform: rotate(-45deg) translate(-5px, 6px);
    }

    .hamburger.active span:nth-child(2) {
      opacity: 0;
    }

    .hamburger.active span:nth-child(3) {
      transform: rotate(45deg) translate(-5px, -6px);
    }

    /* Mobile responsive styles */
    @media (max-width: 768px) {
      .hamburger {
        display: flex;
      }

      .navigation {
        position: fixed;
        top: 70px;
        left: 0;
        right: 0;
        background: rgba(43, 53, 67, 0.95);
        backdrop-filter: blur(10px);
        flex-direction: column;
        padding: 20px;
        margin: 0;
        border-radius: 0 0 20px 20px;
        transform: translateY(-100%);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 998;
      }

      .navigation.mobile-open {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
      }

      .navigation a {
        margin: 10px 0;
        padding: 15px 20px;
        text-align: center;
        border-radius: 10px;
      }

      .logo img {
        width: 120px;
      }
    }
  </style>
</head>

<body>
  <header>
    <nav class="navbar">
      <div class="header">
        <div class="logo"><img src="/assets/img/logo.png" width="150px" /></div>


        <div class="hamburger" onclick="toggleMobileMenu()">
          <span></span>
          <span></span>
          <span></span>
        </div>

        <div class="navigation" id="mobileNavigation">
          <a href="../pages/home.php" class="nav-link">Home</a>
          <a href="../pages/models.php" class="nav-link">Models</a>
          <a href="../pages/aboutus.php" class="nav-link">About Us</a>
          <a href="../pages/contactus.php" class="nav-link">Contact Us</a>
          <a href="../pages/garage.php" class="nav-link">Garage</a>
          <a href="../pages/login.php" class="nav-link">Login</a>
          <!-- <input type="text" placeholder="Search" class="searchbox" /> -->
        </div>
      </div>
    </nav>
  </header>

  <script>
    function toggleMobileMenu() {
      const navigation = document.getElementById('mobileNavigation');
      const hamburger = document.querySelector('.hamburger');

      navigation.classList.toggle('mobile-open');
      hamburger.classList.toggle('active');
    }

    document.addEventListener('click', function(event) {
      const hamburger = document.querySelector('.hamburger');
      const navigation = document.getElementById('mobileNavigation');

      if (!hamburger.contains(event.target) && !navigation.contains(event.target)) {
        navigation.classList.remove('mobile-open');
        hamburger.classList.remove('active');
      }
    });
  </script>
</body>

</html>