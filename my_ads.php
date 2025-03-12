<?php
session_start();
ob_start();
include 'config.php';
include 'navbar.php';
include 'alertFunction.php';

if (!isset($_SESSION['user_id'])) {
    showAlert('Please log in to access your ads.', 'error', '#ff0000', 'login.php');
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
    * {
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        position: relative;
        min-height: 100vh;
        display: flex; 
        flex-direction: column;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("images/B1.jpg");
        background-size: cover;
        opacity: 0.2;
        z-index: -1;
    }

    .title {
        background-color: #dbffc7;
        text-align: center;
        padding: 10px 0;
        font-size: 2rem;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .container {
        width: 75%;
        margin: 0 auto;
        position: relative; 
        z-index: 1;
    }

    /* Card layout for ads */
    .ads-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        margin: 35px 0;
    }

    .ad-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-align: center;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        width: calc(25% - 20px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        padding: 15px;
    }

    .ad-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .ad-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        mix-blend-mode: multiply;
        border-radius: 8px;
    }

    .ad-card h4 {
        font-size: 16px;
        color: #333;
        margin: 10px 0 5px 0;
        font-weight: 600;
        text-transform: capitalize;
    }

    .ad-card p {
        line-height: 1.4;
        font-size: 14px;
        color: #555;
        text-align: left;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 10px 10px;
    }

    .ad-card .price {
        font-weight: 700;
        color: #b03052;
        margin: 10px 10px;
        text-align: left;
    }

    /* Button styles */
    .ad-buttons {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 10px;
    }

    .btn {
        text-decoration: none;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        background-color: #28a745;
        color: white;
        cursor: pointer;
        display: inline-block;
        font-size: 14px;
        transition: background-color 0.2s;
    }

    .btn:hover {
        background-color: #218838;
    }

    .btn-danger {
        background-color: red;
        cursor: pointer;
    }

    .btn-danger:hover {
        background-color: darkred;
    }

    .no-ads {
        text-align: center;
        font-size: 1.5rem;
        margin-top: 50px;
    }

    /* Mobile Devices */
    @media screen and (max-width: 480px) {
        .title {
            padding: 15px 5%;
            font-size: 1.5rem;
        }

        .container {
            width: 95%;
            padding: 10px;
        }

        .ads-container {
            gap: 15px;
            margin: 20px 0;
        }

        .ad-card {
            width: 100%;
            padding: 10px;
        }

        .ad-card img {
            height: 150px;
        }

        .ad-card h4 {
            font-size: 14px;
        }

        .ad-card p {
            font-size: 12px;
            -webkit-line-clamp: 3;
        }

        .ad-card .price {
            font-size: 12px;
        }

        .btn {
            padding: 8px 12px;
            font-size: 12px;
        }

        .no-ads {
            font-size: 1.2rem;
            margin-top: 30px;
        }
    }

    /* Tablets */
    @media screen and (min-width: 481px) and (max-width: 1200px) {
        .title {
            padding: 20px 8%;
            font-size: 1.8rem;
        }

        .container {
            width: 95%;
        }

        .ad-card {
            width: calc(50% - 10px);
        }

        .ad-card img {
            height: 180px;
        }

        .ad-card h4 {
            font-size: 15px;
        }

        .ad-card p {
            font-size: 13px;
        }

        .ad-card .price {
            font-size: 13px;
        }

        .btn {
            padding: 9px 13px;
            font-size: 13px;
        }
    }
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
                    <img src="<?= htmlspecialchars($first_image) ?>" alt="Ad Image">
                    <h4><?= htmlspecialchars($row['title']) ?></h4>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <p class="price">Price: Rs <?= number_format($row['price'], 2) ?></p>
                </div>
                <div class="ad-buttons" style="margin-top: 10px;">
                    <a href="view_ad.php?ad_id=<?= $row['ad_id'] ?>" class="btn">View Ad</a>
                    <a href="edit_ad.php?ad_id=<?= $row['ad_id'] ?>" class="btn">Edit Ad</a>
                    <button class="btn btn-danger" onclick="confirmDelete(<?= $row['ad_id'] ?>)">Delete Ad</button>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <p class="no-ads">You haven't placed any ads yet!</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

<?php
$stmt->close();
$conn->close();

include 'footer.php';
?>