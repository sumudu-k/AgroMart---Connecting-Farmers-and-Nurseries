<?php
session_start();
include 'config.php';
include 'navbar.php';

if (isset($_GET['ad_id'])) {
    $ad_id = $_GET['ad_id'];

    $ad_sql = "
    SELECT ads.*, categories.category_name, ads.created_at 
    FROM ads 
    JOIN categories ON ads.category_id = categories.category_id 
    WHERE ads.ad_id = ?";

    $stmt = $conn->prepare($ad_sql);
    $stmt->bind_param("i", $ad_id);
    $stmt->execute();
    $ad_result = $stmt->get_result();
    $ad = $ad_result->fetch_assoc();

    if ($ad) {
        $category_id = $ad['category_id'];
    } else {

        echo "<script>
        window.onload = function() {
            showAlert('Ad not found', 'error', '#ff0000');
        };
        setTimeout(function() {
        window.location.href = 'home.php';
        }, 2000);
    </script>";
    }

    $img_sql = "SELECT image_path FROM ad_images WHERE ad_id = ?";
    $stmt_img = $conn->prepare($img_sql);
    $stmt_img->bind_param("i", $ad_id);
    $stmt_img->execute();
    $img_result = $stmt_img->get_result();
    $images = [];
    while ($image = $img_result->fetch_assoc()) {
        $images[] = $image['image_path'];
    }

    $similar_ads_sql = "
        SELECT ads.*, categories.category_name, 
            (SELECT image_path FROM ad_images WHERE ad_id = ads.ad_id LIMIT 1) AS image 
        FROM ads 
        JOIN categories ON ads.category_id = categories.category_id 
        WHERE ads.category_id = ? AND ads.ad_id != ? 
        ORDER BY ads.created_at DESC 
        LIMIT 4";
    $similar_stmt = $conn->prepare($similar_ads_sql);
    $similar_stmt->bind_param("ii", $category_id, $ad_id);
    $similar_stmt->execute();
    $similar_ads_result = $similar_stmt->get_result();

    $user_id = $_SESSION['user_id'] ?? null;
    $is_wishlisted = false;
} else {
    echo "<script>
    window.onload = function() {
        showAlert('Ad Id is missing', 'error', '#ff0000');
    };
    setTimeout(function() {
    window.location.href = 'home.php';
}, 2000);

    
    </script>";
    exit;
}

if ($user_id) {
    $wishlist_check_sql = "SELECT * FROM wishlist WHERE user_id = ? AND ad_id = ?";
    $wishlist_stmt = $conn->prepare($wishlist_check_sql);
    $wishlist_stmt->bind_param("ii", $user_id, $ad_id);
    $wishlist_stmt->execute();
    $wishlist_result = $wishlist_stmt->get_result();
    $is_wishlisted = $wishlist_result->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ad['title']); ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            color: #333;
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Add background image */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("images/B1.jpg");
            background-size: cover;
            opacity: 0.2;
            z-index: -1;
        }

        .container {
            max-width: 75%;
            margin: auto;
            padding: 20px 0;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .ad-image {
            flex: 1 1 45%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-width: 300px;
        }

        .displayed-image {
            display: flex;
            justify-content: center;
            overflow: hidden;
            width: 100%;
            height: 350px;
            border-radius: 10px;
        }

        .displayed-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            mix-blend-mode: multiply;
        }

        .thumbnail-images {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .thumbnail-images img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 2px solid #a9e6a9;
            cursor: pointer;
            border-radius: 5px;
            transition: transform 0.2s;
        }

        .thumbnail-images img:hover {
            transform: scale(1.05);
        }

        .ad-details {
            flex: 1 1 45%;
            min-width: 300px;
            padding: 20px;
            background-color: #f0ffe8;
            border-radius: 10px;
        }

        .ad-details h1 {
            font-size: 2rem;
            color: #ff8c00;
            margin: 10px 0 20px 0;
        }

        .ad-details .ad-description {
            font-size: 1.1rem;
            color: #444;
            margin: 10px 0 30px 0;
            line-height: 1.3;
        }

        .ad-details .price {
            font-weight: bold;
            color: #b03052;
        }

        .ad-details p {
            font-size: 1.1rem;
            color: #444;
            margin: 10px 0;
        }

        .ad-details p img {
            width: 24px;
            height: 24px;
        }

        .ad-details p a {
            text-decoration: underline;
            color: #325;
            transition: color 0.2s;
        }

        .ad-details p a:hover {
            color: #ff8c00;
        }

        .similarProducts {
            max-width: 75%;
            background-color: #f0ffe8;
            margin: 20px auto 30px auto;
            padding: 20px;
            border-radius: 10px;
            position: relative;
            z-index: 1;
        }

        .similarProducts h3 {
            text-align: center;
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 30px;
        }

        .moreItemsContainer {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .moreItemCard {
            flex: 1 1 18%;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 2px rgba(0, 128, 0, 0.15);
            text-align: center;
            padding: 10px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .moreItemCard img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            border-bottom: 4px solid #a9e6a9;
        }

        .moreItemCard h4 {
            font-size: 1.3rem;
            color: #006400;
            margin: 10px 0 5px 0;
            font-weight: 600;
        }

        .moreItemCard p {
            font-size: 1.2rem;
            color: #f95454;
            font-weight: 600;
        }

        .moreItemCard:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 100, 0, 0.2);
        }

        @media screen and (max-width: 480px) {
            .container {
                max-width: 95%;
                padding: 10px;
                flex-direction: column;
            }

            .ad-image,
            .ad-details {
                flex: 1 1 100%;
                min-width: 0;
                padding: 10px 0;
            }

            .displayed-image {
                height: 250px;
            }

            .thumbnail-images img {
                width: 70px;
                height: 70px;
            }

            .ad-details {
                padding: 10px;
            }

            .ad-details h1 {
                font-size: 1.5rem;
            }

            .ad-details .ad-description {
                font-size: 1rem;
            }

            .ad-details p {
                font-size: 1rem;
            }

            .wishlist-button {
                font-size: 1rem;
                padding: 8px 12px;
            }

            .similarProducts {
                max-width: 95%;
                padding: 15px;
            }

            .moreItemsContainer {
                gap: 5px;
                flex-direction: column;
            }

            .similarProducts h3 {
                font-size: 1.5rem;
            }

            .moreItemCard {
                width: 100%;
            }

            .moreItemCard img {
                height: 150px;
            }

            .moreItemCard h4 {
                font-size: 1.2rem;
            }

            .moreItemCard p {
                font-size: 1rem;
            }
        }

        @media screen and (min-width: 481px) and (max-width: 1200px) {
            .container {
                max-width: 95%;
                padding: 15px;
            }

            .ad-image,
            .ad-details {
                flex: 1 1 100%;
            }

            .displayed-image {
                height: 300px;
            }

            .thumbnail-images img {
                width: 80px;
                height: 80px;
            }

            .ad-details h1 {
                font-size: 32px;
            }

            .ad-details .ad-description {
                font-size: 18px;
            }

            .ad-details p {
                font-size: 18px;
            }

            .similarProducts {
                max-width: 95%;
            }

            .moreItemsContainer {
                gap: 10px;
                flex-wrap: wrap;
            }

            .moreItemCard {
                /* width: calc(50% - 10px); */
            }

            .moreItemCard img {
                height: 180px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="ad-image">
            <div class="displayed-image">
                <img id="displayedImage"
                    src="<?= htmlspecialchars($images[0] ?? 'images/placeholder/No_Image_AD.png'); ?>"
                    alt="Main Product Image">
            </div>
            <div class="thumbnail-images">
                <?php foreach ($images as $image): ?>
                    <img src="<?= htmlspecialchars($image); ?>" alt="Thumbnail"
                        onclick="updateMainImage('<?= htmlspecialchars($image); ?>')">
                <?php endforeach; ?>
            </div>
        </div>
        <div class="ad-details">
            <h1><?= htmlspecialchars($ad['title']); ?></h1>
            <p class="ad-description"><?= htmlspecialchars($ad['description']); ?></p>
            <p><strong>Price:</strong> <span class="price">Rs <?= htmlspecialchars($ad['price']); ?></span></p>
            <p><strong>Contact Number:</strong> <a
                    href="https://wa.me/+94<?= htmlspecialchars($ad['phone_number']); ?>"><?= htmlspecialchars($ad['phone_number']); ?></a>
            </p>
            <p><strong>Category:</strong> <?= htmlspecialchars($ad['category_name']); ?></p>
            <p><strong>Posted On:</strong> <?= htmlspecialchars(date('Y-m-d h:i A', strtotime($ad['created_at']))) ?>
            </p>
            <p><strong>District:</strong> <?= htmlspecialchars($ad['district']); ?></p>

            <?php if ($user_id): ?>
                <form method="post" action="wishlist.php">
                    <input type="hidden" name="ad_id" value="<?= $ad_id; ?>">
                    <button type="submit" class="wishlist-button">
                        <?= $is_wishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                    </button>
                </form>
            <?php else: ?>
                <p><a href="login.php">Log in</a> to add this ad to your wishlist.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- similar Products Section -->
    <div class="similarProducts">
        <h3>Similar Products</h3>
        <div class="moreItemsContainer">
            <?php while ($similar_ad = $similar_ads_result->fetch_assoc()): ?>
                <div class="moreItemCard" onclick="window.location.href='view_ad.php?ad_id=<?= $similar_ad['ad_id']; ?>'">
                    <img src="<?= htmlspecialchars($similar_ad['image'] ?? 'images/placeholder/No_Image_AD.png'); ?>"
                        alt="Product Image">
                    <h4><?= htmlspecialchars($similar_ad['title']); ?></h4>
                    <p>Rs <?= htmlspecialchars($similar_ad['price']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function updateMainImage(src) {
            document.getElementById('displayedImage').src = src;
        }
    </script>
    <script src='alertFunction.js'></script>

</body>

</html>

<?php
include 'footer.php';
?>