<?php
//session_start();

include 'config.php';
include 'navbar.php';


if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // Password is correct, start the session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];

            header("Location: home.php");

            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
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
            min-height: 100vh;
            padding: 20px;
        }

        /* Login Container Styling */
        .login-container {
            display: flex;
            align-content: center;
            background-color: #e2e6eb;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 50%;
            max-width: 1300px;
        }

        .plant-image {
            flex: 0.75;
            background-image: url("images/login image.png");
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            min-height: 300px;
        }

        .login-form {
            flex: 1.25;
            max-width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-form h2 {
            font-size: 24px;
            margin-bottom: 40px;
        }

        .login-form form {
            display: flex;
            flex-direction: column;
            /* gap: 5px; */
        }

        .login-form input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-form button {
            background-color: #007a33;
            color: #fff;
            padding: 10px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-form button:hover {
            background-color: #005922;
        }

        p {
            font-size: 15px;
            color: black;
            margin-top: 15px;
        }

        .link {
            text-decoration: none;
            color: #0917ee;
            font-weight: bold;
        }

        /* Responsive Styles */
        @media (max-width: 850px) {
            .login-container {
                flex-direction: column;
                align-items: center;
                width: 80%;
                padding: 10px;
            }

            .plant-image {
                width: 80%;
                height: auto;
                min-height: 350px;
                margin-bottom: 20px;
            }

            .login-form {
                width: 90%;
            }
        }

        @media (min-width: 850px) and (max-width: 1300px) {
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

        @media (min-width: 1301px) {
            .login-container {
                flex-direction: row;
                width: 60%;
                gap: 30px;
            }

            .plant-image {
                width: 45%;
                min-height: 400px;
            }

            .login-form {
                width: 55%;
            }
        }
    </style>

</head>

<body>
    <div class="wrapper">
        <div class="login-container">
            <div class="card plant-image"></div>
            <div class="card login-form">
                <h2>Welcome Back</h2>
                <form action="login.php" method="post">
                    <input type="email" name="email" placeholder="Email" required><br>
                    <input type="password" name="password" placeholder="Password" required><br>
                    <button type="submit" name="login">Login</button>
                </form>
                <p> Forgot Password? &ensp; <a class="link" href="forgotpw.php">Reset here</a>.</p>
                </p>
                <p> Haven't an account yet? &ensp; <a class="link" href="register.php">Register here</a>.</p>
            </div>
        </div>
    </div>
</body>

</html>