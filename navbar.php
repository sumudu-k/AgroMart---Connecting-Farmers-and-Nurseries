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
        text-align: center;
        padding: 15px 12.5%;
    }

    /* Logo */
    nav .logo a {
        color: #f2f2f2;
        font-size: 2.2rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    /* Search Bar */
    nav .search-container {
        display: flex;
        margin: auto 0;
        height: 40px;
        line-height: 35px;
    }

    nav .search-container input {
        border: 2px solid #adb5bd;
        outline: none;
        border-radius: 5px;
        background-color: #f2f2f2;
        height: 100%;
        padding: 0 10px;
        font-size: 16px;
        width: 25rem;
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

    /* Navbar Right */
    nav ul {
        display: flex;
        align-items: center;
        list-style: none;
        margin: auto 0;
    }

    nav ul li {
        border-right: 1px solid rgb(161, 161, 161);
        margin: 0 6px;
    }

    nav ul li:last-child {
        border-right: none;
    }

    nav ul li a {
        color: white;
        display: flex;
        align-items: center;
        font-size: 1.1rem;
        text-decoration: none;
        border-radius: 5px;
        padding: 5px 10px;
        margin-right: 10px;
        transition: 0.3s;
    }

    .place-ad {
        background-color: rgb(255, 243, 174);
        padding: 5px 10px;
        color: black;
        animation: blink 2.5s infinite;
    }

    @keyframes blink {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.9;
            background-color: rgb(255, 153, 0);
        }

        100% {
            opacity: 1;
        }
    }

    nav ul li a i {
        color: white;
        vertical-align: middle;
    }

    nav ul li:hover a,
    nav ul li:hover a i {
        background-color: #e9ecef;
        border-radius: 5px;
        color: black;
    }



    /* Hamburger Menu Icon */
    .hamburger {
        display: none;
        font-size: 2rem;
        color: white;
        cursor: pointer;
        margin: auto 0;
    }

    /* Responsive Design */
    @media (max-width: 1500px) {
        nav {
            flex-direction: column;
            align-items: flex-start;
            padding: 15px 10%;
        }

        nav .logo {
            width: 100%;
            text-align: left;
            margin-bottom: 10px;
        }

        nav .search-container {
            width: 100%;
            align-self: center;
            margin: 10px 0;
        }

        nav .search-container input {
            width: 100%;
        }

        nav .search-results {
            width: 100%;
        }

        nav ul {
            display: none;
            flex-direction: column;
            width: 100%;
            background-color: #006400;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
        }

        nav ul.active {
            display: flex;
        }

        nav ul li {
            width: 40%;
            text-align: left;
            border-right: none;
            border-bottom: 1px solid white;
            margin: 0;
            padding: 15px 10%;
        }

        nav ul li a {
            display: block;
            text-align: center;
            width: 100%;
        }

        nav ul li:last-child {
            border-bottom: none;
        }

        #notif_count {
            animation: textScale 1s infinite alternate ease-in-out;
            position: fixed;

        }

        @keyframes textScale {
            0% {
                font-size: 16px;
            }

            50% {
                font-size: 17px;
            }

            100% {
                font-size: 16px;
            }
        }

        .hamburger {
            display: block;
            position: absolute;
            right: 10%;
            top: 15px;
        }
    }

    /* Mobile Devices (319px–480px) */
    @media (max-width: 480px) {
        nav .logo a {
            font-size: 28px;
        }

        nav .search-container input {
            font-size: 14px;
        }

        nav ul li a {
            font-size: 18px;
        }

        .hamburger {
            font-size: 24px;
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
        const navRight = document.querySelector('.nav-right');

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
            navRight.classList.toggle('active');
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

        <ul class="nav-right">
            <li><a href="my_ads.php">MY ADS</a></li>
            <li><a href="post_ad.php" class="place-ad">POST ADS</a></li>
            <li><a href="wishlist.php"><i class="fas fa-heart" title="Wishlist"></i></a></li>
            <li><a href="notifications.php"> <img src='uploads/bell.png' style='width:24px' title="Notifications">
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

        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
    </nav>

</body>

</html>