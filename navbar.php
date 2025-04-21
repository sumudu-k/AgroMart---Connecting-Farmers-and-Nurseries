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
    <title>AgroMart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/navbar.css">


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src='alertFunction.js'></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="q"]');
        const searchResults = document.getElementById('search-results');

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim();

            if (query.length > 0) {
                fetch(`search_products.php?q=${encodeURIComponent(query)}`)
                    .then(response => response.text())
                    .then(data => {
                        searchResults.innerHTML = data;
                        searchResults.style.display = 'block';
                    });
            } else {
                searchResults.innerHTML = '';
                searchResults.style.display = 'none';
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.nav-bottom')) {
                searchResults.style.display = 'none';
            }
        });

        // Optional: Submit form on Enter key
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                document.querySelector('.nav-bottom form').submit();
            }
        });
    });
    </script>


</head>

<body>

    <nav>
        <div class="logo">
            <a href="home.php">AgroMart</a>
        </div>

        <ul id="menuList">
            <?php if (!isset($_SESSION['username'])): ?>
            <li><a href="requests.php">Product Requests</a></li>
            <li><a href="#" onclick="showAlert('Please login to post an Ad','error','#ff0000')" class="place-ad">Post
                    Ad</a></li>
            <li><a href="#" onclick="showAlert('Please login to see Wishlist','error','#ff0000')"><i
                        class="fas fa-heart nav-icon" title="Wishlist"></i></a></li>
            <li><a href="#" onclick="showAlert('Please login to see Notifications','error','#ff0000')">
                    <i class="fa fa-bell nav-icon" aria-hidden="true"></i>
                    <?php if ($unread_count > 0): ?>
                    <span class="badge" id="notif_count"><?= $unread_count ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <?php else: ?>
            <li><a href="requests.php">Product Requests</a></li>
            <li><a href="post_ad.php" class="place-ad">Post Ad</a></li>
            <li><a href="wishlist.php"><i class="fas fa-heart nav-icon" title="Wishlist"></i></a></li>
            <li><a href="notifications.php" class="notification">
                    <i class="fa fa-bell nav-icon" aria-hidden="true"></i>
                    <?php if ($unread_count > 0): ?>
                    <span class="badge" id="notif_count"><?= $unread_count ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <?php endif; ?>

            <?php if (isset($_SESSION['username'])): ?>
            <li><a href="profile.php"><i class="fas fa-user nav-icon"></i></a></li>
            <li><a href="#" onclick="confirmAlert('Are you sure want to logout?','logout.php')"><i
                        class="fa fa-sign-out nav-icon" aria-hidden="true"></i></a></li>
            <?php else: ?>
            <li><a href="login.php">Log In</a></li>
            <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="nav-bottom">
        <form action="search_results.php" method="GET" style="width: 100%;">
            <input type="text" name="q" placeholder="Search what you want" required>
        </form>

        <div class="search-results" id="search-results"></div>
    </div>

</body>

</html>