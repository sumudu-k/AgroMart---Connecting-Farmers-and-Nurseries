<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// generate_hash.php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $merchant_id = $_ENV['MERCHANT_ID'];
    $merchant_secret = $_ENV['MERCHANT_SECRET'];

    $order_id = $_POST['order_id'];
    $amount = number_format($_POST['amount'], 2, '.', '');
    $currency = $_POST['currency'];

    $hash = strtoupper(
        md5(
            $merchant_id .
                $order_id .
                $amount .
                $currency .
                strtoupper(md5($merchant_secret))
        )
    );

    echo json_encode(['hash' => $hash]);
}
