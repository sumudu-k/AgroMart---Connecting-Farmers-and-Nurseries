<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php"); // Note: Changed from "admin-login.php" to match your other pages
    exit();
}


if (isset($_GET['delete'])) {
    $request_id = $_GET['delete'];
    $delete_stmt = $conn->prepare("DELETE FROM plant_requests WHERE request_id = ?");
    $delete_stmt->bind_param("i", $request_id);
    $delete_stmt->execute();

    if ($delete_stmt->affected_rows > 0) {
        $message = "Request deleted successfully.";
    } else {
        $message = "Error deleting request.";
    }
}


$sql = "SELECT plant_requests.*, users.username FROM plant_requests 
        JOIN users ON plant_requests.user_id = users.user_id 
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

    .request-table {
        width: 100%;
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
        color: #f44336;
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

    .message {
        text-align: center;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .message.success {
        background-color: #d4edda;
        color: #155724;
    }

    .message.error {
        background-color: #f8d7da;
        color: #721c24;
    }

    @media screen and (max-width: 768px) {
        .container {
            padding: 15px;
        }

        .request-table {
            font-size: 14px;
        }

        .request-table th,
        .request-table td {
            padding: 8px;
        }
    }
</style>

<div class="container">
    <h2>All Plant Requests</h2>
    <?php if (isset($message)): ?>
        <p class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
            <?= htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>
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
</div>

<?php

$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>