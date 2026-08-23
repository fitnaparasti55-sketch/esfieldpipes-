<?php
/**
 * Order Confirmation & Success Page
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$orderNumber = trim($_GET['order_number'] ?? '');
if (empty($orderNumber)) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$db = get_db();
$stmt = $db->prepare("SELECT * FROM `orders` WHERE `order_number` = ?");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

// Fetch Order Items
$itemStmt = $db->prepare("SELECT * FROM `order_items` WHERE `order_id` = ?");
$itemStmt->execute([$order['id']]);
$items = $itemStmt->fetchAll();

$pageTitle = "Order Confirmation - " . $order['order_number'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Hero Box -->
            <div class="card-custom p-5 text-center mb-4" style="background-color: var(--bg-card);">
                <div class="rounded-circle bg-success bg-opacity-15 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2.5rem;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h2 class="fw-bold text-main mb-2">Thank You! Your Order is Confirmed</h2>
                <p class="text-muted mb-4">We have received your DWC corrugated pipe order. Our logistics & dispatch desk is preparing your shipment.</p>

                <div class="d-inline-flex flex-wrap align-items-center justify-content-center gap-3 p-3 bg-subtle rounded-3 mb-4">
                    <div>
                        <span class="text-muted small d-block">Order Reference Number:</span>
                        <strong class="text-primary fs-5"><?= htmlspecialchars($order['order_number']) ?></strong>
                    </div>
                    <span class="text-muted opacity-50 d-none d-sm-inline">|</span>
                    <div>
                        <span class="text-muted small d-block">Order Status:</span>
                        <?= get_order_status_badge($order['order_status']) ?>
                    </div>
                    <span class="text-muted opacity-50 d-none d-sm-inline">|</span>
                    <div>
                        <span class="text-muted small d-block">Payment Status:</span>
                        <?= get_payment_status_badge($order['payment_status']) ?>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="<?= BASE_URL ?>order-invoice.php?order_number=<?= urlencode($order['order_number']) ?>" target="_blank" class="btn btn-primary fw-bold px-4 py-2.5 shadow-sm">
                        <i class="fa-solid fa-file-pdf me-2"></i> Download GST Tax Invoice
                    </a>
                    <a href="<?= BASE_URL ?>orders.php" class="btn btn-outline-secondary px-4 py-2.5 fw-semibold">
                        <i class="fa-solid fa-truck-fast me-2"></i> Track in My Orders
                    </a>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="card-custom p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-list-check text-primary me-2"></i> Ordered Specifications</h5>

                <div class="table-responsive mb-3">
                    <table class="table table-borderless align-middle small mb-0">
                        <thead class="bg-subtle text-muted text-uppercase">
                            <tr>
                                <th>Pipe Model</th>
                                <th>Stiffness</th>
                                <th>Qty</th>
                                <th>Rate / Pipe</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr class="border-bottom">
                                    <td>
                                        <strong class="text-main"><?= htmlspecialchars($item['product_name']) ?></strong>
                                        <div class="text-muted">ID: <?= $item['inner_diameter_mm'] ?>mm | Length: <?= number_format($item['pipe_length_m'], 1) ?>m</div>
                                    </td>
                                    <td><span class="badge bg-dark border text-warning"><?= htmlspecialchars($item['stiffness_class']) ?></span></td>
                                    <td><strong><?= $item['quantity'] ?> Pipes</strong></td>
                                    <td><?= format_price($item['unit_price']) ?></td>
                                    <td class="text-end fw-bold text-main"><?= format_price($item['total_price']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Financial Calculations -->
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="p-3 bg-subtle rounded-3 small">
                            <div class="d-flex justify-content-between mb-1 text-muted">
                                <span>Subtotal:</span>
                                <strong><?= format_price($order['subtotal']) ?></strong>
                            </div>
                            <?php if ($order['discount_amount'] > 0): ?>
                                <div class="d-flex justify-content-between mb-1 text-success">
                                    <span>Discount (<?= htmlspecialchars($order['coupon_code'] ?? 'Coupon') ?>):</span>
                                    <strong>- <?= format_price($order['discount_amount']) ?></strong>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <span>GST (18% ITC Eligible):</span>
                                <strong><?= format_price($order['tax_amount']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between pt-2 border-top fs-6 fw-bold">
                                <span>Grand Total Paid:</span>
                                <span class="text-primary"><?= format_price($order['total_amount']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery & Site Address Details -->
            <div class="card-custom p-4">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-location-dot text-primary me-2"></i> Site Delivery Destination</h5>
                <div class="row g-3 small">
                    <div class="col-md-6">
                        <div class="text-muted">Consignee Name:</div>
                        <strong class="text-main fs-6"><?= htmlspecialchars($order['customer_name']) ?></strong>
                        <?php if (!empty($order['company_name'])): ?>
                            <div class="text-muted"><?= htmlspecialchars($order['company_name']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($order['gstin'])): ?>
                            <div class="text-primary fw-bold">GSTIN: <?= htmlspecialchars($order['gstin']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted">Site Unloading Address:</div>
                        <div class="text-main fw-semibold">
                            <?= nl2br(htmlspecialchars($order['shipping_address'])) ?><br>
                            <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['state']) ?> - <?= htmlspecialchars($order['pincode']) ?>
                        </div>
                        <div class="text-muted mt-1">Phone: <strong><?= htmlspecialchars($order['customer_phone']) ?></strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
