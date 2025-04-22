<?php
session_start();
include 'config.php';
include 'navbar.php';

if (isset($_GET['ad_id'])) {
    $ad_id = $_GET['ad_id'];

    $ad_sql = "
    SELECT ads.*, categories.category_name, ads.created_at 
    FROM ads 
    JOIN categories ON ads.category_id = categories.category_id 
    WHERE ads.ad_id = ?";

    $stmt = $conn->prepare($ad_sql);
    $stmt->bind_param("i", $ad_id);
    $stmt->execute();
    $ad_result = $stmt->get_result();
    $ad = $ad_result->fetch_assoc();

    if ($ad) {
        $category_id = $ad['category_id'];
    } else {

        echo "<script>
        window.onload = function() {
            showAlert('Ad not found', 'error', '#ff0000');
        };
        setTimeout(function() {
        window.location.href = 'home.php';
        }, 2000);
    </script>";
    }

    // retrieve existing view count
    $sql_view_count = "SELECT * FROM ads WHERE ad_id=?";
    $stmt_view_count = $conn->prepare($sql_view_count);
    $stmt_view_count->bind_param('i', $ad_id);
    $stmt_view_count->execute();
    $result_view_count = $stmt_view_count->get_result();
    $views_count = $result_view_count->fetch_assoc();
    if (empty($views_count['view_count'])) {
        $views_count['view_count'] = 1;
    }
    echo $views_count['view_count'] . ' Views ';

    // save the view count
    $ad_views = $views_count['view_count'] + 1;
    $sql_view = "UPDATE ads SET view_count=? WHERE ad_id=?";
    $stmt_view = $conn->prepare($sql_view);
    $stmt_view->bind_param('ii', $ad_views, $ad_id);
    $stmt_view->execute();

    $img_sql = "SELECT image_path FROM ad_images WHERE ad_id = ?";
    $stmt_img = $conn->prepare($img_sql);
    $stmt_img->bind_param("i", $ad_id);
    $stmt_img->execute();
    $img_result = $stmt_img->get_result();
    $images = [];
    while ($image = $img_result->fetch_assoc()) {
        $images[] = $image['image_path'];
    }

    $similar_ads_sql = "
    SELECT ads.*, categories.category_name, 
        (SELECT image_path FROM ad_images WHERE ad_id = ads.ad_id LIMIT 1) AS image 
    FROM ads 
    JOIN categories ON ads.category_id = categories.category_id 
    WHERE ads.category_id = ? AND ads.ad_id != ? 
    ORDER BY RAND() 
    LIMIT 4";

    $similar_stmt = $conn->prepare($similar_ads_sql);
    $similar_stmt->bind_param("ii", $category_id, $ad_id);
    $similar_stmt->execute();
    $similar_ads_result = $similar_stmt->get_result();

    $user_id = $_SESSION['user_id'] ?? null;
    $is_wishlisted = false;
} else {
    echo "<script>
    window.onload = function() {
        showAlert('Ad Id is missing', 'error', '#ff0000');
    };
    setTimeout(function() {
    window.location.href = 'home.php';
}, 2000);

    
    </script>";
    exit;
}

if ($user_id) {
    $wishlist_check_sql = "SELECT * FROM wishlist WHERE user_id = ? AND ad_id = ?";
    $wishlist_stmt = $conn->prepare($wishlist_check_sql);
    $wishlist_stmt->bind_param("ii", $user_id, $ad_id);
    $wishlist_stmt->execute();
    $wishlist_result = $wishlist_stmt->get_result();
    $is_wishlisted = $wishlist_result->num_rows > 0;
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

// get seller's name
$seller_name = "SELECT * FROM users 
WHERE user_id=?";
$stmt_name = $conn->prepare($seller_name);
$stmt_name->bind_param('i', $seller_id);
$stmt_name->execute();
$result_name = $stmt_name->get_result();
$seller_name_get = $result_name->fetch_assoc();


// ad report

if (isset($_POST['report'])) {
    if (isset($_GET['ad_id'])) {
        $sql = "INSERT into ad_reports (ad_id,user_id) values (?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $ad_id, $user_id);
        if ($stmt->execute()) {
            echo "<script>
        window.onload = function() {
            showAlert('You reported ad successfully', 'success', '#008000');
        };
        </script>";
        }
    }
};

// check item already in cart
$sql_check = "SELECT * from cart where user_id=? and ad_id=?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param('ii', $user_id, $ad_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();



// add to cart
if (isset($_POST['add_to_cart'])) {
    $sql_insert = "INSERT into cart (user_id, ad_id) values (?,?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ii", $user_id, $ad_id);
    if ($stmt_insert->execute()) {
        echo "<script>
        window.onload = function() {
            showAlert('Product added to cart successfully', 'success', '#008000');
        };
        </script>";
    }
    header("Location:view_ad.php?ad_id=$ad_id");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title><?= htmlspecialchars($ad['title']); ?></title>
    <link rel="stylesheet" href="css/view_ad.css">
</head>

<body>
    <div class="container">
        <div class="ad-image">
            <div class="displayed-image">
                <img id="displayedImage"
                    src="<?= htmlspecialchars($images[0] ?? 'images/placeholder/No_Image_AD.png'); ?>"
                    alt="Main Product Image">
            </div>
            <div class="thumbnail-images">
                <?php foreach ($images as $image): ?>
                    <img src="<?= htmlspecialchars($image); ?>" alt="Thumbnail"
                        onclick="updateMainImage('<?= htmlspecialchars($image); ?>')">
                <?php endforeach; ?>
            </div>
        </div>
        <div class="ad-details">
            <h1><?= htmlspecialchars($ad['title']); ?></h1>
            <form action="view_ad.php?ad_id=<?= $ad['ad_id'] ?>" method="post">
                <button type="submit" name="report"
                    onclick="return confirm('Are you sure you want report the ad?')">Report Ad</button>
            </form>

            <h3>
                <?= 'Seller: ' .  $seller_name_get['username']; ?>
            </h3>
            <?php
            if ($verify == 1): ?>
                <span style=" background-color:green; padding:5px 10px;color:white;"> Verified Seller</span>
            <?php endif;
            ?>
            <p class="ad-description"><?= htmlspecialchars($ad['description']); ?></p>

            <p><strong>Price:</strong> <span class="price">Rs <?= htmlspecialchars($ad['price']); ?></span></p>

            <!-- quantity -->
            <?php if ($ad['quantity'] == 0): ?>
                <p style="color:white; background-color:red; padding:5px 10px;">Almost soldout</p>

            <?php elseif ($ad['quantity'] <= 10): ?>
                <p style="color:white; background-color:orange; padding:5px 10px;"> <?= $ad['quantity'] ?> Items
                    left</p>

            <?php else: ?>
                <p> <?= $ad['quantity'] ?> Items on stock</p>
            <?php endif; ?>

            <p><strong>Contact Number:</strong>
                <span class="contact-num">
                    <a href="https://wa.me/+94<?= htmlspecialchars($ad['phone_number']); ?>"><?= htmlspecialchars($ad['phone_number']); ?>
                        <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    </a>
                </span>
            </p>
            <p><strong>Category:</strong> <?= htmlspecialchars($ad['category_name']); ?></p>
            <p><strong>Posted On:</strong>
                <?= htmlspecialchars(date('Y-m-d h:i A', strtotime($ad['created_at']))) ?>
            </p>
            <p><strong>District:</strong> <?= htmlspecialchars($ad['district']); ?></p>

            <?php if ($user_id): ?>
                <form method="post" action="wishlist.php" class="wishlist-form">
                    <input type="hidden" name="ad_id" value="<?= $ad_id; ?>">
                    <button type="submit" class="wishlist-button">
                        <?= $is_wishlisted ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                    </button>
                </form>
            <?php else: ?>
                <p class="wishlist-login-btn"><a href="#"
                        onclick="showAlert('Please login to see Wishlist','error','#ff0000')">add to wishlsit</a>
                </p>
            <?php endif; ?>

            <?Php
            if ($result_check->num_rows == 1): ?>
                <a href="my_cart.php"><button>Go to Cart</button></a>

            <?php
            elseif (!$ad['quantity'] == 0): ?>
                <form method="post" action="view_ad.php?ad_id=<?= $ad_id ?>">
                    <input type="hidden" name="ad_id">
                    <button type="submit" name="add_to_cart"
                        style="color:white; background-color:blue; padding:5px 10px;">Add to Cart</button>
                </form>
            <?php else: ?>
                <button type="submit" onclick="showAlert('Product is out of stock','error','#ff0000')"
                    style="color:white; background-color:gray; padding:5px 10px;">Add to Cart</button>
            <?php endif; ?>

        </div>
    </div>

    <!-- similar Products Section -->
    <div class="similarProducts">
        <h3>Similar Products</h3>
        <div class="moreItemsContainer">
            <?php while ($similar_ad = $similar_ads_result->fetch_assoc()): ?>
                <div class="moreItemCard" onclick="window.location.href='view_ad.php?ad_id=<?= $similar_ad['ad_id']; ?>'">
                    <img src="<?= htmlspecialchars($similar_ad['image'] ?? 'images/placeholder/No_Image_AD.png'); ?>"
                        alt="Product Image">
                    <h4><?= htmlspecialchars($similar_ad['title']); ?></h4>
                    <h4><?= htmlspecialchars($similar_ad['district']); ?></h4>
                    <p>Rs <?= htmlspecialchars($similar_ad['price']); ?></p>
                    <?php if ($similar_ad['quantity'] == 0): ?>
                        <p style="color:white; background-color:red; padding:5px 10px;">Almost soldout</p>

                    <?php elseif ($similar_ad['quantity'] <= 10): ?>
                        <p style="color:white; background-color:orange; padding:5px 10px;"> <?= $similar_ad['quantity'] ?> Items
                            left</p>

                    <?php else: ?>
                        <p> <?= $similar_ad['quantity'] ?> Items on stock</p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function updateMainImage(src) {
            document.getElementById('displayedImage').src = src;
        }
    </script>
    <script src='alertFunction.js'></script>

</body>

</html>

<?php
include 'footer.php';
?>