<?php
session_start();
include 'config.php';

function isValidPassword($password)
{
    return preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/', $password);
}

if (!isset($_SESSION['user_id'])) {
    echo "<script>
    window.onload = function() {
        showAlert('You must log in first!', 'error', '#ff0000');
        };
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['submit'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo "<script>
        window.onload = function() {
            showAlert('Please fill in all fields.', 'error', '#ff0000');
        };
        </script>";
    } elseif (!isValidPassword($new_password)) {
        echo "<script>
        window.onload = function() {
            showAlert('Password must contain at least 8 characters, one uppercase letter, one number and one special character.', 'error', '#ff0000');
        };
        </script>";
    } else {

        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!password_verify($current_password, $user['password'])) {
            echo "<script>
    window.onload = function() {
        showAlert('Your current password is incorrect.', 'error', '#ff0000');
    };
</script>";
        } elseif ($new_password !== $confirm_password) {
            echo "<script>
        window.onload = function() {
            showAlert('New passwords do not match.', 'error', '#ff0000');
            };
        </script>";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $update_stmt->bind_param("si", $hashed_password, $user_id);

            if ($update_stmt->execute()) {
                echo "<script>
            window.onload = function() {
                showAlert('Password updated successfully!', 'success', '#008000');
                };
                setTimeout(function() {
                    window.location.href = 'profile.php';
                }, 2000);
            </script>";
            } else {
                echo "<script>
            window.onload = function() {
                showAlert('An error occurred while updating your password.', 'error', '#ff0000');
                };
            </script>";
            }
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
    <link rel="stylesheet" href="css/change_password.css">
    
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="main-content">
        <h1>Change Password</h1>

        <!-- password change container -->
        <div class="container">
            <form action="change_password.php" method="post">
                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password"
                        placeholder="Enter current password">
                </div>
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password">
                    <span> <i class="fa fa-exclamation-circle" aria-hidden="true" id="icon"></i> Password must contain
                        at least 8
                        characters, one uppercase
                        letter, one number and one special
                        character.</span>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                        placeholder="Confirm new password">
                </div>
                <button type="submit" name='submit'>Change Password</button>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src='alertFunction.js'></script>
</body>

</html>