<?php
include 'config.php';
include 'navbar.php';

$sql = "SELECT plant_requests.*, users.username FROM plant_requests 
        JOIN users ON plant_requests.user_id = users.user_id 
        ORDER BY created_at DESC";
$result = $conn->query($sql);

echo "<h2>All Plant Requests</h2>";
while ($row = $result->fetch_assoc()) { ?>

<div class="request-container">
    <p><strong>Subject:</strong> <?= htmlspecialchars($row['subject']); ?> by <?= htmlspecialchars($row['username']); ?>
    </p>
    <p class="request-description"><?= htmlspecialchars($row['description']); ?></p>
    <p><strong>Contact Number:</strong><?= htmlspecialchars($row['contact']); ?></p>
    <p><strong>Connect WhatsApp:</strong> <a href="https://wa.me/+94<?= htmlspecialchars($row['contact']); ?>"
            target="_blank">
            <img src="uploads/whatsapp.gif" /></a></p>
    <p><strong>District:</strong> <?= htmlspecialchars($row['district']); ?></p>
    <p><strong>Posted On:</strong> <?= htmlspecialchars($row['created_at']); ?></p>
    <hr>
</div>

<?php }
?>