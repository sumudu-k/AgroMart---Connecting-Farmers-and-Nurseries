<?php
session_start();
include '../config.php';
include '../alertFunction.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['block_user'])) {
    $user_id = $_GET['block_user'];
    $sql_block = "UPDATE users SET status = 'y' WHERE user_id=? ";
    $stmt_block = $conn->prepare($sql_block);
    $stmt_block->bind_param('i', $user_id);
    if ($stmt_block->execute()) {
        echo "<script>
                alert('User blocked successfully!');
              </script>";
        header('Location:view_users.php');
        exit();
    } else {
        echo "<script>alert('Failed to block user.');</script>";
        header('Location:view_users.php');
        exit();
    }
}

if (isset($_GET['unblock_user'])) {
    $user_id = $_GET['unblock_user'];
    $sql_unblock = "UPDATE users SET status = 'n' WHERE user_id=? ";
    $stmt_unblock = $conn->prepare($sql_unblock);
    $stmt_unblock->bind_param('i', $user_id);
    if ($stmt_unblock->execute()) {
        echo "<script>
                alert('User Unblocked successfully!');
              </script>";
        header('Location:view_users.php');
        exit();
    } else {
        echo "<script>alert('Failed to Unblock user.');</script>";
        header('Location:view_users.php');
        exit();
    }
}


$result = $conn->query("SELECT * FROM users");

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

th,
td {
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

.block-button {
    background-color: rgb(0, 172, 37);
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

.block-button:hover {
    background-color: rgb(0, 136, 23);
}

.unblock-button {
    background-color: rgb(233, 48, 48);
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

.unblock-button:hover {
    background-color: rgb(184, 18, 18);
}
</style>

<div class="user-container">
    <h1>Manage Users</h1>

    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()) { ?>
            <tr>
                <td data-label="User ID"><?= htmlspecialchars($user['user_id']) ?></td>
                <td data-label="Username"><?= htmlspecialchars($user['username']) ?></td>
                <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
                <td data-label="Contact Number"><?= htmlspecialchars($user['contact_number']) ?></td>
                <td data-label="Action">
                    <?php if ($user['status'] == 'y'): ?>
                    <a href="view_users.php?unblock_user=<?= $user['user_id'] ?>" class="unblock-button"
                        onclick="return confirm('Are you sure you want to UNBLOCK this user?')">UNBLOCK</a>
                    <?php else : ?>
                    <a href="view_users.php?block_user=<?= $user['user_id'] ?>" class="block-button"
                        onclick="return confirm('Are you sure you want to BLOCK this user?')">BLOCK</a>
                    <?php endif ?>
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