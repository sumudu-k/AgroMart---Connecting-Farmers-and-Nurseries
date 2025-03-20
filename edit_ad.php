<?php
session_start();
include 'config.php';

$ad_id = $_GET['ad_id'];

function isValidContact($phone_number)
{
    return preg_match('/^0\d{9}$/', $phone_number);
}


$ad_sql = "SELECT * FROM ads WHERE ad_id = ?";
$stmt = $conn->prepare($ad_sql);
$stmt->bind_param("i", $ad_id);
$stmt->execute();
$ad_result = $stmt->get_result();
$ad = $ad_result->fetch_assoc();

$img_sql = "SELECT * FROM ad_images WHERE ad_id = ?";
$stmt_img = $conn->prepare($img_sql);
$stmt_img->bind_param("i", $ad_id);
$stmt_img->execute();
$img_result = $stmt_img->get_result();

$categories_sql = "SELECT * FROM categories";
$categories_result = $conn->query($categories_sql);

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $phone_number = $_POST['phone_number'];
    $category_id = $_POST['category'];
    $district = $_POST['district'];

    if (empty($title) || empty($description) || empty($district) || empty($category_id) || empty($price) || empty($phone_number)) {
        echo "<script>
        window.onload = function() {
            showAlert('Please fill all fields!', 'error', '#ff0000');
        };
        </script>";
    } elseif (strlen($title) < 20) {
        echo "<script>
            window.onload = function() {
                showAlert('Title is too short!', 'error', '#ff0000');
            };
        </script>";
    } elseif (strlen($title) > 150) {
        echo "<script>
            window.onload = function() {
                showAlert('Title is too long!', 'error', '#ff0000');
            };
        </script>";
    } elseif (strlen($description) < 20) {
        echo "<script>
            window.onload = function() {
                showAlert('Description is too short!', 'error', '#ff0000');
            };
        </script>";
    } elseif (strlen($description) > 700) {
        echo "<script>
            window.onload = function() {
                showAlert('Description is too long!', 'error', '#ff0000');
            };  
        </script>";
    } elseif (!isValidContact($phone_number)) {
        echo "<script>
            window.onload = function() {
                showAlert('Invalid Contact Number!', 'error', '#ff0000');
            };
        </script>";
    } elseif (is_numeric($price) == false) {
        echo "<script>
            window.onload = function() {
                showAlert('Price must be a number!', 'error', '#ff0000');
            };
        </script>";
    } else {

        $update_sql = "UPDATE ads SET title = ?, description = ?, price = ?, phone_number = ?, category_id = ?, district = ? WHERE ad_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssdsisi", $title, $description, $price, $phone_number, $category_id, $district, $ad_id);
        $stmt->execute();

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
        echo "<script>
    window.onload = function() {
        showAlert('Ad updated successfully!', 'success', '#008000');
    };
        setTimeout(function() {
    window.location.href = 'my_ads.php';
    }, 2000);
</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'navbar.php'; ?>
<link rel="stylesheet" href="css/edit_ad.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="removeImage.js" defer></script>
    <title>Edit Ad - AgroMart</title>

    <script>
    function removeImage(button) {
        button.parentElement.classList.add('hidden');
        button.nextElementSibling.checked = true;
    }
    </script>
</head>

<body>
    <div class="main-content">
        <h2>Edit Your Advertisement</h2>
        <form action="edit_ad.php?ad_id=<?= $ad_id ?>" method="POST" enctype="multipart/form-data" class="ad-form">
            <div class="form-group">
                <label for="title">Ad Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($ad['title']) ?>">
            </div>

            <div class="form-group">
                <label for="description">Ad Description</label>
                <textarea name="description"><?= htmlspecialchars($ad['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="text" name="price" value="<?= htmlspecialchars($ad['price']) ?>">
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" name="phone_number" value="<?= htmlspecialchars($ad['phone_number']) ?>">
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select name="category">
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
                <select name="district">
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
    <script src='alertFunction.js'></script>
</body>

</html>