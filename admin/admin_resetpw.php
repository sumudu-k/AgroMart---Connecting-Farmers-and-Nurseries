admin_resetpw.php

<?php
session_start();
ob_start();
include '../config.php';


if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT * FROM admins WHERE reset_token = ? AND reset_expiry >= ?";
    $stmt = $conn->prepare($sql);
    $currentTime = date("U");
    $stmt->bind_param("si", $token, $currentTime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (isset($_POST['reset'])) {
            $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $email = $admin['email'];

            $sql = "UPDATE admins SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $new_password, $email);
            $stmt->execute();

            echo "Password has been reset. <a href='admin_login.php'>Login here</a>";
            exit;
        }
    } else {
        echo "Invalid or expired token!";
        exit;
    }
} else {
    echo "No token provided!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>

<body>
    <h2>Reset Password</h2>
    <form action="resetpw.php?token=<?= $token ?>" method="POST">
        <input type="password" name="password" placeholder="New password" required><br>
        <button type="submit" name="reset">Reset Password</button>
    </form>
</body>

</html>