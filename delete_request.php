<?php
session_start();
ob_start();
include 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['request_id'])) {
    $request_id = $_GET['request_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM plant_requests WHERE request_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $request_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_sql = "DELETE FROM plant_requests WHERE request_id = ? AND user_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("ii", $request_id, $user_id);

        if ($stmt->execute()) {
            header("Location: my_requests.php");
            exit();
        } else {
            echo "<script>
                window.onload = function() {
                    showAlert('Error deleting request', 'error', '#ff0000', 'my_requests.php');
                };
            </script>";
        }
    } else {
        echo "<script>
            window.onload = function() {
                showAlert('Invalid request or you do not have permission!', 'error', '#ff0000', 'my_requests.php');
            };
        </script>";
    }
} else {
    echo "<script>
        window.onload = function() {
            showAlert('Invalid request!', 'error', '#ff0000', 'my_requests.php');
        };
    </script>";
}

$conn->close();
?>
<script src='alertFunction.js'></script>