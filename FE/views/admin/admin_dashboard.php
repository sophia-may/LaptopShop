<?php 
require_once 'config/db.php';
$pdo = Database::getConnection();

// Get stats
$stats = [];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$stats['products'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$stats['orders'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT SUM(final_amount) as revenue FROM orders WHERE status = 'completed'");
$stats['revenue'] = $stmt->fetch()['revenue'] ?? 0;
$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
$stats['pending_orders'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM contacts WHERE status = 'unread'");
$stats['unread_contacts'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$stats['users'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM articles WHERE published_at IS NOT NULL");
$stats['posts'] = $stmt->fetch()['total'];
$stmt = $pdo->query("SELECT COUNT(*) as total FROM reviews");
$stats['reviews'] = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$recent_orders = $stmt->fetchAll();

include 'views/layouts/admin_header.php'; 
?>

<!-- Sales Report Area Start -->
<div class="sales-report-area mt-5 mb-5">
    <div class="row">
        <!-- Total Members -->
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="icon bg-light rounded p-2 text-primary">
                            <i class="ti-user fs-4"></i>
                        </div>
                        <span class="text-success fw-bold">+12.5%</span>
                    </div>
                    <div class="text-muted small">Total Members</div>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['users']) ?></h3>
                </div>
            </div>
        </div>
        <!-- New Orders -->
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="icon bg-danger text-white rounded p-2">
                            <i class="ti-shopping-cart fs-4"></i>
                        </div>
                        <span class="text-danger fw-bold">Urgent</span>
                    </div>
                    <div class="text-muted small">New Orders</div>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['orders']) ?></h3>
                </div>
            </div>
        </div>
        <!-- Pending Reviews -->
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="icon bg-info text-white rounded p-2">
                            <i class="ti-comments fs-4"></i>
                        </div>
                        <span class="text-dark fw-bold"><?= number_format($stats['unread_contacts']) ?> Active</span>
                    </div>
                    <div class="text-muted small">Pending Reviews</div>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['reviews']) ?></h3>
                </div>
            </div>
        </div>
        <!-- Active Services -->
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="icon bg-light rounded p-2">
                            <i class="ti-settings fs-4"></i>
                        </div>
                        <span class="text-primary fw-bold">Stable</span>
                    </div>
                    <div class="text-muted small">Active Services</div>
                    <h3 class="mb-0 fw-bold">12 / 12</h3>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Sales Report Area End -->

<!-- Main Dashboard Content -->
<div class="row">
    <!-- Weekly Traffic Analysis -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="header-title mb-0">Weekly Traffic Analysis</h4>
                    <div>
                        <span class="badge bg-light text-dark px-3 py-2 border">7D</span>
                        <span class="px-2 font-weight-bold">30D</span>
                    </div>
                </div>
                <!-- Placeholder for the traffic analysis image/chart as requested -->
                <div class="border rounded text-center bg-light" style="height: 300px; display: flex; flex-direction: column; justify-content: flex-end; padding: 20px; background-image: url('assets/img/hero-bg.jpg'); background-size: cover; background-position: center; position: relative;">
                     <div style="position: absolute; bottom: 10px; left: 10px; background: rgba(255,255,255,0.8); padding: 5px 10px; border-radius: 5px; font-weight: 500;">
                         <i class="ti-stats-up text-success"></i> Traffic increased by 22% compared to last week.
                     </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="header-title mb-0">Recent Activities</h4>
                    <a href="#" class="text-danger fw-bold" style="font-size: 14px;">View All</a>
                </div>
                <div class="recent-activity">
                    <div class="timeline-task pb-3 border-bottom mb-3" style="border-left: 3px solid #e11b22; padding-left: 15px;">
                        <div class="tm-title">
                            <h5 class="mb-1" style="font-size: 15px;">Inventory Update</h5>
                            <span class="time text-muted" style="font-size: 12px;"><i class="ti-time"></i> 14 minutes ago</span>
                        </div>
                        <p class="text-muted small mb-0">MacBook Pro M3 restocked (+50 units)</p>
                    </div>
                    <div class="timeline-task pb-3 border-bottom mb-3" style="border-left: 3px solid #17a2b8; padding-left: 15px;">
                        <div class="tm-title">
                            <h5 class="mb-1" style="font-size: 15px;">New Member</h5>
                            <span class="time text-muted" style="font-size: 12px;"><i class="ti-time"></i> 1 hour ago</span>
                        </div>
                        <p class="text-muted small mb-0">John Smith registered as Platinum Member</p>
                    </div>
                    <div class="timeline-task pb-3 border-bottom mb-3" style="border-left: 3px solid #28a745; padding-left: 15px;">
                        <div class="tm-title">
                            <h5 class="mb-1" style="font-size: 15px;">Order Fulfilled</h5>
                            <span class="time text-muted" style="font-size: 12px;"><i class="ti-time"></i> 3 hours ago</span>
                        </div>
                        <p class="text-muted small mb-0">Order #99214 successfully shipped</p>
                    </div>
                    <div class="timeline-task pb-3" style="border-left: 3px solid #e11b22; padding-left: 15px;">
                        <div class="tm-title">
                            <h5 class="mb-1" style="font-size: 15px;">System Alert</h5>
                            <span class="time text-muted" style="font-size: 12px;"><i class="ti-time"></i> 5 hours ago</span>
                        </div>
                        <p class="text-muted small mb-0">High latency detected in payment gateway</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Low Stock Alert -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100 bg-primary text-white" style="background-color: var(--primary-color) !important;">
            <div class="card-body">
                <h4 class="card-title text-white">Low Stock Alert</h4>
                <p class="mb-4">8 products are currently below the critical threshold. Action required.</p>
                <button class="btn btn-light text-danger w-100 fw-bold">Restock Inventory</button>
            </div>
        </div>
    </div>

    <!-- Customer Sentiment -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100 bg-info text-white" style="background-color: #007bff !important;">
            <div class="card-body">
                <h4 class="card-title text-white">Customer Sentiment</h4>
                <h1 class="display-4 text-white fw-bold mb-2">94% <i class="ti-face-smile"></i></h1>
                <p class="mb-4">Positive feedback increased by 4% this month.</p>
                <button class="btn btn-light text-info w-100 fw-bold" style="color: #007bff !important;">Analyze Reviews</button>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h4 class="card-title mb-4">System Health</h4>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size: 14px;">Main Database</span>
                        <span class="text-danger fw-bold" style="font-size: 14px;">99.9%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 99.9%" aria-valuenow="99.9" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size: 14px;">API Gateway</span>
                        <span class="text-info fw-bold" style="font-size: 14px;">94.2%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 94.2%" aria-valuenow="94.2" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layouts/admin_footer.php'; ?>