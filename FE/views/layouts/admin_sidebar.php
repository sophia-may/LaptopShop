<?php
// Determine active page
$currentPage = $_GET['page'] ?? 'admin_dashboard';
?>

<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="?page=admin_dashboard"><h2 style="color: white;">CellphoneS</h2></a>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">
                    <li class="<?= $currentPage === 'admin_dashboard' ? 'active' : '' ?>">
                        <a href="?page=admin_dashboard" aria-expanded="true"><i class="ti-dashboard"></i><span>Dashboard</span></a>
                    </li>
                    <li class="<?= $currentPage === 'manage_products' ? 'active' : '' ?>">
                        <a href="?page=manage_products" aria-expanded="true"><i class="ti-package"></i><span>Inventory</span></a>
                    </li>
                    <li class="<?= $currentPage === 'manage_orders' ? 'active' : '' ?>">
                        <a href="?page=manage_orders" aria-expanded="true"><i class="ti-receipt"></i><span>Orders</span></a>
                    </li>
                    <li class="<?= $currentPage === 'manage_profile' ? 'active' : '' ?>">
                        <a href="?page=manage_profile" aria-expanded="true"><i class="ti-user"></i><span>Customers</span></a>
                    </li>
                    <li class="<?= $currentPage === 'admin_reviews' || $currentPage === 'admin_reviews' ? 'active' : '' ?>">
                        <a href="?page=admin_reviews" aria-expanded="true"><i class="ti-star"></i><span>Reviews</span></a>
                    </li>
                    <li class="<?= $currentPage === 'manage_qna' ? 'active' : '' ?>">
                        <a href="?page=manage_qna" aria-expanded="true"><i class="ti-help-alt"></i><span>Q&A</span></a>
                    </li>

                    <li class="<?= $currentPage === 'manage_about_info' || $currentPage === 'manage_info' ? 'active' : '' ?>">
                        <a href="javascript:void(0)" aria-expanded="true"><i class="ti-settings"></i><span>Settings</span></a>
                        <ul class="collapse <?= in_array($currentPage, ['manage_about_info', 'manage_info']) ? 'in' : '' ?>">
                            <li class="<?= $currentPage === 'manage_info' ? 'active' : '' ?>"><a href="?page=manage_info">Site Settings</a></li>
                            <li class="<?= $currentPage === 'manage_about_info' ? 'active' : '' ?>"><a href="?page=manage_about_info">About Page</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
