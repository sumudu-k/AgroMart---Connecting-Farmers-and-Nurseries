<?php
session_start();
ob_start();
include 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// check user blocked or not
$user_id = $_SESSION['user_id'];

$sql_block_ckeck = "SELECT * FROM users WHERE user_id=?";
$stmt_block_ckeck = $conn->prepare($sql_block_ckeck);
$stmt_block_ckeck->bind_param('i', $user_id);
$stmt_block_ckeck->execute();
$result_block_check = $stmt_block_ckeck->get_result();
$block_result = $result_block_check->fetch_assoc();

if ($block_result['status'] == 'y') {
    echo "<script>
        window.onload = function() {
            showAlert('Your account has been blocked. You can not post product requests', 'error', '#ff0000');
        };
        setTimeout(function() {
            window.location.href = 'profile.php';
        }, 2000);
        </script>";
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
    <link rel="stylesheet" href="css/post_request.css">
</head>

<body>
    <div class="main-content">
        <h1>Post a Request </h1>
        <form method="post" class="ad-form">
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" name="subject" id="subject" placeholder="Enter subject here. Max 150 characters"
                    value="<?= isset($_SESSION['u-subject']) ? htmlspecialchars($_SESSION['u-subject']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description"
                    placeholder="Describe your request here. Max 700 characters"><?= isset($_SESSION['u-description']) ? htmlspecialchars($_SESSION['u-description']) : '' ?></textarea>
            </div>


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

            <?php
            if ($block_result['status'] == 'y'):
            ?>
            <button disabled type="submit" name="submit">Submit Request</button>
            <?php else:
            ?>
            <button type="submit" name="submit">Submit Request</button>
            <?php endif; ?>


        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
<?php
$conn->close();
?>