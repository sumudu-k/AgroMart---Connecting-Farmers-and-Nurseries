<?php
session_start();
include 'config.php';

$ad_id = $_GET['ad_id'];

// Fetch the current ad details
$ad_sql = "SELECT * FROM ads WHERE ad_id = ?";
$stmt = $conn->prepare($ad_sql);
$stmt->bind_param("i", $ad_id);
$stmt->execute();
$ad_result = $stmt->get_result();
$ad = $ad_result->fetch_assoc();

// Fetch ad images
$img_sql = "SELECT * FROM ad_images WHERE ad_id = ?";
$stmt_img = $conn->prepare($img_sql);
$stmt_img->bind_param("i", $ad_id);
$stmt_img->execute();
$img_result = $stmt_img->get_result();

// Fetch categories
$categories_sql = "SELECT * FROM categories";
$categories_result = $conn->query($categories_sql);

// Form submission
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $phone_number = $_POST['phone_number'];
    $category_id = $_POST['category'];
    $district = $_POST['district'];

    $update_sql = "UPDATE ads SET title = ?, description = ?, price = ?, phone_number = ?, category_id = ?, district = ? WHERE ad_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssdsisi", $title, $description, $price, $phone_number, $category_id, $district, $ad_id);
    $stmt->execute();

    // New image uploads
    if (!empty($_FILES['new_images']['name'][0])) {
        for ($i = 0; $i < count($_FILES['new_images']['name']); $i++) {
            if ($_FILES['new_images']['error'][$i] == 0) {
                $image_name = basename($_FILES['new_images']['name'][$i]);
                $target_path = 'uploads/' . $image_name;

                if (move_uploaded_file($_FILES['new_images']['tmp_name'][$i], $target_path)) {
                    $insert_img_sql = "INSERT INTO ad_images (ad_id, image_path) VALUES (?, ?)";
                    $stmt_img = $conn->prepare($insert_img_sql);
                    $stmt_img->bind_param("is", $ad_id, $target_path);
                    $stmt_img->execute();
                }
            }
        }
    }

    // Image deletions
    if (isset($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $image_id) {
            $del_img_sql = "SELECT image_path FROM ad_images WHERE image_id = ?";
            $stmt = $conn->prepare($del_img_sql);
            $stmt->bind_param("i", $image_id);
            $stmt->execute();
            $img_path = $stmt->get_result()->fetch_assoc()['image_path'];

            if (file_exists($img_path)) {
                unlink($img_path);
            }

            $delete_sql = "DELETE FROM ad_images WHERE image_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $image_id);
            $stmt->execute();
        }
    }

    header("Location: my_ads.php?ad_id=" . $ad_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'navbar.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="removeImage.js" defer></script>
    <title>Edit Ad - AgroMart</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", Arial, sans-serif;
            margin: 0;
            padding: 0;
            position: relative;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Background image */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("images/B1.jpg");
            background-size: cover;
            opacity: 0.5;
            z-index: -1;
        }

        /* Main content */
        .main-content {
            flex: 1;
        }

        h2 {
            background-color: #dbffc7;
            text-align: center;
            padding: 10px 12.5%;
            font-size: 2rem;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }

        /* Form container with blur effect */
        .ad-form {
            max-width: 50%;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(225, 225, 225, 0.3);
            backdrop-filter: blur(10px);
            border-radius: 5px;
            position: relative;
            z-index: 1;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-group label {
            flex: 0.2;
            width: 150px;
            text-align: right;
            padding-right: 10px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            flex: 0.8;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #007a33;
        }

        textarea {
            height: 200px;
        }

        h3 {
            margin-bottom: 10px;
            color: #333;
        }

        /* Image section */
        .image-wrapper {
            display: inline-block;
            text-align: center;
            justify-content: center;
            position: relative;
            margin: 10px;
        }

        .image-wrapper img {
            width: 200px;
            height: 200px;
            object-fit: cover;
        }

        .image-wrapper button {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: red;
            color: white;
            border: 2px solid white;
            border-radius: 50%;
            padding: 5px 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .image-wrapper button:hover {
            background-color: rgb(180, 0, 0);
            transform: scale(1.1);
        }

        .image-wrapper button:active {
            transform: scale(0.95);
        }

        .hidden {
            display: none;
        }

        /* Submit button */
        button[type="submit"] {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        /* Responsive styles */
        @media screen and (max-width: 480px) {
            h2 {
                font-size: 1.5rem;
                padding: 15px 5%;
            }

            .ad-form {
                max-width: 95%;
                padding: 15px;
            }

            .form-group {
                flex-direction: column;
                align-items: stretch;
            }

            .form-group label {
                width: auto;
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
            }

            .image-wrapper img {
                width: 150px;
                height: 150px;
            }

            button[type="submit"] {
                font-size: 14px;
                padding: 8px 15px;
            }
        }

        @media screen and (min-width: 481px) and (max-width: 1200px) {
            h2 {
                font-size: 1.8rem;
                padding: 20px 8%;
            }

            .ad-form {
                max-width: 80%;
            }

            .form-group {
                flex-direction: column;
                align-items: stretch;
            }

            .form-group label {
                width: auto;
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
            }

            .image-wrapper img {
                width: 180px;
                height: 180px;
            }

            button[type="submit"] {
                font-size: 15px;
                padding: 9px 18px;
            }
        }
    </style>
</head>

<body>
    <div class="main-content">
        <h2>Edit Your Advertisement</h2>
        <form action="edit_ad.php?ad_id=<?= $ad_id ?>" method="POST" enctype="multipart/form-data" class="ad-form">
            <div class="form-group">
                <label for="title">Ad Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($ad['title']) ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Ad Description</label>
                <textarea name="description" required><?= htmlspecialchars($ad['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" name="price" value="<?= htmlspecialchars($ad['price']) ?>" required>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" name="phone_number" value="<?= htmlspecialchars($ad['phone_number']) ?>" required>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" required>
                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                        <option value="<?= $category['category_id'] ?>"
                            <?= $category['category_id'] == $ad['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['category_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="district">District</label>
                <select name="district" required>
                    <option value="Ampara" <?= $ad['district'] == 'Ampara' ? 'selected' : '' ?>>Ampara</option>
                    <option value="Anuradhapura" <?= $ad['district'] == 'Anuradhapura' ? 'selected' : '' ?>>Anuradhapura
                    </option>
                    <option value="Badulla" <?= $ad['district'] == 'Badulla' ? 'selected' : '' ?>>Badulla</option>
                    <option value="Batticaloa" <?= $ad['district'] == 'Batticaloa' ? 'selected' : '' ?>>Batticaloa
                    </option>
                    <option value="Colombo" <?= $ad['district'] == 'Colombo' ? 'selected' : '' ?>>Colombo</option>
                    <option value="Galle" <?= $ad['district'] == 'Galle' ? 'selected' : '' ?>>Galle</option>
                    <option value="Gampaha" <?= $ad['district'] == 'Gampaha' ? 'selected' : '' ?>>Gampaha</option>
                    <option value="Hambantota" <?= $ad['district'] == 'Hambantota' ? 'selected' : '' ?>>Hambantota
                    </option>
                    <option value="Jaffna" <?= $ad['district'] == 'Jaffna' ? 'selected' : '' ?>>Jaffna</option>
                    <option value="Kalutara" <?= $ad['district'] == 'Kalutara' ? 'selected' : '' ?>>Kalutara</option>
                    <option value="Kandy" <?= $ad['district'] == 'Kandy' ? 'selected' : '' ?>>Kandy</option>
                    <option value="Kegalle" <?= $ad['district'] == 'Kegalle' ? 'selected' : '' ?>>Kegalle</option>
                    <option value="Kilinochchi" <?= $ad['district'] == 'Kilinochchi' ? 'selected' : '' ?>>Kilinochchi
                    </option>
                    <option value="Kurunegala" <?= $ad['district'] == 'Kurunegala' ? 'selected' : '' ?>>Kurunegala
                    </option>
                    <option value="Mannar" <?= $ad['district'] == 'Mannar' ? 'selected' : '' ?>>Mannar</option>
                    <option value="Matale" <?= $ad['district'] == 'Matale' ? 'selected' : '' ?>>Matale</option>
                    <option value="Matara" <?= $ad['district'] == 'Matara' ? 'selected' : '' ?>>Matara</option>
                    <option value="Monaragala" <?= $ad['district'] == 'Monaragala' ? 'selected' : '' ?>>Monaragala
                    </option>
                    <option value="Mullaitivu" <?= $ad['district'] == 'Mullaitivu' ? 'selected' : '' ?>>Mullaitivu
                    </option>
                    <option value="Nuwara Eliya" <?= $ad['district'] == 'Nuwara Eliya' ? 'selected' : '' ?>>Nuwara Eliya
                    </option>
                    <option value="Polonnaruwa" <?= $ad['district'] == 'Polonnaruwa' ? 'selected' : '' ?>>Polonnaruwa
                    </option>
                    <option value="Puttalam" <?= $ad['district'] == 'Puttalam' ? 'selected' : '' ?>>Puttalam</option>
                    <option value="Ratnapura" <?= $ad['district'] == 'Ratnapura' ? 'selected' : '' ?>>Ratnapura</option>
                    <option value="Trincomalee" <?= $ad['district'] == 'Trincomalee' ? 'selected' : '' ?>>Trincomalee
                    </option>
                    <option value="Vavuniya" <?= $ad['district'] == 'Vavuniya' ? 'selected' : '' ?>>Vavuniya</option>
                </select>
            </div>

            <h3>Current Images</h3>
            <?php while ($img_row = $img_result->fetch_assoc()): ?>
                <div class="image-wrapper">
                    <img src="<?= htmlspecialchars($img_row['image_path']) ?>" alt="Ad Image">
                    <button type="button" onclick="removeImage(this)">X</button>
                    <input type="checkbox" name="delete_images[]" value="<?= $img_row['image_id'] ?>" class="hidden">
                </div>
            <?php endwhile; ?>

            <h3>Add New Images</h3>
            <div class="form-group">
                <input type="file" name="new_images[]" multiple>
            </div>

            <button type="submit" name="submit">Update Ad</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>