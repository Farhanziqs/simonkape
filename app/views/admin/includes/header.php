<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title'] ?? 'SIMONKAPE'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
    <style>
        /* General Layout */
        body { font-family: Arial, sans-serif; margin: 0; display: flex; min-height: 100vh; background-color: #f8f9fa; }
        .sidebar {
            width: 250px;
            background-color: #ffffff;
            color: #343a40;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
            border-right: 1px solid #dee2e6;
            flex-shrink: 0;
            transition: transform 0.3s ease-in-out;
            position: fixed;
            height: 100%;
            z-index: 100;
        }
        .sidebar-title { text-align: center; margin-bottom: 30px; color: #007bff; font-weight: bold; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li a { display: block; padding: 15px 20px; color: #343a40; text-decoration: none; border-bottom: 1px solid #f8f9fa; transition: all 0.3s ease; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background-color: #e9f5ff; color: #007bff; border-left: 3px solid #007bff; padding-left: 17px; }
        .logout-btn { background-color: transparent; color: #dc3545; margin-top: 30px; display: block; text-align: center; border: 1px solid #dc3545; }
        .logout-btn:hover { background-color: #dc3545; color: white; }

        .main-content { flex-grow: 1; padding: 20px; background-color: #f8f9fa; display: flex; flex-direction: column; margin-left: 250px; transition: margin-left 0.3s ease-in-out; }
        .header { background-color: #fff; padding: 15px 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px;}
        .header h2 { margin: 0; color: #333; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;}

        .hamburger-menu { display: none; cursor: pointer; }
        .hamburger-menu div { width: 25px; height: 3px; background-color: #333; margin: 5px 0; }

        /* Table Styles */
        .table-container { overflow-x: auto; }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .header {
                justify-content: space-between;
            }
            .hamburger-menu {
                display: block;
            }
            .header h2 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="hamburger-menu" id="hamburgerMenu">
        <div></div>
        <div></div>
        <div></div>
    </div>
