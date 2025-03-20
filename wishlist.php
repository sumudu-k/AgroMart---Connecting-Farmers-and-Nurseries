<?php
session_start();
ob_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    //showAlert('Please log in to access your wishlist.', 'error', '#ff0000', 'login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// handle add/remove wishlist actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ad_id'])) {
    $ad_id = $_POST['ad_id'];

    // check if the ad is already in the wishlist
    $check_sql = "SELECT * FROM wishlist WHERE user_id = ? AND ad_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $ad_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // remove from wishlist
        $delete_sql = "DELETE FROM wishlist WHERE user_id = ? AND ad_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("ii", $user_id, $ad_id);
        $stmt->execute();
        //showAlert('Removed from wishlist!', 'success', '#008000', 'wishlist.php');
    } else {
        //add to wishlist
        $insert_sql = "INSERT INTO wishlist (user_id, ad_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ii", $user_id, $ad_id);
        $stmt->execute();
        //showAlert('Added to wishlist!', 'success', '#008000', 'wishlist.php');
    }
}

//fetch wishlist items
$wishlist_sql = "SELECT ads.*, categories.category_name, 
    (SELECT image_path FROM ad_images WHERE ad_id = ads.ad_id LIMIT 1) AS image 
    FROM wishlist 
    JOIN ads ON wishlist.ad_id = ads.ad_id 
    JOIN categories ON ads.category_id = categories.category_id 
    WHERE wishlist.user_id = ?";
$stmt = $conn->prepare($wishlist_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'navbar.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
    <link rel="stylesheet" href="css/wishlist.css">
</head>

<body>
    <div class="main-content">
        <h1>Your Wishlist</h1>
        <div class="container">
            <div class="wishlist-items">
                <?php if ($wishlist_result->num_rows > 0): ?>
                <?php while ($item = $wishlist_result->fetch_assoc()): ?>
                <div class="wishlist-item">
                <a href="view_ad.php?ad_id=<?= $item['ad_id']; ?>" class="wishlist-link">
                    <img src="<?= htmlspecialchars($item['image'] ?? 'default.jpg'); ?>" alt="Product Image">
                    <h3><?= htmlspecialchars($item['title']); ?></h3>
                    <p>Category: <?= htmlspecialchars($item['category_name']); ?></p>
                    <p>Price: Rs <?= htmlspecialchars($item['price']); ?></p>
                </a>
                    <form method="post">
                        <input type="hidden" name="ad_id" value="<?= $item['ad_id']; ?>">
                        <button type="submit">Remove from Wishlist</button>
                    </form>
                </div>
              
                <?php endwhile; ?>
                <?php else: ?>
                <p class="empty-message">Your wishlist is empty.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>