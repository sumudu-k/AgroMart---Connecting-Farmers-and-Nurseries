<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}


if (isset($_GET['delete_created_at'])) {
    $created_at = $_GET['delete_created_at'];


    $sql_delete = "DELETE FROM notifications WHERE created_at = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("s", $created_at);
    $stmt_delete->execute();

    echo "<script>alert('Notification deleted!'); window.location='admin_manage_notifications.php';</script>";
}


$sql = "SELECT DISTINCT message, created_at, link, image
        FROM notifications
        ORDER BY created_at DESC";
$result = $conn->query($sql);


ob_start();
?>

<style>
    .container {
        max-width: 100%;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #ddd;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    th {
        text-align: center;
        background-color: #f2f2f2;
        font-weight: bold;
    }

    tr:hover {
        background-color: #f5f5f5;
    }

    a {
        color: #007a33;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    .delete-link {
        color: #f44336;
    }

    .delete-link:hover {
        color: #d32f2f;
    }

    img {
        max-width: 100px;
        height: auto;
    }

    @media screen and (max-width: 768px) {
        .container {
            padding: 15px;
        }

        table {
            font-size: 14px;
        }

        th, td {
            padding: 8px;
        }

        img {
            max-width: 80px;
        }
    }
</style>

<div class="container">
    <h2>Manage Notifications</h2>
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
                <td><?= htmlspecialchars($row['message']); ?></td>
                <td>
                    <?= $row['link'] ? "<a href='" . htmlspecialchars($row['link']) . "' target='_blank'>View Link</a>" : 'No Link'; ?>
                </td>
                <td>
                    <?php if (!empty($row['image'])): ?>
                        <img src="../<?= htmlspecialchars($row['image']); ?>" alt="Notification Image">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['created_at']); ?></td>
                <td>
                    <a href="?delete_created_at=<?= urlencode($row['created_at']); ?>"
                       class="delete-link"
                       onclick="return confirm('Delete this notification? All notifications with the same timestamp will be deleted.')">
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php

$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>