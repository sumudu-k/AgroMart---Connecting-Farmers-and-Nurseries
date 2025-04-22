<?php
include 'config.php';
session_start();

$cart_id = $_POST['cart_id'] ?? 0;
$response = ['success' => false];

if ($cart_id) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
    $stmt->bind_param('i', $cart_id);
    if ($stmt->execute()) {
        $response['success'] = true;
    }
}

echo json_encode($response);