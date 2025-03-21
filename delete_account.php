<?php
session_start();
include 'config.php';
include 'navbar.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];

    // verify password
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        echo "<script>
        window.onload = function() {
            showAlert('Incorrect password. Account not deleted', 'error', '#ff0000');
        };
        setTimeout(function() {
            window.location.href = 'delete_account.php';
        }, 2000);
        </script>";
        exit();
    }

    // delete user
    $delete_stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $delete_stmt->bind_param("i", $user_id);

    if ($delete_stmt->execute()) {
        session_destroy();
        echo "<script>
        window.onload = function() {
            showAlert('Your account deleted', 'success', '#008000');
        };
        setTimeout(function() {
            window.location.href = 'home.php';
        }, 2000);
        </script>";
    } else {
        echo "<script>
        window.onload = function() {
            showAlert('error deleting account', 'error', '#ff0000');
        };
        setTimeout(function() {
            window.location.href = 'profile.php';
        }, 2000);
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account - AgroMart</title>
    <link rel="stylesheet" href="css/delete_account.css">
</head>

<body>
    <div class="main-content">
        <h1>Delete My Account</h1>
        <div class="container">
            <form action="delete_account.php" method="post">
                <p>This action cannot be undone. Please confirm your password to proceed.</p>
                <div class="form-group">
                    <label for="password">Enter Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit">Delete Account</button>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src='alertFunction.js'></script>
</body>

</html>