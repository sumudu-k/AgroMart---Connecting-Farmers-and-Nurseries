<?php
//session_start();
ob_start();
include 'config.php';
include 'navbar.php';
include 'alertFunction.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

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

            header("Location: home.php");

            exit();
        } else {
            showAlert('Invalid password!', 'error', '#ff0000', 'login.php');
        }
    } else {
        showAlert('User not found!', 'error', '#ff0000', 'login.php');
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f3f4f6;
        }

        /* Centered Wrapper */
        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
            padding: 20px;
            width: 75%; 
            margin: 0 auto; 
        }

        /* login Container Styling */
        .login-container {
            display: flex;
            background-color: #e2e6eb;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1200px;
        }

        .plant-image {
            flex: 0.75;
            background-image: url("images/login_image.png");
            background-size: contain;
            background-repeat: no-repeat;
            mix-blend-mode:multiply;
            background-position: center;
            border-radius: 10px;
            min-height: 300px;
        }

        .login-form {
            flex: 1.25;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-form form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .login-form h2 {
            font-size: 24px;
            margin-bottom: 30px;
        }

        .login-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input:focus {
            outline: none;
            border-color: #007a33;
        }

        .login-form form button {
            width: 200px;
            background-color: #007a33;
            color: #fff;
            padding: 10px;
            font-size: 1.125rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-form button:hover {
            background-color: #005922;
        }

        p {
            font-size: 0.938rem;
            color: black;
            margin-top: 15px;
        }

        .link {
            text-decoration: none;
            color: #0917ee;
            font-weight: bold;
        }


    /* responsive design */
    @media (max-width: 480px) {
        .wrapper {
            padding: 10px;
            width: 90%;
        }

        .login-container {
            flex-direction: column;
            padding: 10px;
            width: 100%;
        }

        .plant-image {
            width: 100%;
        }

        .login-form {
            width: 100%;
            padding: 15px;
        }

        .login-form h2 {
            font-size: 1.25rem;
            margin-bottom: 20px;
        }

        .login-form input {
            font-size: 14px;
            padding: 8px;
        }

        .login-form button {
            font-size: 1rem;
            padding: 8px;
        }

        p {
            font-size: 0.875rem;
        }
    }


    
    @media (min-width: 481px) and (max-width: 1200px) {
        .wrapper {
            min-height: 50vh;
            
        }
        .login-container {
            flex-direction: row;
            width: 90%;
            padding: 15px;
        }

        .plant-image {
            width: 50%;
            min-height: 300px;
        }

        .login-form {
            width: 50%;
        }
    }
    </style>

</head>

<body>
    <div class="wrapper">
        <div class="login-container">
            <div class="plant-image"></div>
            <div class="login-form">
                <h2>Welcome Back</h2>
                <form action="login.php" method="post">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
                </form>
                <p> Forgot Password? &ensp; <a class="link" href="forgotpw.php">Reset here</a>.</p>
                <p> Haven't an account yet? &ensp; <a class="link" href="register.php">Register here</a>.</p>
            </div>
        </div>
    </div>
</body>

</html>

<?php
include 'footer.php';
?>