<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];

    // Verify password
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        echo "Incorrect password. Account not deleted.";
        exit();
    }

    // Delete user
    $delete_stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $delete_stmt->bind_param("i", $user_id);

    if ($delete_stmt->execute()) {
        session_destroy();
        header("Location: register.php");
        exit();
    } else {
        echo "Error deleting account.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Delete Account</title>
</head>

<body>
    <h1>Delete My Account</h1>
    <p>This action cannot be undone. Please confirm your password to proceed.</p>
    <form action="delete_account.php" method="post">
        <label>Enter Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Delete Account</button>
    </form>
</body>

</html>