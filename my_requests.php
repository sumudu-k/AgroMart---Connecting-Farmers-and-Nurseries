<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM plant_requests WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = $conn->query($sql);

echo "<h2>My Plant Requests</h2>";
while ($row = $result->fetch_assoc()) {
    echo "<div>
            <strong>{$row['subject']}</strong><br>
            {$row['description']}<br>
            Contact: {$row['contact']}<br>
            District: {$row['district']}<br>
            <a href='request_edit.php?id={$row['request_id']}'>Edit</a> |
            <a href='delete_request.php?id={$row['request_id']}'>Delete</a>
          </div><hr>";
}