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


<style>
.request-table {
    width: 90%;
    border-collapse: collapse;
    margin-top: 20px;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
}

.request-table th,
.request-table td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
}

.request-table th {
    background-color: #f4f4f4;
    font-weight: bold;
    color: #333;
}

.request-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.request-table tr:hover {
    background-color: #f1f1f1;
}

.request-table a {
    color: red;
    font-weight: bold;
    text-decoration: none;
}

.request-table a:hover {
    text-decoration: underline;
}

.request-table td a {
    margin-right: 10px;
}

.request-table thead {
    background-color: #f5f5f5;
    font-size: 16px;
    color: #444;
}

.request-table td.empty {
    color: #888;
}
</style>

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