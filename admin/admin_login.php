<?php
session_start();
include '../config.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? and STATUS = 'approved'");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Poppins", Arial, sans-serif;
        }

        body {
            
            color: #333;
            position: relative;
            min-height: 100vh;
            display: flex;
            justify-content: center;
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

        .login-container{
            min-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(240, 255, 232, 0.2); /* Light green with transparency */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }

        h2 {
            text-align: center;
            font-size: 2rem;
            color: #333;
            margin-bottom: 25px;
            padding: 10px 0;
            border-radius: 5px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            width: 100%;
            transition: border-color 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: #007a33;
        }

        input::placeholder {
            font-size: 1rem;
            font-style: italic;
            letter-spacing: 0.5px;
            font-weight: 500;
            opacity: 0.7;
        }

        button {
            background-color: #007a33;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #005922;
        }

        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }

        .forgot-password p {
            color: #666;
            margin-bottom: 5px;
        }


        .forgot-password a{
            color: #007a33;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .forgot-password a:hover {
            text-decoration: underline;
            color: #f09319;
        }

    </style>
</head>

<body>
    <div class="login-container">
        <h2>Admin Login</h2>

        <?php if (isset($error)) echo "<p>$error</p>"; ?>

        <form action="admin_login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <div class="forgot-password">
            <p><a href="admin_forgotpw.php">Forgot Password?</a></p>
            <p>Don't have an account? <a href="admin_register.php">Register here</a></p>
        </div>
    </div>
</body>

</html>