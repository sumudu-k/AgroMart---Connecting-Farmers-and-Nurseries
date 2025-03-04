<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = null;
}

// Query to count unread notifications
$sql = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND status = 'unread'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$unread_count = $row['unread_count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
    }

    nav {
        display: flex;
        width: 100%;
        background-color: #006400;
        position: relative;
        justify-content: space-between;
        align-items: center;
        min-height: 60px;
        padding: 15px 12.5%;
    }

    /* Logo */
    nav .logo {
        flex-shrink: 0; 
        position: relative;
        z-index: 2;
    }
    nav .logo a {
        color: #f2f2f2;
        font-size: 2.1rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    /* Search Bar */
    .search-container {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        height: 40px;
        line-height: 35px;
        width: 25rem;
        z-index: 1;
    }

    .search-container input {
        border: 2px solid #adb5bd;
        outline: none;
        border-radius: 5px;
        background-color: #f2f2f2;
        height: 100%;
        padding: 0 10px;
        font-size: 16px;
        width: 100%;
    }

    /* Search Results */
    .search-results {
        position: absolute;
        background-color: #e9ecef;
        border-radius: 5px;
        box-shadow: 0px 8px 10px rgba(0, 0, 0, 0.2);
        width: 25rem;
        margin-top: 40px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        left: 50%;
        transform: translateX(-50%);
    }

    .search-results a {
        height: 40px;
        padding-top: 5px;
        display: block;
        font-size: 16px;
        text-decoration: none;
        color: black;
        transition: background-color 0.3s;
    }

    .search-results a:hover {
        background-color: #ddd;
        font-weight: 600;
        border-radius: 5px;
    }

    /* Navbar Right (Visible items on large screens) */
    .nav-right {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        list-style: none;
        margin: auto 0;
        flex-shrink: 0; 
        position: relative;
        z-index: 2;
    }

    .visible-items {
        display: flex;
        align-items: center;
    }

    .visible-items li {
        list-style: none;
        display: inline-block;
        padding: 5px 10px;
    }

    .visible-items li a {
        color: white;
        font-weight: 600;
        display: flex;
        align-items: center;
        font-size: 1.1rem;
        text-decoration: none;
        border-radius: 5px;
        padding: 5px 10px;
        transition: 0.3s;
    }

    .place-ad {
        background-color: #006400;
        animation: blink 2.5s infinite;
    }

    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.9; background-color: rgb(255, 153, 0); }
        100% { opacity: 1; }
    }

    .nav-right .visible-items li a i {
        font-size: 1.1rem;
        color: white;
        vertical-align: middle;
    }

    .nav-right .visible-items li:hover a,
    .nav-right .visible-items li:hover a i {
        background-color:rgb(239, 236, 233);
        color: black;
    }

    /* Hamburger Menu */
    .hamburger-menu {
        display: none;
        flex-direction: column;
        width: 20%;
        background-color:rgb(118, 175, 115);
        position: absolute;
        top: 100%;
        right: 12.5%;
        z-index: 1000;
    }

    .hamburger-menu.active {
        display: flex;
    }

    .hamburger-menu li {
        list-style: none;
        width: 100%;
        text-align: left;
        border-bottom: 1px solid white;
        margin: 0;
        padding: 15px 10%;
    }

    .hamburger-menu li a {
        display: block;
        text-align: center;
        width: 100%;
        color: black;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
    }

    .hamburger-menu li:hover{
        border: 1px solid black;
        border-radius: 5px;
        background-color:rgba(233, 236, 239, 0.77);
    }

    .hamburger-menu li:last-child {
        border-bottom: none;
    }

    /* Hamburger Menu Icon */
    .hamburger {
        font-size: 1.5rem;
        color: white;
        cursor: pointer;
        margin: auto 10px auto 10px;
    }


    /* Tablets */

    @media (min-width: 481px) and (max-width: 1200px) {
        nav {
            padding: 10px 10%;
        }

        .nav-right .visible-items {
            display: none;
        }

        .hamburger {
            display: block;
        }

        .hamburger-menu {
            display: none;
        }

        .search-container {
            width: 100%;
            max-width: 20rem;
        }

        .search-results {
            width: 100%;
            max-width: 20rem;
            left: 50%;
            transform: translateX(-50%);
        }

        .visible-items li a {
            font-size: 1rem;
        }
    }

    /* Mobile (max-width: 480px) */
    @media (max-width: 480px) {
        nav {
            flex-direction: column;
            align-items: flex-start;
            padding: 10px 5%;
            min-height: auto;
        }

        nav .logo {
            margin-bottom: 10px;
        }

        nav .logo a {
        font-size: 1.7rem;
        
    }

        .search-container {
            position: relative;
            left: auto;
            transform: none;
            width: 100%;
            max-width: 100%;
            margin: 10px 0;
        }

        .search-container input {
            font-size: 14px;
        }

        .search-results {
            width: 100%;
            max-width: 100%;
            left: 0;
            transform: none;
            margin-top: 45px;
        }

        .nav-right {
            width: 100%;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .visible-items {
            display: none;
        }

        .hamburger {

            position: absolute;
            top: 0;
            right: 0;
            transform: translateY(-50%);
            display:flex;
            margin: 0 10px;
        }

        .hamburger-menu li a {
            font-size: 16px;
        }
    }
    </style>
</head>

<body>
    <script>
    function confirmLogout() {
        var confirmAction = confirm("Are you sure you want to log out?");
        if (confirmAction) {
            window.location.href = "logout.php";
        }
    }

    function searchProducts(query) {
        const results = document.getElementById("search-results");
        if (query.length === 0) {
            results.style.display = "none";
            results.innerHTML = "";
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("GET", "search_products.php?q=" + query, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                results.innerHTML = xhr.responseText;
                results.style.display = "block";
            }
        };
        xhr.send();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('.search-container input[type="text"]');
        const searchResults = document.getElementById('search-results');
        const hamburger = document.querySelector('.hamburger');
        const hamburgerMenu = document.querySelector('.hamburger-menu');

        // Search functionality
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.trim() !== '') {
                searchResults.style.display = 'block';
            }
        });

        document.addEventListener('click', (event) => {
            if (!event.target.closest('.search-container')) {
                searchResults.style.display = 'none';
            }
        });

        searchInput.addEventListener('input', () => {
            if (searchInput.value.trim() !== '') {
                searchResults.style.display = 'block';
            } else {
                searchResults.style.display = 'none';
            }
        });

        // Hamburger menu toggle
        hamburger.addEventListener('click', () => {
            hamburgerMenu.classList.toggle('active');
        });

        // Close hamburger menu when clicking outside
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.hamburger') && !event.target.closest('.hamburger-menu')) {
                hamburgerMenu.classList.remove('active');
            }
        });
    });
    </script>

    <nav>
        <div class="logo">
            <a href="home.php">AgroMart</a>
        </div>

        <div class="search-container">
            <input type="text" placeholder="Search what you want" onkeyup="searchProducts(this.value)"
                aria-label="Search">
            <div class="search-results" id="search-results" aria-live="polite"></div>
        </div>

        <div class="nav-right">
            <div class="visible-items">
                <ul>
                    <li><a href="my_ads.php">MY ADS</a></li>
                    <li><a href="post_ad.php" class="place-ad">POST ADS</a></li>
                </ul>
            </div>
            <div class="hamburger-menu">
                <ul>
                    <li><a href="wishlist.php"><i class="fas fa-heart" title="Wishlist"></i></a></li>
                    
                    <li><a href="notifications.php"> <i class="fa fa-bell" aria-hidden="true"  title="Notifications"></i>
                            <?php if ($unread_count > 0): ?>
                            <span class="badge" id="notif_count"><?= $unread_count ?></span>
                            <?php endif; ?>
                        </a></li>
                    <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="profile.php"><i class="fas fa-user" title="Profile"></i></a></li>
                    <li><a href="#" onclick="confirmLogout(); return false;">LOGOUT</a></li>
                    <?php else: ?>
                    <li><a href="login.php">LOGIN</a></li>
                    <li><a href="register.php">REGISTER</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="hamburger">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>
</body>

</html>