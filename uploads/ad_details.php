<?php
// session_start();
// include 'config.php';

// // Check if ad_id is passed in the URL
// if (isset($_GET['ad_id'])) {
//     $ad_id = $_GET['ad_id'];

//     // Fetch ad details using the ad_id
//     $sql = "SELECT * FROM ads WHERE ad_id = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $ad_id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $ad = $result->fetch_assoc();
    
//     if (!$ad) {
//         // If ad is not found, redirect to home or display error
//         header("Location: home.php");
//         exit();
//     }
// } else {
//     // If no ad_id, redirect to home page
//     header("Location: home.php");
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<!-- <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $ad['title']; ?> - Product Details</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .ad-details {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        .ad-details img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        .ad-details h2 {
            font-size: 26px;
            color: #333;
        }

        .ad-details p {
            font-size: 18px;
            color: #666;
        }

        .ad-details .price {
            font-size: 22px;
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .ad-details .contact {
            font-size: 16px;
            color: #333;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="ad-details">
        <h2><?= $ad['title']; ?></h2>
        <!-- Display ad image if exists -->
        <?php if ($ad['images']): ?>
            <img src="<?= $ad['images']; ?>" alt="<?= $ad['title']; ?>">
        <?php endif; ?>

        <p><?= $ad['description']; ?></p>
        <p class="price">Price: $<?= $ad['price']; ?></p>

        <div class="contact">
            <h4>Contact Information:</h4>
            <p>Phone: <?= $ad['phone_number']; ?></p>
        </div>
    </div>
</body> -->
</html>
