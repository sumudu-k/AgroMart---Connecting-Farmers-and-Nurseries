<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include '../config.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        
        <title>Admin Dashboard</title>
        <style>
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                list-style: none;
                text-decoration: none;
                font-family: 'Poppins', sans-serif;
            }
            body {
                background-color: #E9ECEF; 
            }

            
            .wrapper {
                display: flex;
                position: relative;
                flex-wrap: wrap;
            }

            .wrapper .sidebar {
                position: fixed;
                width: 200px;
                height: 100%;
                background: #006400; 
                padding: 23px 0 30px 0;
                transition: all 0.3s ease; 
            }
            .wrapper .sidebar .logo {
                text-align: center;
                margin-bottom: 30px; 
            }

            .wrapper .sidebar .logo a {
                font-size: 35px;
                font-weight: 600;
                color: #fff; 
                text-transform: capitalize;
            }

            
            .wrapper .sidebar ul li {
                border-bottom: 1px solid rgba(0,0,0,0.05); 
                border-top: 1px solid rgba(225,225,225,0.05); 
            }

            .wrapper .sidebar ul li a {
                color: #fff; 
                display: block;
                padding: 15px;
            }

            
            .sidebar ul li a.active {
                background-color: #E9ECEF; 
                color: black; 
            }

            
            .wrapper .sidebar ul li a .fa {
                width: 25px;
            }

            
            .wrapper .sidebar ul li:hover {
                background-color: #E9ECEF; 
            }

            
            .wrapper .sidebar ul li:hover a {
                color: black;
            }

            
            .wrapper .main-content {
                width: 100%;
                margin-left: 200px; 
            }

            .wrapper .main-content .header {
                padding: 20px;
                background: #fff;
                color: #717171; 
                border-bottom: 1px solid #e0e4e8; 
            }

            .wrapper .main-content .info {
                margin: 20px;
                line-height: 25px;
            }

           
            .menu-toggle {
                display: none; 
                background-color: #006400;
                color: #fff;
                padding: 10px;
                border: none;
                font-size: 20px;
                width: 100%;
                text-align: center;
                cursor: pointer;
            }

                        @media (max-width: 768px) {
                .menu-toggle {
                    display: block; 
                }

                
                .wrapper .sidebar {
                    display: none;
                    position: static; 
                }

                
                .wrapper .sidebar.show {
                    display: block;
                }
            }



            
           
            @media (max-width: 768px) {
               
                .wrapper {
                    flex-direction: column;
                }
                
                .wrapper .sidebar {
                    position: static;
                    width: 100%;
                    height: auto;
                    border-right: none;
                    display: none; 
                }

                .wrapper .sidebar.show {
                    display: block; 
                }

                
                .menu-toggle {
                    display: block;
                    background-color: #006400;
                    color: #fff;
                    padding: 10px;
                    border: none;
                    font-size: 20px;
                    width: 100%;
                    text-align: center;
                    cursor: pointer;
                }

                
                .wrapper .main-content {
                    margin-left: 0;
                }

                
                .wrapper .sidebar ul li a {
                    padding: 10px;
                    font-size: 16px;
                }
            }

            
            @media (max-width: 480px) {
                .wrapper .sidebar ul li a {
                    font-size: 14px;
                    padding: 12px;
                }

                .wrapper .main-content .info {
                    font-size: 14px;
                    margin: 10px;
                }
            }

        </style>

        <script>
            
            function loadContent(page, element) {
                const xhr = new XMLHttpRequest();
                xhr.open("GET", page, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        
                        document.querySelector('.info').innerHTML = xhr.responseText;
                        
                        
                        document.querySelectorAll('.sidebar ul li a').forEach(link => {
                            link.classList.remove('active');
                        });

                        
                        element.classList.add('active');
                    }
                };
                xhr.send();
            }

            
            xhr.onerror = function() {
                alert("Failed to load the content. Please try again.");
            };

            function toggleMenu() {
                document.querySelector('.sidebar').classList.toggle('show');
            }
        </script>
    </head>
    <body>
        <button class="menu-toggle" onclick="toggleMenu()">☰ Menu</button>

        <div class="wrapper">
            
            <div class="sidebar">
                <div class="logo"><a href="admin_dashboard.php">AgroMart</a></div>
                <ul>
                    
                    <li><a href="javascript:void(0);" onclick="loadContent('add_category.php', this)"> <i class="fa fa-solid fa-folder-plus"></i> Add Category</a></li>
                    <li><a href="javascript:void(0);" onclick="loadContent('delete_category.php', this)"> <i class="fa fa-trash" aria-hidden="true"></i> Delete Category</a></li>
                    <li><a href="javascript:void(0);" onclick="loadContent('view_users.php', this)"> <i class="fa fa-solid fa-user"></i> Manage Users</a></li>
                    <li><a href="javascript:void(0);" onclick="loadContent('view_ads.php', this)"> <i class="fa fa-ad"></i> View & Delete Ads</a></li>
                </ul>
            </div>

            
            <div class="main-content">
                <?php include 'admin_navbar.php'; ?> 
                <div class="info">
                    <!-- The content of the selected page will load here -->
                </div>
            </div>
        </div>
    </body>
    
</html>