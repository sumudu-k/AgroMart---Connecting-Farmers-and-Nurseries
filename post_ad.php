<?php
session_start();
ob_start();
include 'config.php';
include 'navbar.php';
include 'alertFunction.php';



//check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    showAlert('Please login to post an ad', 'error', '#ff0000', 'login.php');
    exit();
}


if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $phone_number = $_POST['phone_number'];
    $district = $_POST['district'];
    $user_id = $_SESSION['user_id'];
    $category_id = $_POST['category'];

    // insert ad details into the ads table
    $ad_sql = "INSERT INTO ads (title, description, price, phone_number, user_id, category_id, district) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($ad_sql);
    $stmt->bind_param("ssdsiss", $title, $description, $price, $phone_number, $user_id, $category_id, $district);
    $stmt->execute();
    $ad_id = $conn->insert_id;

    for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
        if ($_FILES['images']['error'][$i] == 0) {
            $image_name = basename($_FILES['images']['name'][$i]);
            $target_path = 'uploads/' . $image_name;

            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_path)) {
                $img_sql = "INSERT INTO ad_images (ad_id, image_path) VALUES (?, ?)";
                $stmt_img = $conn->prepare($img_sql);
                $stmt_img->bind_param("is", $ad_id, $target_path);
                $stmt_img->execute();
            }
        }
    }
    showAlert('Your ad has posted successfully', 'success', '#008000', 'my_ads.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Post Ad</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        position: relative;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("images/B1.jpg");
        background-size: cover;
        opacity: 0.2;
        z-index: -1;
    }

    h1 {
        background-color: #dbffc7;
        text-align: center;
        padding: 10px 12.5%;
        font-size: 1.8rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }

    .main-content {
        flex: 1;
    }

    /* form container */
    .ad-form {
        max-width: 50%;
        margin: 20px auto;
        padding: 20px;
        background-color: #e1e1e1;
        border-radius: 5px;
        position: relative;
        z-index: 1;
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

    input,
    select,
    textarea {
        flex: 1.5;
        font-size: 1rem;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 100%;
    }

    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #007a33;
    }

    textarea {
        resize: vertical;
        min-height: 150px;
    }

    input::placeholder,
    textarea::placeholder {
        font-size: 1rem;
        font-style: italic;
        letter-spacing: 0.5px;
        font-weight: 500;
    }

    button {
        background-color: #007a33;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        display: block;
        margin: 20px auto 0;
        transition: background-color 0.2s;
    }

    button:hover {
        background-color: #005922;
    }

    /* mobile Devices */
    @media screen and (max-width: 480px) {
        h1 {
            padding: 15px 5%;
            font-size: 1.5rem;
        }

        .ad-form {
            padding: 15px;
            max-width: 90%;
        }

        .form-group {
            flex-direction: column;
            align-items: stretch;
            margin-bottom: 10px;
        }

        label {
            text-align: left;
            padding-right: 0;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        input,
        select,
        textarea {
            font-size: 0.9rem;
            padding: 6px;
            margin-bottom: 10px;
        }

        input::placeholder,
        textarea::placeholder {
            font-size: 0.9rem;
        }

        button {
            padding: 8px 15px;
            font-size: 14px;
        }
    }

    /* tablets */
    @media screen and (min-width: 481px) and (max-width: 1200px) {
        h1 {
            padding: 20px 8%;
            font-size: 1.8rem;
        }

        .ad-form {
            max-width: 80%;
        }

        .form-group {
            flex-direction: column;
            align-items: stretch;
            margin-bottom: 20px;
        }

        label {
            text-align: left;
            padding-right: 0;
            margin-bottom: 5px;
            font-size: 0.95rem;
        }

        input,
        select,
        textarea {
            font-size: 0.95rem;
            padding: 7px;
            margin-bottom: 10px;
        }

        button {
            padding: 9px 18px;
            font-size: 15px;
        }
    }
    </style>
</head>

<body>
    <div class="main-content">
        <h1>Post an Ad Totally Free</h1>
        <form action="post_ad.php" method="POST" enctype="multipart/form-data" class="ad-form">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" placeholder="Enter title here" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" placeholder="Describe your ad here" required></textarea>
            </div>

            <div class="form-group">
                <label for="district">District</label>
                <select name="district" required>
                    <option value="">Select District</option>
                    <option value="Ampara">Ampara</option>
                    <option value="Anuradhapura">Anuradhapura</option>
                    <option value="Badulla">Badulla</option>
                    <option value="Batticaloa">Batticaloa</option>
                    <option value="Colombo">Colombo</option>
                    <option value="Galle">Galle</option>
                    <option value="Gampaha">Gampaha</option>
                    <option value="Hambantota">Hambantota</option>
                    <option value="Jaffna">Jaffna</option>
                    <option value="Kalutara">Kalutara</option>
                    <option value="Kandy">Kandy</option>
                    <option value="Kegalle">Kegalle</option>
                    <option value="Kilinochchi">Kilinochchi</option>
                    <option value="Kurunegala">Kurunegala</option>
                    <option value="Mannar">Mannar</option>
                    <option value="Matale">Matale</option>
                    <option value="Matara">Matara</option>
                    <option value="Monaragala">Monaragala</option>
                    <option value="Mullaitivu">Mullaitivu</option>
                    <option value="Nuwara Eliya">Nuwara Eliya</option>
                    <option value="Polonnaruwa">Polonnaruwa</option>
                    <option value="Puttalam">Puttalam</option>
                    <option value="Ratnapura">Ratnapura</option>
                    <option value="Trincomalee">Trincomalee</option>
                    <option value="Vavuniya">Vavuniya</option>
                </select>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" required>
                    <?php
                    $categories = $conn->query("SELECT * FROM categories");
                    while ($category = $categories->fetch_assoc()) {
                        echo "<option value='{$category['category_id']}'>{$category['category_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="phone_number">Contact</label>
                <input type="text" name="phone_number" placeholder="Enter 10 digit number" required>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" placeholder="Rs" required>
            </div>

            <div class="form-group">
                <label for="images">Images</label>
                <input type="file" name="images[]" multiple required>
            </div>

            <button type="submit" name="submit">Submit Ad</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>