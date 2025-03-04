<?php
session_start();
include 'config.php';
include 'alertFunction.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $request_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM plant_requests WHERE request_id = '$request_id' AND user_id = '$user_id'";
    $result = $conn->query($sql);
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

    $update_sql = "UPDATE plant_requests SET 
                    subject='$subject', description='$description', 
                    contact='$contact', district='$district' 
                    WHERE request_id='$request_id' AND user_id='$user_id'";

    if ($conn->query($update_sql) === TRUE) {
        showAlert("Request updated successfully!", "success", "#008000", "my_requests.php");
    } else {
        showAlert("Error updating request", "error", "#ff0000", "request_edit.php?id=$request_id");
    }
}
?>

<form method="post">
    <label>Subject:</label>
    <input type="text" name="subject" value="<?= $row['subject'] ?>" required><br>

    <label>Description:</label>
    <textarea name="description" required><?= $row['description'] ?></textarea><br>

    <label>Contact:</label>
    <input type="text" name="contact" value="<?= $row['contact'] ?>" required><br>

    <label>District:</label>
    <select name="district">
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
    </select><br>

    <button type="submit">Update Request</button>
</form>