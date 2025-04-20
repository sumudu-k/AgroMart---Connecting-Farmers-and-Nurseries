<?php
session_start();
ob_start();
include 'config.php';
include 'navbar.php';

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


$check_boosted = $conn->query("UPDATE ads 
SET boosted = 0 ,
 boosted_at=null
WHERE boosted = 1 
AND boosted_at < NOW() - INTERVAL 5 MINUTE;");


$sql = "SELECT ads.*, GROUP_CONCAT(ad_images.image_path) AS images 
        FROM ads 
        LEFT JOIN ad_images ON ads.ad_id = ad_images.ad_id 
        WHERE ads.user_id = ? 
        GROUP BY ads.ad_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// boost
// if (isset($_POST['boost'])) {
//     $ad_id = $_POST['ad_id']; // Get the ad ID from the form
//     $sql_boost = "UPDATE ads SET boosted='1', boosted_at=now() WHERE ad_id=?";
//     $stmt_boost = $conn->prepare($sql_boost);
//     $stmt_boost->bind_param('i', $ad_id);
//     if ($stmt_boost->execute()) {
//         echo "<script>alert('Ad boosted successfully!');</script>";
//     } else {
//         echo "<script>alert('Failed to boost ad.');</script>";
//     }
//     header('Location:my_ads.php');
// }

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

                            <?php if ($row['quantity'] == 0): ?>
                                <p style="color:white; background-color:red; padding:5px 10px;">Almost soldout</p>

                            <?php elseif ($row['quantity'] <= 10): ?>
                                <p style="color:white; background-color:orange; padding:5px 10px;"> <?= $row['quantity'] ?> Items
                                    left</p>

                            <?php else: ?>
                                <p> <?= $row['quantity'] ?> Items on stock</p>
                            <?php endif; ?>

                            <P>Posted on:<?= htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))); ?></P>

                            <?php if ($row['boosted'] == 1): ?>
                                <p style="color:white; background-color:green; padding:5px 10px;">Boosted</p>
                                <P>Boost will stop on <?= date('Y-m-d h:i A', strtotime($row['boosted_at'] . '+ 5 minutes')) ?></P>
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