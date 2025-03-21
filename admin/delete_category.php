<?php
session_start();
include '../config.php';
include '../alertFunction.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}


if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];


    $check_ads = $conn->prepare("SELECT COUNT(*) as total FROM ads WHERE category_id = ?");
    $check_ads->bind_param("i", $category_id);
    $check_ads->execute();
    $result = $check_ads->get_result();
    $row = $result->fetch_assoc();


    $delete_category = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $delete_category->bind_param("i", $category_id);

    if ($delete_category->execute()) {
        echo "<script>alert('Category deleted successfully.');</script>";
    } else {
        echo "<script>alert('Failed to delete the category.');</script>";
    }
    header("Location: delete_category.php");
    exit();
}


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

.dlt-category-container {
    max-width: 90%;
    margin: 20px auto;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1;
}

.dlt-category-container h1 {
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

<div class="dlt-category-container">
    <h1>Delete Category</h1>
    <table>
        <thead>
            <tr>
                <th>Category Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $categories = $conn->query("SELECT * FROM categories");
            while ($category = $categories->fetch_assoc()):
            ?>
            <tr>
                <td data-label="Category Name"><?= htmlspecialchars($category['category_name']); ?></td>
                <td data-label="Action">
                    <a href="delete_category.php?category_id=<?= $category['category_id']; ?>" class="delete-button"
                        onclick="return confirm('Are you sure you want to delete this category?')">
                        Delete
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>