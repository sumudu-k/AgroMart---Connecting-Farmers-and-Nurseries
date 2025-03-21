<?php
session_start();
include 'config.php';
include 'navbar.php';

$ads_per_page = 16;

$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $ads_per_page;

$total_ads_sql = "SELECT COUNT(*) AS total FROM ads";
$total_ads_result = $conn->query($total_ads_sql);
$total_ads = $total_ads_result->fetch_assoc()['total'];
$total_pages = ceil($total_ads / $ads_per_page);

$ads_sql = "
    SELECT ads.*, categories.category_name, 
        (SELECT image_path FROM ad_images WHERE ad_id = ads.ad_id LIMIT 1) AS image 
    FROM ads 
    JOIN categories ON ads.category_id = categories.category_id 
    ORDER BY RAND() 
    LIMIT $ads_per_page OFFSET $offset";
$result = $conn->query($ads_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Ads</title>
    <link rel="stylesheet" href="css/all_ads.css">
</head>

<body>

    <div class="container">
        <!-- ads container -->
        <div class="ads-container" id="ads-container">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($ad = $result->fetch_assoc()):
                    $description = $ad['description'];
                    if (strlen($description) > 200) {
                        $description = substr($description, 0, 200) . '...';
                    }
                ?>
                    <!-- ad card container -->
                    <div class="ad-card" onclick="window.location.href='view_ad.php?ad_id=<?= $ad['ad_id']; ?>'">
                        <img src="<?= htmlspecialchars($ad['image'] ?? 'images/placeholder/No_Image_AD.png'); ?>"
                            alt="Product Image">
                        <h4><?= htmlspecialchars($ad['title']); ?></h4>
                        <div class="ad-details">
                            <p class="description"><?= htmlspecialchars($description); ?></p>
                            <p class="price">Rs <?= htmlspecialchars($ad['price']); ?></p>
                            <p class="district"><?= htmlspecialchars($ad['district']); ?></p>
                            <p class="date"><?= htmlspecialchars(date('Y-m-d h:i A', strtotime($ad['created_at']))) ?></p>
                        </div>



                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No ads found.</p>
            <?php endif; ?>
        </div>

        <div class="pagination">
            <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                <a href="?page=<?= $page; ?>" class="<?= $page == $current_page ? 'active' : ''; ?>">
                    <?= $page; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>

</body>

</html>
<?php
include 'footer.php';
?>