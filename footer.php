<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/footer.css">
    <title>Footer</title>
</head>

<body>
    <footer>
        <div class="footer-container">
            <div class="footer-info">
                <h3>AgroMart</h3>
                <p>No 15, Haputhalegama, Haputhale</p>
                <p>Phone: 071 3864286</p>
                <p>Email: info@agromart.com</p>
            </div>
            <div class="footer-links">
                <h3>Useful Links</h3>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <?php
                    if (!isset($_SESSION['username'])) { ?>
                    <li><a href="#" onclick="showAlert('Please login to post an Ad','error','#ff0000')">Post an Ad</a>
                    </li>
                    <?php
                    } else { ?>
                    <li><a href="post_ad.php">Post an Ad</a></li>
                    <?php }
                    ?>
                    <li><a href="about_us.php">About Us</a></li>
                </ul>
            </div>
            <div class="social-links">
                <h3>Social Links</h3>
                <ul>
                    <li><a href="#"><i class="fa-brands fa-linkedin"></i></a></li>
                    <li><a href="#"><i class="fa-brands fa-facebook"></i></a></li>
                    <li><a href="#"><i class="fa-brands fa-tiktok"></i></a></li>
                    <li><a href="#"><i class="fa-brands fa-youtube"></i></a></li>
                </ul>
            </div>
        </div>
    </footer>
    <script src='alertFunction.js'></script>
</body>

</html>