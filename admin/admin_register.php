<?php
include '../config.php';
include '../alertFunction.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $confirm_email = $_POST['confirm_email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $status = 'pending';
    <?php
    session_start();
    include '../config.php';
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $confirm_email = trim($_POST['confirm_email']);
        $password = $_POST['password'];
    
        // Basic input validation
        if (empty($username) || empty($email) || empty($confirm_email) || empty($password)) {
            $_SESSION['error'] = "All fields are required.";
        } elseif ($email !== $confirm_email) {
            $_SESSION['error'] = "Emails do not match.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Please enter a valid email address.";
        } elseif (strlen($password) < 6) {
            $_SESSION['error'] = "Password must be at least 6 characters long.";
        } else {
            $check_email = $conn->prepare("SELECT * FROM admins WHERE email = ?");
            $check_email->bind_param('s', $email);
            $check_email->execute();
            $result = $check_email->get_result();
    
            if ($result->num_rows > 0) {
                $_SESSION['error'] = "Email already exists. Please use the 'forgot password' option.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $status = 'pending';
                $stmt = $conn->prepare("INSERT INTO admins (username, email, password, status) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('ssss', $username, $email, $hashed_password, $status);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Admin registered successfully! Waiting for approval.";
                } else {
                    $_SESSION['error'] = "Error registering admin: " . $conn->error;
                }
            }
        }
        header("Location: admin_register.php"); // Redirect to show message
        exit();
    }
    ?>
    
    <!DOCTYPE html>
    <html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Registration</title>
        <style>
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
    
            body {
                font-family: "Poppins", Arial, sans-serif;
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
            .register-container {
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
    
            .login-link {
                text-align: center;
                margin-top: 15px;
            }
    
            .login-link p {
                color: #666;
                margin-bottom: 5px;
            }
    
    
            .login-link a{
                color: #007a33;
                text-decoration: none;
                font-size: 0.9rem;
            }
    
            .login-link a:hover {
                text-decoration: underline;
                color: #f09319;
            }
    
        </style>
    </head>
    
    <body>
        <div class="register-container">
            <h2>Admin Registration</h2>
    
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message"><?= htmlspecialchars($_SESSION['success']); ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
    
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message"><?= htmlspecialchars($_SESSION['error']); ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
    
            <form method="POST" action="admin_register.php">
                <input type="text" name="username" placeholder="Admin Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="email" name="confirm_email" placeholder="Confirm Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>
            <div class="login-link">
                <p>Already have an account? <a href="admin_login.php">Log in</a></p>
            </div>
        </div>
    </body>
    
    </html>
    if ($email !== $confirm_email) {
        echo "Emails do not match.";
        exit;
    }

    $check_email = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $check_email->bind_param('s', $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        echo "Email already exists. Please use the 'forgot password' option.";
    } else {
        $stmt = $conn->prepare("INSERT INTO admins (username, email, password, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $username, $email, $password, $status);
        if ($stmt->execute()) {
            echo "Admin registered successfully! Waiting for approval.";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

<form method="POST" action="admin_register.php">
    <input type="text" name="username" placeholder="Admin Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="email" name="confirm_email" placeholder="Confirm Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
</form>