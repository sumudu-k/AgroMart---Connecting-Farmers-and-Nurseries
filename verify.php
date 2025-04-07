<?php

include 'config.php';
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <style>
    section {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 12px;
        font-family: Arial, sans-serif;
    }

    section h2 {
        color: #2b9348;
    }

    ul {
        list-style-type: none;
        padding-left: 0;
    }

    ul li {
        margin-bottom: 15px;
        line-height: 1.6;
    }

    strong {
        color: #333;
    }

    button {
        background-color: green;
        padding: 10px;
        font-size: 20px;
        color: white;
    }
    </style>

    <h1>Verify seller page</h1>
</body>
<h4>Becoming a Verified Seller on MyAgroMart helps build trust with buyers and enhances your credibility. To ensure
    quality and reliability, the following criteria must be fulfilled:</h4>

<section>
    <h2>✅ Verified Seller Criteria</h2>
    <p>To become a Verified Seller on <strong>MyAgroMart</strong>, the following conditions must be met:</p>

    <ul>
        <li>
            <strong>🔹 1. Phone Number Verification:</strong><br>
            Seller must verify their phone number (via OTP or admin confirmation).
        </li>
        <li>
            <strong>🔹 2. Email Verification:</strong><br>
            Email must be confirmed through a verification link.
        </li>
        <li>
            <strong>🔹 3. NIC (National ID) Upload:</strong><br>
            Seller uploads a valid Sri Lankan NIC, passport, or driver’s license.
        </li>
        <li>
            <strong>🔹 4. Business Registration Certificate:</strong><br>
            Seller should provide a valid Business Registration Certificate.
        </li>
        <li>
            <strong>🔹 5. Completed Profile:</strong><br>
            Must have a complete seller profile including name, contact info, and address.
        </li>
        <li>
            <strong>🔹 6. Minimum Number of Ads Posted:</strong><br>
            Sellers must post at least <strong>10 ads</strong> to request verification.
        </li>
        <li>
            <strong>🔹 7. No Rule Violations:</strong><br>
            Seller has not violated site policies (spam, fake ads, etc.).
        </li>
        <li>
            <strong>🔹 8. Manual Review by Admin:</strong><br>
            Even after meeting the above, admin manually approves the seller.
        </li>
    </ul>
    <a href='go_to_verify.php'><button> Go to verify</button></a>
</section>


</html>

<?php
include 'footer.php';
?>