<?php
include 'config.php';
include 'navbar.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>
    window.onload = function() {
        showAlert('Please login to view your cart', 'error', '#ff0000');
    };
</script>";
    header('Location: login.php');
    exit();
} else {
    $user_id = $_SESSION['user_id'];
}



$sql_get = "
    SELECT cart.*, ads.*, 
           (SELECT image_path FROM ad_images WHERE ad_id = ads.ad_id LIMIT 1) AS image_path
    FROM cart 
    JOIN ads ON cart.ad_id = ads.ad_id 
    WHERE cart.user_id = ?";

$stmt_get = $conn->prepare($sql_get);
$stmt_get->bind_param('i', $user_id);
$stmt_get->execute();
$result_get = $stmt_get->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #f7f7f7;
    }

    h1 {
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
        padding: 15px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #333;
        color: white;
    }

    button {
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .qty-btn {
        background-color: #007bff;
        color: white;
    }

    .remove-btn {
        background-color: red;
        color: white;
    }

    #grand-total {
        font-size: 1.4rem;
        font-weight: bold;
    }

    img {
        border-radius: 5px;
    }
    </style>
</head>

<body>

    <h1>My Cart</h1>
    <table id="cart-table">
        <tr>
            <th>Select</th>
            <th>Image</th>
            <th>Product</th>
            <th>Price</th>
            <th>Available</th>
            <th>Qty</th>
            <th>Total</th>
            <th>Remove</th>
        </tr>

        <?php while ($row = $result_get->fetch_assoc()):
            $item_total = $row['price'] * $row['qty'];
        ?>
        <?php
            $isOutOfStock = $row['quantity'] == 0;
            $qty = $isOutOfStock ? 0 : $row['qty'];
            ?>
        <tr data-cart-id="<?= $row['cart_id'] ?>" data-ad-id="<?= $row['ad_id'] ?>">
            <td><input type="checkbox" class="item-select" <?= $isOutOfStock ? 'disabled' : '' ?>></td>

            <td><a href="view_ad.php?ad_id=<?= $row['ad_id'] ?>"><img src="<?= $row['image_path'] ?>" width="100"></a>
            </td>
            <td><a href="view_ad.php?ad_id=<?= $row['ad_id'] ?>"><?= $row['title'] ?></a>
                <!-- quantity -->
                <?php if ($row['quantity'] == 0): ?>
                <p style="color:white; background-color:red; padding:5px 10px;">Almost soldout</p>
                <?php elseif ($row['quantity'] <= 10): ?>
                <p style="color:white; background-color:orange; padding:5px 10px;"> <?= $row['quantity'] ?> Items
                    left</p>

                <?php else: ?>

                <?php endif; ?>
            </td>
            <td class="price"><?= $row['price'] ?></td>
            <td class="stock"><?= $row['quantity'] ?></td>
            <td>
                <button class="qty-btn" data-action="decrease">-</button>
                <span class="qty"><?= $qty ?></span>
                <button class="qty-btn" data-action="increase">+</button>
            </td>
            <td class="item-total"><?= $row['price'] * $qty ?></td>

            <td>
                <button class="remove-btn">X</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Grand Total: Rs. <span id="grand-total">0</span></h3>

    <script>
    function updateGrandTotal() {
        let selectedCartIds = [];

        $('.item-select:checked').each(function() {
            const row = $(this).closest('tr');
            selectedCartIds.push(row.data('cart-id'));
        });

        $.post('fetch_cart_total.php', {
            cart_ids: selectedCartIds
        }, function(data) {
            $('#grand-total').text(data);
        });
    }


    $(document).ready(function() {
        updateGrandTotal();

        $('.qty-btn').click(function() {
            const btn = $(this);
            const row = btn.closest('tr');
            const action = btn.data('action');
            const cart_id = row.data('cart-id');
            const ad_id = row.data('ad-id');
            const currentQty = parseInt(row.find('.qty').text());
            const stock = parseInt(row.find('.stock').text());

            let newQty = currentQty;
            if (action === 'increase') {
                if (currentQty < stock) {
                    newQty++;
                } else {
                    //alert('Only ' + stock + ' items left in stock!');
                    showAlert('Only ' + stock + ' items left in stock!', 'error', '#ff0000');
                    return;
                }
            } else if (action === 'decrease') {
                if (currentQty > 1) {
                    newQty--;
                }
            }


            $.post('update_cart_quantity.php', {
                cart_id: cart_id,
                qty: newQty,
                ad_id: ad_id
            }, function(response) {
                if (response.success) {
                    row.find('.qty').text(newQty);
                    const price = parseFloat(row.find('.price').text());
                    row.find('.item-total').text(price * newQty);
                    updateGrandTotal();
                } else {
                    alert(response.message);
                }
            }, 'json');
        });

        $('.remove-btn').click(function() {
            if (!confirm('Are you sure you want to remove this item?')) return;

            const row = $(this).closest('tr');
            const cart_id = row.data('cart-id');

            $.post('remove_cart_item.php', {
                cart_id: cart_id
            }, function(response) {
                if (response.success) {
                    row.remove();
                    updateGrandTotal();
                } else {
                    alert('Failed to remove item.');
                }
            }, 'json');
        });
    });

    // Trigger when checkbox changes
    $('.item-select').on('change', function() {
        updateGrandTotal();
    });

    // Update grand total after quantity changes
    $('.qty-btn').click(function() {
        // inside success callback after AJAX POST
        updateGrandTotal();
    });

    // Also update total after removing
    $('.remove-btn').click(function() {
        // inside success callback after removing
        updateGrandTotal();
    });
    </script>

</body>

</html>