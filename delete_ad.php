<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['ad_id'])) {
    $ad_id = $_GET['ad_id'];


    $sql = "DELETE FROM ads WHERE ad_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $ad_id, $_SESSION['user_id']);
    $stmt->execute();

    $img_sql = "DELETE FROM ad_images WHERE ad_id = ?";
    $stmt_img = $conn->prepare($img_sql);
    $stmt_img->bind_param("i", $ad_id);
    $stmt_img->execute();

    echo "<script>
    window.onload = function() {
        showAlert('Ad deleted successfully!', 'success', '#008000');
    };
    setTimeout(function() {
        window.location.href = 'my_ads.php';
    }, 2000);
    </script>";
} else {

    echo "<script>
    window.onload = function() {
        showAlert('Ad ID is missing', 'error', '#ff0000');
    };
    </script>";
}

include 'footer.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete ad</title>
</head>

<body>
    <script src='alertFunction.js'></script>
</body>

</html>