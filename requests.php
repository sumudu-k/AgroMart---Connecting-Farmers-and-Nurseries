<?php
include 'config.php';
include 'navbar.php';

$sql = "SELECT plant_requests.*, users.username FROM plant_requests 
        JOIN users ON plant_requests.user_id = users.user_id 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Plant Requests</title>
    <!-- Add Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            width: 100%;
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
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            font-weight: normal;
            font-size: 1.2rem;
            padding: 20px 5px;
            margin: 10px 0;
        }

        .whatsapp-link {
            color: #25D366; /* WhatsApp green color */
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .whatsapp-link i {
            font-size: 24px;
            margin-left: 5px;
            transition: color 0.2s ease;
        }

        .whatsapp-link:hover {
            color: #128C7E; /* Darker shade on hover */
        }

        .whatsapp-link:hover i {
            color: #128C7E;
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

            .whatsapp-link i {
                font-size: 20px;
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
    <h2>All Plant Requests</h2>
    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="request-card">
                    <strong><?= htmlspecialchars($row['subject']); ?> by <?= htmlspecialchars($row['username']); ?></strong>
                    <p><?= htmlspecialchars($row['description']); ?></p>
                    <p>Contact Number: <?= htmlspecialchars($row['contact']); ?></p>
                    <p>Connect via: 
                        <a href="https://wa.me/+94<?= htmlspecialchars($row['contact']); ?>" target="_blank" class="whatsapp-link">
                            WhatsApp
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </p>
                    <p>District: <?= htmlspecialchars($row['district']); ?></p>
                    <p>Posted On: <?= htmlspecialchars($row['created_at']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-requests">No plant requests available.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
<?php
$conn->close();
?>