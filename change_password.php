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
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Poppins", Arial, sans-serif;
        position: relative;
        overflow-x: hidden;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* background image */
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

    h1 {
        background-color: #dbffc7;
        text-align: center;
        padding: 10px 12.5%;
        font-size: 2rem;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }

    .main-content {
        flex: 1;
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
        position: relative;
        z-index: 1;
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

    #icon {
        color: rgb(114, 114, 114);
        margin: 7px 7px 7px 0px;
    }

    span {
        font-size: 0.8rem;
        color: rgb(114, 114, 114);
        align-self: flex-start;
        margin-bottom: 10px;
    }

    /* mobile Devices */
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

    /* tablets */
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
    <?php include 'navbar.php'; ?>
    <div class="main-content">
        <h1>Change Password</h1>
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