<?php
include 'config.php';
include 'navbar.php';

$sql = "SELECT plant_requests.*, users.username FROM plant_requests 
        JOIN users ON plant_requests.user_id = users.user_id 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Plant Requests</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/requests.css">
</head>

<body>
    <h2>All Plant Requests</h2>
    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="request-card">
                    <strong><?= htmlspecialchars($row['subject']); ?></strong>
                    <p><?= htmlspecialchars($row['description']); ?></p>
                    <p>Contact Number: <?= htmlspecialchars($row['contact']); ?></p>
                    <p>Connect via:
                        <a href="https://wa.me/+94<?= htmlspecialchars($row['contact']); ?>" target="_blank"
                            class="whatsapp-link">
                            WhatsApp
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </p>
                    <p>District: <?= htmlspecialchars($row['district']); ?></p>
                    <p>Posted On: <?= htmlspecialchars($row['created_at']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-requests">Sorry! No plant requests available.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
<?php
$conn->close();
?>