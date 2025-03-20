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

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
    }

    nav {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #006400;
        padding: 15px 12.5%;
    }

    .logo {
        font-size: 1.5rem;
        padding: 5px 8px ;
    }

    .logo a {
        color: #f2f2f2;
        font-size: 35px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    nav ul{
        display: flex;
        justify-content: space-between;
        gap: 10px;
        list-style: none;
    }

    nav ul li {
        list-style-type: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    nav ul li a {
        color: white;
        font-size: 20px;
        text-decoration: none;
        text-transform: capitalize;
        text-align: center;
        letter-spacing: 1px;
        border-radius: 5px;
        padding: 5px 10px;
    }

    nav ul li a:hover {
        background-color: #e9ecef;
        border-radius: 5px;
        color: black;
    }

    nav ul a.place-ad {
        background-color: #ffd700;
        padding: 5px 10px;
        color: black;
    }

    nav ul li .notification {
        position: relative;
    }
    
    .badge {
        background: red;
        color: white;
        font-size: 12px;
        padding: 3px 6px;
        border-radius: 50%;
        position: absolute;
        top: -10px;
        left: -5px;
    }

    .nav-bottom {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-color: #006400;
        padding: 15px 12.5% 25px;
    }

    .nav-bottom input {
        position: relative;
        width: 600px;
        border: 2px solid #adb5bd;
        border-radius: 5px;
        background-color: #f2f2f2;
        height: 100%;
        padding: 10px 10px;
        font-size: 1rem;
        margin-bottom: 0;
    }

    .nav-bottom input::placeholder {
        font-size: 1rem;
        font-style: italic;
        letter-spacing: 0.5px;
        font-weight: 500;
        opacity: 0.7;
    }

    .search-results {
        position: absolute;
        top: 60px;
        background-color:rgb(210, 244, 210);
        border-radius: 5px;
        padding: 10px;
        box-shadow: 0px 8px 10px rgba(0, 0, 0, 0.2);
        width: 600px;
        max-height: 250px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }

    .search-results a {
        height: 40px;
        display: flex;
        align-items: center;
        font-size: 16px;
        text-decoration: none;
        color: black;
        transition: background-color 0.3s;
    }

    .search-results a:hover {
        background-color:rgb(172, 246, 172);
        font-weight: 600;
        border-radius: 5px;
    }

    .search-results::-webkit-scrollbar {
        width: 5px;
    }

    .search-results::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 5px;
    }


    /* Tablet view */
    @media (min-width: 481px) and (max-width: 1200px){

        nav {
            flex-direction: column;
        }

        nav ul{
            margin-top: 20px;
        }

        nav ul li {
            list-style-type: none;
        }

        nav ul li a {
            font-size: 20px;
        }

        nav ul li .notification {
            position: relative;
        }

        .nav-bottom input {
            width: 600px;
        }

        .search-results {
            width: 600px;
        }

    }

    /* Mobile view */
    @media (max-width: 480px) {

        nav {
            flex-direction: column;
        }

        nav ul{
            margin-top: 20px;
        }

        nav ul li {
            list-style-type: none;
        }

        nav ul li a {
            font-size: 20px;
        }
        
        nav {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #006400;
            padding: 15px 12.5%;
        }

        .logo {
            font-size: 1.5rem;
            padding: 5px 8px ;
        }

        .logo a {
            font-size: 35px;
            font-weight: 800;
        }

        nav ul{
            gap: 5px;
        }
        nav ul li a {
            font-size: 15px;
            letter-spacing: 0; 
        }

        nav ul li .notification {
            position: relative;
        }
        
        .badge {
            font-size: 8px;
            top: -8px;
            left: -15px;
        }


        .nav-bottom input {
            width: 350px;
            
        }

        .search-results {
            width: 350px;
        }
    }

    </style>
    

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src='alertFunction.js'></script>

</head>

<body>

    <script>
    
    document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.nav-bottom input'); // Target the input inside .nav-bottom
    const searchResults = document.getElementById('search-results');

    // Search functionality
    searchInput.addEventListener('focus', () => {
        if (searchInput.value.trim() !== '') {
            searchResults.style.display = 'block';
        }
    });

    searchInput.addEventListener('input', () => {
        if (searchInput.value.trim() !== '') {
            searchResults.style.display = 'block';
            searchProducts(searchInput.value); // Call searchProducts on input
        } else {
            searchResults.style.display = 'none';
        }
    });

    // Hide search results when clicking outside
    document.addEventListener('click', (event) => {
        const isClickInside = event.target.closest('.nav-bottom'); // Check if click is inside .nav-bottom
        if (!isClickInside) {
            searchResults.style.display = 'none';
        }
    });
});

function searchProducts(query) {
    const results = document.getElementById("search-results");
    if (query.length === 0) {
        results.style.display = "none";
        results.innerHTML = "";
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("GET", "search_products.php?q=" + encodeURIComponent(query), true); // Encode query for safety
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            results.innerHTML = xhr.responseText;
            results.style.display = "block";
        }
    };
    xhr.send();
}
    </script>


    <nav>
        <div class="logo">
            <a href="home.php">AgroMart</a>
        </div>

        <ul id="menuList">
            <?php if (!isset($_SESSION['username'])): ?>
            <li><a href="requests.php">Product Requests</a></li>
            <li><a href="#" onclick="showAlert('Please login to post an Ad','error','#ff0000')" class="place-ad">Post Ad</a></li>
            <li><a href="#" onclick="showAlert('Please login to see Wishlist','error','#ff0000')"><i class="fas fa-heart nav-icon" title="Wishlist"></i></a></li>
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
            <li><a href="#" onclick="confirmAlert('Are you sure want to logout?','logout.php')"><i class="fa fa-sign-out nav-icon" aria-hidden="true"></i></a></li>
            <?php else: ?>
            <li><a href="login.php">Log In</a></li>
            <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="nav-bottom">
        <input type="text" placeholder="Search what you want" onkeyup="searchProducts(this.value)">
        <div class="search-results" id="search-results"></div>
    </div>

</body>

</html>