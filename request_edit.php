<?php
session_start();
include 'config.php';

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
        echo "Invalid request!";
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
        header("Location: my_requests.php");
    } else {
        echo "Error updating request: " . $conn->error;
    }
}
?>