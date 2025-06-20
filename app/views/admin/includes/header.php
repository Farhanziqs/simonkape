<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title'] ?? 'SIMONKAPE'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
    <style>
        /* General Layout */
        body { font-family: Arial, sans-serif; margin: 0; display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: #2c3e50; color: white; padding-top: 20px; box-shadow: 2px 0 5px rgba(0,0,0,0.1); flex-shrink: 0; }
        .sidebar h3 { text-align: center; margin-bottom: 30px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li a { display: block; padding: 15px 20px; color: white; text-decoration: none; border-bottom: 1px solid #34495e; transition: background-color 0.3s ease; }
        .sidebar ul li a:hover, .sidebar ul li a.active { background-color: #34495e; }
        .main-content { flex-grow: 1; padding: 20px; background-color: #ecf0f1; display: flex; flex-direction: column; }
        .header { background-color: #fff; padding: 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px;}
        .header h2 { margin: 0; color: #333; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;}

        /* Form Styles */
        .form-container { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
        .form-container h3 { margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="date"],
        .form-group select,
        .form-group textarea { width: calc(100% - 22px); padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .multiselect-box { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }

        /* Button Styles */
        .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; margin-right: 5px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-success:hover { background-color: #218838; }
        .btn-warning { background-color: #ffc107; color: #333; }
        .btn-warning:hover { background-color: #e0a800; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-danger:hover { background-color: #c82333; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .btn-secondary:hover { background-color: #5a6268; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .logout-btn { background-color: #e74c3c; color: white; margin-top: 30px; display: block; text-align: center; }

        /* Table Styles */
        .table-container { margin-top: 20px; }
        .table-container h3 { margin-bottom: 15px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background-color: #f2f2f2; color: #333; }
        table tbody tr:nth-child(even) { background-color: #f9f9f9; }
        table tbody tr:hover { background-color: #f1f1f1; }

        /* Alert Messages */
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        /* Stats Grid for Dashboard/Laporan */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; margin-bottom: 30px; }
        .stat-card { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        .stat-card h3 { color: #555; margin-bottom: 10px; }
        .stat-card p { font-size: 2em; font-weight: bold; color: #007bff; }

        .report-detail { margin-top: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9;}
        .report-detail h3 { margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .report-detail h4 { margin-top: 0; color: #555; margin-bottom: 15px;}
    </style>
</head>
<body>
