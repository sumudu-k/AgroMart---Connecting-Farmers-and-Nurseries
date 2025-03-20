<?php
session_start();
include 'config.php';
include 'navbar.php';

function isValidContact($contact)
{
    return preg_match('/^0\d{9}$/', $contact);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $request_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM plant_requests WHERE request_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $request_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "<script>
        window.onload = function() {
            showAlert('Invalid request!', 'error', '#ff0000', 'my_requests.php');
        };
        </script>";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $contact = $_POST['contact'];
    $district = $_POST['district'];

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

        $update_sql = "UPDATE plant_requests SET subject = ?, description = ?, contact = ?, district = ? WHERE request_id = ? AND user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssii", $subject, $description, $contact, $district, $request_id, $user_id);

        if ($stmt->execute()) {
            echo "<script>
        window.onload = function() {
            showAlert('Request updated successfully!', 'success', '#008000', 'my_requests.php');
        };
        setTimeout(function() {
            window.location.href = 'my_requests.php';
        }, 2000);
        </script>";
        } else {
            echo "<script>
        window.onload = function() {
            showAlert('Error updating request', 'error', '#ff0000', 'request_edit.php?id=$request_id');
        };
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
    <title>Edit Request - AgroMart</title>
    <link rel="stylesheet" href="css/request_edit.css">
</head>

<body>
    <h2>Edit Your Request</h2>
    <div class="main-content">
        <form method="post" class="ad-form">
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" name="subject" id="subject" value="<?= htmlspecialchars($row['subject']) ?>">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description"><?= htmlspecialchars($row['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="contact">Contact</label>
                <input type="text" name="contact" id="contact" value="<?= htmlspecialchars($row['contact']) ?>">
            </div>

            <div class="form-group">
                <label for="district">District</label>
                <select name="district">
                    <option value="Ampara" <?= $row['district'] == 'Ampara' ? 'selected' : '' ?>>Ampara</option>
                    <option value="Anuradhapura" <?= $row['district'] == 'Anuradhapura' ? 'selected' : '' ?>>
                        Anuradhapura
                    </option>
                    <option value="Badulla" <?= $row['district'] == 'Badulla' ? 'selected' : '' ?>>Badulla</option>
                    <option value="Batticaloa" <?= $row['district'] == 'Batticaloa' ? 'selected' : '' ?>>Batticaloa
                    </option>
                    <option value="Colombo" <?= $row['district'] == 'Colombo' ? 'selected' : '' ?>>Colombo</option>
                    <option value="Galle" <?= $row['district'] == 'Galle' ? 'selected' : '' ?>>Galle</option>
                    <option value="Gampaha" <?= $row['district'] == 'Gampaha' ? 'selected' : '' ?>>Gampaha</option>
                    <option value="Hambantota" <?= $row['district'] == 'Hambantota' ? 'selected' : '' ?>>Hambantota
                    </option>
                    <option value="Jaffna" <?= $row['district'] == 'Jaffna' ? 'selected' : '' ?>>Jaffna</option>
                    <option value="Kalutara" <?= $row['district'] == 'Kalutara' ? 'selected' : '' ?>>Kalutara</option>
                    <option value="Kandy" <?= $row['district'] == 'Kandy' ? 'selected' : '' ?>>Kandy</option>
                    <option value="Kegalle" <?= $row['district'] == 'Kegalle' ? 'selected' : '' ?>>Kegalle</option>
                    <option value="Kilinochchi" <?= $row['district'] == 'Kilinochchi' ? 'selected' : '' ?>>Kilinochchi
                    </option>
                    <option value="Kurunegala" <?= $row['district'] == 'Kurunegala' ? 'selected' : '' ?>>Kurunegala
                    </option>
                    <option value="Mannar" <?= $row['district'] == 'Mannar' ? 'selected' : '' ?>>Mannar</option>
                    <option value="Matale" <?= $row['district'] == 'Matale' ? 'selected' : '' ?>>Matale</option>
                    <option value="Matara" <?= $row['district'] == 'Matara' ? 'selected' : '' ?>>Matara</option>
                    <option value="Monaragala" <?= $row['district'] == 'Monaragala' ? 'selected' : '' ?>>Monaragala
                    </option>
                    <option value="Mullaitivu" <?= $row['district'] == 'Mullaitivu' ? 'selected' : '' ?>>Mullaitivu
                    </option>
                    <option value="Nuwara Eliya" <?= $row['district'] == 'Nuwara Eliya' ? 'selected' : '' ?>>Nuwara
                        Eliya
                    </option>
                    <option value="Polonnaruwa" <?= $row['district'] == 'Polonnaruwa' ? 'selected' : '' ?>>Polonnaruwa
                    </option>
                    <option value="Puttalam" <?= $row['district'] == 'Puttalam' ? 'selected' : '' ?>>Puttalam</option>
                    <option value="Ratnapura" <?= $row['district'] == 'Ratnapura' ? 'selected' : '' ?>>Ratnapura
                    </option>
                    <option value="Trincomalee" <?= $row['district'] == 'Trincomalee' ? 'selected' : '' ?>>Trincomalee
                    </option>
                    <option value="Vavuniya" <?= $row['district'] == 'Vavuniya' ? 'selected' : '' ?>>Vavuniya</option>
                </select>
            </div>

            <button type="submit">Update Request</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
    <script src='alertFunction.js'></script>
</body>

</html>
<?php
$conn->close();
?>