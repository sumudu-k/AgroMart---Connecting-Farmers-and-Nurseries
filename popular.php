<?php
include 'config.php';
// $ad_id = $_GET['ad_id'];

// if (isset($_GET['ad_id'])) {
//     echo 'parameter passed';

// $ad_sql = "SELECT * from ads order by view_count desc";
// $result = $conn->query($ad_sql);

// $img_sql="SELECT * from ad_images";

$sql = "SELECT ads.*,
categories.category_name,
(select image_path from ad_images where ad_id=ads.ad_id limit 1) as image
from ads
join categories on ads.category_id= categories.category_id
order by ads.view_count desc limit 2 ";

$result = $conn->query($sql);
// } else {
//     header('Location:404.php');
// }


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h2>Popular page</h2>
    <?php
    while ($row = $result->fetch_assoc()) : ?>
    <a href="view_ad.php?ad_id=<?= $row['ad_id'] ?>">
        <div>
            <h3><?= htmlspecialchars($row['title']); ?></h3>
            <h3><?= htmlspecialchars($row['view_count']); ?></h3>
            <img src="<?= htmlspecialchars($row['image']); ?>" alt="">
            <p><?= htmlspecialchars($row['description']); ?></p>
            <p><?= htmlspecialchars($row['created_at']); ?></p>
            <p><?= htmlspecialchars($row['district']); ?></p>
            <hr>
        </div>
    </a>
    <?php endwhile; ?>

</body>

</html>