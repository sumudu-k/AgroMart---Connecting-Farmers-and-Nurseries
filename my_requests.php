<?php
session_start();
include 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM plant_requests WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Plant Requests</title>
    <link rel="stylesheet" href="css/my_requests.css">
</head>

<body>
    <h2>My Product Requests</h2>
    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="request-card">
                    <strong><?= htmlspecialchars($row['subject']); ?></strong>
                    <p><?= htmlspecialchars($row['description']); ?></p>
                    <p>Contact: <?= htmlspecialchars($row['contact']); ?></p>
                    <p>District: <?= htmlspecialchars($row['district']); ?></p>
                    <p>Created at: <?= htmlspecialchars(date('Y-m-d h:i A', strtotime($row['created_at']))) ?></p>
                    <div class="request-actions">
                        <a href="request_edit.php?id=<?= $row['request_id']; ?>">Edit</a>
                        <a href="#" onclick="confirmAlerRequest(<?= $row['request_id']; ?>)">Delete</a>

                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-requests">You have no any requests.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
    <script src="alertFunction.js"></script>
</body>

</html>
<?php
$stmt->close();
$conn->close();
?>