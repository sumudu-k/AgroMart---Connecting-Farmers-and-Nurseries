<?php
// Send 200 OK response to PayHere to avoid retries
http_response_code(200);
$user_id = $_SESSION['user_id'];

// Log incoming POST for debugging
file_put_contents('notify_log.txt', date('Y-m-d H:i:s') . "\n" . print_r($_POST, true) . "\n\n", FILE_APPEND);

include 'config.php';

// Validate and extract data
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : null;
$status_code = $_POST['status_code'] ?? null;
$user_id = $_POST['custom_1'] ?? null;

// Check for success status
if ($status_code == 2 && $order_id) {
    $stmt = $conn->prepare("UPDATE ads SET boosted = 1, boosted_at = NOW() WHERE ad_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $order_id);
        if ($stmt->execute()) {
            file_put_contents('notify_log.txt', "Ad ID $order_id boosted successfully.\n", FILE_APPEND);
        } else {
            file_put_contents('notify_errors.txt', "DB error: " . $stmt->error . "\n", FILE_APPEND);
        }
        $stmt->close();
    } else {
        file_put_contents('notify_errors.txt', "Prepare failed: " . $conn->error . "\n", FILE_APPEND);
    }

    // Insert into boost_payments table
    $sql_insert = "INSERT INTO boost_payments (ad_id, payed_at, user_id) VALUES ( ?, NOW(), ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    if ($stmt_insert) {

        $stmt_insert->bind_param("ii", $order_id, $user_id);
        if (!$stmt_insert->execute()) {
            file_put_contents('notify_errors.txt', "Insert error: " . $stmt_insert->error . "\n", FILE_APPEND);
        }
        $stmt_insert->close();
    } else {
        file_put_contents('notify_errors.txt', "Prepare failed: " . $conn->error . "\n", FILE_APPEND);
    }
} else {
    file_put_contents('notify_errors.txt', "Invalid request: status_code=$status_code, order_id=$order_id\n", FILE_APPEND);
}

$conn->close();
