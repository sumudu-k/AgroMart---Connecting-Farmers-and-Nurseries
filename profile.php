<?php
ob_start();
session_start();
include 'config.php';
include 'navbar.php';
include 'alertFunction.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

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

    if ($email !== $userData['email']) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND user_id != ?");
        $stmt->bind_param('si', $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            showAlert('Email already exists. Please use another email.', 'error', '#ff0000', 'profile.php');
        } else {
            $update_sql = "UPDATE users SET username=?, email=?, contact_number=?, address=? WHERE user_id=?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssisi", $username, $email, $contact_number, $address, $user_id);

            if ($stmt->execute()) {
                showAlert('Profile updated successfully!', 'success', '#008000', 'profile.php');
            } else {
                showAlert('Error updating profile.', 'error', '#ff0000', 'profile.php');
            }
        }
    } else {
        $update_sql = "UPDATE users SET username=?, email=?, contact_number=?, address=? WHERE user_id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssisi", $username, $email, $contact_number, $address, $user_id);

        if ($stmt->execute()) {
            showAlert('Profile updated successfully!', 'success', '#008000', 'profile.php');
        } else {
            showAlert('Error updating profile.', 'error', '#ff0000', 'profile.php');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
        margin: 0 0 30px 0;
        text-transform: capitalize;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    h2 {
        font-size: 1.8rem;
        margin: 30px 0;
    }

    .container {
        width: 75%;
        margin: 0 auto;
        padding: 20px;
    }

    form {
        background-color: #e1e1e1;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 0 auto;
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

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="number"] {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.3s ease;
    }

    .form-group input[type="text"]:focus,
    .form-group input[type="email"]:focus,
    .form-group input[type="number"]:focus {
        border-color: #f09319;
    }

    .form-group input[type="number"]::-webkit-inner-spin-button,
    .form-group input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .updateBtn {
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

    .updateBtn:hover {
        background-color: #cb790d;
    }

    .accountSettings {
        list-style: none;
        padding: 0;
        max-width: 600px;
        margin: 20px auto 0;
        border-radius: 10px;
    }

    .accountSettings h2 {
        text-align: center;
        margin: 20px 0;
    }

    .accountSettingsBtn {
        display: flex;
        justify-content: center;
        align-items: center;

    }

    .accountSettingsBtn button {
        background-color: #28a745;
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        margin: 10px;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .accountSettingsBtn button a {
        text-decoration: none;
        color: #fff;
        font-size: 1rem;
    }




    /* Responsive Styles */
    /* Mobile Devices */
    @media screen and (max-width: 480px) {
        h1 {
            font-size: 1.5rem;
            padding: 15px 5%;
        }

        h2 {
            font-size: 1.3rem;
            padding: 15px 5%;
        }

        .container {
            width: 95%;
            padding: 10px;
        }

        form {
            padding: 15px;
        }

        .form-group label {
            font-size: 0.9rem;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="number"] {
            font-size: 0.9rem;
            padding: 6px;
        }

        .updateBtn {
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

        h2 {
            font-size: 1.5rem;
            padding: 20px 8%;
        }

        .container {
            width: 95%;
        }

        form {
            padding: 20px;
        }

        .form-group label {
            font-size: 0.95rem;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="number"] {
            font-size: 0.95rem;
            padding: 7px;
        }
    }
    </style>
</head>

<body>
    <h1>Edit Profile</h1>
    <div class="container">

        <form action="profile.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" required name="username"
                    value="<?= htmlspecialchars($userData['username']) ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" required name="email"
                    value="<?= htmlspecialchars($userData['email']) ?>">
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="number" id="contact_number" required name="contact_number"
                    value="<?= htmlspecialchars($userData['contact_number']) ?>">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" required name="address"
                    value="<?= htmlspecialchars($userData['address']) ?>">
            </div>
            <button class="updateBtn" type="submit" name="update">Update</button>
        </form>

        <div class="accountSettings">
            <h2>Account Settings</h2>
            <div class="accountSettingsBtn">
                <button><a href="change_password.php">Change Password</a></button>
                <button><a href="delete_account.php">Delete My Account</a></button>
            </div>
        </div>

    </div>
</body>

</html>
<?php
include 'footer.php';
?>