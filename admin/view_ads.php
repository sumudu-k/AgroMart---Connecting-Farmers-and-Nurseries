<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['delete_ad'])) {
    $ad_id = $_GET['delete_ad'];
    $stmt = $conn->prepare("DELETE FROM ads WHERE ad_id = ?");
    $stmt->bind_param('i', $ad_id);
    if ($stmt->execute()) {
        echo "<script>
                alert('Ad deleted successfully!');
                window.location='view_ads.php';
              </script>";
    } else {
        echo "<script>alert('Failed to delete ad.');</script>";
    }
    header("Location: view_ads.php");
    exit();
}

$result = $conn->query("SELECT ads.*, users.username FROM ads JOIN users ON ads.user_id = users.user_id");

ob_start();
?>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: "Poppins", Arial, sans-serif;
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

.view-ads-container {
    max-width: 90%;
    margin: 20px auto;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1;
}

.view-ads-container h1 {
    text-align: center;
    font-size: 2rem;
    color: #333;
    padding: 10px 0;
    border-bottom: 2px solid #007a33;
}

table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    margin-top: 40px;
}

th,
td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    vertical-align: middle;
}

th {
    text-align: center;
    background-color: #a9e6a9;
    font-weight: 600;
    color: #333;
    border-right: 2px solid rgba(51, 51, 51, 0.2);
}

th:last-child {
    border-right: none;
}


td:last-child {
    text-align: center;
}

tr {
    transition: background-color 0.2s ease;
}

tr:hover {
    background-color: #e6ffe6;
}

.delete-button {
    background-color: #f44336;
    color: white;
    border: none;
    padding: 8px 16px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.delete-button:hover {
    background-color: #d32f2f;
}
</style>

<div class="view-ads-container">
    <h1>View & Delete Ads</h1>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Ad ID</th>
                <th class="ad_description">Description</th>
                <th>Price</th>
                <th>Posted By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($ad = $result->fetch_assoc()) { ?>
            <tr>
                <td data-label="Title"><?= htmlspecialchars($ad['title']) ?></td>
                <td data-label="Ad ID"><?= htmlspecialchars($ad['ad_id']) ?></td>
                <td data-label="Description"><?= htmlspecialchars($ad['description']) ?></td>
                <td data-label="Price">
                    <?= htmlspecialchars($ad['price']) ?>
                    <?php
                        $price_unit = htmlspecialchars($ad['price_unit'] ?? '');
                        switch ($price_unit) {
                            case 'per_unit':
                                echo ' per unit';
                                break;
                            case 'bulk':
                                echo ' for bulk';
                                break;
                            case '100_units':
                                echo ' per 100 units';
                                break;
                            default:
                                echo '';
                        }
                        ?>
                </td>
                <td data-label="Posted By"><?= htmlspecialchars($ad['username']) ?></td>
                <td data-label="Action">
                    <a href="view_ads.php?delete_ad=<?= $ad['ad_id'] ?>" class="delete-button"
                        onclick="return confirm('Are you sure you want to delete this Ad?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>