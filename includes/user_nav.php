<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .user-profile {
            position: relative;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 20px;
        }

        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            object-fit: cover;
        }

        .profile-picture:hover {
            border-color: rgba(255, 255, 255, 0.6);
            transform: scale(1.05);
        }

        .profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: rgba(43, 53, 67, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 15px 0;
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .profile-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 10px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 10px;
        }

        .dropdown-header h4 {
            color: #f0f0f0;
            margin: 0;
            font-family: "Montserrat", sans-serif;
            font-size: 0.9rem;
        }

        .dropdown-header p {
            color: rgba(255, 255, 255, 0.6);
            margin: 5px 0 0 0;
            font-family: "Montserrat", sans-serif;
            font-size: 0.8rem;
        }

        .dropdown-item {
            display: block;
            padding: 10px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-family: "Montserrat", sans-serif;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #f0f0f0;
        }

        .dropdown-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 10px 0;
        }

        .logout-item {
            color: rgba(255, 82, 82, 0.8);
        }

        .logout-item:hover {
            background: rgba(255, 82, 82, 0.1);
            color: #ff5252;
        }

        /* Added hamburger menu styles for mobile responsiveness */
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

            .user-profile {
                margin: 20px 0 0 0;
                justify-content: center;
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

                <!-- Added hamburger menu button -->
                <div class="hamburger" onclick="toggleMobileMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <div class="navigation" id="mobileNavigation">
                    <a href="/pages/home.php" class="nav-link">Home</a>
                    <a href="/pages/models.php" class="nav-link">Models</a>
                    <a href="/pages/aboutus.php" class="nav-link">About Us</a>
                    <a href="/pages/contactus.php" class="nav-link">Contact Us</a>
                    <a href="/pages/garage.php" class="nav-link">Garage</a>
                    <a href="/pages/wishlist.php" class="nav-link">Wishlist</a>
                    <!-- <input type="text" placeholder="Search" class="searchbox" /> -->

                    <div class="user-profile">
                        <?php
                        $host = 'localhost';
                        $port = '3306';
                        $db_name = 'revgaragedb';
                        $username = 'root';
                        $password = '';

                        try {
                            $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8", $username, $password);
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE user_id = :user_id");
                            $stmt->execute(['user_id' => $_SESSION['user_id']]);
                            $user_data = $stmt->fetch();

                            $nav_avatar_url = ($user_data && $user_data['profile_picture'])
                                ? $user_data['profile_picture']
                                : "/placeholder.svg?height=40&width=40&text=" . substr($_SESSION['user_username'], 0, 1);
                        } catch (PDOException $e) {

                            $nav_avatar_url = "/placeholder.svg?height=40&width=40&text=" . substr($_SESSION['user_username'], 0, 1);
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($nav_avatar_url); ?>"
                            alt="Profile" class="profile-picture" onclick="toggleProfileDropdown()">

                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="dropdown-header">
                                <h4><?php echo htmlspecialchars($_SESSION['user_username']); ?></h4>
                                <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                            </div>
                            <a href="../pages/userpfp.php" class="dropdown-item">My Profile</a>
                            <a href="../pages/garage.php" class="dropdown-item">My Garage</a>
                            <a href="../pages/wishlist.php" class="dropdown-item">My Wishlist</a>
                            <a href="../pages/orders.php" class="dropdown-item">Order History</a>
                            <div class="dropdown-divider"></div>
                            <a href="../pages/logout.php" class="dropdown-item logout-item">Sign Out</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <script>
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('show');
        }

        function toggleMobileMenu() {
            const navigation = document.getElementById('mobileNavigation');
            const hamburger = document.querySelector('.hamburger');

            navigation.classList.toggle('mobile-open');
            hamburger.classList.toggle('active');
        }


        document.addEventListener('click', function(event) {
            const profile = document.querySelector('.user-profile');
            const dropdown = document.getElementById('profileDropdown');
            const hamburger = document.querySelector('.hamburger');
            const navigation = document.getElementById('mobileNavigation');

            if (!profile.contains(event.target)) {
                dropdown.classList.remove('show');
            }

            if (!hamburger.contains(event.target) && !navigation.contains(event.target)) {
                navigation.classList.remove('mobile-open');
                hamburger.classList.remove('active');
            }
        });
    </script>
</body>

</html>