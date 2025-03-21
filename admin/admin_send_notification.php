<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];
    $link = !empty($_POST['link']) ? $_POST['link'] : NULL;
    $image = NULL;

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/notifications/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $imageName = time() . "_" . basename($_FILES["image"]["name"]);
        $imagePath = $targetDir . $imageName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            $image = "uploads/notifications/" . $imageName;
        } else {
            echo "<script>alert('Failed to upload image.');</script>";
            header("Location: admin_send_notification.php");
            exit();
        }
    }

    //fetch all users from the users table
    $sql = "SELECT user_id FROM users";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, link, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $message, $link, $image);

        $success = true;
        while ($row = $result->fetch_assoc()) {
            $user_id = $row['user_id'];
            if (!$stmt->execute()) {
                $success = false;
                echo "<script>alert('Error sending notification');</script>";
                break;
            }
        }

        if ($success) {
            echo "<script>alert('Notification sent successfully to all users!');</script>";
            header("Location: admin_manage_notifications.php");
        }
        exit();
    } else {
        echo "<script>alert('No users found!');</script>";
        header("Location: admin_send_notification.php");
        exit();
    }
}

ob_start();
?>

<style>
* {
    font-family: "Poppins", Arial, sans-serif;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    color: #333;
    position: relative;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: #f4f4f4;
}

body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url("../images/B1.jpg");
    background-size: cover;
    opacity: 0.2;
    z-index: -1;
}

.send-notification-container {
    max-width: 90%;
    margin: 20px auto;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1;
}

.send-notification-container h2 {
    text-align: center;
    font-size: 2rem;
    color: #333;
    padding: 10px 0;
    border-bottom: 2px solid #007a33;

}

.send-notification-container form {
    margin-top: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

textarea,
input {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
    width: 100%;
    box-sizing: border-box;
    transition: border-color 0.2s ease;
}

textarea:focus,
input:focus {
    outline: none;
    border-color: #007a33;
}

textarea {
    resize: vertical;
    height: 200px;
}

input::placeholder,
textarea::placeholder {
    font-size: 1rem;
    font-style: italic;
    letter-spacing: 0.5px;
    font-weight: 500;
    opacity: 0.7;
}

button {
    background-color: #007a33;
    color: white;
    text-align: center;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    display: block;
    width: 250px;
    margin: 20px auto 0;
    transition: background-color 0.2s ease;
}

button:hover {
    background-color: #005922;
}
</style>

<div class="send-notification-container">
    <h2>Send Notification</h2>


    <form method="post" enctype="multipart/form-data">
        <textarea name="message" required placeholder="Enter notification message"></textarea>
        <input type="text" name="link" placeholder="Optional Link (e.g., https://example.com)">
        <input type="file" name="image" accept="image/*">
        <button type="submit">Send Notification</button>
    </form>
</div>

<?php
//capture the content and include the layout
$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>