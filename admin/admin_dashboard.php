<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
    body {
        display: flex;
    }

    .sidebar {
        width: 200px;
        background-color: #333;
        color: white;
        height: 100vh;
        padding: 20px;
        position: fixed;
    }

    .sidebar a {
        display: block;
        padding: 10px;
        color: white;
        text-decoration: none;
    }

    .sidebar a:hover {
        background-color: #575757;
    }

    .content {
        margin-left: 220px;
        padding: 0px 20px;
        width: 100%;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table,
    th,
    td {
        border: 1px solid black;
    }

    th,
    td {
        padding: 12px;
        text-align: left;
    }

    .delete-button {
        color: red;
        text-decoration: none;
        cursor: pointer;
    }

    .delete-button:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <!-- Left Sidebar Menu -->
    <div class="sidebar">
        <h2>Admin Menu</h2>
        <a href="add_category.php">Add Category</a>
        <a href="delete_category.php">Delete Category</a>
        <a href="view_users.php">Manage Users</a>
        <a href="view_ads.php">View & Delete Ads</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <?php include 'admin_navbar.php'; ?>
        <h1>Welcome to Admin Dashboard</h1>

        <thead>
            <tr>
                <th>Category Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </div>
</body>

</html>