<?php
session_start();
ob_start();
include 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

function isValidContact($contact_number)
{
    return preg_match('/^0\d{9}$/', $contact_number);
}

if (isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $contact = $_POST['contact'];
    $district = $_POST['district'];

    $_SESSION['u-subject'] = $subject;
    $_SESSION['u-description'] = $description;
    $_SESSION['u-contact'] = $contact;

    if (empty($subject) || empty($description) || empty($contact) || empty($district)) {
        echo "<script>
            window.onload = function() {
                showAlert('All fields are required!', 'error', '#ff0000');
            };
        </script>";
    } elseif (strlen($subject) < 20) {
        echo "<script>
            window.onload = function() {
                showAlert('Subject is too short!', 'error', '#ff0000');
            };
        </script>";
    } elseif (strlen($subject) > 150) {
        echo "<script>
            window.onload = function() {
                showAlert('Subject is too long!', 'error', '#ff0000');
            };
        </script>";
    } elseif (strlen($description) < 20) {
        echo "<script>
            window.onload = function() {
                showAlert('description is too short!', 'error', '#ff0000');
            };
        </script>";
    } elseif (strlen($description) > 700) {
        echo "<script>
            window.onload = function() {
                showAlert('Description is too long!', 'error', '#ff0000');
            };  
        </script>";
    } elseif (!isValidContact($contact)) {
        echo "<script>
            window.onload = function() {
                showAlert('Invalid Contact Number!', 'error', '#ff0000');
            };
        </script>";
    } else {
        $sql = "INSERT INTO plant_requests (user_id, subject, description, contact, district) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $user_id, $subject, $description, $contact, $district);

        if ($stmt->execute()) {
            echo "<script>
                window.onload = function() {
                    showAlert('Request Created Successfully!', 'success', 'green');
                    setTimeout(function() {
                        window.location.href = 'my_requests.php';
                    }, 2000);
                };
            </script>";
            unset($_SESSION['u-subject'], $_SESSION['u-description'], $_SESSION['u-contact']);
        } else {
            echo "<script>
                window.onload = function() {
                    showAlert('An Error Creating Request!', 'error', 'red');
                };
            </script>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Request</title>
    <script src='alertFunction.js'></script>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        position: relative;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("images/B1.jpg");
        background-size: cover;
        opacity: 0.2;
        z-index: -1;
    }

    .main-content {
        flex: 1;
    }

    h1 {
        background-color: #dbffc7;
        text-align: center;
        padding: 10px 0;
        font-size: 2rem;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }

    /* form container */
    .ad-form {
        max-width: 50%;
        margin: 20px auto;
        padding: 20px;
        background-color: rgba(196, 196, 196, 0.3);
        backdrop-filter: blur(10px);
        border-radius: 5px;
        position: relative;
        z-index: 1;
    }

    .form-group {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    label {
        flex: 0.5;
        font-size: 1rem;
        text-align: right;
        padding-right: 20px;
        font-weight: bold;
    }

    input,
    select,
    textarea {
        flex: 1.5;
        font-size: 1rem;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 100%;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #007a33;
    }

    textarea {
        resize: vertical;
        min-height: 150px;
    }

    input::placeholder,
    textarea::placeholder {
        font-size: 1rem;
        font-style: italic;
        letter-spacing: 0.5px;
        font-weight: 500;
    }

    button {
        background-color: #007a33;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        display: block;
        margin: 20px auto 0;
        transition: background-color 0.2s;
    }

    button:hover {
        background-color: #005922;
    }

    /* Mobile Devices */
    @media screen and (max-width: 480px) {
        h1 {
            padding: 15px 5%;
            font-size: 1.5rem;
        }

        .ad-form {
            padding: 15px;
            max-width: 90%;
        }

        .form-group {
            flex-direction: column;
            align-items: stretch;
            margin-bottom: 10px;
        }

        label {
            text-align: left;
            padding-right: 0;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        input,
        select,
        textarea {
            font-size: 0.9rem;
            padding: 6px;
            margin-bottom: 10px;
        }

        input::placeholder,
        textarea::placeholder {
            font-size: 0.9rem;
        }

        button {
            padding: 8px 15px;
            font-size: 14px;
        }
    }

    /* Tablets */
    @media screen and (min-width: 481px) and (max-width: 1200px) {
        h1 {
            padding: 20px 8%;
            font-size: 1.8rem;
        }

        .ad-form {
            max-width: 80%;
        }

        .form-group {
            flex-direction: column;
            align-items: stretch;
            margin-bottom: 20px;
        }

        label {
            text-align: left;
            padding-right: 0;
            margin-bottom: 5px;
            font-size: 0.95rem;
        }

        input,
        select,
        textarea {
            font-size: 0.95rem;
            padding: 7px;
            margin-bottom: 10px;
        }

        button {
            padding: 9px 18px;
            font-size: 15px;
        }
    }
    </style>
</head>

<body>
    <div class="main-content">
        <h1>Post a Request </h1>
        <form method="post" class="ad-form">
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" name="subject" id="subject" placeholder="Enter subject here"
                    value="<?= isset($_SESSION['u-subject']) ? htmlspecialchars($_SESSION['u-subject']) : '' ?>">
            </div>
            <span>Maximum Allowed Characters 150</span>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description"
                    placeholder="Describe your request here"><?= isset($_SESSION['u-description']) ? htmlspecialchars($_SESSION['u-description']) : '' ?></textarea>

            </div>
            <span>Maximum Allowed Characters 700</span>

            <div class="form-group">
                <label for="contact">Contact Number</label>
                <input type="text" name="contact" id="contact" placeholder="Enter 10 digit number"
                    value="<?= isset($_SESSION['u-contact']) ? htmlspecialchars($_SESSION['u-contact']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="district">District</label>
                <select name="district">
                    <option value="">Select District</option>
                    <option value="Ampara">Ampara</option>
                    <option value="Anuradhapura">Anuradhapura</option>
                    <option value="Badulla">Badulla</option>
                    <option value="Batticaloa">Batticaloa</option>
                    <option value="Colombo">Colombo</option>
                    <option value="Galle">Galle</option>
                    <option value="Gampaha">Gampaha</option>
                    <option value="Hambantota">Hambantota</option>
                    <option value="Jaffna">Jaffna</option>
                    <option value="Kalutara">Kalutara</option>
                    <option value="Kandy">Kandy</option>
                    <option value="Kegalle">Kegalle</option>
                    <option value="Kilinochchi">Kilinochchi</option>
                    <option value="Kurunegala">Kurunegala</option>
                    <option value="Mannar">Mannar</option>
                    <option value="Matale">Matale</option>
                    <option value="Matara">Matara</option>
                    <option value="Monaragala">Monaragala</option>
                    <option value="Mullaitivu">Mullaitivu</option>
                    <option value="Nuwara Eliya">Nuwara Eliya</option>
                    <option value="Polonnaruwa">Polonnaruwa</option>
                    <option value="Puttalam">Puttalam</option>
                    <option value="Ratnapura">Ratnapura</option>
                    <option value="Trincomalee">Trincomalee</option>
                    <option value="Vavuniya">Vavuniya</option>
                </select>
            </div>

            <button type="submit" name="submit">Submit Request</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
<?php
$conn->close();
?>