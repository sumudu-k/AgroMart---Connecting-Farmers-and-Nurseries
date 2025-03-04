<?php
session_start();
include 'config.php';
include 'alertFunction.php';

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

    showAlert('Ad deleted successfully!', 'success', '#008000', 'my_ads.php');
    exit();
} else {
    showAlert('Ad ID is missing', 'error', '#ff0000', 'delete_ad.php');
}

include 'footer.php';