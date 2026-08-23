<?php
/**
 * Customer Orders & Invoices History - Esfield Pipe
 */
$pageTitle = "My Orders & Tax Invoices";
require_once __DIR__ . '/includes/header.php';

require_login();

$user = current_user();
$db = get_db();

$stmt = $db->prepare("SELECT * FROM `orders` WHERE `user_id` = ? OR `customer_email` = ? ORDER BY `created_at` DESC");
$stmt->execute([$user['id'], $user['email']]);
$orders = $stmt->fetchAll();
?>

<div class="py-5" style="background-color: var(--bg-body);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-black mb-1 text-dark">My Orders & GST Invoices</h3>
                <p class="text-muted small mb-0">Track pipe shipments, delivery vehicles, and view GST Tax Invoices for project billing.</p>
            </div>
            <a href="<?= BASE_URL ?>products.php" class="btn btn-primary fw-bold">
                <i class="fa-solid fa-cart-plus me-1"></i> Order More Pipes
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Order ID & Date</th>
                                <th>Delivery Destination</th>
                                <th>Grand Total</th>
                                <th>Payment</th>
                                <th>Fulfillment</th>
                                <th class="text-end pe-4">Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-box-open fs-1 text-muted opacity-50 mb-3 d-block"></i>
                                        You have not placed any orders yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($orders as $ord): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark font-monospace"><?= htmlspecialchars($ord['order_number']) ?></div>
                                        <div class="small text-muted"><?= date('d M Y, H:i', strtotime($ord['created_at'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="small text-dark"><?= htmlspecialchars($ord['city']) ?>, <?= htmlspecialchars($ord['state']) ?></div>
                                        <?php if (!empty($ord['tracking_number'])): ?>
                                            <div class="small text-primary"><i class="fa-solid fa-truck me-1"></i> Tracking: <?= htmlspecialchars($ord['tracking_number']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">₹<?= number_format($ord['total_amount'], 2) ?></span>
                                    </td>
                                    <td>
                                        <?= get_payment_status_badge($ord['payment_status']) ?>
                                    </td>
                                    <td>
                                        <?= get_order_status_badge($ord['order_status']) ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= BASE_URL ?>order-confirmation.php?order=<?= urlencode($ord['order_number']) ?>" class="btn btn-sm btn-outline-primary fw-semibold">
                                            <i class="fa-solid fa-file-invoice me-1"></i> View Invoice
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
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
