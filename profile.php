<?php
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

    $hashpassword = password_hash($password, PASSWORD_DEFAULT);


    if ($password != $confirm_password) {
        echo "password does not match";
        exit;
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

<h1>Edit Profile</h1>

<script>
function updateProfile() {

}
</script>


<body>
    <form action="profile.php?user_id=" method="post">
        <table>
            <tbody>
                <tr>
                    <td><label>Username</label></td>
                    <td><input type='text' required name="username" value=<?= $userData['username']  ?>></td>
                </tr>

                <tr>
                    <td><label>Email</label></td>
                    <td><input type='text' name="email" required value=<?= $userData['email'] ?>></td>
                </tr>

                <tr>
                    <td><label>New Password</label></td>
                    <td><input type='text' required name="password"></td>
                </tr>

                <tr>
                    <td><label>Confirm Password</label></td>
                    <td><input type='text' required name="confirm_password"></td>
                </tr>


                <tr>
                    <td><label>Contact Number</label></td>
                    <td><input type='number' required name="contact_number" value=<?= $userData['contact_number'] ?>>
                    </td>
                </tr>

                <tr>
                    <td><label>Address</label></td>
                    <td><input type='text' required name="address" value=<?= $userData['address'] ?>></td>
                </tr>

                <tr>
                    <td></td>
                    <td><button name="update" onClick="updateProfile">Update</button></td>
                </tr>

            </tbody>
        </table>
    </form>

    <label>Delete my profile</label><br>
    <span> This action can not be undo. Please make sure are you want to delete your account. All posted advertisemets
        will remove associated with you </span><br>
    <button> Delete Account</button>
</body>

</html>