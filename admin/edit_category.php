<?php
session_start();
include '../config.php';
include '../alertFunction.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$query = "SELECT * FROM categories";
$result = $conn->query($query);

ob_start();
?>

<style>
* {
    font-family: "Poppins", Arial, sans-serif;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    color: #333;
    background-color: #f4f4f4;
}

.category-container {
    max-width: 90%;
    margin: 20px auto;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #fff;
}

h1 {
    text-align: center;
    font-size: 2rem;
    padding: 10px 0;
    border-bottom: 2px solid #007a33;
}

table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
}

th,
td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #a9e6a9;
    text-align: center;
}

td:last-child {
    text-align: center;
}

.edit-button {
    padding: 8px 16px;
    background-color: #007a33;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
}

.edit-button:hover {
    background-color: #005922;
}
</style>

<div class="category-container">
    <h1>Manage Categories</h1>
    <table>
        <tr>
            <th>Category Name</th>
            <th>Image</th>
            <th>Action</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['category_name']) . "</td>
                        <td><img src='" . htmlspecialchars($row['category_image']) . "' width='50' height='50'></td>
                        <td><a href='update_category.php?id=" . $row['category_id'] . "' class='edit-button'>Edit</a></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3' style='text-align:center;'>No categories found.</td></tr>";
        }
        ?>
    </table>
</div>

<?php
$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>