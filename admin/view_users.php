<?php
session_start();
include '../config.php';
include '../alertFunction.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    // Prevent deleting the current admin
    if ($user_id == $_SESSION['admin_id']) {
        $_SESSION['error'] = "You cannot delete your own account!";
        header("Location: view_users.php");
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "User deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete user.";
    }
    header("Location: view_users.php"); // Redirect to refresh the page
    exit();
}

$result = $conn->query("SELECT * FROM users");

// Start output buffering to capture page content
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

    .user-container {
        max-width: 90%;
        margin: 20px auto;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1;
    }

    .user-container h1 {
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

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        vertical-align: middle;
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

</style>

<div class="user-container">
    <h1>Manage Users</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message"><?= htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message"><?= htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()) { ?>
            <tr>
                <td data-label="Username"><?= htmlspecialchars($user['username']) ?></td>
                <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
                <td data-label="Contact Number"><?= htmlspecialchars($user['contact_number']) ?></td>
                <td data-label="Action">
                    <a href="view_users.php?delete_user=<?= $user['user_id'] ?>" class="delete-button" 
                       onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
// Get the captured content and clean the buffer
$content = ob_get_clean();

// Include the layout file and pass the content
include '../admin/admin_navbar.php';
?>