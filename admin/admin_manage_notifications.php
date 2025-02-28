<?php
include '../config.php';

// Delete notification
if (isset($_GET['delete_created_at'])) {
    $created_at = $_GET['delete_created_at'];

    // Delete all notifications with the same 'created_at'
    $sql_delete = "DELETE FROM notifications WHERE created_at = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("s", $created_at);
    $stmt_delete->execute();

    echo "<script>alert('Notification deleted!'); window.location='admin_manage_notifications.php';</script>";
}

// Fetch unique notifications
$sql = "SELECT DISTINCT message, created_at, link, image
        FROM notifications
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manage Notifications</title>
</head>

<body>
    <h2>Manage Notifications</h2>
    <table border="1">
        <tr>
            <th>Message</th>
            <th>Link</th>
            <th>Image</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['message']; ?></td>
                <td><?= $row['link'] ? "<a href='{$row['link']}' target='_blank'>View Link</a>" : 'No Link'; ?></td>
                <td>
                    <?php if (!empty($row['image'])): ?>
                        <img src="../<?= htmlspecialchars($row['image']); ?>" width="100">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?= $row['created_at']; ?></td>
                <td>
                    <a href="?delete_created_at=<?= urlencode($row['created_at']); ?>"
                        onclick="return confirm('Delete this notification? All notifications with the same timestamp will be deleted.')">
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>