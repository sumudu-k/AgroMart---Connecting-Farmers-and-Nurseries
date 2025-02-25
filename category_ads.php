<?php
session_start();
include 'config.php';
include 'navbar.php'; 


$category_id = isset($_GET['category_id_qp']) ? (int) $_GET['category_id_qp'] : 0;

// Fetch the category name based on category_id
$category_name = 'Category'; 
if ($category_id > 0) {
    $query = "SELECT category_name FROM categories WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $stmt->bind_result($category_name);
    $stmt->fetch();
    $stmt->close();
}

// Use htmlspecialchars to avoid XSS
$category_name = htmlspecialchars($category_name);

    // Fetch all ads for the selected category
    $ad_sql = "SELECT ads.*, categories.category_name 
           FROM ads 
           JOIN categories ON ads.category_id = categories.category_id 
           WHERE ads.category_id = ?";
    $stmt = $conn->prepare($ad_sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
?>
    
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/category_ads.css">
        <title>Category Ads</title>
        <style>
          * {
            box-sizing: border-box;
          }

          body {
            font-family: Arial, sans-serif;
            margin: 0;
            overflow-x: hidden;
          }

           /* Title styles */
           .title {
              background-color: #dbffc7;
              text-align: left;
              text-transform: capitalize;
              padding: 20px 12.5%;
              font-size: 2.2rem;
              margin: 0;
           }


          /*container for ad cards */
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
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            width: calc(25% - 20px); 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            padding-bottom: 15px;
            cursor: pointer;
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

          .ad-card a {
            text-decoration: none; 
            color: inherit; 
            display: block; 
          }

          .ad-card a:hover {
            text-decoration: none;
          }

          .ad-card h4 {
            font-size: 16px;
            color: #333;
            margin: 10px 0 5px 0;
            font-weight: 600;
            text-transform: capitalize;
          }

          .ad-card .description {
            line-height: 1.4;
            font-size: 14px;
            color: #555;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 10px 10px;
          }

          .ad-card p {
            font-size: 14px;
            color: #555;
            font-weight: 500;
            margin: 8px 0;
          }

          
          .ad-details {
            margin-left: 20px;
            text-align: left;
            margin-top: 10px;
          }

          .ad-details p {
            font-weight: 700;
            color: black;
            margin: 15px 0;
          }

          .price {
            font-weight: 700;
            color: #b03052;
            margin: 0;
          }

          /* Responsive styles */

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
            }

            .ad-card img {
                height: 150px;
            }

            .ad-card h4 {
                font-size: 14px;
            }

            .ad-card .description {
                font-size: 12px;
                -webkit-line-clamp: 3;
            }

            .ad-details p {
                font-size: 12px;
                margin: 6px 0;
            }
        }

        /* Tablets*/
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

            .ad-card .description {
                font-size: 13px;
            }
        }

        
                      
        </style>
    </head>
    <body>

        <h2 class="title"> <?php echo $category_name; ?></h2>

        <div class="container">
            <div class="ads-container">
                <?php
                    if ($result->num_rows > 0) {
                        while ($ad = $result->fetch_assoc()) {
                            $ad_id = $ad['ad_id'];

                            // Fetch the first image of the ad from the ad_images table
                            $img_sql = "SELECT image_path FROM ad_images WHERE ad_id = ? LIMIT 1";
                            $stmt_img = $conn->prepare($img_sql);
                            $stmt_img->bind_param("i", $ad_id);
                            $stmt_img->execute();
                            $img_result = $stmt_img->get_result();
                            $image = $img_result->fetch_assoc(); 

                            // Limit the description to 100 characters
                            $description = substr(htmlspecialchars($ad['description']), 0, 100);
                            if (strlen($ad['description']) > 100) {
                                $description .= '...'; 
                            }
                            ?>
                            <div class="ad-card">
                                <a href="view_ad.php?ad_id=<?= $ad_id; ?>">
                                    <!-- Display the first image if available, else show a placeholder -->
                                    <?php if ($image): ?>
                                        <img src="<?= htmlspecialchars($image['image_path']); ?>" alt="<?= htmlspecialchars($ad['title']); ?>">
                                    <?php else: ?>
                                        <img src="placeholder.png" alt="No Image Available">
                                    <?php endif; ?>
                                    <h4><?= htmlspecialchars($ad['title']); ?></h4>
                                    <p  class="description"><?= $description; ?></p>
                                    <p>Price: <span class="price"> Rs <?= htmlspecialchars($ad['price']); ?></span></p>
                                    <p>District: <?= htmlspecialchars($ad['district']); ?></p> 
                                    <p>Posted on: <?= date('F j, Y', strtotime($ad['created_at'])); ?></p> 
                                </a>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>No ads found in this category.</p>";
                    }
                
                ?>
            </div>
        </div>
    </body>
</html>

