<?php
session_start();
include '../config.php';
include '../alertFunction.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request! Category ID is missing.");
}

$category_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    die("Category not found!");
}

if (isset($_POST['update_category'])) {
    $category_name = $_POST['category_name'];
    $new_image = $_FILES['category_image']['name'];
    $image_temp_name = $_FILES['category_image']['tmp_name'];

    if ($new_image) {
        $target_dir = "../uploads/categories/";
        $target_file = $target_dir . basename($new_image);
        move_uploaded_file($image_temp_name, $target_file);
    } else {
        $target_file = $category['category_image'];
    }

    $update_stmt = $conn->prepare("UPDATE categories SET category_name = ?, category_image = ? WHERE category_id = ?");
    $update_stmt->bind_param("ssi", $category_name, $target_file, $category_id);

    if ($update_stmt->execute()) {
        echo "<script>
                alert('Category updated successfully!');
                window.location='edit_category.php';
              </script>";
    } else {
        echo "<script>alert('Error: " . addslashes($update_stmt->error) . "');</script>";
    }
}

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
    max-width: 50%;
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

.form-group {
    margin-bottom: 15px;
}

label {
    font-size: 1rem;
    font-weight: bold;
}

input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.button {
    background-color: #007a33;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
}

.button:hover {
    background-color: #005922;
}
</style>

<div class="category-container">
    <h1>Edit Category</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="category_name">Category Name:</label>
            <input type="text" name="category_name" value="<?= htmlspecialchars($category['category_name']) ?>"
                required>
        </div>

        <div class="form-group">
            <label>Current Image:</label><br>
            <img src="<?= htmlspecialchars($category['category_image']) ?>" width="100" height="100">
        </div>

        <div class="form-group">
            <label for="category_image">New Image (optional):</label>
            <input type="file" name="category_image" accept="image/*">
        </div>

        <input class="button" type="submit" name="update_category" value="Update Category">
    </form>
</div>

<?php
$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>