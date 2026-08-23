<?php
/**
 * Order Detail & Invoice Management - Esfield Pipe
 */
$pageTitle = "Order Details";
require_once __DIR__ . '/includes/header.php';

$orderId = (int)($_GET['id'] ?? 0);
$db = get_db();

$stmt = $db->prepare("SELECT * FROM `orders` WHERE `id` = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    set_flash('danger', 'Order not found.');
    header('Location: orders.php');
    exit;
}

// Fetch line items
$itemStmt = $db->prepare("SELECT * FROM `order_items` WHERE `order_id` = ?");
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll();

$siteName = get_setting('site_name', 'Esfield Pipe Pvt. Ltd.');
$siteAddress = get_setting('site_address', 'Plot No. 42-45, Industrial Mega Infrastructure Park, Phase-II, New Delhi - 110001, India');
$sitePhone = get_setting('site_phone', '+91 98765 43210');
$siteEmail = get_setting('site_email', 'sales@esfieldpipe.com');
$gstin = get_setting('gstin', '07AABCE9876F1Z4');
$siteLogo = get_setting('site_logo', 'assets/images/logo.svg');
?>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h4 class="fw-black mb-1 text-dark">Order <?= htmlspecialchars($order['order_number']) ?></h4>
        <p class="text-muted small mb-0">Placed on <?= date('d F Y, H:i A', strtotime($order['created_at'])) ?></p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm fw-bold" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Print GST Tax Invoice
        </button>
        <a href="orders.php" class="btn btn-outline-secondary btn-sm fw-bold">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Orders
        </a>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Invoice Container (Printable) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm invoice-container p-4 p-md-5">
            <!-- Invoice Header -->
            <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                <div>
                    <img src="<?= BASE_URL . $siteLogo ?>" alt="ESFIELD" style="height: 44px;" class="mb-2">
                    <div class="fw-bold text-dark"><?= htmlspecialchars($siteName) ?></div>
                    <div class="small text-muted" style="max-width: 280px;"><?= htmlspecialchars($siteAddress) ?></div>
                    <div class="small text-muted">GSTIN: <strong><?= htmlspecialchars($gstin) ?></strong></div>
                </div>
                <div class="text-end">
                    <h4 class="fw-black text-primary mb-1">TAX INVOICE</h4>
                    <div class="fw-bold font-monospace"><?= htmlspecialchars($order['order_number']) ?></div>
                    <div class="small text-muted">Date: <?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                    <div class="mt-2">
                        <?= get_payment_status_badge($order['payment_status']) ?>
                    </div>
                </div>
            </div>

            <!-- Billed To & Shipped To -->
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <h6 class="fw-bold text-uppercase small text-muted mb-2">Billed To (Customer):</h6>
                    <div class="fw-bold text-dark"><?= htmlspecialchars($order['customer_name']) ?></div>
                    <?php if (!empty($order['company_name'])): ?>
                        <div class="fw-semibold text-primary"><?= htmlspecialchars($order['company_name']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($order['gstin'])): ?>
                        <div class="small">GSTIN: <strong><?= htmlspecialchars($order['gstin']) ?></strong></div>
                    <?php endif; ?>
                    <div class="small text-muted"><?= htmlspecialchars($order['customer_phone']) ?></div>
                    <div class="small text-muted"><?= htmlspecialchars($order['customer_email']) ?></div>
                </div>
                <div class="col-6">
                    <h6 class="fw-bold text-uppercase small text-muted mb-2">Delivery Destination:</h6>
                    <div class="small text-dark">
                        <?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br>
                        <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['state']) ?> - <?= htmlspecialchars($order['pincode']) ?>
                    </div>
                </div>
            </div>

            <!-- Itemized Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="bg-light small text-uppercase">
                        <tr>
                            <th>Item Description & Sizing</th>
                            <th class="text-center" style="width: 80px;">Qty</th>
                            <th class="text-end" style="width: 120px;">Unit Price (6m)</th>
                            <th class="text-end" style="width: 140px;">Total (Ex-GST)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $it): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($it['product_name']) ?></div>
                                <div class="small text-muted">
                                    Size: <?= $it['inner_diameter_mm'] ?>mm ID / <?= $it['outer_diameter_mm'] ?>mm OD | Stiffness: <?= $it['stiffness_class'] ?> | Length: <?= $it['pipe_length_m'] ?>m
                                </div>
                            </td>
                            <td class="text-center fw-bold"><?= $it['quantity'] ?></td>
                            <td class="text-end">₹<?= number_format($it['unit_price'], 2) ?></td>
                            <td class="text-end fw-bold">₹<?= number_format($it['total_price'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-semibold">Subtotal:</td>
                            <td class="text-end fw-bold">₹<?= number_format($order['subtotal'], 2) ?></td>
                        </tr>
                        <?php if ((float)$order['discount_amount'] > 0): ?>
                        <tr>
                            <td colspan="3" class="text-end text-success">Discount (Coupon: <?= htmlspecialchars($order['coupon_code'] ?? 'Applied') ?>):</td>
                            <td class="text-end text-success fw-bold">- ₹<?= number_format($order['discount_amount'], 2) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="3" class="text-end text-muted">Goods & Services Tax (GST 18%):</td>
                            <td class="text-end fw-bold">₹<?= number_format($order['tax_amount'], 2) ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end text-muted">Freight & Transportation:</td>
                            <td class="text-end fw-bold">₹<?= number_format($order['shipping_charge'], 2) ?></td>
                        </tr>
                        <tr class="table-primary">
                            <td colspan="3" class="text-end fw-black fs-6">Grand Total Amount:</td>
                            <td class="text-end fw-black fs-6 text-primary">₹<?= number_format($order['total_amount'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes & Terms -->
            <div class="border-top pt-3 small text-muted">
                <strong>Standard Quality Warranty:</strong> Goods conform strictly to IS 16098 Part 2 & EN 13476 specifications.
            </div>
        </div>
    </div>

    <!-- Fulfillment Control Panel (No Print) -->
    <div class="col-lg-4 no-print">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-sliders text-primary me-2"></i> Update Order Status</h5>
            </div>
            <div class="card-body p-4 border-top">
                <form action="<?= BASE_URL ?>api/admin-orders.php" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update_order">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Fulfillment Status</label>
                        <select name="order_status" class="form-select">
                            <option value="pending" <?= $order['order_status'] === 'pending' ? 'selected' : '' ?>>Pending Confirmation</option>
                            <option value="confirmed" <?= $order['order_status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="processing" <?= $order['order_status'] === 'processing' ? 'selected' : '' ?>>Manufacturing / In Production</option>
                            <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>In Transit / Shipped</option>
                            <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>Delivered to Site</option>
                            <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Payment Status</label>
                        <select name="payment_status" class="form-select">
                            <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>Unpaid / Pending</option>
                            <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid & Verified</option>
                            <option value="failed" <?= $order['payment_status'] === 'failed' ? 'selected' : '' ?>>Failed</option>
                            <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Logistics Trailer / Tracking #</label>
                        <input type="text" name="tracking_number" class="form-control" value="<?= htmlspecialchars($order['tracking_number'] ?? '') ?>" placeholder="e.g. TRK-MH-991823">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Dispatch Notes / Remarks</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Special delivery instructions or factory batch details..."><?= htmlspecialchars($order['notes'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold rounded-3">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Update Order Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
