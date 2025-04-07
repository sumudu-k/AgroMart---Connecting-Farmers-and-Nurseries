<?php
session_start();
ob_start();
include 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

// check user blocked or not
$user_id = $_SESSION['user_id'];

$sql_block_ckeck = "SELECT * FROM users WHERE user_id=?";
$stmt_block_ckeck = $conn->prepare($sql_block_ckeck);
$stmt_block_ckeck->bind_param('i', $user_id);
$stmt_block_ckeck->execute();
$result_block_check = $stmt_block_ckeck->get_result();
$block_result = $result_block_check->fetch_assoc();

if ($block_result['status'] == 'y') {
    echo "<script>
        window.onload = function() {
            showAlert('Your account has been blocked. You can not post ads', 'error', '#ff0000');
        };
        setTimeout(function() {
            window.location.href = 'profile.php';
        }, 2000);
        </script>";
}



function isValidContact($phone_number)
{
    return preg_match('/^0\d{9}$/', $phone_number);
}

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $phone_number = $_POST['phone_number'];
    $district = $_POST['district'];
    $user_id = $_SESSION['user_id'];
    $category_id = $_POST['category'];

    $_SESSION['u-title'] = $title;
    $_SESSION['u-description'] = $description;
    $_SESSION['u-price'] = $price;
    $_SESSION['u-phone_number'] = $phone_number;


    if (empty($title) || empty($description) || empty($district) || empty($category_id) || empty($price) || empty($phone_number)) {
        echo "<script>
        window.onload = function() {
            showAlert('Please fill all fields!', 'error', '#ff0000');
        };
        </script>";
    } elseif (empty($_FILES['images']['name'][0])) {
        echo "<script>
        window.onload = function() {
            showAlert('Please upload at least one image!', 'error', '#ff0000');
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
    } elseif (!is_numeric($price)) {
        echo "<script>
            window.onload = function() {
                showAlert('Price must be a number!', 'error', '#ff0000');
            };
        </script>";
    } elseif (max($_FILES['images']['size']) > 1048576) {
        echo "<script>
            window.onload = function() {
                showAlert('Each file must be less than 1 MB!', 'error', '#ff0000');
            };
        </script>";
    } elseif (count($_FILES['images']['name']) > 5) {
        echo "<script>
            window.onload = function() {
                showAlert('You can upload maximum 5 images', 'error', '#ff0000');
            };
        </script>";
    } else {
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
        echo "<script>
        window.onload = function() {
            showAlert('Your ad has posted successfully', 'success', '#008000');
        };
        setTimeout(function() {
            window.location.href = 'my_ads.php';
        }, 2000);
        </script>";
        unset($_SESSION['u-title'], $_SESSION['u-description'], $_SESSION['u-price'], $_SESSION['u-phone_number']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Post Ad</title>
    <link rel="stylesheet" href="css/post_ad.css">
</head>

<body>
    <div class="main-content">
        <h1>Post an Ad Totally Free</h1>
        <form action="post_ad.php" method="POST" enctype="multipart/form-data" class="ad-form">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" placeholder="Enter title here. Max 150 characters"
                    value="<?= isset($_SESSION['u-title']) ? htmlspecialchars($_SESSION['u-title']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description"
                    placeholder="Describe your request here. Max 700 characters"><?= isset($_SESSION['u-description']) ? htmlspecialchars($_SESSION['u-description']) : '' ?></textarea>

            </div>

            <div class="form-group">
                <label for="district">District</label>
                <select name="district">
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
                <select name="category">
                    <option value="">Select Category</option>
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
                <input type="text" name="phone_number" placeholder="Enter 10 digit number"
                    value="<?= isset($_SESSION['u-phone_number']) ? htmlspecialchars($_SESSION['u-phone_number']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="text" name="price" placeholder="Rs"
                    value="<?= isset($_SESSION['u-price']) ? htmlspecialchars($_SESSION['u-price']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="images">Images</label>
                <input type="file" name="images[]" multiple>
            </div>
            <span><i class="fa fa-exclamation-circle" aria-hidden="true" id="icon"></i>You can upload maximum 5
                images</span>
            <?php
            if ($block_result['status'] == 'y'):
            ?>
            <button disabled type="submit" name="submit">Submit Ad</button>
            <?php else:
            ?>
            <button type="submit" name="submit">Submit Ad</button>
            <?php endif; ?>
        </form>
    </div>
    <?php include 'footer.php'; ?>
    <script src='alertFunction.js'></script>
</body>

</html>