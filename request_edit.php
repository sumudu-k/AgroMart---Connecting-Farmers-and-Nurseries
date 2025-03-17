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
        display: flex;
        justify-content: center;
        align-items: center;
        flex: 1;
        width: 100%;
        padding: 0 15px;

    }

    h2 {
        background-color: #dbffc7;
        text-align: center;
        padding: 10px 0;
        font-size: 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }

    .ad-form {
        min-width: 50%;
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

    .form-group label {
        flex: 0.2;
        width: 150px;
        text-align: right;
        padding-right: 10px;
        font-weight: bold;
    }

    input,
    select,
    textarea {
        flex: 0.8;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #007a33;
    }

    textarea {
        height: 200px;
    }

    /* Submit button */
    button {
        display: block;
        margin: 20px auto;
        padding: 10px 20px;
        background-color: #4caf50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #45a049;
    }

    /* Responsive styles */
    @media screen and (max-width: 480px) {
        h2 {
            font-size: 1.5rem;
            padding: 15px 5%;
        }

        .ad-form {
            min-width: 95%;
            padding: 15px;
        }

        .form-group {
            flex-direction: column;
            align-items: stretch;
        }

        .form-group label {
            width: auto;
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
        }

        button[type="submit"] {
            font-size: 14px;
            padding: 8px 15px;
        }
    }

    @media screen and (min-width: 481px) and (max-width: 1200px) {

        h2 {
            font-size: 1.8rem;
            padding: 20px 8%;
        }


        .ad-form {
            min-width: 80%;
        }

        .form-group {
            flex-direction: column;
            align-items: stretch;
        }

        .form-group label {
            width: auto;
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
        }

        button[type="submit"] {
            font-size: 15px;
            padding: 9px 18px;
        }
    }
    </style>
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