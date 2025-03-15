<?php
include 'config.php';
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - AgroMart</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .aboutCover {
            background-image: url("uploads/aboutus.jpg");
            background-size: cover;
            background-position: center;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        body {
            font-family: "Poppins", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            overflow-x: hidden;
            line-height: 1.6;
        }

        .container {
            width: 75%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            margin-bottom: 20px;
        }

        h1 {
            text-align: center;
            padding: 20px;
            font-size: 2.2rem;
            margin: 0 0 30px 0;
            text-transform: capitalize;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 1.8rem;
            margin: 30px 0 15px 0;
            text-transform: capitalize;
        }

        p li {
            font-size: 1rem;
            color: #555;
            margin-bottom: 20px;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif
        }

        .Why-list {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
        }

        .Why-list li {
            font-size: 1rem;
            color: #333;
            padding: 10px 0;
            position: relative;
            padding-left: 25px;
        }

        .Why-list li:before {
            content: '\f058';
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #f09319;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Mobile Devices*/
        @media screen and (max-width: 480px) {
            .container {
                width: 95%;
                padding: 10px;
            }

            h1 {
                font-size: 1.5rem;
                padding: 15px 5%;
            }

            h2 {
                font-size: 1.3rem;
                margin: 20px 0 10px 0;
            }

            p {
                font-size: 0.9rem;
            }

            .Why-list li {
                font-size: 0.9rem;
                padding: 8px 0 8px 20px;
            }
        }

        /* Tablets*/
        @media screen and (min-width: 481px) and (max-width: 1200px) {
            .container {
                width: 95%;
                padding: 15px;
            }

            h1 {
                font-size: 1.8rem;
                padding: 20px 8%;
            }

            h2 {
                font-size: 1.5rem;
                margin: 25px 0 12px 0;
            }

            p {
                font-size: 0.95rem;
            }

            .Why-list li {
                font-size: 0.95rem;
                padding: 9px 0 9px 22px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="aboutCover">
        <h1>Welcome to Our AgroMart Marketplace</h1>
    </div>


    <div class="container">


        <p>We are a dedicated online platform connecting farmers, nursery owners, and plant enthusiasts across Sri
            Lanka. Our mission is to bridge the gap between sellers and buyers, making it easier to access high-quality
            plants, seeds, and agricultural products at affordable prices.</p>

        <h2>Our Mission</h2>
        <p>We aim to be the leading provider of quality plants and agricultural products while offering expert guidance
            to support gardening and farming communities. Our platform enables sellers to showcase their products
            effortlessly and helps buyers find exactly what they need for their farms or home gardens.</p>

        <h2>Our Vision</h2>
        <p>Our vision is to foster a sustainable and greener future by making agricultural trade more efficient,
            accessible, and affordable. We believe in the power of nature and strive to bring people closer to it
            through our platform.</p>

        <h2>Why Choose Us?</h2>
        <ul class="Why-list">
            <li>Easy online marketplace for anyone to buy and sell plants and agricultural products.</li>
            <li>Wide range of quality seeds, seedlings, and gardening supplies.</li>
            <li>Supports local farmers and nurseries by increasing their reach.</li>
            <li>Direct communication between buyers and sellers for transparency.</li>
            <li>Updated product listings with competitive pricing.</li>
        </ul>

        <h2>How It Works?</h2>
        <p>Our platform allows users to register and post their products for sale. Whether you are a professional
            nursery owner or an individual looking to sell surplus plants, our website provides a convenient solution.
            Buyers can browse through categories, search for specific plants, and contact sellers directly.</p>

        <h2>Join Us Today</h2>
        <p>Be a part of our growing community and contribute to the sustainable agricultural industry in Sri Lanka.
            Register now and start buying or selling with ease!</p>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>