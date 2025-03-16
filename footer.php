<?php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
</head>

<body>
    <footer>
        <div class="footer-container">
            <div class="footer-info">
                <h3>AgroMart</h3>
                <p class="address">No 15, Haputhalegama, Haputhale</p>
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
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Footer */
    footer {
        background-color: #006400;
        width: 100%;
        padding: 5vh 12.5%;
    }

    .footer-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .footer-info {
        width: 30%;
    }

    .footer-info h3 {
        color: #d2d2d2;
        text-align: center;
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .footer-info p {
        font-weight: 600;
        color: white;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .footer-info .address {
        text-transform: capitalize;
        text-align: center;
    }

    .footer-links {
        width: 20%;
    }

    .footer-links h3 {
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
        color: #d2d2d2;
        position: relative;
        margin-bottom: 20px;
    }

    .footer-links h3::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -8px;
        width: 100px;
        height: 3px;
        background-color: #181c14;
        border-radius: 10px;
        transform: translateX(-50%);
    }

    .footer-links ul {
        display: flex;
        flex-direction: column;
        align-items: center;
        list-style: none;
    }

    .footer-links ul li {
        width: 100px;
        height: 40px;
    }

    .footer-links ul li a {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        font-weight: 600;
        color: white;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .social-links {
        width: 20%;
    }

    .social-links h3 {
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
        color: #d2d2d2;
        position: relative;
        margin-bottom: 20px;
    }

    /* Create underline using ::after */
    .social-links h3::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -8px;
        width: 100px;
        height: 3px;
        background-color: #181c14;
        border-radius: 10px;
        transform: translateX(-50%);
    }

    .social-links ul {
        display: flex;
        justify-content: space-evenly;
        list-style: none;
        align-items: center;
    }

    .social-links i {
        font-size: 25px;
        color: white;
        cursor: pointer;
    }

    .footer-links li:hover a {
        color: #b7b7b7;
        cursor: pointer;
    }

    .social-links li:hover i {
        color: #b7b7b7;
        cursor: pointer;
    }

    /* Responsive Footer Styles */
    @media screen and (max-width: 480px) {
        footer {
            padding: 30px 8%;
        }

        .footer-container {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .footer-info {
            width: 90%;
            margin-bottom: 20px;
        }

        .footer-info h3 {
            position: relative;
            margin-bottom: 20px;

        }

        .footer-info h3::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -8px;
            width: 100px;
            height: 3px;
            background-color: #181c14;
            border-radius: 10px;
            transform: translateX(-50%);
        }

        .footer-info p {
            color: white;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }


        .footer-links h3 {
            font-size: 1.2rem;
            font-weight: 600;
            text-align: center;
            color: #d2d2d2;
            position: relative;
            margin-bottom: 10px;
        }

        .footer-links {
            width: 90%;
        }

        .footer-links ul {
            flex-direction: row;
            /* align-items: center; */
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .footer-links ul li {
            width: 100px;
            height: unset;
            padding: 10px 0;
        }

        .footer-links ul li a {
            font-size: 0.9rem;
        }

        .social-links {
            width: 90%;
        }

        .social-links h3 {
            margin-bottom: 20px;
        }

        .social-links ul {
            justify-content: space-between;
            align-items: center;
        }

        .social-links li {
            margin: 0 10px;
        }

        .social-links i {
            font-size: 20px;
        }
    }

    @media screen and (min-width: 481px) and (max-width: 1200px) {
        footer {
            padding: 30px 8%;
        }

        .footer-container {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .footer-info {
            width: 50%;
            margin-bottom: 20px;
        }

        .footer-info h3 {
            position: relative;
            margin-bottom: 20px;

        }

        .footer-info h3::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -8px;
            width: 100px;
            height: 3px;
            background-color: #181c14;
            border-radius: 10px;
            transform: translateX(-50%);
        }

        .footer-info p {
            color: white;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }


        .footer-links h3 {
            font-size: 1.2rem;
            font-weight: 600;
            text-align: center;
            color: #d2d2d2;
            position: relative;
            margin-bottom: 10px;
        }

        .footer-links {
            width: 75%;
        }

        .footer-links ul {
            flex-direction: row;
            /* align-items: center; */
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .footer-links ul li {
            width: 100px;
            height: unset;
            padding: 10px 0;
        }

        .footer-links ul li a {
            font-size: 1.2rem;
        }

        .social-links {
            width: 75%;
        }

        .social-links h3 {
            margin-bottom: 20px;
        }

        .social-links ul {
            justify-content: space-between;
            align-items: center;
        }

        .social-links li {
            margin: 0 10px;
        }

        .social-links i {
            font-size: 30px;
        }
    }
    </style>
    <script src='alertFunction.js'></script>
</body>

</html>