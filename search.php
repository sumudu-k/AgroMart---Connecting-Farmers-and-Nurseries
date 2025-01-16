
<?php
include 'config.php'; 
$search_results = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['query'])) {
    $query = $_GET['query'];

    
    $sql = "SELECT * FROM ads WHERE title LIKE ? OR description LIKE ? OR keyword LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term = '%' . $query . '%';
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h2>Search Products</h2>
    <form method="GET" action="search.php">
        <input type="text" name="query" placeholder="Search for products..." required>
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($search_results)): ?>
    <h3>Search Results:</h3>
    <ul>
        <?php foreach ($search_results as $ad): ?>
        <li>
            <h3><?= $ad['title'] ?></h3>
            <p><?= $ad['description'] ?></p>
            <p><strong>Price:</strong> <?= $ad['price'] ?></p>
            <a href="view_ad.php?ad_id=<?= $ad['ad_id'] ?>">View Details</a>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php elseif (isset($_GET['query'])): ?>
    <p>No results found for "<?= htmlspecialchars($_GET['query']) ?>"</p>
    <?php endif; ?>
</body>

</html>