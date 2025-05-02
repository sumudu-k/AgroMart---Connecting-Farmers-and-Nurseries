<?php
session_start();
ob_start();
include 'config.php';
include 'navbar.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>
    window.onload = function() {
        showAlert('Please login to post an ad', 'error', '#ff0000');
    };
</script>";
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// check boosted ads older than 1 year and set boosted to 0
$check_boosted = $conn->query("UPDATE ads 
SET boosted = 0 ,
 boosted_at=null
WHERE boosted = 1 
AND boosted_at < NOW() - INTERVAL 1 YEAR;");

// get all ads posted by the user
$sql = "SELECT ads.*, GROUP_CONCAT(ad_images.image_path) AS images 
        FROM ads 
        LEFT JOIN ad_images ON ads.ad_id = ad_images.ad_id 
        WHERE ads.user_id = ? 
        GROUP BY ads.ad_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// get number of ads posted by the user
$sql_count = "SELECT COUNT(*) as total_ads FROM ads WHERE user_id = ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $user_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();

// get number of boosted ads posted by the user
$sql_boosted_count = "SELECT COUNT(*) as total_boosted_ads FROM ads WHERE user_id = ? AND boosted = 1";
$stmt_boosted_count = $conn->prepare($sql_boosted_count);
$stmt_boosted_count->bind_param("i", $user_id);
$stmt_boosted_count->execute();
$result_boosted_count = $stmt_boosted_count->get_result();
$row_boosted_count = $result_boosted_count->fetch_assoc();

// get number of ads where quantity is 0
$sql_zero_count = "SELECT COUNT(*) as total_zero_ads FROM ads WHERE user_id = ? AND quantity = 0";
$stmt_zero_count = $conn->prepare($sql_zero_count);
$stmt_zero_count->bind_param("i", $user_id);
$stmt_zero_count->execute();
$result_zero_count = $stmt_zero_count->get_result();
$row_zero_count = $result_zero_count->fetch_assoc();

// get number of ads where quantity is less than 10
$sql_less_than_10_count = "SELECT COUNT(*) as total_less_than_10_ads FROM ads WHERE user_id = ? AND quantity < 10 AND quantity > 0";
$stmt_less_than_10_count = $conn->prepare($sql_less_than_10_count);
$stmt_less_than_10_count->bind_param("i", $user_id);
$stmt_less_than_10_count->execute();
$result_less_than_10_count = $stmt_less_than_10_count->get_result();
$row_less_than_10_count = $result_less_than_10_count->fetch_assoc();

// get total number of views
$sql_total_views = "SELECT SUM(view_count) as total_views FROM ads WHERE user_id = ?";
$stmt_total_views = $conn->prepare($sql_total_views);
$stmt_total_views->bind_param("i", $user_id);
$stmt_total_views->execute();
$result_total_views = $stmt_total_views->get_result();
$row_total_views = $result_total_views->fetch_assoc();


// unboost
if (isset($_POST['unboost'])) {
    $ad_id = $_POST['ad_id']; // Get the ad ID from the form
    $sql_boost = "UPDATE ads SET boosted='0', boosted_at=null WHERE ad_id=?";
    $stmt_boost = $conn->prepare($sql_boost);
    $stmt_boost->bind_param('i', $ad_id);
    if ($stmt_boost->execute()) {
        echo "<script>alert('Ad unboosted !');</script>";
    } else {
        echo "<script>alert('Failed to unboost ad.');</script>";
    }
    header('Location:my_ads.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Ads</title>
    <link rel="stylesheet" href="css/my_ads.css">
    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>

</head>

<body>
    <h2 class="title">My Ads</h2>

    <div class="container">
        <div class="ads-count">
            <p>Total Ads: <?= $row_count['total_ads'] ?></p>
            <p>Total Boosted Ads: <?= $row_boosted_count['total_boosted_ads'] ?></p>
            <p>Total Sold Out Ads: <?= $row_zero_count['total_zero_ads'] ?></p>
            <p>Total Ads with Less than 10 Items: <?= $row_less_than_10_count['total_less_than_10_ads'] ?></p>
            <p>Total Ad Views: <?= $row_total_views['total_views'] ?></p>

        </div>
        <div class="container">
            <div class="ads-container">
                <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()):
                        $images = explode(',', $row['images']);
                        $first_image = !empty($images[0]) ? $images[0] : 'default_image.jpg';
                    ?>
                <div class="ad-card">
                    <div class="details">
                        <p><?= htmlspecialchars($row['ad_id']) ?></p>
                        <img src="<?= htmlspecialchars($first_image) ?>" alt="Ad Image">
                        <h4><?= htmlspecialchars($row['title']) ?></h4>
                        <p><?= htmlspecialchars($row['description']) ?></p>
                        <p class="price">Price: Rs <?= number_format($row['price'], 2) ?></p>
                        <p>Views: <?= $row['view_count'] ?> </p>

                        <?php if ($row['quantity'] == 0): ?>
                        <p style="color:white; background-color:red; padding:5px 10px;">Almost soldout</p>

                        <?php elseif ($row['quantity'] <= 10): ?>
                        <p style="color:white; background-color:orange; padding:5px 10px;"> <?= $row['quantity'] ?>
                            Items
                            left</p>

                        <?php else: ?>
                        <p> <?= $row['quantity'] ?> Items on stock</p>
                        <?php endif; ?>

                        <P>Posted on:<?= htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))); ?></P>

                        <?php if ($row['boosted'] == 1): ?>
                        <p style="color:white; background-color:blue; padding:5px 10px;">Boosted</p>
                        <P>Boost will stop on <?= date('Y-m-d h:i A', strtotime($row['boosted_at'] . '+ 1 year')) ?></P>
                        <?php endif; ?>
                    </div>
                    <div class="ad-buttons" style="margin-top: 10px;">
                        <a href="view_ad.php?ad_id=<?= $row['ad_id'] ?>" class="btn">View Ad</a>
                        <a href="edit_ad.php?ad_id=<?= $row['ad_id'] ?>" class="btn">Edit Ad</a>
                        <button class="btn btn-danger" onclick="confirmAlertAd(<?= $row['ad_id'] ?>)">Delete Ad</button>

                        <?php if ($row['boosted'] == 1): ?>
                        <form action="my_ads.php" method="post"
                            onsubmit="return confirm('Are you sure you want to stop boost this ad?');">
                            <input type="hidden" name="ad_id" value="<?= $row['ad_id'] ?>">
                            <button class="btn btn-danger" type="submit" name="unboost"
                                onclick="setTimeout(() => alert('Ad unboosted!'), 500);">Stop Boost Ad</button>
                        </form>
                        <?php else: ?>

                        <button class="btn btn-danger"
                            onclick="boostAdWithPayment('<?= $row['ad_id'] ?>', '<?= htmlspecialchars($row['title']) ?>', '100')">Boost
                            Ad</button>

                        <?php endif; ?>




                    </div>
                </div>
                <?php endwhile; ?>
                <?php else: ?>
                <p class="no-ads">You haven't placed any ads yet!</p>
                <?php endif; ?>
            </div>
        </div>
        <script src='alertFunction.js'></script>
        <script>
        payhere.onCompleted = function(orderId) {
            alert("Payment completed successfully! Ad will be boosted.");
            location.reload();
        };

        payhere.onDismissed = function() {
            alert("Payment dismissed.");
        };

        payhere.onError = function(error) {
            alert("Payment Error: " + error);
        };

        function boostAdWithPayment(ad_id, title, price) {
            // Get hash for payment
            fetch('generate_hash.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        order_id: ad_id,
                        amount: price,
                        currency: 'LKR'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const payment = {
                        "sandbox": true,
                        "merchant_id": "1230058",
                        "return_url": "https://55a0-175-157-231-239.ngrok-free.app/AgroMart/payment/success.php",
                        "cancel_url": "https://55a0-175-157-231-239.ngrok-free.app/AgroMart/payment/failed.php",
                        "notify_url": "https://55a0-175-157-231-239.ngrok-free.app/AgroMart/notify_url.php",
                        "order_id": ad_id,
                        "items": title,
                        "amount": price,
                        "currency": "LKR",
                        "hash": data.hash,
                        "first_name": "<?php echo $_SESSION['username']; ?>",
                        "last_name": "",
                        "email": "<?php echo $_SESSION['email']; ?>",
                        "phone": "<?php echo $_SESSION['contact_number']; ?>",
                        "address": "<?php echo $_SESSION['address']; ?>",
                        "city": "",
                        "country": "Sri Lanka",
                        "custom_1": <?= $user_id ?>
                    };
                    payhere.startPayment(payment);
                });
        }
        </script>

</body>

</html>

<?php
$stmt->close();
$conn->close();

include 'footer.php';
?>