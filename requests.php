<?php
include 'config.php';
include 'navbar.php';

$sql = "SELECT plant_requests.*, users.username FROM plant_requests 
        JOIN users ON plant_requests.user_id = users.user_id 
        ORDER BY created_at DESC";
$result = $conn->query($sql);

echo "<h2>All Plant Requests</h2>";
while ($row = $result->fetch_assoc())  ?>