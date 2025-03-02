<?php
include 'config.php';
include 'navbar.php';

$sql = "SELECT plant_requests.*, users.username FROM plant_requests 
        JOIN users ON plant_requests.user_id = users.user_id 
        ORDER BY created_at DESC";
$result = $conn->query($sql);

echo "<h2>All Plant Requests</h2>";
while ($row = $result->fetch_assoc()) { ?>

<style>
.request-container {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    width: 75%;
    justify-content: center;
    align-items: center;
    display: flex;
    flex-direction: column;
}

.request-container p {
    margin: 5px 0;
    font-size: 16px;
    color: #333;
}

.request-container .request-description {
    font-size: 14px;
    color: #555;
    font-style: italic;
}

.request-container a {
    text-decoration: none;
    color: #25d366;
    font-weight: bold;
}

.request-container a img {
    width: 20px;
    height: 20px;
    vertical-align: middle;
    margin-left: 5px;
}

.request-container hr {
    border: 0;
    height: 1px;
    background: #ccc;
    margin-top: 10px;
}
</style>

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