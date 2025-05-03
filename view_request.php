<?php
include 'config.php';
include 'navbar.php';
$request_id = $_GET['request_id'] ?? null;
if ($request_id) {
    $stmt = $conn->prepare("SELECT * FROM plant_requests WHERE request_id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<p>No request found.</p>";
        exit;
    }
} else {
    echo "<p>Invalid request ID.</p>";
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <h2>Request Details</h2>
    <div class="request-card">
        <strong><?= htmlspecialchars($row['subject']); ?></strong>
        <p><?= htmlspecialchars($row['description']); ?></p>
        <p>Contact Number: <?= htmlspecialchars($row['contact']); ?></p>
        <p>Connect via:
            <a href="https://wa.me/+94<?= htmlspecialchars($row['contact']); ?>" target="_blank" class="whatsapp-link">
                WhatsApp
                <i class="fab fa-whatsapp"></i>
            </a>
        </p>
        <p>District: <?= htmlspecialchars($row['district']); ?></p>
        <p>Posted On: <?= htmlspecialchars($row['created_at']); ?></p>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>