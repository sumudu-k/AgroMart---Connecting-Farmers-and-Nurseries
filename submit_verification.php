<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}

// delete already inserted request
$sql_delete = 'DELETE FROM verification_requests WHERE user_id=?';
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param('i', $user_id);
$stmt_delete->execute();

$sql = 'INSERT INTO verification_requests (user_id) VALUES (?)';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
echo "<script>
    alert('You submitted the request successfully');
    window.location.href = 'verify.php';
</script>";