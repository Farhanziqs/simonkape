<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title'] ?? 'SIMONKAPE Dosen'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
    <style>
        /* General Layout & New Theme */
        body { font-family: Arial, sans-serif; margin: 0; display: flex; min-height: 100vh; background-color: #f8f9fa; }
        .dosen-sidebar {
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
        .dosen-sidebar ul { list-style: none; padding: 0; }
        .dosen-sidebar ul li a { display: block; padding: 15px 20px; color: #343a40; text-decoration: none; border-bottom: 1px solid #f8f9fa; transition: all 0.3s ease; }
        .dosen-sidebar ul li a:hover, .dosen-sidebar ul li a.active { background-color: #e9f5ff; color: #007bff; border-left: 3px solid #007bff; padding-left: 17px; }
        .logout-btn { background-color: transparent !important; color: #dc3545 !important; margin-top: 30px; display: block; text-align: center; border: 1px solid #dc3545; }
        .logout-btn:hover { background-color: #dc3545 !important; color: white !important; }

        .dosen-main-content { flex-grow: 1; padding: 20px; background-color: #f8f9fa; display: flex; flex-direction: column; margin-left: 250px; transition: margin-left 0.3s ease-in-out; }
        .dosen-header { background-color: #fff; padding: 15px 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px;}
        .dosen-header h2 { margin: 0; color: #333; }
        .dosen-container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;}

        /* Hamburger Menu for Responsive */
        .hamburger-menu { display: none; cursor: pointer; position: fixed; top: 15px; left: 15px; z-index: 101; }
        .hamburger-menu div { width: 25px; height: 3px; background-color: #333; margin: 5px 0; }

        /* Styles for Content - DIKEMBALIKAN */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            padding: 25px;
            text-align: center;
            transition: transform 0.2s ease-in-out;
        }
        .card:hover { transform: translateY(-5px); }
        .card h3 { color: #333; margin-top: 0; margin-bottom: 15px; font-size: 1.5em; }
        .card .icon { font-size: 3em; color: #007bff; margin-bottom: 15px; }
        .card .btn { margin-top: 15px; padding: 10px 20px; border-radius: 5px; font-weight: bold; text-decoration: none; }
        .btn-green { background-color: #28a745; color: white; }
        .btn-green:hover { background-color: #218838; }
        .btn-blue { background-color: #007bff; color: white; }
        .btn-blue:hover { background-color: #0056b3; }
        .btn-yellow { background-color: #ffc107; color: #333; }
        .btn-yellow:hover { background-color: #e0a800; }

        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { border: 1px solid #dee2e6; padding: 8px; text-align: left; }
        table th { background-color: #f2f2f2; color: #333; }
        table tbody tr:nth-child(even) { background-color: #f9f9f9; }
        table tbody tr:hover { background-color: #f1f1f1; }

        .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; margin-right: 5px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-warning { background-color: #ffc107; color: #333; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-success { background-color: #28a745; color: white; }

        /* Filter form */
        .filter-container { background-color: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #eee; }
        .filter-group { display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; }
        .filter-group label { font-weight: bold; margin-bottom: 5px; }
        .filter-group select, .filter-group input { padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
        .filter-group div { flex: 1; min-width: 150px; }

        /* Modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fff; margin: 10% auto; padding: 30px; border-radius: 10px; width: 90%; max-width: 600px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); position: relative; }
        .close-button { position: absolute; top: 10px; right: 20px; font-size: 30px; font-weight: bold; color: #aaa; cursor: pointer; }
        .close-button:hover { color: #333; }
        .modal-content h3 { margin-top: 0; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .modal-content img { max-width: 100%; height: auto; margin-top: 15px; border-radius: 5px; }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .dosen-sidebar {
                transform: translateX(-250px);
            }
            .dosen-sidebar.show {
                transform: translateX(0);
            }
            .dosen-main-content {
                margin-left: 0;
            }
            .hamburger-menu {
                display: block;
            }
            .dosen-header {
                padding-left: 50px; /* Memberi ruang untuk hamburger menu */
            }
            .dosen-header h2 {
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
