<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch user's current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!password_verify($current_password, $user['password'])) {
        echo "<script>alert('Current password is incorrect.');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('New passwords do not match.');</script>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $hashed_password, $user_id);

        if ($update_stmt->execute()) {
            echo "<script>alert('Password updated successfully!'); window.location.href='profile.php';</script>";
        } else {
            echo "<script>alert('Error updating password.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - AgroMart</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            overflow-x: hidden;
        }

        h1 {
            background-color: #dbffc7;
            text-align: center;
            padding: 20px;
            font-size: 2.2rem;
            color: #006400;
            margin: 0 0 30px 0;
            text-transform: capitalize;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 90px);
        }

        form {
            background-color: #e1e1e1;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 1rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .form-group input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .form-group input[type="password"]:focus {
            border-color: #f09319;
        }

        .form-group input[type="password"]::placeholder {
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
            display: block;
            margin: 20px auto 0;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #cb790d;
        }

        /* Mobile Devices */
        @media screen and (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
                padding: 15px 5%;
            }

            .container {
                width: 95%;
                padding: 10px;
                min-height: calc(100vh - 70px);
            }

            form {
                padding: 15px;
            }

            .form-group label {
                font-size: 0.9rem;
            }

            .form-group input[type="password"] {
                font-size: 0.9rem;
                padding: 6px;
            }

            button {
                font-size: 0.9rem;
                padding: 8px 15px;
            }
        }

        /* Tablets*/
        @media screen and (min-width: 481px) and (max-width: 1200px) {
            h1 {
                font-size: 1.8rem;
                padding: 20px 8%;
            }

            .container {
                width: 95%;
                padding: 15px;
            }

            form {
                padding: 20px;
            }

            .form-group label {
                font-size: 0.95rem;
            }

            .form-group input[type="password"] {
                font-size: 0.95rem;
                padding: 7px;
            }

            button {
                font-size: 0.95rem;
                padding: 9px 18px;
            }
        }
    </style>
</head>

<body>
    <h1>Change Password</h1>
    <div class="container">
        <form action="change_password.php" method="post">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password"
                    placeholder="Enter current password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password"
                    required>
            </div>
            <button type="submit">Change Password</button>
        </form>
    </div>
</body>

</html>
<?php
include 'footer.php';
?>