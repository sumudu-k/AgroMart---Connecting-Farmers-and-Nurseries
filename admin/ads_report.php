<?php
session_start();

include '../config.php';
include 'admin_navbar.php';


$sql = "SELECT ads.*, ad_reports.* 
        FROM ads 
        JOIN ad_reports ON ads.ad_id = ad_reports.ad_id";
$result = $conn->query($sql);


if (isset($_POST['delete'])) {
    $ad_id = $_POST['ad_id'];
    $sql_delete = "DELETE FROM ads WHERE ad_id=?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param('i', $ad_id);
    $stmt_delete->execute();
    header('Location: ads_report.php');
    exit();
}

if (isset($_POST['reject'])) {
    $ad_id = $_POST['ad_id'];
    $sql_reject = "DELETE FROM ad_reports WHERE ad_id=?";
    $sql_reject = $conn->prepare($sql_reject);
    $sql_reject->bind_param('i', $ad_id);
    $sql_reject->execute();
    header('Location: ads_report.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

    .report-container {
        max-width: 90%;
        margin: 20px auto;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1;
    }
    </style>
</head>

<body>
    <div class="report-container">
        <h2>Reported Ads </h2>
        <?php if ($result->num_rows === 0): ?>
        <h2>No reports found</h2>
        <?php else: ?>
        <table border=1>
            <tr>
                <th>Seller id</th>
                <th>ad id</th>
                <th>ad title</th>
                <th>Reported at</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) :
                    $title = substr($row['title'], 0, 20); ?>
            <tr>
                <td><?= $row['user_id'] ?></td>
                <td><?= $row['ad_id'] ?></td>
                <td><?= $title ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <form action="ads_report.php" method="post">
                        <input type="hidden" name="ad_id" value="<?= $row['ad_id'] ?>">
                        <button type="submit" name="delete">Delete</button>
                        <button type="submit" name="reject">Reject</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php endif; ?>


</body>

</html>


<?php
//capture the content and include the layout
//$content = ob_get_clean();
//include '../admin/admin_navbar.php';
?>