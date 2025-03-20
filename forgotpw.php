<?php
session_start();
ob_start();
include 'config.php';
include 'navbar.php';

function isValidPassword($password)
{
    return preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/', $password);
}

function sendResetCode($email, $code)
{
    $subject = "Password Reset Code - AgroMart";
    $message = "Your password reset code is: $code\n\nEnter this code to reset your password.\n\nIf you did not request this, please ignore this email.";
    $headers = "From: noreply@agromart.com";

    return mail($email, $subject, $message, $headers);
}

if (isset($_POST['submit_email'])) {
    $email = $_POST['email'];

    if (empty($email)) {
        echo "<script>window.onload = function() { showAlert('Please enter your email!', 'error', '#ff0000'); };</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>window.onload = function() { showAlert('Please enter a valid email!', 'error', '#ff0000'); };</script>";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate and store reset code
            $reset_code = rand(100000, 999999);
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code'] = $reset_code;

            $sql = "UPDATE users SET reset_code = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $reset_code, $email);
            $stmt->execute();

            //send email with reset code
            if (sendResetCode($email, $reset_code)) {
                header("Location: forgotpw.php?enter_code=true");
                exit;
            } else {
                echo "<script>window.onload = function() { showAlert('Failed to send email. Try again later!', 'error', '#ff0000'); };</script>";
            }
        } else {
            echo "<script>window.onload = function() { showAlert('Email not found!', 'error', '#ff0000'); };</script>";
        }
    }
}

//verify reset code
if (isset($_POST['verify_code'])) {
    $entered_code = $_POST['reset_code'];

    if (!isset($_SESSION['reset_email'])) {
        echo "<script>window.onload = function() { showAlert('Session expired. Try again!', 'error', '#ff0000'); };</script>";
    } elseif ($entered_code != $_SESSION['reset_code']) {
        echo "<script>window.onload = function() { showAlert('Invalid reset code!', 'error', '#ff0000'); };</script>";
    } else {
        $_SESSION['code_verified'] = true;
        header("Location: forgotpw.php?reset_password=true");
        exit;
    }
}

//reset password
if (isset($_POST['reset_password'])) {
    if (!isset($_SESSION['reset_email']) || !isset($_SESSION['code_verified'])) {
        echo "<script>window.onload = function() { showAlert('Session expired or no valid reset request found', 'warning', '#ff0000'); };</script>";
    } else {
        $new_password = $_POST['new_password'];
        $email = $_SESSION['reset_email'];

        if (empty($new_password)) {
            echo "<script>window.onload = function() { showAlert('Please enter a new password!', 'error', '#ff0000'); };</script>";
        } elseif (!isValidPassword($new_password)) {
            echo "<script>window.onload = function() { showAlert('Password must contain at least 8 characters, one uppercase letter, one number, and one special character!', 'error', '#ff0000'); };</script>";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $sql = "UPDATE users SET password = ?, reset_code = NULL WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $hashed_password, $email);
            $stmt->execute();

            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_code']);
            unset($_SESSION['code_verified']);

            echo "<script>
            window.onload = function() {
                showAlert('Password has been reset', 'success', '#008000', 'login.php');
            };
            setTimeout(function() {
                window.location.href = 'login.php';
            }, 2000);
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/forgotpw.css">
    <title>Forgot Password - AgroMart</title>
</head>

<body>
    <div class="wrapper">
        <div class="login-box">
            <h2>Forgot Password</h2>
            <?php if (!isset($_GET['enter_code']) && !isset($_GET['reset_password'])): ?>
                <form action="forgotpw.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <button type="submit" name="submit_email">Send Reset Code</button>
                </form>
            <?php elseif (isset($_GET['enter_code'])): ?>

                <form action="forgotpw.php" method="POST">
                    <div class="form-group">
                        <label for="reset_code">Enter Reset Code</label>
                        <input type="text" id="reset_code" name="reset_code" required>
                    </div>
                    <button type="submit" name="verify_code">Verify Code</button>
                </form>
            <?php elseif (isset($_GET['reset_password'])): ?>
                <!-- Step 3: Reset Password -->
                <form action="forgotpw.php" method="POST">
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <button type="submit" name="reset_password">Reset Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>