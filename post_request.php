<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $contact = $_POST['contact'];
    $district = $_POST['district'];

    $sql = "INSERT INTO plant_requests (user_id, subject, description, contact, district) 
            VALUES ('$user_id', '$subject', '$description', '$contact', '$district')";

    if ($conn->query($sql) === TRUE) {
        echo "Request submitted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="post">
    <label>Subject:</label>
    <input type="text" name="subject" required><br>

    <label>Description:</label>
    <textarea name="description" required></textarea><br>

    <label>Contact Number:</label>
    <input type="text" name="contact" required><br>

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
            echo "<option value='$district'>$district</option>";
        }
        ?>
    </select><br>

    <button type="submit">Submit Request</button>
</form>