<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit();
}

// check user clicked request verify
$sql = "SELECT * FROM verification_requests WHERE status='pending' AND user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// check user already verified
$verified_sql = "SELECT * FROM verification_requests WHERE status='approved' AND user_id=?";
$verified_stmt = $conn->prepare($verified_sql);
$verified_stmt->bind_param('i', $user_id);
$verified_stmt->execute();
$verified_result = $verified_stmt->get_result();


if ($result->num_rows > 0) :
    $row = $result->fetch_assoc();
?>
<p>You already submit the request on <?= $row['request_date'] ?>. Please wait for Admin approval. This may takes 2
    working days</p>
<button disabled>Request Now!</button>


<?php

elseif ($verified_result->num_rows > 0) :
    $verified_row = $verified_result->fetch_assoc();
    echo 'You are already verified seller';

else: ?>
<p>submit now</p>
<form action="submit_verification.php" method='post'>
    <button name='submit' onclick="return confirm('Are you sure want to request the verifiy badge?');">Request
        Now!</button>
</form>

<?php endif;




?>