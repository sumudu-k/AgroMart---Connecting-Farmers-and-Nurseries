<?php
session_start();
include '../config.php';
include '../alertFunction.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];
    $link = !empty($_POST['link']) ? $_POST['link'] : NULL;
    $image = NULL;


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


    $sql = "SELECT user_id FROM users";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            $user_id = $row['user_id'];


            $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, link, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $message, $link, $image);

            if (!$stmt->execute()) {
                echo "<script>alert('Error sending notification to user ID: $user_id');</script>";
                continue;
            }
        }

        echo "<script>alert('Notification sent successfully to all users!'); window.location='admin_send_notification.php';</script>";
    } else {
        echo "<script>alert('No users found!');</script>";
    }
}


ob_start();
?>

<style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    textarea,
    input[type="text"],
    input[type="file"] {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        width: 100%;
        box-sizing: border-box;
    }

    textarea {
        resize: vertical;
        height: 100px;
    }

    button {
        background-color: #007a33;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #45a049;
    }

    @media screen and (max-width: 768px) {
        .container {
            padding: 15px;
        }

        textarea,
        input[type="text"],
        input[type="file"],
        button {
            font-size: 14px;
        }
    }
</style>

<div class="container">
    <h2>Send Notification</h2>
    <form method="post" enctype="multipart/form-data">
        <textarea name="message" required placeholder="Enter notification message"></textarea>
        <input type="text" name="link" placeholder="Optional Link">
        <input type="file" name="image" accept="image/*">
        <button type="submit">Send Notification</button>
    </form>
</div>

<?php

$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>