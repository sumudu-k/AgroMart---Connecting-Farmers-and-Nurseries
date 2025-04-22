<?php
include 'config.php';
session_start();

$cart_id = $_POST['cart_id'];
$new_qty = $_POST['qty'];
$ad_id = $_POST['ad_id'];

$response = ['success' => false];

$stmt = $conn->prepare("SELECT quantity FROM ads WHERE ad_id = ?");
$stmt->bind_param('i', $ad_id);
$stmt->execute();
$stock = $stmt->get_result()->fetch_assoc()['quantity'];

if ($new_qty > $stock) {
    $response['message'] = "Exceeds available stock.";
} else {
    $stmt = $conn->prepare("UPDATE cart SET qty = ? WHERE cart_id = ?");
    $stmt->bind_param('ii', $new_qty, $cart_id);
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['message'] = "Database update failed.";
    }
}

echo json_encode($response);