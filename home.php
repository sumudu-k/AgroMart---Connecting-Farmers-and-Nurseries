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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <title>AgroMart Home</title>
    <style>
    </style>
</head>

<body>

    <!-- Home page banner slider -->
    <div class="banner-image">
        <img class="banner-slides active" src="images/cover.jpg" alt="Slide 1">
        <img class="banner-slides" src="images/lettuce-plant-on-field-vegetable-and-agriculture-sunset-and-light-free-photo.jpg" alt="Slide 2">
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

            if (slideIndex > slides.length) {slideIndex = 1}
            slides[slideIndex - 1].classList.add("active");
            setTimeout(showSlides, 3000);
        }

        showSlides(); // Start the slideshow

    </script>


    <!-- Home page category list -->
    <h1>Our Categories</h1>
    <div class="category-container">
        <?php while ($category = $result->fetch_assoc()): ?>
        <div class="category-card">
            <a href="category_ads.php?category_id_qp=<?php echo $category['category_id']; ?>">
                <img src="uploads/<?php echo $category['category_image']; ?>"
                    alt="<?php echo $category['category_name']; ?>">
                <h3><?php echo $category['category_name']; ?></h3>
            </a>
        </div>
        <?php endwhile; ?>
    </div>

    <div class="main-container">

        <!-- category section -->
        <div class="category-container">
            <h1 class="category-title">Categories</h1>

            <?php while ($category = $result->fetch_assoc()): ?>
                <div class="category-card">
                    <a href="category_ads.php?category_id_qp=<?php echo $category['category_id']; ?>">
                        <img src="uploads/<?php echo $category['category_image']; ?>" alt="<?php echo $category['category_name']; ?>">
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
                <p>AgroMart is an innovative online platform developed by Idea Innovators (Pvt) Ltd. that aims to revolutionize Sri Lanka's agricultural sector by creating a seamless connection between farmers and nurseries. The platform serves as a centralized marketplace where farmers can easily access detailed crop information, compare prices, and find quality agricultural resources, while nurseries can expand their customer reach through a user-friendly advertisement system. Through its robust search functionality and comprehensive product categorization, AgroMart eliminates the traditional challenges farmers face in finding suitable nurseries, while simultaneously providing nurseries with direct market access and simplified product promotion capabilities. This digital bridge between agricultural buyers and sellers not only streamlines the supply chain but also fosters a more efficient and transparent marketplace for Sri Lanka's farming community.</p>
            </div>
        </section>

    <?php
    $conn->close();
    ?>

</body>

</html>