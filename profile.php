<?php
ob_start();
session_start();
include 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

function isValidContact($contact_number)
{
    return preg_match('/^0\d{9}$/', $contact_number);
}

$sql = 'SELECT * FROM users WHERE user_id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if (isset($_POST['update'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    if (empty($username) || empty($email) || empty($contact_number) || empty($address)) {
        echo "<script>
            showAlert('Please fill in all fields.', 'error', '#ff0000');
        </script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
            showAlert('Please enter a valid email.', 'error', '#ff0000');
        </script>";
    } elseif (!isValidContact($contact_number)) {
        echo "<script>
            showAlert('Please enter a valid contact number.', 'error', '#ff0000');
        </script>";
    } elseif ($email !== $userData['email']) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param('si', $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "<script>
                showAlert('Email already in use.', 'error', '#ff0000');
            </script>";
        } else {
            $update_sql = "UPDATE users SET username=?, email=?, contact_number=?, address=? WHERE user_id=?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssssi", $username, $email, $contact_number, $address, $user_id);

            if ($stmt->execute()) {
                echo "<script>
                    showAlert('Profile updated successfully!', 'success', '#008000');
                    setTimeout(function() {
                        window.location.href = 'profile.php';
                    }, 2000);
                </script>";
            } else {
                echo "<script>
                    showAlert('Error updating profile.', 'error', '#ff0000');
                </script>";
            }
        }
    } else {
        $update_sql = "UPDATE users SET username=?, email=?, contact_number=?, address=? WHERE user_id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssi", $username, $email, $contact_number, $address, $user_id);

        if ($stmt->execute()) {

            echo "<script>
                showAlert('Profile updated successfully!', 'success', '#008000');
                setTimeout(function() {
                    window.location.href = 'profile.php';
                }, 2000);
            </script>";
        } else {
            echo "<script>
                showAlert('Error updating profile.', 'error', '#ff0000');
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
    <title>Profile - AgroMart</title>
    <link rel="stylesheet" href="css/profile.css">
    
    <script src='alertFunction.js'></script>
</head>

<body>
    <div class="main-content">
        <h1>My Account</h1>
        <div class='accountFunctionBtn'>
            <button><a href="my_ads.php">My Ads</a></button>
            <button><a href="post_request.php">Post a Product Request</a></button>
            <button><a href="my_requests.php">My Product Requests</a></button>
        </div>

        <div class="container">
            <h2>Edit Profile</h2>
            <form action="profile.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username"
                        value="<?= htmlspecialchars($userData['username']) ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>">
                </div>
                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number"
                        value="<?= htmlspecialchars($userData['contact_number']) ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address"
                        value="<?= htmlspecialchars($userData['address']) ?>">
                </div>
                <button class="updateBtn" type="submit" name="update">Update</button>
            </form>

            <h2>Account Settings</h2>
            <div class="accountSettings">
                <div class="accountSettingsBtn">
                    <button><a href="change_password.php">Change Password</a></button>
                    <button class='delete'><a href="delete_account.php">Delete My Account</a></button>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>