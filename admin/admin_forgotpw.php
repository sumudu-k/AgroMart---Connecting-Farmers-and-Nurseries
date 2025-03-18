<?php
session_start();
ob_start();
include '../config.php';
include '../alertFunction.php';

if (isset($_POST['submit_email'])) {
    $email = $_POST['email'];

    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['reset_email'] = $email;
        header("Location:admin_forgotpw.php?reset_password=true");
        exit;
    } else {
        showAlert('Email not found!', 'error', '#ff0000', 'admin_forgotpw.php');
    }
}

if (isset($_POST['reset_password'])) {
    if (isset($_SESSION['reset_email'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];

        $sql = "UPDATE admins SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_password, $email);
        $stmt->execute();

        unset($_SESSION['reset_email']);
        showAlert('Password has been reset', 'success', '#008000', 'admin_login.php');
        exit;
    } else {
        showAlert('Session expired or no reset request found', 'warning', '#ff0000', 'admin_forgotpw.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - AgroMart</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', Arial, sans-serif;
        }

        body {
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .login-box {
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            min-width: 600px;
            text-align: center;
        }

        .login-box h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            text-transform: capitalize;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 1rem;
            color: #333;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #f09319;
        }

        .form-group input::placeholder {
            font-size: 1rem;
            font-style: italic;
            letter-spacing: 0.5px;
            font-weight: 500;
            opacity: 0.7;
        }

        button {
            background-color: #007a33;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #005922;
        }

        .links {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #666;
        }

        .links a {
            color: #006400;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .links a:hover {
            text-decoration: underline;
            color: #f09319;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h2>Forgot Password</h2>
        <?php if (!isset($_GET['reset_password'])): ?>
            <form action="admin_forgotpw.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <button type="submit" name="submit_email">Submit</button>
                <div class="links">
                    <p>Remembered your password? <a href="admin_login.php">Login here</a></p>
                </div>
            </form>
        <?php else: ?>
            <form action="admin_forgotpw.php" method="POST">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                </div>
                <button type="submit" name="reset_password">Reset Password</button>
                <div class="links">
                    <p>Back to <a href="admin_login.php">Login</a></p>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>