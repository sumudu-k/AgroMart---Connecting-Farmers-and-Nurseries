<?php
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];
    $link = !empty($_POST['link']) ? $_POST['link'] : NULL;
    $image = NULL;

    // Upload the image if provided
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $imagePath = $targetDir . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], "../" . $imagePath)) {
            $image = $imagePath;
        } else {
            $image = NULL;
        }
    }

    // Fetch all users from the users table
    $sql = "SELECT user_id FROM users";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Loop through all users and insert the notification for each
        while ($row = $result->fetch_assoc()) {
            $user_id = $row['user_id'];
            
            // Insert notification for each user
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, link, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $message, $link, $image);

            if (!$stmt->execute()) {
                echo "<script>alert('Error sending notification to user ID: $user_id');</script>";
                continue; // Proceed to the next user if there is an error
            }
        }
        
        echo "<script>alert('Notification sent successfully to all users!'); window.location='admin_send_notification.php';</script>";
    } else {
        echo "<script>alert('No users found!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin - Send Notification</title>
</head>

<body>
    <h2>Send Notification</h2>
    <form method="post" enctype="multipart/form-data">
        <textarea name="message" required placeholder="Enter notification message"></textarea><br>
        <input type="text" name="link" placeholder="Optional Link"><br>
        <input type="file" name="image" accept="image/*"><br>
        <button type="submit">Send Notification</button>
    </form>
</body>

</html>