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

$sql = 'SELECT * FROM users WHERE user_id =? ';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if (isset($_POST['update'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    if ($email !== $userData['email']) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param('si', $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "Email already exists. Please use another email.";
            return;
        }
    }

    if (!empty($password) || !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            echo "Passwords do not match.";
            return;
        }
        $hashpassword = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $hashpassword = $userData['password'];
    }

    $update_sql = "UPDATE users SET username=?, email=?, password=?, contact_number=?, address=? WHERE user_id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssisi", $username, $email, $hashpassword, $contact_number, $address, $user_id);

    if ($stmt->execute()) {
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile.";
    }
}

if (isset($_POST['delete_account'])) {
    $entered_password = $_POST['delete_password'];

    if (password_verify($entered_password, $userData['password'])) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            session_destroy();
            header("Location: register.php");
            exit();
        } else {
            echo "Error deleting account.";
        }
    } else {
        echo "Incorrect password. Account not deleted.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>

<body>
    <h1>Edit Profile</h1>
    <form action="profile.php" method="post">
        <table>
            <tbody>
                <tr>
                    <td><label>Username</label></td>
                    <td><input type='text' required name="username" value="<?= $userData['username'] ?>"></td>
                </tr>
                <tr>
                    <td><label>Email</label></td>
                    <td><input type='email' name="email" required value="<?= $userData['email'] ?>"></td>
                </tr>
                <tr>
                    <td><label>New Password</label></td>
                    <td><input type='password' name="password"></td>
                </tr>
                <tr>
                    <td><label>Confirm Password</label></td>
                    <td><input type='password' name="confirm_password"></td>
                </tr>
                <tr>
                    <td><label>Contact Number</label></td>
                    <td><input type='number' required name="contact_number" value="<?= $userData['contact_number'] ?>">
                    </td>
                </tr>
                <tr>
                    <td><label>Address</label></td>
                    <td><input type='text' required name="address" value="<?= $userData['address'] ?>"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit" name="update">Update</button></td>
                </tr>
            </tbody>
        </table>
    </form>

    <h2>Delete My Account</h2>
    <p>This action cannot be undone. Please confirm your password to proceed with account deletion.</p>
    <form action="profile.php" method="post">
        <label for="delete_password">Enter Password:</label>
        <input type="password" name="delete_password" required>
        <button type="submit" name="delete_account">Delete Account</button>
    </form>
</body>

</html>