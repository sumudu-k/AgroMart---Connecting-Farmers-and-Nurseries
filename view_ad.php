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

// get the seller id for verify badge
$seller_id = $ad['user_id'];

// check seller is verified
$verify = 0;
$check_seller = "SELECT * FROM verification_requests WHERE user_id=?  AND status ='approved'";
$stmt_check = $conn->prepare($check_seller);
$stmt_check->bind_param('i', $seller_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows > 0) {
    $verify = 1;
} else {
    $verify = 0;
}

// get seller's name
$seller_name = "SELECT * FROM users 
WHERE user_id=?";
$stmt_name = $conn->prepare($seller_name);
$stmt_name->bind_param('i', $seller_id);
$stmt_name->execute();
$result_name = $stmt_name->get_result();
$seller_name_get = $result_name->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title><?= htmlspecialchars($ad['title']); ?></title>
    <link rel="stylesheet" href="css/view_ad.css">
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

            <h3>
                <?= 'Seller: ' .  $seller_name_get['username']; ?>
            </h3>
            <?php
            if ($verify == 1): ?>
            <span style="background-color:green; padding:5px 10px;color:white;"> Verified Seller</span>
            <?php endif;
            ?>
            <p class="ad-description"><?= htmlspecialchars($ad['description']); ?></p>

            <p><strong>Price:</strong> <span class="price">Rs <?= htmlspecialchars($ad['price']); ?></span></p>
            <p><strong>Contact Number:</strong>
                <span class="contact-num">
                    <a href="https://wa.me/+94<?= htmlspecialchars($ad['phone_number']); ?>"><?= htmlspecialchars($ad['phone_number']); ?>
                        <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    </a>
                </span>
            </p>
            <p><strong>Category:</strong> <?= htmlspecialchars($ad['category_name']); ?></p>
            <p><strong>Posted On:</strong> <?= htmlspecialchars(date('Y-m-d h:i A', strtotime($ad['created_at']))) ?>
            </p>
            <p><strong>District:</strong> <?= htmlspecialchars($ad['district']); ?></p>

            <?php if ($user_id): ?>
            <form method="post" action="wishlist.php" class="wishlist-form">
                <input type="hidden" name="ad_id" value="<?= $ad_id; ?>">
                <button type="submit" class="wishlist-button">
                    <?= $is_wishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                </button>
            </form>
            <?php else: ?>
            <p class="wishlist-login-btn"><a href="#"
                    onclick="showAlert('Please login to see Wishlist','error','#ff0000')">add to wishlsit</a></p>
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