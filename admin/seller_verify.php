<?php
session_start();
include '../config.php';

// Handle approve button submission
if (isset($_POST['approve'])) {
    $user_id = $_POST['user_id'];

    $sql = "UPDATE verification_requests SET status = 'approved' WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    header("Location: seller_verify.php");
    exit();
}

// Handle reject button submission
if (isset($_POST['reject'])) {
    $user_id = $_POST['user_id'];

    $sql = "UPDATE verification_requests SET status = 'rejected' WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    header("Location: seller_verify.php");
    exit();
}

// Retrieve all pending seller verification requests
$sql = "SELECT verification_requests.*, users.username 
        FROM verification_requests 
        LEFT JOIN users ON verification_requests.user_id = users.user_id 
        WHERE verification_requests.status = 'pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <a href="verified_sellers.php"><button style="background-color:green; padding:5px 10px; color:white;">Verified
            Sellers</button></a>
    <title>Seller Verification Requests</title>
</head>

<body>
    <h3>Seller Verify Requests</h3>

    <table border="1" cellpadding="10">
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Requested On</th>
            <th>Status</th>
            <th>Approval</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['user_id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= $row['request_date'] ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td>
                <form method="post" action="">
                    <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                    <button type="submit" name="approve"
                        onclick="return confirm('Approve this seller?');">Approve</button>
                    <button type="submit" name="reject" onclick="return confirm('Reject this seller?');">Reject</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>