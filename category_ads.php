<?php
session_start();
include 'config.php';
include 'navbar.php';


$category_id = $_GET['category_id_qp'];
$category_name = 'Category';

$check_boosted = $conn->query("UPDATE ads 
SET boosted = 0 ,
 boosted_at=null
WHERE boosted = 1 
AND boosted_at < NOW() - INTERVAL 5 MINUTE;");

if (isset($_GET['category_id_qp'])) {
    $query = "SELECT category_name FROM categories WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $category_name = $row['category_name'];
    };


    $category_name = htmlspecialchars($category_name);

    $ad_sql = "SELECT ads.*, categories.category_name 
           FROM ads 
           JOIN categories ON ads.category_id = categories.category_id 
           WHERE ads.category_id = ?";
    $stmt = $conn->prepare($ad_sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/category_ads.css">
    <title>Category Ads</title>
    <link rel="stylesheet" href="css/category_ads.css">
</head>

<body>
    <div class="main-content">
        <h2 class="title"> <?php echo $category_name; ?></h2>

        <!-- category ads container -->
        <div class="container">
            <div class="ads-container">
                <?php
                if ($result->num_rows > 0) {
                    while ($ad = $result->fetch_assoc()) {
                        $ad_id = $ad['ad_id'];

                        $img_sql = "SELECT image_path FROM ad_images WHERE ad_id = ? LIMIT 1";
                        $stmt_img = $conn->prepare($img_sql);
                        $stmt_img->bind_param("i", $ad_id);
                        $stmt_img->execute();
                        $img_result = $stmt_img->get_result();
                        $image = $img_result->fetch_assoc();

                        $description = substr(htmlspecialchars($ad['description']), 0, 100);
                        if (strlen($ad['description']) > 100) {
                            $description .= '...';
                        }
                ?>
                <!-- ad card container -->
                <div class="ad-card">
                    <a href="view_ad.php?ad_id=<?= $ad_id; ?>">
                        <?php if ($image): ?>
                        <img src="<?= htmlspecialchars($image['image_path']); ?>"
                            alt="<?= htmlspecialchars($ad['title']); ?>">
                        <?php else: ?>
                        <img src="images/placeholder/No_Image_AD.png" alt="No Image Available">
                        <?php endif; ?>
                        <h4><?= htmlspecialchars($ad['title']); ?></h4>

                        <?php if ($ad['boosted'] == 1): ?>
                        <p style="color:white; background-color:green; padding:5px 10px;">Boosted</p>
                        <?php endif; ?>
                        <p class="description"><?= $description; ?></p>
                        <p>Price: <span class="price"> Rs <?= htmlspecialchars($ad['price']); ?></span></p>

                        <?php if ($ad['quantity'] == 0): ?>
                        <p style="color:white; background-color:red; padding:5px 10px;">Almost soldout</p>

                        <?php elseif ($ad['quantity'] <= 10): ?>
                        <p style="color:white; background-color:orange; padding:5px 10px;"> <?= $ad['quantity'] ?>
                            Items
                            left</p>

                        <?php else: ?>
                        <p> <?= $ad['quantity'] ?> Items on stock</p>
                        <?php endif; ?>
                        <p>District: <?= htmlspecialchars($ad['district']); ?></p>
                        <p>Posted on: <?= date('F j, Y', strtotime($ad['created_at'])); ?></p>
                    </a>
                </div>
                <?php
                    }
                } else {
                    echo "<h3>Sorry! No ads found in this category.</h3>";
                }
                ?>
            </div>
        </div>

    </div>
</body>

</html>

<?php
include 'footer.php';
?>