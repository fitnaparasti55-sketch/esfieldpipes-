<?php
/**
 * User Profile & Project Dashboard - Esfield Pipe
 */
$pageTitle = "My Profile & Project Account";
require_once __DIR__ . '/includes/header.php';

require_login();

$user = current_user();
$db = get_db();

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $company = trim($_POST['company_name'] ?? '');
        $gstin = trim($_POST['gstin'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $pincode = trim($_POST['pincode'] ?? '');

        $stmt = $db->prepare("
            UPDATE `users` SET 
                `name` = ?, `phone` = ?, `company_name` = ?, `gstin` = ?, 
                `address` = ?, `city` = ?, `state` = ?, `pincode` = ? 
            WHERE `id` = ?
        ");
        $stmt->execute([$name, $phone, $company, $gstin, $address, $city, $state, $pincode, $user['id']]);

        $_SESSION['user_name'] = $name;
        set_flash('success', 'Your project profile details have been saved.');
        header('Location: ' . BASE_URL . 'profile.php');
        exit;
    }
}

// Fetch user orders
$orderStmt = $db->prepare("SELECT * FROM `orders` WHERE `user_id` = ? OR `customer_email` = ? ORDER BY `created_at` DESC LIMIT 5");
$orderStmt->execute([$user['id'], $user['email']]);
$userOrders = $orderStmt->fetchAll();
?>

<div class="py-5" style="background-color: var(--bg-body);">
    <div class="container">
        <div class="row g-4">
            <!-- Profile Info Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 text-center mb-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-black mb-3 mx-auto" style="width: 70px; height: 70px; font-size: 1.75rem;">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                    <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars($user['name']) ?></h5>
                    <p class="text-muted small mb-2"><?= htmlspecialchars($user['email']) ?></p>
                    <span class="badge bg-light text-dark border text-uppercase mb-3"><?= $user['role'] ?> Account</span>

                    <?php if (is_editor_or_admin()): ?>
                        <a href="<?= ADMIN_URL ?>dashboard.php" class="btn btn-warning fw-bold w-100 py-2 mb-2">
                            <i class="fa-solid fa-gauge-high me-1"></i> Open Admin Control Panel
                        </a>
                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>logout.php" class="btn btn-outline-danger btn-sm w-100">
                        <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Sign Out
                    </a>
                </div>

                <div class="card border-0 shadow-sm p-4">
                    <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-briefcase text-primary me-2"></i> Quick Shortcuts</h6>
                    <div class="d-grid gap-2">
                        <a href="<?= BASE_URL ?>orders.php" class="btn btn-light text-start border">
                            <i class="fa-solid fa-box-open me-2 text-success"></i> My Orders & Invoices
                        </a>
                        <a href="<?= BASE_URL ?>pipe-calculator.php" class="btn btn-light text-start border">
                            <i class="fa-solid fa-calculator me-2 text-warning"></i> Hydraulic Pipe Sizing
                        </a>
                        <a href="<?= BASE_URL ?>products.php" class="btn btn-light text-start border">
                            <i class="fa-solid fa-boxes-stacked me-2 text-primary"></i> Browse Pipe Catalog
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Details Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 p-md-5 mb-4">
                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-3">
                        <i class="fa-solid fa-user-pen text-primary me-2"></i> Contractor & Billing Information
                    </h5>

                    <form action="<?= BASE_URL ?>profile.php" method="POST">
                        <?= csrf_field() ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Full Name *</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Email Address</label>
                                <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Phone / Mobile *</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+91 98765 43210">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Company / Contracting Firm</label>
                                <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($user['company_name'] ?? '') ?>" placeholder="e.g. Apex Civil Infrastructure">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">GSTIN Number (For Input Tax Credit)</label>
                                <input type="text" name="gstin" class="form-control" value="<?= htmlspecialchars($user['gstin'] ?? '') ?>" placeholder="e.g. 27AAACA9999P1ZV">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Shipping / Site Delivery Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Trench site or central warehouse..."><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">City</label>
                                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">State</label>
                                <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($user['state'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Pincode</label>
                                <input type="text" name="pincode" class="form-control" value="<?= htmlspecialchars($user['pincode'] ?? '') ?>">
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2.5 fw-bold">
                                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Recent Orders -->
                <div class="card border-0 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-box-open text-primary me-2"></i> Recent Order Invoices</h6>
                        <a href="<?= BASE_URL ?>orders.php" class="btn btn-sm btn-link text-decoration-none fw-semibold">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($userOrders)): ?>
                                    <tr><td colspan="4" class="text-center py-3 text-muted">No orders placed yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach($userOrders as $uo): ?>
                                    <tr>
                                        <td class="fw-bold text-primary font-monospace"><?= htmlspecialchars($uo['order_number']) ?></td>
                                        <td><?= date('d M Y', strtotime($uo['created_at'])) ?></td>
                                        <td class="fw-bold">₹<?= number_format($uo['total_amount'], 2) ?></td>
                                        <td><?= get_order_status_badge($uo['order_status']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
