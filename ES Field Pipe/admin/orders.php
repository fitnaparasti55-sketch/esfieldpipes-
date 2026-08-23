<?php
/**
 * Orders Management - Esfield Pipe
 */
$pageTitle = "Orders & Invoices";
require_once __DIR__ . '/includes/header.php';

$db = get_db();

$search = trim($_GET['search'] ?? '');
$orderStatus = trim($_GET['order_status'] ?? '');
$paymentStatus = trim($_GET['payment_status'] ?? '');

$query = "SELECT * FROM `orders` WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (`order_number` LIKE ? OR `customer_name` LIKE ? OR `customer_email` LIKE ? OR `customer_phone` LIKE ? OR `company_name` LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($orderStatus)) {
    $query .= " AND `order_status` = ?";
    $params[] = $orderStatus;
}

if (!empty($paymentStatus)) {
    $query .= " AND `payment_status` = ?";
    $params[] = $paymentStatus;
}

$query .= " ORDER BY `created_at` DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-black mb-1 text-dark">Infrastructure Orders & GST Invoices</h4>
        <p class="text-muted small mb-0">Track pipe procurement orders, verify B2B payments, and monitor nationwide logistics dispatch.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="orders.php" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search order #, customer, email or GSTIN..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="order_status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Fulfillment Statuses</option>
                    <option value="pending" <?= $orderStatus === 'pending' ? 'selected' : '' ?>>Pending Confirmation</option>
                    <option value="confirmed" <?= $orderStatus === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="processing" <?= $orderStatus === 'processing' ? 'selected' : '' ?>>Manufacturing / Packing</option>
                    <option value="shipped" <?= $orderStatus === 'shipped' ? 'selected' : '' ?>>In Transit / Shipped</option>
                    <option value="delivered" <?= $orderStatus === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="cancelled" <?= $orderStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment_status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Payments</option>
                    <option value="paid" <?= $paymentStatus === 'paid' ? 'selected' : '' ?>>Paid (Verified)</option>
                    <option value="pending" <?= $paymentStatus === 'pending' ? 'selected' : '' ?>>Unpaid / Pending</option>
                    <option value="failed" <?= $paymentStatus === 'failed' ? 'selected' : '' ?>>Failed</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">Filter</button>
                <?php if (!empty($search) || !empty($orderStatus) || !empty($paymentStatus)): ?>
                    <a href="orders.php" class="btn btn-sm btn-outline-secondary" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Order ID & Date</th>
                        <th>Customer / Contractor</th>
                        <th>Total Amount</th>
                        <th>Payment</th>
                        <th>Fulfillment Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No orders found.</td></tr>
                    <?php else: ?>
                        <?php foreach($orders as $ord): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark font-monospace"><?= htmlspecialchars($ord['order_number']) ?></div>
                                <div class="small text-muted"><?= date('d M Y, H:i', strtotime($ord['created_at'])) ?></div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                <div class="small text-muted">
                                    <?= htmlspecialchars($ord['company_name'] ?: $ord['customer_email']) ?> &bull; <?= htmlspecialchars($ord['customer_phone']) ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">₹<?= number_format($ord['total_amount'], 2) ?></div>
                                <div class="small text-muted"><?= strtoupper($ord['payment_method']) ?></div>
                            </td>
                            <td>
                                <?= get_payment_status_badge($ord['payment_status']) ?>
                            </td>
                            <td>
                                <?= get_order_status_badge($ord['order_status']) ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="order-detail.php?id=<?= $ord['id'] ?>" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="fa-solid fa-file-invoice me-1"></i> Details & Invoice
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
