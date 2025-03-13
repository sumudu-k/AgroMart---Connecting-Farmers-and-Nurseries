<?php
//session_start();
ob_start();
include 'config.php';
include 'navbar.php';
include 'alertFunction.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password != $confirm_password) {
        showAlert('Password do not match!', 'error', '#ff0000', 'register.php');
        exit;
    }
    $hashpassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $user_check_sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($user_check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        showAlert('Email already registered!', 'error', '#ff0000', 'register.php');
    } else {
        $sql = "INSERT INTO users (username, email, password, contact_number, address) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $email, $hashpassword, $contact_number, $address);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user_id;
            showAlert('User registered successfully!', 'success', '#008000', 'home.php');
            exit;
        } else {
            showAlert('Error registering user.', 'error', '#ff0000', 'register.php');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        margin: 0;
        padding: 0;
        position: relative;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Add background image */
    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("images/B1.jpg");
        background-size: cover;
        opacity: 0.5;
        z-index: -1;
    }

    /* Centered Wrapper */
    .wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 20px;
        width: 75%;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    /* Register Container Styling */
    .register-container {
        display: flex;
        width: 100%;
        gap: 20px;
        background-color: #e2e6eb;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .plant-image {
        flex: 1;
        min-height: 300px;
        max-width: 1000px;
        background-image: url("images/register image.jpg");
        mix-blend-mode: multiply;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        border-radius: 10px;
    }

    .register-form {
        flex: 1;
        padding: 1.25rem;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .register-form form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .register-form h2 {
        font-size: 24px;
        margin-bottom: 20px;
    }

    .register-form input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    input:focus {
        outline: none;
        border-color: #007a33;
    }

    .register-form button {
        width: 200px;
        background-color: #007a33;
        color: #fff;
        padding: 10px;
        margin: 10px 0 10px;
        font-size: 18px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .register-form button:hover {
        background-color: #005922;
    }

    .p1 {
        font-size: 0.875rem;
        color: black;
        margin: 10px 0;
    }

    .link {
        color: #006400;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .link:hover {
        color: #f09319;
    }

    /* Responsive design */
    @media (max-width: 480px) {
        .wrapper {
            width: 95%;
            padding: 10px;
        }

        .register-container {
            flex-direction: column;
            align-items: center;
            width: 100%;
            padding: 10px;
        }

        .plant-image {
            width: 100%;
            margin-bottom: 20px;
            height: 250px;
        }

        .register-form {
            width: 100%;
        }

        .register-form input {
            font-size: 14px;
            padding: 8px;
        }

        .register-form button {
            width: 150px;
            padding: 8px;
            font-size: 16px;
        }
    }

    @media (min-width: 481px) and (max-width: 1200px) {
        .wrapper {
            width: 85%;
            min-height: 60vh;
        }

        .register-container {
            width: 90%;
            padding: 15px;
        }

        .plant-image {
            width: 50%;
            min-height: 300px;
            background-size: contain;
            background-repeat: no-repeat;
        }

        .register-form {
            width: 50%;
        }
    }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="register-container">
            <div class="plant-image"></div>
            <div class="register-form">
                <h2>Register</h2>
                <form action="register.php" method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm_Password" required>
                    <input type="text" name="address" placeholder="Address" required>
                    <input type="text" name="contact" placeholder="Contact Number" required>
                    <button type="submit" name="register">Register</button>
                </form>
                <p class="p1">Already have an account?   <a class="link" href="login.php">Login here</a>.</p>
            </div>
        </div>
    </div>
</body>

</html>

<?php
include 'footer.php';
?>