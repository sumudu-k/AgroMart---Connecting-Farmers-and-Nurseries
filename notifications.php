<?php
session_start();
include 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    exit();
}

$user_id = $_SESSION['user_id'];

// mark all unread notifications as read when the user views them
$updateSQL = "UPDATE notifications SET status = 'read' WHERE user_id = ? AND status = 'unread'";
$stmt = $conn->prepare($updateSQL);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// fetch notifications for the logged-in user
$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

// count unread notifications for the user
$countSQL = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND status = 'unread'";
$stmt = $conn->prepare($countSQL);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$countResult = $stmt->get_result();
$row = $countResult->fetch_assoc();
$unread_count = $row['unread_count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>My Notifications</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        color: #333;
        position: relative;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Add background image */
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

    h1 {
        background-color: #dbffc7;
        text-align: center;
        padding: 10px 0;
        font-size: 2rem;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }

    .notification-container {
        width: 75%;
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        margin: 20px auto;
        flex: 1;
    }

    p{
        text-align: center;
        font-size: 1.3rem;
        color: #333;
    }

    .card {
        margin-bottom: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(228, 228, 228, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .card-body {
        padding: 20px;
        background-color: rgb(243, 249, 255);
    }

    .card-body p {
        margin: 0 0 15px;
        font-size: 1rem;
        line-height: 1.6;
    }

    .card-body a {
        color: #006400;
        text-decoration: underline;
        letter-spacing: 2px;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .card-body a:hover {
        color: #f09319;
    }

    .card-body img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        margin-top: 15px;
        object-fit: cover;
    }

    .card-footer {
        background: rgb(233, 233, 233);
        padding: 10px 20px;
        text-align: right;
        font-size: 0.9rem;
        color: #666;
    }

    .card-footer small {
        display: inline-block;
        font-style: italic;
        font-size: 0.8rem;
        font-weight: bold;
    }

    @media screen and (max-width: 480px) {
        .notification-container {
            width: 90%;
            padding: 10px;
        }

        .card-body img {
            border-radius: 5px;
        }
        }

        @media screen and (min-width: 481px) and (max-width: 1200px) {
            .notification-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <h1>Notifications</h1>
    <div class="notification-container">
        <?php if (empty($notifications)): ?>
        <p>No notifications available.</p>
        <?php else: ?>
        <?php foreach ($notifications as $notif): ?>
        <div class="card" style="background: <?= ($notif['status'] == 'unread') ? '#fff8e1' : '#ffffff'; ?>">
            <div class="card-body">
                <p class='title'><?= htmlspecialchars($notif['message']); ?></p>
                <?php if (!empty($notif['link'])): ?>
                <a href="<?= htmlspecialchars($notif['link']); ?>" target="_blank">Visit</a>
                <?php endif; ?>
                <?php if (!empty($notif['image'])): ?>
                <img src="<?= htmlspecialchars($notif['image']); ?>" alt="Notification Image">
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <small><?= date('F j, Y \a\t g:i A', strtotime($notif['created_at'])); ?></small>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>

<?php
include 'footer.php';
?>