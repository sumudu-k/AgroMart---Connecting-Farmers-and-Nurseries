<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT ads.*, GROUP_CONCAT(ad_images.image_path) AS images 
        FROM ads 
        LEFT JOIN ad_images ON ads.ad_id = ad_images.ad_id 
        WHERE ads.user_id = ? 
        GROUP BY ads.ad_id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Ads</title>
    <style>
    </style>

    <script>
    function confirmDelete(adId) {
        if (confirm("Are you sure you want to delete this ad?")) {
            window.location.href = "delete_ad.php?ad_id=" + adId;
        }
    }
    </script>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <h2 style="text-align: center;">My Ads</h2>

    <div class="card-container">
        <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $images = explode(',', $row['images']);
            $first_image = !empty($images[0]) ? $images[0] : 'default_image.jpg'; 
            ?>
        <div class="card">
            <img src="<?= $first_image ?>" alt="Ad Image">
            <h4><?= $row['title'] ?></h4>
            <p><?= $row['description'] ?></p>
            <p>Price: $<?= number_format($row['price'], 2) ?></p>
            <a href="view_ad.php?ad_id=<?= $row['ad_id'] ?>" class="btn">View Ad</a>
            <br><br>
            <a href="edit_ad.php?ad_id=<?= $row['ad_id'] ?>" class="btn">Edit Ad</a>
            <button class="btn btn-danger" onclick="confirmDelete(<?= $row['ad_id'] ?>)">Delete Ad</button>
        </div>
        <?php
        }
    } else {
        echo "<p class='no-ads'>You haven't placed any ads yet!</p>";
    }
    ?>
    </div>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>