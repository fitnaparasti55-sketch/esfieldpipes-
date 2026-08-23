<?php
/**
 * Admin Dashboard - Esfield Pipe Management
 */
$pageTitle = "Enterprise Control Dashboard";
require_once __DIR__ . '/includes/header.php';

$db = get_db();

// Metrics & KPI queries
$totalProducts = $db->query("SELECT COUNT(*) FROM `products`")->fetchColumn() ?: 0;
$activeProducts = $db->query("SELECT COUNT(*) FROM `products` WHERE `status` = 'active'")->fetchColumn() ?: 0;
$totalCategories = $db->query("SELECT COUNT(*) FROM `categories`")->fetchColumn() ?: 0;
$totalUsers = $db->query("SELECT COUNT(*) FROM `users`")->fetchColumn() ?: 0;
$totalOrders = $db->query("SELECT COUNT(*) FROM `orders`")->fetchColumn() ?: 0;
$activeOrders = $db->query("SELECT COUNT(*) FROM `orders` WHERE `order_status` NOT IN ('delivered', 'cancelled')")->fetchColumn() ?: 0;
$todayRevenue = $db->query("SELECT SUM(total_amount) FROM `orders` WHERE DATE(created_at) = CURDATE() AND payment_status = 'paid'")->fetchColumn() ?: 0;
$totalRevenue = $db->query("SELECT SUM(total_amount) FROM `orders` WHERE payment_status = 'paid'")->fetchColumn() ?: 0;
$newInquiries = $db->query("SELECT COUNT(*) FROM `support_inquiries` WHERE `status` = 'new'")->fetchColumn() ?: 0;

// Recent Products
$recentProducts = $db->query("
    SELECT p.*, c.name as category_name 
    FROM `products` p 
    LEFT JOIN `categories` c ON p.category_id = c.id 
    ORDER BY p.created_at DESC 
    LIMIT 5
")->fetchAll();

// Recent Orders
$recentOrders = $db->query("SELECT * FROM `orders` ORDER BY `created_at` DESC LIMIT 5")->fetchAll();

// Recent User Registrations
$recentUsers = $db->query("SELECT * FROM `users` ORDER BY `created_at` DESC LIMIT 5")->fetchAll();

// Check Uploads Directory writeable status
$uploadsDir = __DIR__ . '/../../assets/uploads';
$uploadsWritable = is_dir($uploadsDir) && is_writable($uploadsDir);
?>

<!-- Welcome Banner -->
<div class="card border-0 shadow-sm mb-4 text-white overflow-hidden position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
    <div class="card-body p-4 p-md-5 position-relative z-1">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold mb-2">
                    <i class="fa-solid fa-industry me-1"></i> MANUFACTURING DASHBOARD
                </span>
                <h2 class="fw-black mb-2 text-white">Welcome back, <?= htmlspecialchars($user['name'] ?? 'Administrator') ?>!</h2>
                <p class="text-light opacity-75 mb-0" style="max-width: 600px;">
                    Monitor live DWC pipe inventories, track infrastructure orders, manage customer inquiries, and configure website appearance seamlessly.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="product-add.php" class="btn btn-primary px-4 py-2.5 rounded-3 fw-bold me-2">
                    <i class="fa-solid fa-plus me-1"></i> Add Product
                </a>
                <a href="settings.php" class="btn btn-outline-light px-4 py-2.5 rounded-3 fw-bold">
                    <i class="fa-solid fa-sliders me-1"></i> Settings
                </a>
            </div>
        </div>
    </div>
    <div class="position-absolute end-0 bottom-0 opacity-10 pe-4 pb-2 d-none d-md-block" style="font-size: 8rem; line-height: 1;">
        <i class="fa-solid fa-pipe"></i>
    </div>
</div>

<!-- ========================================================
     KPI STATS METRIC CARDS
     ======================================================== -->
<div class="row g-3 g-lg-4 mb-4">
    <!-- Total Products -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Products</span>
                    <h3 class="fw-black mb-0 text-dark"><?= number_format($totalProducts) ?></h3>
                    <small class="text-success fw-semibold"><i class="fa-solid fa-check me-1"></i><?= $activeProducts ?> active</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Orders -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Active Orders</span>
                    <h3 class="fw-black mb-0 text-dark"><?= number_format($activeOrders) ?></h3>
                    <small class="text-muted"><?= $totalOrders ?> total orders</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Total Revenue</span>
                    <h3 class="fw-black mb-0 text-dark">₹<?= number_format($totalRevenue, 2) ?></h3>
                    <small class="text-success fw-semibold">₹<?= number_format($todayRevenue, 2) ?> today</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Registered Users -->
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <span class="text-muted small fw-semibold text-uppercase tracking-wider">Users & Accounts</span>
                    <h3 class="fw-black mb-0 text-dark"><?= number_format($totalUsers) ?></h3>
                    <small class="text-muted"><i class="fa-solid fa-layer-group me-1"></i><?= $totalCategories ?> categories</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     MAIN TABLES & QUICK ACTIONS
     ======================================================== -->
<div class="row g-4 mb-4">
    <!-- Recent Products Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-boxes-stacked text-primary me-2"></i> Recent Products in Catalog</h6>
                <a href="products.php" class="btn btn-sm btn-link text-decoration-none fw-semibold">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Product</th>
                                <th>Category</th>
                                <th>Price/m</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentProducts)): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">No products found.</td></tr>
                            <?php else: ?>
                                <?php foreach($recentProducts as $p): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= BASE_URL . ($p['image'] ?: 'assets/images/logo.svg') ?>" alt="Product" class="rounded border" style="width: 44px; height: 44px; object-fit: contain; background: #fff;">
                                            <div>
                                                <div class="fw-bold text-dark text-truncate" style="max-width: 240px;"><?= htmlspecialchars($p['name']) ?></div>
                                                <div class="small text-muted"><?= $p['inner_diameter_mm'] ?>mm ID / <?= $p['outer_diameter_mm'] ?>mm OD (<?= $p['stiffness_class'] ?>)</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category_name'] ?? 'General') ?></span>
                                    </td>
                                    <td class="fw-bold text-primary">₹<?= number_format($p['price_per_meter'], 2) ?></td>
                                    <td>
                                        <span class="badge <?= $p['stock_quantity'] < 100 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' ?>">
                                            <?= number_format($p['stock_quantity']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $p['status'] === 'active' ? 'bg-success text-white' : 'bg-secondary text-white' ?>">
                                            <?= ucfirst($p['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="product-edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit Product">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-cart-shopping text-warning me-2"></i> Recent Customer Orders</h6>
                <a href="orders.php" class="btn btn-sm btn-link text-decoration-none fw-semibold">All Orders <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentOrders)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted">No orders found.</td></tr>
                            <?php else: ?>
                                <?php foreach($recentOrders as $ord): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($ord['order_number']) ?></td>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($ord['customer_phone']) ?></div>
                                    </td>
                                    <td class="fw-bold text-success">₹<?= number_format($ord['total_amount'], 2) ?></td>
                                    <td><?= get_order_status_badge($ord['order_status']) ?></td>
                                    <td class="text-end pe-4">
                                        <a href="order-detail.php?id=<?= $ord['id'] ?>" class="btn btn-sm btn-light border">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & System Status -->
    <div class="col-lg-4">
        <!-- Quick Actions Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-bolt text-warning me-2"></i> Quick Actions</h6>
            </div>
            <div class="card-body px-4 pb-4 pt-1">
                <div class="d-grid gap-2">
                    <a href="product-add.php" class="btn btn-primary py-2.5 text-start d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-circle-plus me-2"></i> Add New Product</span>
                        <i class="fa-solid fa-chevron-right small opacity-75"></i>
                    </a>
                    <a href="categories.php" class="btn btn-light border py-2.5 text-start d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-folder-plus me-2 text-primary"></i> Manage Categories</span>
                        <i class="fa-solid fa-chevron-right small text-muted"></i>
                    </a>
                    <a href="homepage.php" class="btn btn-light border py-2.5 text-start d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-house-laptop me-2 text-warning"></i> Customize Homepage</span>
                        <i class="fa-solid fa-chevron-right small text-muted"></i>
                    </a>
                    <a href="appearance.php" class="btn btn-light border py-2.5 text-start d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-palette me-2 text-info"></i> Appearance / Theme</span>
                        <i class="fa-solid fa-chevron-right small text-muted"></i>
                    </a>
                    <a href="logo.php" class="btn btn-light border py-2.5 text-start d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-icons me-2 text-success"></i> Change Logos</span>
                        <i class="fa-solid fa-chevron-right small text-muted"></i>
                    </a>
                    <a href="company.php" class="btn btn-light border py-2.5 text-start d-flex align-items-center justify-content-between">
                        <span><i class="fa-solid fa-building me-2 text-secondary"></i> Company & Contact Info</span>
                        <i class="fa-solid fa-chevron-right small text-muted"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Registrations Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user-plus text-info me-2"></i> Recent Registrations</h6>
                <a href="users.php" class="btn btn-sm btn-link text-decoration-none fw-semibold">View All</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach($recentUsers as $ru): ?>
                    <li class="list-group-item px-4 py-3 d-flex align-items-center justify-content-between border-0 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                <?= strtoupper(substr($ru['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark small"><?= htmlspecialchars($ru['name']) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($ru['email']) ?></div>
                            </div>
                        </div>
                        <span class="badge <?= $ru['role'] === 'admin' ? 'bg-danger' : ($ru['role'] === 'editor' ? 'bg-warning text-dark' : 'bg-light text-dark border') ?> text-uppercase" style="font-size: 0.65rem;">
                            <?= $ru['role'] ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Website Status Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-server text-secondary me-2"></i> Infrastructure & Server Status</h6>
            </div>
            <div class="card-body px-4 pb-4 pt-1 small">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Database Engine:</span>
                    <span class="fw-semibold text-success"><i class="fa-solid fa-circle-check me-1"></i> MySQL Connected</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">PHP Version:</span>
                    <span class="fw-semibold"><?= phpversion() ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Uploads Directory:</span>
                    <span class="fw-semibold <?= $uploadsWritable ? 'text-success' : 'text-danger' ?>">
                        <?= $uploadsWritable ? '<i class="fa-solid fa-check me-1"></i> Writable' : '<i class="fa-solid fa-triangle-exclamation me-1"></i> Not Writable' ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Server Time:</span>
                    <span class="fw-semibold"><?= date('d M Y, H:i') ?></span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Brand Platform:</span>
                    <span class="fw-semibold text-primary">ESFIELD PIPE DWC</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
