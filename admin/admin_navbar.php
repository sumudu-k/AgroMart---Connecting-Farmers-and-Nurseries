
<nav>
    <div class="navbar">
        <div class="logo">
            <a href="admin_dashboard.php">MyAgroMart - Admin Dashboard</a>
        </div>
        <div class="nav-right">
            <?php if (isset($_SESSION['admin_username'])): ?>
                <span>Welcome, <?= htmlspecialchars($_SESSION['admin_username']); ?></span>
                <a href="admin_logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
                <a href="admin_login.php" class="btn-login">Login</a>
                <a href="admin_register.php" class="btn-register">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #333;
    color: white;
}

.logo a {
    color: white;
    font-size: 18px;
    font-weight: bold;
    text-decoration: none;
}

.nav-right {
    display: flex;
    align-items: center;
}

.nav-right a, .nav-right span {
    color: white;
    margin-left: 20px;
    font-size: 16px;
}

.btn-login, .btn-register {
    text-decoration: none;
    background-color: #4CAF50;
    padding: 5px 10px;
    border-radius: 4px;
}

.btn-login:hover, .btn-register:hover {
    background-color: #45a049;
}

.btn-logout {
    text-decoration: none;
    background-color: #f44336;
    padding: 5px 10px;
    border-radius: 4px;
}

.btn-logout:hover {
    background-color: #d32f2f;
}
</style>
