<?php
include '../config.php';

//fetch pending admin registrations
$query = "SELECT * FROM admins WHERE status = 'pending'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<h2>Pending Admin Approvals</h2>";
    echo "<table border='1'>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Action</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['username']}</td>
                <td>{$row['email']}</td>
                <td>
                    <form method='POST'>
                        <input type='hidden' name='admin_id' value='{$row['admin_id']}'>
                        <button type='submit' name='approve'>Approve</button>
                        <button type='submit' name='reject'>Reject</button>
                    </form>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No pending admin requests.";
}

//handle approval or rejecton
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_id = $_POST['admin_id'];

    if (isset($_POST['approve'])) {
        $update_status = $conn->prepare("UPDATE admins SET status = 'approved' WHERE admin_id = ?");
    } elseif (isset($_POST['reject'])) {
        $update_status = $conn->prepare("DELETE FROM admins WHERE admin_id = ?");
    }

    $update_status->bind_param('i', $admin_id);
    if ($update_status->execute()) {
        echo "<script>alert('Action successful!');window.location = 'admin_approval.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}