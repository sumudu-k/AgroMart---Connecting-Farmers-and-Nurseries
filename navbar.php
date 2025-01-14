<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="navbar.css">
    <style>
 
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
    </script>

    <div class="navbar">
        <a href="home.php" class="logo">Plant Nursery</a>

        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" placeholder="Search products..." onkeyup="searchProducts(this.value)">
            <div class="search-results" id="search-results"></div>
        </div>

        <div class="dropdown">
            <button class="dropbtn">Categories
            
            </button>
            <div class="dropdown-content">
                <?php
            $categories = $conn->query("SELECT * FROM categories");
            while ($category = $categories->fetch_assoc()) {
                echo "<a href='category_ads.php?category_id={$category['category_id']}'>{$category['category_name']}</a>";
            }
            ?>
            </div>
        </div>

        <div class="navbar-right">
            <a href="my_ads.php">My Ads</a>
            <a href="post_ad.php">Post Ad</a>
            <?php if (isset($_SESSION['username'])): ?>
            <a href="#">Welcome, <?= $_SESSION['username']; ?></a>
            <a href="#" onclick="confirmLogout(); return false;">Log Out</a>
            <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>