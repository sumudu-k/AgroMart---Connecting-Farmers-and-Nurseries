<?php

session_start();


if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <style>
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
        }

        
        .container {
            max-width: 800px;
            margin: 55px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        
        h1 {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        
        .form-group {
            display: flex;
            flex-direction: column; 
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px; 
        }

       
        input[type="text"], input[type="file"], textarea {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            width: 100%; 
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        
        .button {
            margin-top: 20px;
            background-color: #007a33;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            width: 100%;
        }

        .button:hover {
            background-color: #45a049;
        }

        
        @media (max-width: 768px) {
            
            .container {
                padding: 15px;
                max-width: 100%;
            }

           
            .form-group {
                flex-direction: column;
            }

            .button {
                font-size: 14px; 
                padding: 8px 15px; 
            }
        }

    </style>
</head>

<body>
    
    <div class="container">
        <h1>Add Category</h1>
        <form action="add_category.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                
                <label for="category_name">Category Name:</label>
                <input type="text" name="category_name" required>
            </div>
            <div class="form-group">
                
                <label for="category_image">Category Image:</label>
                <input type="file" name="category_image" accept="image/*" required>
            </div>
            
            <input class="button" type="submit" name="add_category" value="Add Category">
        </form>
    </div>

    <?php
    include '../config.php';

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
                echo  "<script>
                alert('Category added successfully!');
                window.location.href = 'admin_dashboard.php';
                </script>";
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "Failed to upload image.";
        }
    }
    ?>
</body>

</html>