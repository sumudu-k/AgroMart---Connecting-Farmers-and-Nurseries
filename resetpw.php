<?php
session_start();
ob_start();
include 'config.php';
include 'alertFunction.php';

// check if the token is valid
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_expiry >= ?";
    $stmt = $conn->prepare($sql);
    $currentTime = date("U");
    $stmt->bind_param("si", $token, $currentTime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // proceed with password reset
        if (isset($_POST['reset'])) {
            $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $email = $user['email'];

            //update the new password in the database
            $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $new_password, $email);
            $stmt->execute();

            showAlert('Password has been reset.', 'success', '#008000', 'login.php');
            exit;
        }
    } else {
        showAlert('Invalid or expired token!', 'error', '#ff0000', 'forgotpw.php');
        exit;
    }
} else {
    showAlert('No token provided!', 'error', '#ff0000', 'forgotpw.php');
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

<?php
include 'footer.php';
?>