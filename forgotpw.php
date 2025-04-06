<?php
session_start();
ob_start();
include 'config.php';
include 'navbar.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Assuming you've installed PHPMailer via Composer

function sendResetCode($email, $code)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sumuduytube@gmail.com';
        $mail->Password = 'lotv zvir kuag roxw';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('sumuduytube@gmail.com', 'AgroMart');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'AgroMart Password Reset Code';
        $mail->Body    = "<h3>Your reset code is: <b>$code</b></h3>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function isValidPassword($password)
{
    return preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/', $password);
}

if (isset($_POST['submit_email'])) {
    $email = $_POST['email'];
    if (empty($email)) {
        echo "<script>alert('Please enter your email!');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address!');</script>";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $reset_code = rand(100000, 999999);
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code'] = $reset_code;
            if (sendResetCode($email, $reset_code)) {
                echo "<script>
                    alert('A reset code was sent to your email.');
                    window.location.href='forgotpw.php?verify_code=true';
                </script>";
            } else {
                echo "<script>alert('Email sending failed!');</script>";
            }
        } else {
            echo "<script>alert('Email not found!');</script>";
        }
    }
}

if (isset($_POST['reset_password'])) {
    $email = $_SESSION['reset_email'] ?? '';
    $user_code = $_POST['reset_code'] ?? '';
    $new_password = $_POST['new_password'];

    if (!$email || !isset($_SESSION['reset_code'])) {
        echo "<script>alert('Session expired or invalid!');</script>";
    } elseif ($user_code != $_SESSION['reset_code']) {
        echo "<script>alert('Invalid reset code!');</script>";
    } elseif (!isValidPassword($new_password)) {
        echo "<script>alert('Password must contain at least 8 characters, 1 uppercase, 1 number & 1 special character');</script>";
    } else {
        $hashed_pw = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_pw, $email);
        $stmt->execute();

        unset($_SESSION['reset_email'], $_SESSION['reset_code']);
        echo "<script>
            alert('Password successfully reset!');
            window.location.href='login.php';
        </script>";
    }
}
?>

<!-- HTML Part -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>

<body>
    <div style="max-width: 500px; margin: auto; padding: 20px;">
        <h2>Forgot Password</h2>

        <?php if (!isset($_GET['verify_code'])): ?>
        <form method="POST">
            <label>Email:</label><br>
            <input type="email" name="email" required><br><br>
            <button type="submit" name="submit_email">Send Reset Code</button>
        </form>
        <?php else: ?>
        <form method="POST">
            <label>Enter Reset Code:</label><br>
            <input type="text" name="reset_code" required><br><br>
            <label>New Password:</label><br>
            <input type="password" name="new_password" required><br>
            <small>Password must be 8+ chars, 1 uppercase, 1 number, 1 symbol</small><br><br>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
        <?php endif; ?>
    </div>
</body>

</html>