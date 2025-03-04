<?php
session_start();
ob_start();
include 'config.php';
include 'navbar.php';

if (isset($_POST['submit_email'])) {
    $email = $_POST['email'];

    // Check if email exists in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // email found, set a session to allow password reset
        $_SESSION['reset_email'] = $email;
        header("Location: forgotpw.php?reset_password=true");
        exit;
    } else {
        echo "<script>alert('Email not found!');</script>";
    }
}

if (isset($_POST['reset_password'])) {
    if (isset($_SESSION['reset_email'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];

        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_password, $email);
        $stmt->execute();

        unset($_SESSION['reset_email']);
        echo "<script>alert('Password has been reset.'); window.location.href='login.php';</script>";
        exit;
    } else {
        echo "<script>alert('Session expired or no reset request found!');</script>";
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
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: "Poppins", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            overflow-x: hidden;
            
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 75%;
            min-height: 70vh;
            padding: 20px;
        }

        .container-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 57vh;
            text-align: center;
        }

        .container-box h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
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
            color: #888;
            font-style: italic;
            font-size: 0.9rem;
        }

        button {
            background-color: #f09319;
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
            background-color: #cb790d;
        }

        .links {
            margin-top: 20px;
            font-size: 0.9rem;
            color: #666;
        }

        .links a {
            color: #006400;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .links a:hover {
            color: #f09319;
        }

        /* mobile Devices (319px - 480px) */
        @media screen and (max-width: 480px) {
            .container {
                padding: 10px;
            }

            .container-box {
                padding: 10px;
            }

            .container-box h2 {
                font-size: 1.2rem;
            }

            .form-group label {
                font-size: 0.9rem;
            }

            .form-group input {
                font-size: 0.9rem;
                padding: 8px;
            }

            button {
                font-size: 0.9rem;
                padding: 8px 15px;
            }

            .links {
                font-size: 0.85rem;
            }
        }

        /* tablets */
        @media screen and (min-width: 481px) and (max-width: 1200px) {
            .container {
                padding: 15px;
            }

            .container-box {
                padding: 25px;
            }

            .container-box h2 {
                font-size: 1.4rem;
            }

            .form-group label {
                font-size: 0.95rem;
            }

            .form-group input {
                font-size: 0.95rem;
                padding: 9px;
            }

            button {
                font-size: 0.95rem;
                padding: 9px 18px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="container-box">
            <h2>Forgot Password</h2>
            <?php if (!isset($_GET['reset_password'])): ?>
                <form action="forgotpw.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <button type="submit" name="submit_email">Submit</button>
                    <div class="links">
                        <p>Remembered your password? &nbsp; <a href="login.php">Login here</a></p>
                    </div>
                </form>
            <?php else: ?>
                <form action="forgotpw.php" method="POST">
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password"
                            required>
                    </div>
                    <button type="submit" name="reset_password">Reset Password</button>
                    <div class="links">
                        <p>Back to &nbsp;<a href="login.php">Login</a></p>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php
include 'footer.php';
?>