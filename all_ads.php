<?php
session_start();
include 'config.php';
include 'navbar.php';

// Set the number of ads per page
$ads_per_page = 16;

// Get the current page number from the URL, default to 1 if not set
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $ads_per_page;

// Fetch the total number of ads
$total_ads_sql = "SELECT COUNT(*) AS total FROM ads";
$total_ads_result = $conn->query($total_ads_sql);
$total_ads = $total_ads_result->fetch_assoc()['total'];
$total_pages = ceil($total_ads / $ads_per_page);

// Fetch ads for the current page
$ads_sql = "
    SELECT ads.*, categories.category_name, 
        (SELECT image_path FROM ad_images WHERE ad_id = ads.ad_id LIMIT 1) AS image 
    FROM ads 
    JOIN categories ON ads.category_id = categories.category_id 
    ORDER BY ads.created_at DESC 
    LIMIT $ads_per_page OFFSET $offset";
$result = $conn->query($ads_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Ads</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            overflow-x: hidden;
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

        .container {
            width: 75%;
            margin: 0 auto;
        }

        .ads-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin: 35px 0;
        }

        .ad-card {
            text-align: center;
            position: relative;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            width: calc(25% - 20px);
            min-height: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            padding-bottom: 15px;
            cursor: pointer;
            margin-bottom: 20px;
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
            border-radius: 8px 8px 0 0;
        }

        .ad-card h4 {
            font-size: 1.1rem;
            color: #333;
            margin: 10px 0 5px 0;
            font-weight: 600;
            height: 50px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: capitalize;
            padding: 0 10px;
        }

        .ad-details {
            padding: 5px 10px;
        }

        .ad-details .description {
            line-height: 1.4;
            font-size: 0.9rem;
            color: #000000;
            text-align: left;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ad-details .price {
            font-size: 1rem;
            color: #d84400;
            font-weight: bolder;
            margin-top: 10px;
            text-align: left;
            line-height: 2;
        }

        .ad-details .district {
            font-size: 0.9rem;
            color: #000000;
            text-align: left;
            padding: 0 0 10px 0;
            font-weight: bolder;
        }

        .date {
            position: absolute;
            bottom: 8px;
            font-size: 0.8rem;
            font-style: italic;
            color: #666666;
            text-align: left;
        }

        .pagination {
            text-align: center;
            margin: 20px 0;
            padding-bottom: 20px;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .pagination a.active {
            background-color: #333;
            color: #fff;
            border-color: #333;
        }

        .pagination a:hover {
            background-color: #555;
            color: #fff;
        }

        /* Mobile Devices */
        @media screen and (max-width: 480px) {
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
                min-height: 350px;
            }

            .ad-card img {
                height: 150px;
            }

            .ad-card h4 {
                font-size: 1rem;
                height: 40px;
            }

            .ad-details .description {
                font-size: 0.85rem;
                -webkit-line-clamp: 3;
            }

            .ad-details .price {
                font-size: 0.95rem;
            }

            .ad-details .district {
                font-size: 0.85rem;
            }

            .ad-details .date {
                font-size: 0.75rem;
            }

            .pagination a {
                padding: 6px 10px;
                font-size: 0.9rem;
            }
        }

        /* Tablets */
        @media screen and (min-width: 481px) and (max-width: 1200px) {
            .container {
                width: 95%;
            }

            .ad-card {
                width: calc(50% - 10px);
            }

            .ad-card img {
                height: 250px;
            }

            .ad-details .description {
                -webkit-line-clamp: 3;
            }

            .ad-card h4 {
                font-size: 1.05rem;
                height: 40px;
            }

            .pagination a {
                padding: 7px 11px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="ads-container" id="ads-container">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($ad = $result->fetch_assoc()):
                    $description = $ad['description'];
                    if (strlen($description) > 200) {
                        $description = substr($description, 0, 200) . '...';
                    }
                ?>
                    <div class="ad-card" onclick="window.location.href='view_ad.php?ad_id=<?= $ad['ad_id']; ?>'">
                        <img src="<?= htmlspecialchars($ad['image'] ?? 'images/placeholder/no-image.jpg'); ?>"
                            alt="Product Image">
                        <h4><?= htmlspecialchars($ad['title']); ?></h4>
                        <div class="ad-details">
                            <p class="description"><?= htmlspecialchars($description); ?></p>
                            <p class="price">Rs <?= htmlspecialchars($ad['price']); ?></p>
                            <p class="district"><?= htmlspecialchars($ad['district']); ?></p>
                            <p class="date"><?= date('F j, Y h:i A', strtotime($ad['created_at'])); ?></p>
                        </div>



                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No ads found.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination Links Below the Ads Container -->
        <div class="pagination">
            <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                <a href="?page=<?= $page; ?>" class="<?= $page == $current_page ? 'active' : ''; ?>">
                    <?= $page; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>

</body>

</html>
<?php
include 'footer.php';
?>