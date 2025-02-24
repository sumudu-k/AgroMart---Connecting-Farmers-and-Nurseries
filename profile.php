<?php
session_start();
include 'config.php';


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
print_r($userData);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>

<body>
    <form>
        <table>
            <thead>
                <th>A</th>
                <th>B</th>
            </thead>
            <tbody>
                <tr>
                    <td><label>Username</label></td>
                    <td><input type='text' value=<?= $userData['username'] ?>></td>
                </tr>

                <tr>
                    <td><label>Email</label></td>
                    <td><input type='text' value=<?= $userData['email'] ?>></td>
                </tr>

                <tr>
                    <td><label>New Password</label></td>
                    <td><input type='text'></td>
                </tr>

                <tr>
                    <td><label>Confirm Password</label></td>
                    <td><input type='text'></td>
                </tr>


                <tr>
                    <td><label>Contact Number</label></td>
                    <td><input type='number' value=<?= $userData['contact_number'] ?>></td>
                </tr>

                <tr>
                    <td><label>Address</label></td>
                    <td><input type='text' value=<?= $userData['address'] ?>></td>
                </tr>

            </tbody>
        </table>
    </form>
</body>

</html>