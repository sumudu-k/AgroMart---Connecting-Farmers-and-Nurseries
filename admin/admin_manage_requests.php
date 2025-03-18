<?php
session_start();
include '../config.php';
include '../alertFunction.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle request deletion
if (isset($_GET['delete'])) {
    $request_id = $_GET['delete'];
    $delete_stmt = $conn->prepare("DELETE FROM plant_requests WHERE request_id = ?");
    $delete_stmt->bind_param("i", $request_id);
    $delete_stmt->execute();

    if ($delete_stmt->affected_rows > 0) {
        $_SESSION['success'] = "Request deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting request.";
    }
    header("Location: admin_manage_requests.php"); // Redirect to refresh the page
    exit();
}

// Fetch plant requests
$sql = "SELECT plant_requests.*, users.username FROM plant_requests 
        JOIN users ON plant_requests.user_id = users.user_id 
        ORDER BY created_at DESC";
$result = $conn->query($sql);

// Start output buffering
ob_start();
?>

<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: "Poppins", Arial, sans-serif;
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

    .request-container {
        max-width: 90%;
        margin: 20px auto;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1;
    }

    .request-container h1 {
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

    th {
        text-align: center;
        background-color: #a9e6a9;
        font-weight: 600;
        color: #333;
        border-right: 2px solid rgba(51, 51, 51, 0.2);
    }

    th:last-child {
        border-right: none;
    }

    td:last-child {
        text-align: center;
        border-right: none;
    }

    tr {
        transition: background-color 0.2s ease;
    }

    tr:hover {
        background-color: #e6ffe6;
    }

    .delete-button {
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

    .delete-button:hover {
        background-color: #d32f2f;
    }


    td:last-child a {
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

    td:last-child a:hover {
        background-color: #d32f2f;
    }

    .no-requests {
        text-align: center;
        color: #666;
        font-size: 1.1rem;
        margin-top: 20px;
        padding: 15px;
        background-color: #fff;
        border-radius: 5px;
    }
</style>

<div class="request-container">
    <h1>All Plant Requests</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message"><?= htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message"><?= htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if ($result->num_rows > 0) { ?>
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
                        <td data-label="ID"><?= htmlspecialchars($row['request_id']); ?></td>
                        <td data-label="Subject"><?= htmlspecialchars($row['subject']); ?></td>
                        <td data-label="Username"><?= htmlspecialchars($row['username']); ?></td>
                        <td data-label="Description"><?= htmlspecialchars($row['description']); ?></td>
                        <td data-label="Contact"><?= htmlspecialchars($row['contact']); ?></td>
                        <td data-label="District"><?= htmlspecialchars($row['district']); ?></td>
                        <td data-label="Posted On"><?= htmlspecialchars($row['created_at']); ?></td>
                        <td data-label="Actions">
                            <a href="admin_manage_requests.php?delete=<?= htmlspecialchars($row['request_id']); ?>"
                                onclick="return confirm('Are you sure you want to delete this request?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="no-requests">No plant requests found.</p>
    <?php } ?>
</div>

<?php
// Capture the content and include the layout
$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>