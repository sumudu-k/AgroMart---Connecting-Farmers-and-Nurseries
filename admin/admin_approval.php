<?php
session_start();
include '../config.php';
include '../alertFunction.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle approval or rejection
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
        echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
    }
}

// Start output buffering
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

    .admin-approval-container {
        max-width: 90%;
        margin: 20px auto;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1;
    }

    .admin-approval-container h1 {
        text-align: center;
        font-size: 2rem;
        color: #333;
        padding: 10px 0;
        border-bottom: 2px solid #007a33;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 40px;
    }

    th,
    td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        vertical-align: middle;
    }

    th {
        text-align: center;
        background-color: #a9e6a9;
        font-weight: 600;
        color: #333;
        border-right: 2px solid rgba(51, 51, 51, 0.2);
    }

    th:last-child {
        border-right: none;
    }

    td:last-child {
        text-align: center;
    }

    tr {
        transition: background-color 0.2s ease;
    }

    tr:hover {
        background-color: #e6ffe6;
    }

    .approve-button,
    .reject-button {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        color: white;
        margin-right: 5px;
        transition: background-color 0.2s ease;
    }

    .approve-button {
        background-color: #007a33;
    }

    .approve-button:hover {
        background-color: #005922;
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
        font-size: 1.1rem;
        margin-top: 20px;
        padding: 15px;
        background-color: #fff;
        border-radius: 5px;
    }
</style>

<div class="admin-approval-container">
    <?php
    // Fetch pending admin registrations
    $query = "SELECT * FROM admins WHERE status = 'pending'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<h1>Pending Admin Approvals</h1>";
        echo "<table>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td data-label='Username'>" . htmlspecialchars($row['username']) . "</td>
                    <td data-label='Email'>" . htmlspecialchars($row['email']) . "</td>
                    <td data-label='Action'>
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
// Capture the content and include the layout
$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>