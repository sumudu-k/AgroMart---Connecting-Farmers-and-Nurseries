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
    <style>
    * {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
    }

    body {
        font-family: "Poppins", Arial, sans-serif;
        background-color: #f4f4f4;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        position: relative;
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

    .main-content {
        flex: 1;
    }

    h1 {
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
        padding: 20px;
    }

    .wishlist-items {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;

    }

    .wishlist-item {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        width: calc(25% - 15px);
        min-height: 400px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }

    .wishlist-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .wishlist-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        mix-blend-mode: multiply;
    }

    .wishlist-item h3 {
        font-size: 1.2rem;
        color: #333;
        margin: 10px 5px 5px;
        font-weight: 600;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        text-transform: capitalize;
    }

    .wishlist-item p {
        font-size: 1rem;
        color: #555;
        margin: 10px 0 5px;
    }

    .wishlist-item p:nth-child(4) {
        color: #b03052;
        font-weight: 700;
    }

    .wishlist-item form {
        position: absolute;
        bottom: 20px;
        right: 50%;
        transform: translateX(50%);
    }

    .wishlist-item button {
        background-color: #28a745;
        width: 170px;
        padding: 5px 5px;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 0.9rem;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .wishlist-item button:hover {
        background-color: #dc3545;
    }

    .empty-message {
        text-align: center;
        font-size: 1.5rem;
        color: #666;
        margin-top: 50px;
    }

    /* mobile Devices */
    @media screen and (max-width: 480px) {
        h1 {
            font-size: 1.5rem;
            padding: 15px 5%;
        }

        .container {
            width: 95%;
            padding: 10px;
        }

        .wishlist-items {
            gap: 15px;
        }

        .wishlist-item {
            width: 100%;
            min-height: 350px;
        }

        .wishlist-item img {
            height: 150px;
        }

        .wishlist-item h3 {
            font-size: 1rem;
        }

        .wishlist-item p {
            font-size: 0.85rem;
        }

        .wishlist-item form {
            bottom: 30px;
        }

        .wishlist-item button {
            font-size: 0.85rem;
            padding: 6px 12px;
        }

        .empty-message {
            font-size: 1.2rem;
            margin-top: 30px;
        }
    }

    /* tablets */
    @media screen and (min-width: 481px) and (max-width: 1200px) {
        h1 {
            font-size: 1.8rem;
            padding: 20px 8%;
        }

        .container {
            width: 95%;
        }

        .wishlist-item {
            width: calc(50% - 10px);
            min-height: 370px;
        }

        .wishlist-item img {
            height: 180px;
        }

        .wishlist-item h3 {
            font-size: 1.1rem;
        }

        .wishlist-item p {
            font-size: 0.9rem;
        }

        .wishlist-item form {
            bottom: 30px;
        }
    }
    </style>
</head>

<body>
    <div class="main-content">
        <h1>Your Wishlist</h1>
        <div class="container">
            <div class="wishlist-items">
                <?php if ($wishlist_result->num_rows > 0): ?>
                <?php while ($item = $wishlist_result->fetch_assoc()): ?>
                <div class="wishlist-item">
                    <img src="<?= htmlspecialchars($item['image'] ?? 'default.jpg'); ?>" alt="Product Image">
                    <h3><?= htmlspecialchars($item['title']); ?></h3>
                    <p>Category: <?= htmlspecialchars($item['category_name']); ?></p>
                    <p>Price: Rs <?= htmlspecialchars($item['price']); ?></p>
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