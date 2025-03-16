<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle category deletion
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    $check_ads = $conn->query("SELECT * FROM ads WHERE category_id = $category_id");
    if ($check_ads->num_rows > 0) {
        $_SESSION['error'] = "Category cannot be deleted as it has associated ads.";
        header("Location: admin_dashboard.php");
        exit();
    }

    $delete_category = $conn->query("DELETE FROM categories WHERE category_id = $category_id");

    if ($delete_category) {
        echo "Category deleted successfully.";
    } else {
        echo "Failed to delete the category.";
    }
    exit();
}


ob_start();
?>

<style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
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
    }

    .delete-button:hover {
        background-color: #d32f2f;
    }

    @media screen and (max-width: 768px) {
        .container {
            padding: 15px;
        }

        table {
            font-size: 14px;
        }

        th, td {
            padding: 8px;
        }

        .delete-button {
            font-size: 12px;
            padding: 6px 12px;
        }
    }
</style>

<div class="container">
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
                <td><?= htmlspecialchars($category['category_name']); ?></td>
                <td>
                    <a href="delete_category.php?category_id=<?= $category['category_id']; ?>" 
                       class="delete-button" 
                       onclick="return confirm('Are you sure you want to delete this category?')">Delete
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