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
    <link rel="stylesheet" href="assets/srtdash/css/styles.css">
    <link rel="stylesheet" href="assets/srtdash/css/responsive.css">
    
    <!-- Custom styling to override Srtdash with CellphoneS primary red -->
    <style>
        :root {
            --primary-color: #e11b22;
        }
        .sidebar-menu .sidebar-header {
            background: var(--primary-color);
        }
        .metismenu li.active > a, .metismenu li:hover > a {
            color: var(--primary-color);
        }
        .metismenu li.active > a i, .metismenu li:hover > a i {
            color: var(--primary-color);
        }
        .btn-primary, .bg-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        .page-title-area {
            background: #f9f9f9;
        }
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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
