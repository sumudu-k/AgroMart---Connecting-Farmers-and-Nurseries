<?php
include 'config.php';
include 'navbar.php';

$user_id = $_SESSION['user_id'];

//$sql_get = "SELECT * from cart where user_id =?";
$sql_get = "SELECT cart.*, ads.title,ads.price from cart join ads on cart.ad_id=ads.ad_id where cart.user_id=?";


$stmt_get = $conn->prepare($sql_get);
$stmt_get->bind_param('i', $user_id);
$stmt_get->execute();
$result_get = $stmt_get->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>My cart</h1>
    <table border='1'>
        <tr>
            <th>Item Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Remove</th>
        </tr>
        <?php
        while ($row = $result_get->fetch_assoc()): ?>
            <tr>
                <td><?= $row['title'] ?></td>
                <td><?= $row['price'] ?></td>
                <td><span>1</span></td>
                <td>X</td>
            </tr>
        <?php endwhile; ?>
    </table>
    <!-- cart_id 	user_id 	ad_id 	added_at 	 -->
</body>

</html>