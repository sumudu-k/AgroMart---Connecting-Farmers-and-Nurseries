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
    /* General navbar styling */
    nav {
        display: flex;
        flex-direction: column;
        width: 100%;
        background-color: #006400;
        /* Green background */
        padding: 15px 12.5%;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
    }

    /* First Row (Logo + Search) */
    .nav-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    /* Logo styling */
    .logo a {
        font-size: 24px;
        font-weight: bold;
        color: white;
        text-decoration: none;
    }

    /* Search bar */
    .search-container {
        position: relative;
    }

    .search-container input {
        width: 250px;
        padding: 8px;
        border: none;
        border-radius: 5px;
    }

    /* Search results dropdown */
    .search-results {
        position: absolute;
        top: 35px;
        left: 0;
        width: 250px;
        background: white;
        border: 1px solid #ccc;
        display: none;
    }

    /* Second Row (Navigation Links) */
    .nav-bottom {
        width: 100%;
        margin-top: 10px;
        display: flex;
        justify-content: center;
    }

    .nav-right {
        display: flex;
        list-style: none;
        padding: 0;
    }

    .nav-right li {
        margin: 0 10px;
    }

    .nav-right a {
        text-decoration: none;
        color: white;
        font-weight: bold;
        padding: 10px 15px;
        border-radius: 5px;
        transition: 0.3s;
    }

    .nav-right a:hover {
        background-color: #228B22;
        /* Darker green */
    }

    /* Special styling for Post Ads button */
    .place-ad {
        background-color: #FFD700;
        /* Gold */
        color: black;
        padding: 10px 15px;
        border-radius: 5px;
    }

    .place-ad:hover {
        background-color: #FFA500;
        /* Orange */
    }

    /* Notification Icon */
    .nav-right img {
        width: 24px;
    }

    .badge {
        background: red;
        color: white;
        font-size: 12px;
        padding: 3px 6px;
        border-radius: 50%;
        position: relative;
        top: -10px;
        left: -5px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        nav {
            padding: 15px;
        }

        .nav-top {
            flex-direction: column;
            text-align: center;
        }

        .search-container input {
            width: 100%;
        }

        .nav-bottom {
            margin-top: 10px;
        }

        .nav-right {
            flex-wrap: wrap;
            justify-content: center;
            text-align: center;
        }

        .nav-right li {
            margin: 5px;
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
        <div class="nav-top">
            <div class="logo">
                <a href="home.php">AgroMart</a>
            </div>
            <div class="search-container">
                <input type="text" placeholder="Search what you want" onkeyup="searchProducts(this.value)">
                <div class="search-results" id="search-results"></div>
            </div>
        </div>

        <div class="nav-bottom">
            <ul class="nav-right">
                <li><a href="my_ads.php">MY ADS</a></li>
                <li><a href="post_ad.php" class="place-ad">POST ADS</a></li>
                <li><a href="wishlist.php"><i class="fas fa-heart" title="Wishlist"></i></a></li>
                <li><a href="notifications.php">
                        <img src='uploads/bell.png' style='width:24px' title="Notifications">
                        <?php if ($unread_count > 0): ?>
                        <span class="badge" id="notif_count"><?= $unread_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php if (isset($_SESSION['username'])): ?>
                <li><a href="profile.php"><i class="fas fa-user"></i></a></li>
                <li><a href="javascript:void(0);" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i></a></li>
                <?php else: ?>
                <li><a href="login.php">LOGIN</a></li>
                <li><a href="register.php">REGISTER</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

</body>

</html>