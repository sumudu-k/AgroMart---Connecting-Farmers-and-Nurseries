<?php
//session_start();
ob_start();
include 'config.php';
include 'navbar.php';


// Check if the form is submitted
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $confirm_email = $_POST['confirm_email'];
    $contact_number = $_POST['contact'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    // Validate email confirmation
    if ($email != $confirm_email) {
        echo "Emails do not match!";
        exit;
    }

    // Check if the user already exists
    $user_check_sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($user_check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email already registered!";
    } else {
        // Insert the new user into the database
        $sql = "INSERT INTO users (username, email, password, contact_number, address) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $email, $password, $contact_number, $address);

        if ($stmt->execute()) {
            // Get the inserted user's ID
            $user_id = $stmt->insert_id;

            // Set session variables to log the user in
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user_id;
            header("Location: home.php");
            exit;
        } else {
            echo "Error registering user.";
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

        /* Register Container Styling */
        .register-container {
            display: flex;
            gap: 20px;
            background-color: #e2e6eb;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1200px;
        }

        .plant-image {
            flex: 1;
            background-image: url("images/register_image.png");
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            min-height: 300px;
        }

        .register-form {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            width: 100%;
            background-color: #007a33;
            color: #fff;
            padding: 10px;
            margin: 20px 0 10px;
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
            font-size: 14px;
            color: black;
            margin: 10px 0;
        }

        .link {
            text-decoration: none;
            color: #0917ee;
            font-weight: bold;
        }

        .link:hover {
            text-decoration: underline;
        }

        /* responsive design */
        @media (max-width: 480px) {
            .register-container {
                flex-direction: column;
                align-items: center;
                width: 100%;
                padding: 10px;
            }

            .plant-image {
                width: 100%;
                height: 200px;
                min-height: 200px;
                margin-bottom: 20px;
            }

            .register-form {
                width: 100%;
            }
        }


        @media (min-width: 481px) and (max-width: 1200px) {
            .register-container {
                flex-direction: row;
                width: 90%;
                padding: 15px;
            }

            .plant-image {
                width: 50%;
                min-height: 300px;
            }

            .register-form {
                width: 50%;
            }
        }


        @media (min-width: 1201px) {
            .register-container {
                flex-direction: row;
                width: 70%;
                gap: 30px;
            }

            .plant-image {
                width: 45%;
                min-height: 400px;
            }

            .register-form {
                width: 55%;
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
                    <input type="text" name="username" placeholder="Username" required><br>
                    <input type="email" name="email" placeholder="Email" required><br>
                    <input type="email" name="confirm_email" placeholder="Confirm Email" required><br>
                    <input type="text" name="contact" placeholder="Contact Number" required><br>
                    <input type="text" name="address" placeholder="Address" required><br>
                    <input type="password" name="password" placeholder="Password" required><br>
                    <button type="submit" name="register">Register</button>
                </form>
                <p class="p1">Already have an account? &ensp; <a class="link" href="login.php">Login here</a>.</p>
            </div>
        </div>
    </div>
</body>

</html>

<?php
include 'footer.php';
?>