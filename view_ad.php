<?php
session_start();
include 'config.php';
include 'navbar.php';

if (isset($_GET['ad_id'])) {
    $ad_id = $_GET['ad_id'];

    // Fetch ad details from the database
    $ad_sql = "
        SELECT ads.*, categories.category_name, DATE_FORMAT(ads.created_at, '%M %d, %Y %h:%i %p') AS formatted_date 
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
        echo "Ad not found.";
        exit;
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
} else {
    echo "No ad selected.";
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$is_wishlisted = false;

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
        .wishlist-button {
            background-color: #ff4081;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($ad['title']); ?></h2>
    <div class="ad-details">
        <p><strong>Description:</strong> <?= htmlspecialchars($ad['description']); ?></p>
        <p><strong>Price:</strong> Rs <?= htmlspecialchars($ad['price']); ?></p>
        <p><strong>Contact Number:</strong> <?= htmlspecialchars($ad['phone_number']); ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($ad['category_name']); ?></p>
        <p><strong>Posted On:</strong> <?= htmlspecialchars($ad['formatted_date']); ?></p>
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
    
    <div class="ad-images">
        <?php foreach ($images as $image): ?>
            <img src="<?= htmlspecialchars($image); ?>" alt="Ad Image" onclick="openModal(this.src)">
        <?php endforeach; ?>
    </div>

    <h3>Similar Products</h3>
    <div class="more-items-container">
        <?php while ($similar_ad = $similar_ads_result->fetch_assoc()): ?>
            <div class="more-item-card" onclick="window.location.href='view_ad.php?ad_id=<?= $similar_ad['ad_id']; ?>'">
                <img src="<?= htmlspecialchars($similar_ad['image']); ?>" alt="Product Image">
                <h4><?= htmlspecialchars($similar_ad['title']); ?></h4>
                <p>Rs <?= htmlspecialchars($similar_ad['price']); ?></p>
                <p><strong>District:</strong> <?= htmlspecialchars($similar_ad['district']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    function openModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('imageModal').style.display = 'none';
    }
</script>

</body>
</html>
