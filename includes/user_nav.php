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
    </style>
</head>

<body>

    <header>
        <nav class="navbar">
            <div class="header">
                <div class="logo"><img src="/assets/img/logo.png" width="150px" /></div>
                <div class="navigation">
                    <a href="/pages/home.php" class="nav-link">Home</a>
                    <a href="/pages/models.php" class="nav-link">Models</a>
                    <a href="/pages/aboutus.php" class="nav-link">About Us</a>
                    <a href="/pages/contactus.php" class="nav-link">Contact Us</a>
                    <a href="/pages/garage.php" class="nav-link">Garage</a>
                    <input type="text" placeholder="Search" class="searchbox" />

                    <div class="user-profile">
                        <img src="/placeholder.svg?height=40&width=40&text=<?php echo substr($_SESSION['user_username'], 0, 1); ?>"
                            alt="Profile" class="profile-picture" onclick="toggleProfileDropdown()">

                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="dropdown-header">
                                <h4><?php echo htmlspecialchars($_SESSION['user_username']); ?></h4>
                                <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                            </div>
                            <a href="/pages/userpfp.php" class="dropdown-item">My Profile</a>
                            <a href="/pages/garage.php" class="dropdown-item">My Garage</a>
                            <a href="/pages/orders.php" class="dropdown-item">Order History</a>
                            <div class="dropdown-divider"></div>
                            <a href="/pages/logout.php" class="dropdown-item logout-item">Sign Out</a>
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

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const profile = document.querySelector('.user-profile');
            const dropdown = document.getElementById('profileDropdown');

            if (!profile.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
    </script>
</body>

</html>