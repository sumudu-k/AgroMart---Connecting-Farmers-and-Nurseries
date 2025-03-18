<?php
session_start();
include '../config.php';
include '../alertFunction.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

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

// Start output buffering
ob_start();
?>

<style>
    * {
        font-family: "Poppins", Arial, sans-serif;
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        color: #333;
        position: relative;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background-color: #f4f4f4;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("../images/B1.jpg");
        background-size: cover;
        opacity: 0.2;
        z-index: -1;
    }

    .dlt-notification-container {
        max-width: 90%;
        margin: 20px auto;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1;
    }

    .dlt-notification-container h1 {
        text-align: center;
        font-size: 2rem;
        color: #333;
        padding: 10px 0;
        border-bottom: 2px solid #007a33;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 40px;
    }

    th,
    td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        vertical-align: middle;
        border-right: 2px solid rgba(51, 51, 51, 0.2);
    }

    th:nth-child(1),
    td:nth-child(1) {
        width: 40%;
        max-width: 200px;
    }

    th:nth-child(2),
    td:nth-child(2) {
        width: 15%;
        text-align: center;
    }

    th:nth-child(3),
    td:nth-child(3) {
        width: 20%;
        text-align: center;
    }

    th:nth-child(4),
    td:nth-child(4) {
        width: 15%;
        text-align: center;
    }

    th:nth-child(5),
    td:nth-child(5) {
        width: 10%;
        text-align: center;
    }

    td:nth-child(1) {
        white-space: pre;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    td img {
        max-width: 100%;
        height: auto;
    }

    th {
        text-align: center;
        background-color: #a9e6a9;
        font-weight: 600;
        color: #333;
    }


    th:last-child {
        border-right: none;
        text-align: center;
    }

    tr {
        transition: background-color 0.2s ease;
    }

    tr:hover {
        background-color: #e6ffe6;
    }

    .delete-link {
        background-color: #f44336;
        color: white;
        border: none;
        padding: 8px 16px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .delete-link:hover {
        background-color: #d32f2f;
    }
</style>

<div class="dlt-notification-container">
    <h1>Manage Notifications</h1>
    <table>
        <tr>
            <th>Message</th>
            <th>Link</th>
            <th>Image</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td data-label="Message"><?= htmlspecialchars($row['message']); ?></td>
                <td data-label="Link">
                    <?= $row['link'] ? "<a href='" . htmlspecialchars($row['link']) . "' target='_blank'>View Link</a>" : 'No Link'; ?>
                </td>
                <td data-label="Image">
                    <?php if (!empty($row['image'])): ?>
                        <img src="../<?= htmlspecialchars($row['image']); ?>" alt="Notification Image">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td data-label="Created At"><?= htmlspecialchars($row['created_at']); ?></td>
                <td data-label="Action">
                    <a href="?delete_created_at=<?= urlencode($row['created_at']); ?>" class="delete-link"
                        onclick="return confirm('Delete this notification? All notifications with the same timestamp will be deleted.')">
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php
// Capture the content and include the layout
$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>