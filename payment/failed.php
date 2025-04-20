<?php
include '../config.php';
include '../navbar.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
</head>

<body>
    <h1>
        Payment Failed!<br>
        Your payment could not be processed. Please try again later.<br>
        <a href="../my_ads.php">Go to My Ads</a><br>
    </h1>
</body>

</html>