<?php
session_start();
include 'config.php';
include 'navbar.php'; 
include 'alertFunction.php';

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
        showAlert("Invalid request!", "error", "#ff0000", "my_requests.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $contact = $_POST['contact'];
    $district = $_POST['district'];

    $update_sql = "UPDATE plant_requests SET subject = ?, description = ?, contact = ?, district = ? WHERE request_id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssii", $subject, $description, $contact, $district, $request_id, $user_id);

    if ($stmt->execute()) {
        showAlert("Request updated successfully!", "success", "#008000", "my_requests.php");
    } else {
        showAlert("Error updating request", "error", "#ff0000", "request_edit.php?id=$request_id");
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
                <input type="text" name="subject" id="subject" value="<?= htmlspecialchars($row['subject']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" required><?= htmlspecialchars($row['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="contact">Contact</label>
                <input type="text" name="contact" id="contact" value="<?= htmlspecialchars($row['contact']) ?>" required>
            </div>

            <div class="form-group">
                <label for="district">District</label>
                <select name="district" id="district">
                    <?php
                    $districts = [
                        "Colombo",
                        "Gampaha",
                        "Kalutara",
                        "Kandy",
                        "Matale",
                        "Nuwara Eliya",
                        "Galle",
                        "Matara",
                        "Hambantota",
                        "Jaffna",
                        "Kilinochchi",
                        "Mannar",
                        "Mullaitivu",
                        "Vavuniya",
                        "Trincomalee",
                        "Batticaloa",
                        "Ampara",
                        "Kurunegala",
                        "Puttalam",
                        "Anuradhapura",
                        "Polonnaruwa",
                        "Badulla",
                        "Monaragala",
                        "Ratnapura",
                        "Kegalle"
                    ];
                    foreach ($districts as $district) {
                        $selected = ($district == $row['district']) ? "selected" : "";
                        echo "<option value='$district' $selected>$district</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit">Update Request</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
<?php
$conn->close();
?>