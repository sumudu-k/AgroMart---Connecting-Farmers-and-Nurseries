<?php
session_start();
include 'config.php';
// include 'navbar.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM plant_requests WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Plant Requests</title>
    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: "Poppins", Arial, sans-serif;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        position: relative;
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

    .container {
        width: 75%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        margin: 0 auto;
        padding: 20px;
        flex: 1;
    }

    h2 {
        background-color: #dbffc7;
        text-align: center;
        padding: 10px 0;
        font-size: 2rem;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .request-card {
        width: 70%;
        min-height: 100px;
        backdrop-filter: blur(10px);
        border: 1px solid rgb(148, 184, 129);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .request-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .request-card strong {
        text-align: center;
        text-decoration: underline;
        font-size: 1.5rem;
        color: #333;
        display: block;
        margin-bottom: 5px;
        text-transform: capitalize;
    }

    .request-card p {
        font-size: 1rem;
        font-weight: bold;
        color: #555;
        margin: 5px 0;
        line-height: 1.4;
    }

    .request-card p:nth-child(2) {
        background-color: rgba(219, 255, 199, 0.4);
        border-radius: 5px;
        text-align: center;
        font-weight: normal;
        font-size: 1.2rem;
        padding: 10px 5px;
        margin: 10px 0 10px
    }

    .request-actions {
        margin-top: 10px;
        display: flex;
        justify-content: center;
    }

    .request-actions a {
        display: inline-block;
        text-decoration: none;
        text-align: center;
        color: #fff;
        background-color: #4caf50;
        width: 80px;
        font-size: 1.1rem;
        padding: 5px 10px;
        border-radius: 5px;
        transition: background-color 0.2s ease;
    }

    .request-actions a:first-child {
        margin-right: 10px;
    }

    .request-actions a:first-child:hover {
        background-color: #006400;
        ;
    }

    .request-actions a:last-child:hover {
        background-color: #c82333;
    }

    .no-requests {
        text-align: center;
        font-size: 1.5rem;
        color: #666;
        margin-top: 50px;
    }

    /* Mobile Devices */
    @media screen and (max-width: 480px) {
        h2 {
            font-size: 1.5rem;
            padding: 15px 5%;
        }

        .container {
            width: 95%;
            padding: 10px;
        }

        .request-card {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
        }

        .request-card strong {
            font-size: 1rem;
        }

        .request-card p {
            font-size: 0.9rem;
        }

        .request-actions a {
            font-size: 0.85rem;
            padding: 4px 8px;
            width: 70px;
        }

        .request-actions a:last-child {
            background-color: #c82333;
            color: #fff;
        }
    }

    /* Tablets */
    @media screen and (min-width: 481px) and (max-width: 1200px) {
        h2 {
            font-size: 1.8rem;
            padding: 20px 8%;
        }

        .container {
            width: 95%;
        }

        .request-card {
            width: 80%;
            padding: 12px;
        }

        .request-card strong {
            font-size: 1.1rem;
        }

        .request-card p {
            font-size: 0.95rem;
        }
    }
    </style>
</head>

<body>
    <h2>My Plant Requests</h2>
    <div class="container">
        <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="request-card">
            <strong><?= htmlspecialchars($row['subject']); ?></strong>
            <p><?= htmlspecialchars($row['description']); ?></p>
            <p>Contact: <?= htmlspecialchars($row['contact']); ?></p>
            <p>District: <?= htmlspecialchars($row['district']); ?></p>
            <div class="request-actions">
                <a href="request_edit.php?id=<?= $row['request_id']; ?>">Edit</a>
                <a href="delete_request.php?id=<?= $row['request_id']; ?>">Delete</a>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p class="no-requests">You have no any requests.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
<?php
$stmt->close();
$conn->close();
?>