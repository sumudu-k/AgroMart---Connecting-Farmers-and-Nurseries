<?php
session_start();
include 'config.php';
include 'navbar.php';

if (isset($_GET['ad_id'])) {
    $ad_id = $_GET['ad_id'];

    // Fetch ad details from the database
    $ad_sql = "SELECT * FROM ads WHERE ad_id = ?";
    $stmt = $conn->prepare($ad_sql);
    $stmt->bind_param("i", $ad_id);
    $stmt->execute();
    $ad_result = $stmt->get_result();
    $ad = $ad_result->fetch_assoc();

    if ($ad) {
        $category_id = $ad['category_id']; // Get the category of the current ad
    } else {
        echo "Ad not found.";
        exit;
    }

    // Fetch all images for this ad from the ad_images table
    $img_sql = "SELECT image_path FROM ad_images WHERE ad_id = ?";
    $stmt_img = $conn->prepare($img_sql);
    $stmt_img->bind_param("i", $ad_id);
    $stmt_img->execute();
    $img_result = $stmt_img->get_result();
    $images = [];
    while ($image = $img_result->fetch_assoc()) {
        $images[] = $image['image_path'];
    }
} else {
    echo "No ad selected.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ad['title']); ?></title>
    <style>
    #imageModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    #imageModal img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        border-radius: 8px;
    }

    #closeModal {
        position: absolute;
        top: 10px;
        right: 20px;
        color: white;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
    }

    body,
    html {
        overflow-x: hidden;
    }
    </style>
</head>

<body>

    <div class="container">
        <h2><?= htmlspecialchars($ad['title']); ?></h2>
        <div class="ad-details">
            <p><strong>Description:</strong> <?= htmlspecialchars($ad['description']); ?></p>
            <p><strong>Price:</strong> Rs <?= htmlspecialchars($ad['price']); ?></p>
            <p><strong>Contact Number:</strong> <?= htmlspecialchars($ad['phone_number']); ?></p>
            <p><strong>id:</strong> <?= htmlspecialchars($ad['ad_id']); ?></p>
            <p><strong>District:</strong> <?= htmlspecialchars($ad['district']); ?></p>
        </div>

        <!-- Display ad images -->
        <div class="ad-images">
            <?php foreach ($images as $image): ?>
            <img src="<?= htmlspecialchars($image); ?>" alt="Product Image"
                onclick="openImageModal('<?= htmlspecialchars($image); ?>')">
            <?php endforeach; ?>
        </div>

        <!--   full-size image -->
        <div id="imageModal">
            <span id="closeModal" onclick="closeImageModal()">&times;</span>
            <img id="modalImage" src="" alt="Full-size Image">
        </div>

        <!-- Display more items from the same category -->
        <h3>More items from this category</h3>
        <div class="more-items-container">
            <?php
            // Fetch up to 5 random other ads from the same category
            $more_sql = "SELECT * FROM ads WHERE category_id = ? AND ad_id != ? ORDER BY RAND() LIMIT 5";
            $stmt_more = $conn->prepare($more_sql);
            $stmt_more->bind_param("ii", $category_id, $ad_id);
            $stmt_more->execute();
            $more_result = $stmt_more->get_result();

            if ($more_result->num_rows > 0) {
                while ($more_ad = $more_result->fetch_assoc()) {
                    $more_ad_id = $more_ad['ad_id'];

                    // Fetch the first image of the ad from the ad_images table
                    $more_img_sql = "SELECT image_path FROM ad_images WHERE ad_id = ? LIMIT 1";
                    $stmt_more_img = $conn->prepare($more_img_sql);
                    $stmt_more_img->bind_param("i", $more_ad_id);
                    $stmt_more_img->execute();
                    $more_img_result = $stmt_more_img->get_result();
                    $more_image = $more_img_result->fetch_assoc();
            ?>
            <div class="more-item-card">
                <a href="view_ad.php?ad_id=<?= $more_ad_id; ?>">
                    <?php if ($more_image): ?>
                    <img src="<?= htmlspecialchars($more_image['image_path']); ?>"
                        alt="<?= htmlspecialchars($more_ad['title']); ?>">
                    <?php else: ?>
                    <img src="placeholder.png" alt="No Image Available">
                    <?php endif; ?>
                    <h4><?= htmlspecialchars($more_ad['title']); ?></h4>
                    <p>Price: $<?= htmlspecialchars($more_ad['price']); ?></p>
                </a>
            </div>
            <?php
                }
            } else {
                echo "<p>No other items in this category.</p>";
            }
            ?>
        </div>
    </div>

    <script>
    // Function to open the image modal
    function openImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModal').style.display = 'flex';
    }

    // Function to close the image modal
    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }
    </script>

</body>

</html>