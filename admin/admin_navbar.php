
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            list-style: none;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
            }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 20px;
            background: #006400;
            color: white;
            position: relative; 
        }
        
        .navbar::before {
            content: "";
            position: absolute;
            left: 0; 
            top: 25%; 
            width: 2px; 
            height: 50%; 
            background-color: white; 
        }

        .logo {
            font-size: 25px;
            font-weight: 600;
            color: #fff;
            text-transform: uppercase;
            text-align: center;
        }


        .nav-right {
            display: flex;
            align-items: center;
        }

        .nav-right a, .nav-right span {
            color: white;
            margin-left: 20px;
            font-size: 20px;
            font-weight: 600;
        }

        .btn-login, .btn-register {
            background-color: #4CAF50; 
            padding: 5px 10px;
            border-radius: 4px;
        }

        .btn-login:hover, .btn-register:hover {
            background-color: #45a049;
        }

        .btn-logout {
            background-color: #f44336; 
            padding: 5px 10px;
            border-radius: 4px;
        }

        .btn-logout:hover {
            background-color: #d32f2f;
        }
    </style>

</head>
<body>
    
    <nav>
        <div class="navbar">
            <div class="logo">Admin Dashboard</div>
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

</body>
</html>



