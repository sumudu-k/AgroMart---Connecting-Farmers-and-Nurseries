<?php
session_start();
include 'config.php';
include 'navbar.php';

// Fetch categories from the database
$query = "SELECT * FROM categories";
$result = $conn->query($query);


$ads_query = "
    SELECT ads.*, 
        categories.category_name, 
        (SELECT image_path FROM ad_images WHERE ad_id = ads.ad_id LIMIT 1) AS image 
    FROM ads 
    JOIN categories ON ads.category_id = categories.category_id 
    ORDER BY RAND() LIMIT 8";
$ads_result = $conn->query($ads_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>AgroMart Home</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        /* Banner Image */
        .banner-image {
            position: relative;
            width: 100%;
            height: 750px;
            overflow: hidden;
        }

        .banner-slides {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .banner-slides.active {
            opacity: 1;
            z-index: 1;
        }


        .main-container {
            width: 75%;
            margin: 0 auto;
        }


        /* Category Section */
        /* all categories Container */
        .category-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            border-radius: 10px;
            margin-top: 30px;
            padding: 40px 0 50px 0;
            background-color: #e9ecef;
        }


        .category-title {
            text-align: center;
            width: 100%;
            font-size: 2.3rem;
            color: #333;
            margin-bottom: 20px;
        }

        /* For category card */
        .category-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 200px;
        }

        .category-card a {
            display: block;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            background-color: #f5f5a9;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .category-card:hover {
            transform: scale(1.05);
        }

        .category-card img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }


        .category-name {
            margin-top: 20px;
            font-size: 1.2rem;
            color: #333;
            font-weight: bold;
        }


        /* Welcome Section */
        .welcome-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 10px;
            margin-top: 30px;
            padding: 40px;
        }

        .welcome-image {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .welcome-image img {
            max-width: 100%;
            height: auto;
            mix-blend-mode: multiply;
            border-radius: 10px;
        }

        .welcome-text {
            flex: 1;
            margin-left: 20px;
        }

        .welcome-text h2 {
            color: #ff8c00;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .welcome-text p {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 15;
            /* set line limit to 15 */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 20px;
        }

        .ads-container {
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            justify-content: center;
            background-color: #e9ecef;
            border-radius: 10px;
            padding: 40px 0;
            margin: 30px 0;
        }


        .ads-title {
            text-align: center;
            width: 100%;
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        .sugestion-ads {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            border-radius: 10px;
            padding: 20px 0;
        }

        .ad-card {
            width: calc(20% - 25px);
            text-align: center;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .ad-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid #f09319;
        }

        .ad-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            mix-blend-mode: multiply;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .ad-card h4 {
            font-size: 1.2rem;
            color: #333;
            margin: 5px 0;
        }

        .ad-card .description {
            line-height: 1.4;
            font-size: 14px;
            color: #555;
            text-align: left;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 0px 15px 10px 15px;
        }

        .ad-details {
            margin: 5px 15px 10px 15px;
            text-align: center;
        }

        .ad-details p {
            line-height: 1.4;
            font-size: 14px;
            color: #555;
        }

        .view-all-btn {
            text-align: center;
            margin: 20px 0 30px 0;
        }

        .view-all-btn button {
            padding: 12px 25px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #f09319;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .view-all-btn button:hover {
            background-color: #cb790d;
        }

        .contact-section {
            border-radius: 10px;
            padding: 50px 0;
            margin: 30px 0;
        }

        .contact-section h1 {
            text-align: center;
            width: 100%;
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
        }

        .contact-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 0;
        }

        .contact-info {
            background-color: rgba(0, 100, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 3px 3px 3px 3px rgb(240, 147, 25, 0.8);
            width: 45%;
        }

        .contact-title {
            text-align: center;
            font-size: 1.8rem;
            color: white;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.6);
            width: 80%;
            border-radius: 10px;
            padding: 10px;
            margin: 0 auto 20px auto;
        }

        .info-item i {
            min-width: 25px;
            height: 25px;
            color: #181c14;
            font-size: 1.5rem;
            margin: 0 20px 0 10px;
        }

        .info-item div {
            width: 100%;
        }

        .contact-info h3 {
            text-align: left;
            color: #181c14;
        }

        .info-item div hr {
            border: none;
            width: 80px;
            height: 3px;
            background-color: #181c14;
            border-radius: 10px;
            margin: 5px 0 15px 0;
        }

        .contact-info p {
            text-align: left;
            color: #3c3d37;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .get-in-touch {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 3px 3px 3px 3px rgba(0, 0, 0, 0.1);
            width: 45%;
        }

        .get-in-touch h2 {
            color: #333;
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .get-in-touch p {
            text-align: center;
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .form-row {
            display: flex;
            gap: 10px;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: none;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #f09319;
        }

        input::placeholder,
        textarea::placeholder {
            color: #888;
            font-size: 0.9rem;
            font-style: italic;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        textarea {
            height: 200px;
        }

        .get-in-touch button {
            font-size: 16px;
            background-color: #f09319;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .get-in-touch button:hover {
            background-color: #cb790d;
        }




        /* For Mobile devices */
        @media screen and (max-width: 480px) {
            .main-container {
                width: 95%;
            }

            .banner-image {
                height: 200px;
            }

            /* Categories */
            .category-container {
                padding: 20px 0;
                gap: 15px;
            }

            .category-card {
                width: 120px;
            }

            .category-card a {
                width: 100px;
                height: 100px;
            }

            .category-title {
                font-size: 1.5rem;
            }

            .category-name {
                font-size: 1rem;
            }

            /* Welcome Section */
            .welcome-section {
                flex-direction: column;
                padding: 20px;
            }

            .welcome-text {
                margin-left: 0;
                margin-top: 20px;
            }

            .welcome-text h2 {
                font-size: 1.8rem;
            }

            .welcome-text p {
                font-size: 0.9rem;
            }

            /* Ads Section */
            .ads-title {
                font-size: 1.5rem;
            }

            .ad-card {
                width: calc(100% - 20px);
            }

            /* Contact Section */
            .contact-section {
                padding: 20px 0;
            }

            .contact-container {
                flex-direction: column;
                padding: 0 10px;
            }

            .contact-info,
            .get-in-touch {
                width: 100%;
            }

            .info-item p {
                font-size: 0.9rem;
            }

            .form-row {
                flex-direction: column;
            }

            /* Footer */
            .footer-container {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .footer-links ul {
                align-items: center;
            }

            .social-links hr {
                margin: 5px auto 20px auto;
            }
        }

        /* 481px - 1200px  */
        @media screen and (min-width: 481px) and (max-width: 1200px) {
            .main-container {
                width: 90%;
            }

            .banner-image {
                height: 400px;
            }

            .category-card {
                width: 160px;
            }

            .category-card a {
                width: 120px;
                height: 120px;
            }

            .welcome-section {
                flex-direction: column;
                padding: 30px;
            }

            .welcome-text {
                margin-left: 0;
                margin-top: 20px;
            }

            .welcome-text h2 {
                font-size: 2rem;
            }

            .ad-card {
                width: calc(33.33% - 20px);
            }

            .contact-container {
                flex-direction: column;
                padding: 0 20px;
            }

            .contact-info,
            .get-in-touch {
                width: 100%;
                margin-bottom: 20px;
            }
        }

        @media screen and (min-width: 1201px) {
            .main-container {
                width: 75%;
            }

            .banner-image {
                height: 750px;
            }

            .ad-card {
                width: calc(20% - 25px);
            }
        }


        @media screen and (max-width: 318px) {
            .category-card {
                width: 100px;
            }

            .category-card a {
                width: 80px;
                height: 80px;
            }

            .category-name {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>

    <!-- Home page banner slider -->
    <div class="banner-image">
        <img class="banner-slides active" src="images/cover.jpg" alt="Slide 1">
        <img class="banner-slides"
            src="images/lettuce-plant-on-field-vegetable-and-agriculture-sunset-and-light-free-photo.jpg" alt="Slide 2">
        <img class="banner-slides" src="images/iStock-531690340_c_valentinrussanov.webp" alt="Slide 3">
    </div>

    <script>
        let slideIndex = 0;
        const slides = document.getElementsByClassName("banner-slides");

        function showSlides() {
            for (let i = 0; i < slides.length; i++) {
                slides[i].classList.remove("active");
            }

            slideIndex++;

            if (slideIndex > slides.length) {
                slideIndex = 1
            }
            slides[slideIndex - 1].classList.add("active");
            setTimeout(showSlides, 3000);
        }

        showSlides(); // Start the slideshow
    </script>

    <div class="main-container">

        <!-- category section -->
        <div class="category-container">
            <h1 class="category-title">Categories</h1>

            <?php while ($category = $result->fetch_assoc()): ?>
                <div class="category-card">
                    <a href="category_ads.php?category_id_qp=<?php echo $category['category_id']; ?>">
                        <img src="uploads/<?php echo $category['category_image']; ?>"
                            alt="<?php echo $category['category_name']; ?>">
                    </a>
                    <h3 class="category-name"><?php echo $category['category_name']; ?></h3>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Welcome section -->
        <section class="welcome-section">
            <div class="welcome-image">
                <img src="images/inner_home_05-1024x768.jpg" alt="Gardening Image">
            </div>
            <div class="welcome-text">
                <h2>Nature In Your House</h2>
                <p>AgroMart is an innovative online platform developed by Idea Innovators (Pvt) Ltd. that aims to
                    revolutionize Sri Lanka's agricultural sector by creating a seamless connection between farmers and
                    nurseries. The platform serves as a centralized marketplace where farmers can easily access detailed
                    crop information, compare prices, and find quality agricultural resources, while nurseries can
                    expand their customer reach through a user-friendly advertisement system. Through its robust search
                    functionality and comprehensive product categorization, AgroMart eliminates the traditional
                    challenges farmers face in finding suitable nurseries, while simultaneously providing nurseries with
                    direct market access and simplified product promotion capabilities. This digital bridge between
                    agricultural buyers and sellers not only streamlines the supply chain but also fosters a more
                    efficient and transparent marketplace for Sri Lanka's farming community.</p>
            </div>
        </section>

        <!-- Ads area HTML -->
        <div class="ads-container">
            <h1 class="ads-title">Find What You Want Here</h1>
            <div class="sugestion-ads">
                <?php if ($ads_result->num_rows > 0): ?>
                    <?php while ($ad = $ads_result->fetch_assoc()): ?>
                        <div class="ad-card" onclick="window.location.href='view_ad.php?ad_id=<?= $ad['ad_id']; ?>'">
                            <img src="<?= htmlspecialchars($ad['image']); ?>" alt="Ad Image">
                            <h4><?= htmlspecialchars($ad['title']); ?></h4>
                            <p class="description"><?= htmlspecialchars(substr($ad['description'], 0, 100)) . '...'; ?></p>
                            <div class="ad-details">
                                <p>Rs <?= htmlspecialchars($ad['price']); ?></p>
                                <p><strong>District:</strong> <?= htmlspecialchars($ad['district']); ?></p>
                                <p><strong>Posted on:</strong>
                                    <?= htmlspecialchars(date('Y-m-d', strtotime($ad['created_at']))); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No ads available at the moment.</p>
                <?php endif; ?>
            </div>
            <div class="view-all-btn">
                <a href="all_ads.php"><button>View All Ads</button></a>
            </div>
        </div>

        <section class="contact-section">
            <h1>Contact</h1>
            <div class="contact-container">
                <!-- Contact Info -->
                <div class="contact-info">
                    <h2 class="contact-title">Contact Information</h2>

                    <div class="info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <div>
                            <h3>Our Location</h3>
                            <hr />
                            <p>
                                No 15,<br />
                                Haputhalegama,<br />
                                Haputhale
                            </p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fa-solid fa-phone"></i>
                        <div>
                            <h3>Phone Number</h3>
                            <hr />
                            <p>057 2229534</p>
                            <p>071 3864286</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fa-solid fa-envelope"></i>
                        <div>
                            <h3>Email Address</h3>
                            <hr />
                            <p>info@agromt.com</p>
                            <p>contact@agromt.com</p>
                        </div>
                    </div>
                </div>

                <!-- Get in Touch Form -->
                <div class="get-in-touch">
                    <h2>Get In Touch</h2>
                    <form action="https://api.web3forms.com/submit" method="POST">
                        <div class="form-row">
                            <input type="hidden" name="access_key" value="83e39209-89c2-4845-b467-d7d03f3ba2dd" />
                            <input type="text" name="name" placeholder="Your Name" required />
                            <input type="email" name="email" placeholder="Your Email" required />
                        </div>
                        <input type="text" name="subject" placeholder="Subject" />
                        <textarea placeholder="Message" name="message" required></textarea>
                        <button type="submit">Send Message</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
    <?php
    $conn->close();
    ?>

</body>

</html>
<?php
include 'footer.php';
?>