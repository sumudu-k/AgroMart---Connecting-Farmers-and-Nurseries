<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    /* Navbar CSS */
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
        font-size: 35px;
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

    nav .search-container input[type="text"] {
        border: 2px solid #adb5bd;
        outline: none;
        border-radius: 5px;
        background-color: #f2f2f2;
        height: 100%;
        padding: 0 10px;
        font-size: 16px;
        width: 300px;
    }

    /* Search Results */
    .search-results {
        position: absolute;
        background-color: #e9ecef;
        border-radius: 5px;
        box-shadow: 0px 8px 10px rgba(0, 0, 0, 0.2);
        width: 300px;
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

    /* Navebar Right */
    nav ul {
        display: flex;
        list-style: none;
        margin: auto 0;
    }

    nav ul li {
        border-right: 1px solid white;
        margin: 0 6px;
    }

    nav ul li:last-child {
        border-right: none;
    }

    nav ul li a {
        color: white;
        font-size: 20px;
        text-decoration: none;
        text-transform: capitalize;
        letter-spacing: 1px;
        border-radius: 5px;
        padding: 5px 10px;
        margin-right: 10px;
        transition: 0.3s;
    }

    nav a.place-ad {
        background-color: #ffd700;
        padding: 5px 10px;
        color: black;
    }

    nav ul li:hover a {
        background-color: #e9ecef;
        border-radius: 5px;
        color: black;
    }

    /* Responsive Navbar */
    nav .bar {
        position: relative;
        margin: auto;
        display: none;
    }

    nav .bar span {
        position: absolute;
        color: #f2f2f2;
        cursor: pointer;
    }

    input[type="checkbox"] {
        -webkit-appearance: none;
        display: none;
    }

    @media screen and (max-width: 1500px) {
        nav {
            display: block;
            margin: 0;
            padding: 0;
        }

        nav .logo {
            display: inline-block;
            padding: 15px 30px;
        }

        nav .search-container {
            width: 100%;
            display: inline-flex;
            justify-content: center;
            border: none;
            margin-bottom: 15px;
        }

        nav .search-container input {
            width: 400px;
        }

        nav ul {
            display: flex;
            flex-direction: column;
            background-color: #006400;
            max-height: 0;
            visibility: hidden;
            overflow: hidden;
            transform: translateY(-15px);
            transition: max-height 0.3s ease, visibility 0s 0.3s, transform 0.3s ease;
        }

        nav ul li {
            text-align: center;
            border-right: none;
            margin-bottom: 10px;
        }

        nav ul li a {
            font-size: 20px;
            display: block;
        }

        nav .bar {
            display: block;
            position: absolute;
            top: 30px;
            right: 90px;
            cursor: pointer;
        }

        nav .bar #times {
            display: none;
        }

        #check:checked~nav .bar #times {
            display: block;
        }

        #check:checked~nav .bar #bars {
            display: none;
        }

        #check:checked~nav ul {
            visibility: visible;
            max-height: 230px;
            transform: translateY(0);
            transition: max-height 0.3s ease, transform 0.3s ease;
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
        if (query.length == 0) {
            document.getElementById("search-results").style.display = "none";
            results.innerHTML = "";
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("GET", "search_products.php?q=" + query, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                const results = document.getElementById("search-results");
                results.innerHTML = xhr.responseText;
                results.style.display = "block";
            }
        };
        xhr.send();
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

        // Only Show search results if there is  an input
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.trim() !== '') {
                searchResults.style.display = 'block';
            }
        });

        // Hide search results when clicking outside the search container
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.search-container')) {
                searchResults.style.display = 'none';
            }
        });

        // Show search results again if the input has text on focus
        searchInput.addEventListener('input', () => {
            if (searchInput.value.trim() !== '') {
                searchResults.style.display = 'block';
            } else {
                searchResults.style.display = 'none';
            }
        });
    });
    </script>

    <!-- Navbar -->
    <input type="checkbox" id="check">
    <nav>

        <!-- logo -->
        <div class="logo"><a href="home.php">AgroMart</a></div>

        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" placeholder="Search what you want" onkeyup="searchProducts(this.value)"
                aria-label="Search">
            <div class="search-results" id="search-results" aria-live="polite"></div>
        </div>

        <!-- Navbar Right -->
        <ul>
            <li><a href="my_ads.php">My Ads</a></li>
            <li><a href="post_ad.php" class="place-ad">Post Ad</a></li>
            <li><a href="wishlist.php">Wishlist</a></li>

            <?php if (isset($_SESSION['username'])): ?>
            <li><a href="#">Welcome,<?= $_SESSION['username']; ?></a></li>
            <li><a href="#" onclick="confirmLogout(); return false;">Log Out</a></li>
            <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
        <label for="check" class="bar">
            <span id="bars"><i class="fa fa-bars fa-2x" aria-hidden="true"></i></span>
            <span id="times"><i class="fa fa-times fa-2x" aria-hidden="true"></i></span>
        </label>
    </nav>

</body>

</html>