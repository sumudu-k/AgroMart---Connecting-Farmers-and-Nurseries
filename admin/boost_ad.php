<?php
include '../config.php';
include 'admin_navbar.php';

$sql = "SELECT * from ads where boosted=1";
$result = $conn->query($sql);

if (isset($_POST['unboost'])) {
    $ad_id = $_POST['ad_id']; // Get the ad ID from the form
    $sql_boost = "UPDATE ads SET boosted='0', boosted_at=null WHERE ad_id=?";
    $stmt_boost = $conn->prepare($sql_boost);
    $stmt_boost->bind_param('i', $ad_id);
    if ($stmt_boost->execute()) {
        echo "<script>alert('Ad unboosted !');</script>";
    } else {
        echo "<script>alert('Failed to unboost ad.');</script>";
    }
    header('Location: boost_ad.php');
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

        .boost-container {
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
    <div class="boost-container">
        <table>
            <tr>
                <th>Ad ID</th>
                <th>Ad Title</th>
                <th>Boosted At</th>
                <th>Action</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['ad_id'] . "</td>";
                    echo "<td>" . $row['title'] . "</td>";
                    echo "<td>" . $row['boosted_at'] . "</td>";
                    echo "<td><form method='POST' action='boost_ad.php'>
                        <input type='hidden' name='ad_id' value='" . $row['ad_id'] . "'>
                        <input type='submit'  name='unboost' value='Unboost'>
                      </form></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No ads found.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>

</html>