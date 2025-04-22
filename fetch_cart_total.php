<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

$cart_ids = $_POST['cart_ids'] ?? [];

if (empty($cart_ids)) {
    echo 0;
    exit;
}

$placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
$types = str_repeat('i', count($cart_ids));

$sql = "
    SELECT SUM(ads.price * cart.qty) as grand_total
    FROM cart
    JOIN ads ON cart.ad_id = ads.ad_id
    WHERE cart.user_id = ? AND cart.cart_id IN ($placeholders)";

$params = array_merge([$user_id], $cart_ids);
$bind_types = 'i' . $types;

$stmt = $conn->prepare($sql);
$stmt->bind_param($bind_types, ...$params);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo $result['grand_total'] ?? 0;