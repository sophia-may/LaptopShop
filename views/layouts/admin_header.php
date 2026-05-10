<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php?page=home");
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CellphoneS - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/srtdash/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/srtdash/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/srtdash/css/themify-icons.css">
    <link rel="stylesheet" href="assets/srtdash/css/metismenujs.min.css">
    <link rel="stylesheet" href="assets/srtdash/css/typography.css">
    <link rel="stylesheet" href="assets/srtdash/css/default-css.css">
    <link rel="stylesheet" href="assets/srtdash/css/style.css">
    <link rel="stylesheet" href="assets/srtdash/css/responsive.css">
    
    <!-- Custom styling to override Srtdash with CellphoneS primary red -->
    <style>
        :root {
            --primary-color: #e11b22;
            --sidebar-bg: #2c3e50;
            --sidebar-text: #a0a9b0;
            --sidebar-active: #e11b22;
        }
        
        /* Page Container & Layout */
        .page-container {
            display: flex;
            min-height: 100vh;
            flex-direction: row;
        }
        
        /* Sidebar Menu */
        .sidebar-menu {
            width: 250px;
            background: var(--sidebar-bg) !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            flex-shrink: 0;
            height: 100vh;
            overflow-y: auto;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }
        
        .sidebar-menu .sidebar-header {
            background: var(--primary-color) !important;
            padding: 20px 15px !important;
            border: none !important;
        }
        
        .sidebar-menu .sidebar-header h2 {
            color: white !important;
            font-size: 22px !important;
            margin: 0 !important;
            font-weight: 700 !important;
            letter-spacing: 0.5px;
        }
        
        .sidebar-menu .main-menu {
            margin: 0 !important;
            padding: 15px 0 !important;
        }
        
        .metismenu li {
            margin: 0 !important;
            border: none !important;
        }
        
        .metismenu > li > a {
            color: var(--sidebar-text) !important;
            padding: 12px 20px !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            border-left: 3px solid transparent !important;
        }
        
        .metismenu > li > a:hover {
            color: white !important;
            background: rgba(255,255,255,0.05) !important;
            padding-left: 24px !important;
        }
        
        .metismenu > li > a i {
            margin-right: 12px !important;
            font-size: 16px !important;
            min-width: 20px !important;
            color: var(--sidebar-text) !important;
        }
        
        .metismenu > li.active > a {
            color: var(--primary-color) !important;
            background: rgba(225, 27, 34, 0.1) !important;
            border-left-color: var(--primary-color) !important;
            padding-left: 17px !important;
            font-weight: 600 !important;
        }
        
        .metismenu > li.active > a i {
            color: var(--primary-color) !important;
        }
        
        .metismenu .collapse {
            background: rgba(0,0,0,0.2) !important;
        }
        
        .metismenu .collapse > li > a {
            padding-left: 40px !important;
            color: rgba(160, 169, 176, 0.9) !important;
            font-size: 13px !important;
        }
        
        .metismenu .collapse > li.active > a {
            color: var(--primary-color) !important;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            display: flex;
            flex-direction: column;
        }
        
        /* Header Area */
        .header-area {
            background: white !important;
            border-bottom: 1px solid #e0e0e0 !important;
            padding: 15px 30px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }
        
        .search-box {
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .search-box input {
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            padding: 8px 12px !important;
            min-width: 300px !important;
            transition: all 0.3s ease !important;
        }
        
        .search-box input:focus {
            outline: none !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 2px rgba(225, 27, 34, 0.1) !important;
        }
        
        .notification-area {
            margin: 0 !important;
            list-style: none !important;
            display: flex !important;
            gap: 20px !important;
        }
        
        .notification-area li {
            cursor: pointer !important;
            color: #666 !important;
            font-size: 18px !important;
            transition: color 0.3s ease !important;
        }
        
        .notification-area li:hover {
            color: var(--primary-color) !important;
        }
        
        /* Page Title Area */
        .page-title-area {
            background: white !important;
            padding: 20px 30px !important;
            border-bottom: 1px solid #e0e0e0 !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }
        
        .page-title {
            color: #333 !important;
            font-size: 24px !important;
            font-weight: 600 !important;
            margin: 0 !important;
        }
        
        /* User Profile */
        .user-profile {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }
        
        .user-thumb {
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
        }
        
        .user-name {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            padding: 6px 12px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }
        
        .user-dropdown {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
            border: none !important;
            border-radius: 6px !important;
        }
        
        .user-dropdown .dropdown-item {
            padding: 10px 16px !important;
            color: #333 !important;
            transition: all 0.3s ease !important;
        }
        
        .user-dropdown .dropdown-item:hover {
            background: #f5f5f5 !important;
            color: var(--primary-color) !important;
        }
        
        /* Main Content Inner */
        .main-content-inner {
            flex: 1;
            padding: 30px !important;
            overflow-y: auto !important;
        }
        
        /* Card Styles */
        .card {
            border-radius: 8px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
            border: 1px solid #e8eef5 !important;
        }
        
        .card-body {
            padding: 20px !important;
        }
        
        /* Primary Button */
        .btn-primary, .bg-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        
        .btn-primary:hover {
            background-color: #c71a1a !important;
            border-color: #c71a1a !important;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="page-container">
        <!-- sidebar menu area start -->
        <?php include 'views/layouts/admin_sidebar.php'; ?>
        <!-- sidebar menu area end -->

        <!-- main content area start -->
        <div class="main-content">
            <!-- header area start -->
            <div class="header-area">
                <div class="row align-items-center">
                    <div class="col-md-6 col-sm-8 clearfix">
                        <div class="nav-btn float-start">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <div class="search-box float-start">
                            <form action="#">
                                <input type="text" name="search" placeholder="Search..." required>
                                <i class="ti-search"></i>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-4 clearfix">
                        <ul class="notification-area float-end">
                            <li id="full-view"><i class="ti-fullscreen"></i></li>
                            <li id="full-view-exit"><i class="ti-zoom-out"></i></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- header area end -->

            <!-- page title area start -->
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title float-start">Admin Panel</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile float-end">
                            <img class="avatar user-thumb" src="assets/img/placeholder.png" alt="avatar">
                            <h4 class="user-name dropdown-toggle" data-bs-toggle="dropdown">
                                <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?> <i class="fa-solid fa-angle-down"></i>
                            </h4>
                            <div class="dropdown-menu user-dropdown">
                                <a class="dropdown-item" href="?page=home">Back to Site</a>
                                <a class="dropdown-item" href="?page=logout">Log Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- page title area end -->
            
            <div class="main-content-inner">
