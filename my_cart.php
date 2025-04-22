<?php
include 'config.php';
include 'navbar.php';

$user_id = $_SESSION['user_id'];

// get all products from cart owned to user
$sql_get = "
    SELECT cart.*, ads.*,
        (SELECT image_path FROM ad_images WHERE ad_id = ads.ad_id LIMIT 1) AS image_path
    FROM cart 
    JOIN ads ON cart.ad_id = ads.ad_id 
    WHERE cart.user_id = ?";


$stmt_get = $conn->prepare($sql_get);
$stmt_get->bind_param('i', $user_id);
$stmt_get->execute();
$result_get = $stmt_get->get_result();


// get the cart id
// while ($get_id = $result_get->fetch_assoc()) {
// }

// remove items on cart
if (isset($_POST['remove'])) {
    $cart_id = $_POST['cart_id'];
    $sql_remove = "DELETE from cart where cart_id =? ";
    $stmt_remove = $conn->prepare($sql_remove);
    $stmt_remove->bind_param('i', $cart_id);
    if ($stmt_remove->execute()) {
        header("Location:my_cart.php");
    }
}

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
            <th>cart id</th>
            <th>Image</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Remove</th>
        </tr>
        <?php
        while ($row = $result_get->fetch_assoc()): ?>
            <tr>
                <td><?= $row['cart_id'] ?></td>
                <td><a href="view_ad.php?ad_id=<?= $row['ad_id'] ?>"><img src="<?= $row['image_path'] ?>" alt=""
                            style="width:100px"></a></td>

                <td><a href="view_ad.php?ad_id=<?= $row['ad_id'] ?>"><?= $row['title'] ?>
                        <!-- quantity -->
                        <?php if ($row['quantity'] == 0): ?>
                            <p style="color:white; background-color:red; padding:5px 10px;">Almost soldout</p>

                        <?php elseif ($row['quantity'] <= 10): ?>
                            <p style="color:white; background-color:orange; padding:5px 10px;"> <?= $row['quantity'] ?> Items
                                left. Hurry Up!</p>
                        <?php endif; ?>
                    </a></td>

                <td><?= $row['price'] ?></td>
                <td><span>1</span></td>
                <td>
                    <form action="my_cart.php" method="POST">
                        <input type="hidden" name="cart_id" value="<?= $row['cart_id'] ?>">
                        <button type="submit" name="remove"
                            style="width:30px; background-color:red; color:white;">X</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>