<?php
include '../config.php';

$sql = "SELECT * FROM verification_requests WHERE status='approved'";
$result = $conn->query($sql);

if (isset($_POST['submit'])) {
    // Get the user_id from the POST request
    $u_name = $_POST['user_id'];

    // Update the status to 'rejected' in the database
    $sql = "UPDATE verification_requests SET status='rejected' WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $u_name);

    if ($stmt->execute()) {
        header("Location: verified_sellers.php");
        exit();
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
    <h3>Verified seller list</h3>

    <table>
        <tr>
            <th>user id</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php
        while ($list = $result->fetch_assoc()) {
            $u_name = $list['user_id'] ?>
        <tr>
            <td>
                <?= $list['user_id'] ?>
            </td>
            <td>
                <?= $list['status'] ?>
            </td>
            <td>
                <form action="verified_sellers.php" method="POST">
                    <input type="hidden" name="user_id" value="<?= $list['user_id'] ?>">
                    <button type="submit" name="submit"
                        onclick="return confirm('Are you sure want to remove the verify badge?');">Remove
                        Verification</button>
                </form>
            </td>
        </tr>
        <?php }
        ?>


    </table>

</body>

</html>