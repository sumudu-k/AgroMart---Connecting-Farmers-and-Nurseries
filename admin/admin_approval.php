<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}


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


ob_start();
?>

<style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #ddd;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    th {
        text-align: center;
        background-color: #f2f2f2;
        font-weight: bold;
    }

    tr:hover {
        background-color: #f5f5f5;
    }

    .approve-button, .reject-button {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        color: white;
        margin-right: 5px;
    }

    .approve-button {
        background-color: #007a33;
    }

    .approve-button:hover {
        background-color: #45a049;
    }

    .reject-button {
        background-color: #f44336;
    }

    .reject-button:hover {
        background-color: #d32f2f;
    }

    .no-requests {
        text-align: center;
        color: #666;
        font-size: 16px;
        margin-top: 20px;
    }

    @media screen and (max-width: 768px) {
        .container {
            padding: 15px;
        }

        table {
            font-size: 14px;
        }

        th, td {
            padding: 8px;
        }

        .approve-button, .reject-button {
            font-size: 12px;
            padding: 6px 12px;
        }
    }
</style>

<div class="container">
    <?php
  
    $query = "SELECT * FROM admins WHERE status = 'pending'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<h2>Pending Admin Approvals</h2>";
        echo "<table>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['username']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>
                        <form method='POST'>
                            <input type='hidden' name='admin_id' value='" . $row['admin_id'] . "'>
                            <button type='submit' name='approve' class='approve-button'>Approve</button>
                            <button type='submit' name='reject' class='reject-button'>Reject</button>
                        </form>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='no-requests'>No pending admin requests.</p>";
    }
    ?>
</div>

<?php

$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>