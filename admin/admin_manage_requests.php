<?php
include '../config.php';

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $request_id = $_GET['delete'];
    $delete_stmt = $conn->prepare("DELETE FROM plant_requests WHERE request_id = ?");
    $delete_stmt->bind_param("i", $request_id);
    $delete_stmt->execute();

    if ($delete_stmt->affected_rows > 0) {
        echo "<p>Request deleted successfully.</p>";
    } else {
        echo "<p>Error deleting request.</p>";
    }
}

$sql = "SELECT plant_requests.*, users.username FROM plant_requests 
        JOIN users ON plant_requests.user_id = users.user_id 
        ORDER BY created_at DESC";
$result = $conn->query($sql);

echo "<h2>All Plant Requests</h2>";
?>
<table class="request-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>Username</th>
            <th>Description</th>
            <th>Contact</th>
            <th>District</th>
            <th>Posted On</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['request_id']); ?></td>
            <td><?= htmlspecialchars($row['subject']); ?></td>
            <td><?= htmlspecialchars($row['username']); ?></td>
            <td><?= htmlspecialchars($row['description']); ?></td>
            <td><?= htmlspecialchars($row['contact']); ?></td>
            <td><?= htmlspecialchars($row['district']); ?></td>
            <td><?= htmlspecialchars($row['created_at']); ?></td>
            <td>
                <a href="admin_manage_requests.php?delete=<?= htmlspecialchars($row['request_id']); ?>"
                    onclick="return confirm('Are you sure you want to delete this request?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php
?>