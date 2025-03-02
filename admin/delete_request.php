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

    $sql = "DELETE FROM plant_requests WHERE request_id = '$request_id' AND user_id = '$user_id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: my_requests.php");
    } else {
        echo "Error deleting request: " . $conn->error;
    }
}