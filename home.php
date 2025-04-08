<?php
session_start();
include 'config.php';
include 'navbar.php';

$query = "SELECT * FROM categories";
$result = $conn->query($query);

// retrieve most viewd ads
$sql_most_viewed = "SELECT ads.*,
categories.category_name,
(select image_path from ad_images where ad_id=ads.ad_id limit 1) as image
from ads
join categories on ads.category_id= categories.category_id
order by ads.view_count desc limit 2 ";
$most_viewed_result = $conn->query($sql_most_viewed);


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
    <link rel="stylesheet" href="css/home.css">
    <title>AgroMart Home</title>
</head>

<body>

    <!-- home page banner slider -->
    <div class="banner-image">
        <img class="banner-slides active" src="images/1.jpg" alt="Slide 1">
        <img class="banner-slides" src="images/2.jpg" alt="Slide 2">
        <img class="banner-slides" src="images/3.jpg" alt="Slide 3">
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

    showSlides();
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

        <div class="popular">
            <h3>Most Viewed</h3>
            <?php while ($trending = $most_viewed_result->fetch_assoc()): ?>
            <a href="view_ad.php?ad_id=<?= $trending['ad_id'] ?>">
                <div>
                    <h4><?= htmlspecialchars($trending['view_count']) ?></h4>
                    <h4><?= htmlspecialchars($trending['title']) ?></h4>
                    <p><?= htmlspecialchars($trending['price']) ?></p>
                    <p><?= htmlspecialchars($trending['district']) ?></p>
                    <p><?= htmlspecialchars($trending['category_name']) ?></p>
                    <p><img src="<?= htmlspecialchars($trending['image']) ?>" alt=""></p>
                    <p><?= htmlspecialchars(date('Y-m-d h:i A', strtotime($trending['created_at']))); ?></p>
                </div>
            </a>
            <?php endwhile; ?>
            <a href="popular.php"> View Trending</a>
        </div>

        <!-- welcome section -->
        <section class="welcome-section">
            <div class="welcome-image">
                <img src="images/hero.jpg" alt="Gardening Image">
            </div>
            <div class="welcome-text">
                <h2>Transform your space with greenery today</h2>
                <p>AgroMart is an innovative online platform developed by Idea Innovators (Pvt) Ltd. that aims to
                    revolutionize Sri Lanka's agricultural sector by creating a seamless connection between farmers and
                    nurseries. The platform serves as a centralized marketplace where farmers can easily access detailed
                    crop information, compare prices, and find quality agricultural resources, while nurseries can
                    expand their customer reach through a user-friendly advertisement system. </p>
            </div>
        </section>

        <div class="ads-container">
            <h1 class="ads-title">Find What You Want Here</h1>
            <div class="sugestion-ads">
                <?php if ($ads_result->num_rows > 0): ?>
                <?php while ($ad = $ads_result->fetch_assoc()): ?>
                <div class="ad-card" onclick="window.location.href='view_ad.php?ad_id=<?= $ad['ad_id']; ?>'">
                    <img src="<?= htmlspecialchars($ad['image'] ?? 'images/placeholder/No_Image_AD.png'); ?>"
                        alt="Product Image">
                    <h4><?= htmlspecialchars($ad['title']); ?></h4>
                    <p class="description"><?= htmlspecialchars(substr($ad['description'], 0, 100)) . '...'; ?></p>
                    <div class="ad-details">
                        <p>Rs <?= htmlspecialchars($ad['price']); ?></p>
                        <p><strong>District:</strong> <?= htmlspecialchars($ad['district']); ?></p>
                        <p><strong>Posted on:</strong>
                            <?= htmlspecialchars(date('Y-m-d h:i A', strtotime($ad['created_at']))); ?></p>
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