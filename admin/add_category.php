<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle form submission
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $category_image = $_FILES['category_image']['name'];
    $image_temp_name = $_FILES['category_image']['tmp_name'];

    $target_dir = "../uploads/categories/";
    $target_file = $target_dir . basename($category_image);

    if (move_uploaded_file($image_temp_name, $target_file)) {
        $stmt = $conn->prepare("INSERT INTO categories (category_name, category_image) VALUES (?, ?)");
        $stmt->bind_param("ss", $category_name, $target_file);

        if ($stmt->execute()) {
            echo "<script>alert('Category added successfully.');</script>";
        } else {
            echo "<script>alert('Failed to add a category');</script>";
        }
    } else {
        echo "<script>alert('Failed to upload image.');</script>";
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

.category-container {
    max-width: 90%;
    margin: 20px auto;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1;
}

.category-container h1 {
    text-align: center;
    font-size: 2rem;
    color: #333;
    padding: 10px 0;
    border-bottom: 2px solid #007a33;
}

.category-container form {
    margin-top: 60px;
}

.form-group {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

label {
    flex: 0.5;
    font-size: 1rem;
    text-align: right;
    padding-right: 20px;
    font-weight: bold;
}

.form-group input {
    flex: 1.5;
    font-size: 1rem;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%;
    transition: border-color 0.2s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #007a33;
}

.form-group input::placeholder {
    font-size: 1rem;
    font-style: italic;
    letter-spacing: 0.5px;
    font-weight: 500;
    opacity: 0.7;
}

form .button {
    background-color: #007a33;
    color: white;
    text-align: center;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    display: block;
    width: 200px;
    margin: 20px auto 0;
    transition: background-color 0.2s ease;
}

form .button:hover {
    background-color: #005922;
}
</style>

<div class="category-container">
    <h1>Add Category</h1>
    <form action="add_category.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="category_name">Category Name:</label>
            <input type="text" name="category_name" placeholder="Enter category name" required>
        </div>
        <div class="form-group">
            <label for="category_image">Category Image:</label>
            <input type="file" name="category_image" accept="image/*" required>
        </div>
        <input class="button" type="submit" name="add_category" value="Add Category">
    </form>
</div>

<?php
$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>