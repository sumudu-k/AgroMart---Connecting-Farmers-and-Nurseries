<?php
include 'config.php';
include 'navbar.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
?>

<h2 style="padding: 20px;">Search Results for: <em><?= htmlspecialchars($q) ?></em></h2>

<div style="padding: 0 20px;">
    <?php
    if (!empty($q)) {
        // Query to get matching ads
        $stmt = $conn->prepare("SELECT ad_id, title, description FROM ads WHERE title LIKE CONCAT('%', ?, '%') OR description LIKE CONCAT('%', ?, '%') LIMIT 10");
        $stmt->bind_param('ss', $q, $q);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;'>";
            while ($ad = $result->fetch_assoc()) {
                $ad_id = $ad['ad_id'];
                $title = htmlspecialchars($ad['title']);
                $description = htmlspecialchars(substr($ad['description'], 0, 80)) . "...";

                // Separate query to get the first image using image_path
                $img_stmt = $conn->prepare("SELECT image_path FROM ad_images WHERE ad_id = ? LIMIT 1");
                $img_stmt->bind_param("i", $ad_id);
                $img_stmt->execute();
                $img_result = $img_stmt->get_result();
                $img_row = $img_result->fetch_assoc();
                $imagePath = $img_row ?  $img_row['image_path'] : 'assets/default.jpg'; // fallback image path

                echo "<div style='border: 1px solid #ddd; border-radius: 10px; padding: 10px;'>";
                echo "<a href='view_ad.php?ad_id=$ad_id' style='text-decoration: none; color: inherit;'>";
                echo "<img src='$imagePath' alt='Ad Image' style='width:100%; height:180px; object-fit:cover; border-radius: 8px;'><br>";
                echo "<h3 style='margin: 10px 0 5px;'>$title</h3>";
                echo "<p style='color: #555; font-size: 14px;'>$description</p>";
                echo "</a>";
                echo "</div>";

                $img_stmt->close();
            }
            echo "</div>";
        } else {
            echo "<p>No results found.</p>";
        }

        $stmt->close();
    } else {
        echo "<p>No search query provided.</p>";
    }
    ?>
</div>