<?php
//session_start();
ob_start();
include 'config.php';
include 'navbar.php';

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "<script>
        window.onload = function() {
            showAlert('Please fill in all fields!', 'error', '#ff0000');
        };
        </script>";
    } else {

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();




        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['created_at'] = $user['created_at'];
                $_SESSION['contact_number'] = $user['contact_number'];
                $_SESSION['address'] = $user['address'];
                $_SESSION['status'] = $user['status'];
                echo "<script>
            window.onload = function() {
                showAlert('Login Successful', 'success', '#008000');
            };
            setTimeout(function() {
                window.location.href = 'home.php';
            }, 2000);
            </script>";
            } else {
                echo "<script>
            window.onload = function() {
                showAlert('Invalid password!', 'error', '#ff0000');
            };
            </script>";
            }
        } else {
            echo "<script>
        window.onload = function() {
            showAlert('User not found!', 'error', '#ff0000');
        };
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
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">

</head>

<body>
    <div class="wrapper">
        <div class="login-container">
            <div class="plant-image"></div>
            <div class="login-form">
                <h2>Welcome Back</h2>
                <form action="login.php" method="post">
                    <input type="email" name="email" placeholder="Email">
                    <input type="password" name="password" placeholder="Password">
                    <button type="submit" name="login">Login</button>
                </form>
                <p> Forgot Password? &ensp; <a class="link" href="forgotpw.php">Reset here</a>.</p>
                <p> Haven't an account yet? &ensp; <a class="link" href="register.php">Register here</a>.</p>
            </div>
        </div>
    </div>
    <script src='alertFunction.js'></script>
</body>

</html>

<?php
include 'footer.php';
?>