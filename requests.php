<?php
include 'config.php';
include 'navbar.php';

// get all plant requests from the database
$sql = "SELECT plant_requests.*, users.username FROM plant_requests 
        JOIN users ON plant_requests.user_id = users.user_id 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Plant Requests</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="css/requests.css">
</head>

<body>
    <h2>All Plant Requests</h2>
    <div class="container">

        <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()):
                $seller_id = $row['user_id'];
                // check seller is verified
                $verify = 0;
                $check_seller = "SELECT * FROM verification_requests WHERE user_id=?  AND status ='approved'";
                $stmt_check = $conn->prepare($check_seller);
                $stmt_check->bind_param('i', $seller_id);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
                if ($result_check->num_rows > 0) {
                    $verify = 1;
                } else {
                    $verify = 0;
                }
            ?>
        <div class="request-card">
            <a href="view_request.php?request_id=<?= $row['request_id']; ?>">
                <strong><?= htmlspecialchars($row['subject']); ?></strong>
                <p><?= htmlspecialchars($row['description']); ?></p>
                <p>Requested by: <?= htmlspecialchars($row['username']); ?></p>
                <?php
                        if ($verify == 1): ?>
                <span style=" background-color:green; padding:5px 10px;color:white;"> Verified Seller</span>
                <?php endif;
                        ?>
                <p>Contact Number: <?= htmlspecialchars($row['contact']); ?></p>

                <p>District: <?= htmlspecialchars($row['district']); ?></p>
                <p>Posted On: <?= htmlspecialchars($row['created_at']); ?></p>
        </div>
        <?php endwhile; ?>
        </a>
        <?php else: ?>

        <p class="no-requests">Sorry! No plant requests available.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
<?php
$conn->close();
?>