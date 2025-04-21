<?php
session_start();
include 'config.php';
include 'navbar.php';

$ads_per_page = 50;

$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $ads_per_page;

$total_ads_sql = "SELECT COUNT(*) AS total FROM ads";
$total_ads_result = $conn->query($total_ads_sql);
$total_ads = $total_ads_result->fetch_assoc()['total'];
$total_pages = ceil($total_ads / $ads_per_page);

$check_boosted = $conn->query("UPDATE ads 
SET boosted = 0 ,
 boosted_at=null
WHERE boosted = 1 
AND boosted_at < NOW() - INTERVAL 5 MINUTE;");


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
                            <?php
                            if ($verify == 1): ?>
                                <span style=" background-color:green; padding:5px 10px;color:white;"> Verified Seller</span>
                            <?php endif; ?>

                            <?php if ($ad['boosted'] == 1): ?>
                                <p style="color:white; background-color:green; padding:5px 10px;">Boosted</p>
                            <?php endif; ?>

                            <!-- quantity -->
                            <?php if ($ad['quantity'] == 0): ?>
                                <p style="color:white; background-color:red; padding:5px 10px;">Almost soldout</p>

                            <?php elseif ($ad['quantity'] <= 10): ?>
                                <p style="color:white; background-color:orange; padding:5px 10px;"> <?= $ad['quantity'] ?> Items
                                    left</p>

                            <?php else: ?>
                                <p> <?= $ad['quantity'] ?> Items on stock</p>
                            <?php endif; ?>

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