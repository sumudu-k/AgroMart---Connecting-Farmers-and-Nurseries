<?php
?>
<footer>
    <div class="footer-container">
        <div class="footer-info">
            <h3>AgroMart</h3>
            <address>No 15, Haputhalegama, Haputhale</address>
            <p>Phone: 071 3864286</p>
            <p>Email: info@agromart.com</p>
        </div>
        <div class="footer-links">
            <h3>Useful Links</h3>
            <hr />
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="post_ad.php">Post an Ad</a></li>
                <li><a href="about_us.php">About Us</a></li>
            </ul>
        </div>
        <div class="social-links">
            <h3>Social Links</h3>
            <hr />
            <ul>
                <li><a href="#"><i class="fa-brands fa-facebook"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-linkedin"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-tiktok"></i></a></li>
                <li><a href="#"><i class="fa-brands fa-youtube"></i></a></li>
            </ul>
        </div>
    </div>
</footer>
<style>
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

    .footer-info h3 {
        color: #1c1c1c;
        text-align: center;
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .footer-info address {
        text-align: center;
        color: white;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .footer-info p {
        font-weight: 600;
        color: white;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .footer-links h3 {
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
        color: #d2d2d2;
    }

    .footer-links hr {
        border: none;
        width: 80px;
        height: 3px;
        background-color: #181c14;
        border-radius: 10px;
        margin: 5px 0 20px 15%;
    }

    .footer-links ul {
        display: flex;
        flex-direction: column;
        list-style: none;
    }

    .footer-links ul li {
        height: 40px;
        width: 100px;
        display: flex;
        margin-bottom: 10px;
    }

    .footer-links ul li a {
        width: 100%;
        font-weight: 600;
        color: white;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .social-links {
        width: 200px;
    }

    .social-links h3 {
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
        color: #d2d2d2;
    }

    .social-links hr {
        border: none;
        width: 80px;
        height: 3px;
        background-color: #181c14;
        border-radius: 10px;
        margin: 5px 0 20px 30%;
    }

    .social-links ul {
        display: flex;
        justify-content: space-evenly;
        list-style: none;
    }

    .social-links i {
        font-size: 25px;
        color: white;
        margin-right: 10px;
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
            padding: 20px 5%;
        }

        .footer-container {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .footer-links ul {
            align-items: center;
        }

        .social-links {
            width: 100%;
        }

        .social-links hr {
            margin: 5px auto 20px auto;
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

        .footer-links ul {
            align-items: center;
        }

        .social-links {
            width: 100%;
        }
    }
</style>