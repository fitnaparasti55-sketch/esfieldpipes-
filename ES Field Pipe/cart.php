<?php
/**
 * Shopping Cart Page
 * Esfield Pipe Platform
 */

$pageTitle = "Your Order Cart";
require_once __DIR__ . '/includes/header.php';

$cartItems = get_cart_items();
$totals = get_cart_totals();
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>"><i class="fa-solid fa-house me-1"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>products.php">DWC Pipes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cart</li>
        </ol>
    </nav>

    <h1 class="h3 fw-bold mb-4"><i class="fa-solid fa-cart-shopping text-primary me-2"></i> Your DWC Pipe Order Cart</h1>

    <?php if (empty($cartItems)): ?>
        <div class="card-custom p-5 text-center my-4">
            <div class="rounded-circle bg-subtle d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.5rem; color: var(--text-muted);">
                <i class="fa-solid fa-cart-arrow-down"></i>
            </div>
            <h3 class="fw-bold mb-2">Your Cart is Currently Empty</h3>
            <p class="text-muted mb-4">You haven't selected any DWC corrugated pipes or ducts for your order yet.</p>
            <a href="<?= BASE_URL ?>products.php" class="btn btn-primary px-4 py-2.5 fw-semibold">
                <i class="fa-solid fa-cubes-stacked me-2"></i> Browse DWC Pipe Catalog
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Left: Cart Items List -->
            <div class="col-lg-8">
                <div class="card-custom overflow-hidden mb-3">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="bg-subtle text-muted small text-uppercase">
                                <tr>
                                    <th style="width: 45%;">Pipe Specification</th>
                                    <th style="width: 15%;">Unit Price</th>
                                    <th style="width: 20%;">Qty (Pipes)</th>
                                    <th style="width: 15%;" class="text-end">Total</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr id="cart_row_<?= $item['id'] ?>" class="border-bottom">
                                        <td>
                                            <div class="d-flex align-items-center gap-3 py-2">
                                                <img src="<?= ASSETS_URL ?><?= htmlspecialchars($item['image'] ?: 'images/dwc-pipe-100mm.svg') ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="rounded-3 border" style="width: 65px; height: 65px; object-fit: contain; background: var(--bg-subtle);">
                                                <div>
                                                    <h6 class="fw-bold mb-1">
                                                        <a href="<?= BASE_URL ?>product-detail.php?slug=<?= urlencode($item['slug']) ?>" class="text-main hover-primary">
                                                            <?= htmlspecialchars($item['name']) ?>
                                                        </a>
                                                    </h6>
                                                    <div class="small text-muted">
                                                        <span>ID: <strong><?= $item['inner_diameter_mm'] ?>mm</strong></span> | 
                                                        <span>OD: <strong><?= $item['outer_diameter_mm'] ?>mm</strong></span> | 
                                                        <span class="badge bg-dark text-warning border"><?= $item['stiffness_class'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-main"><?= format_price($item['price_per_pipe']) ?></div>
                                            <div class="small text-muted" style="font-size: 0.72rem;">(<?= format_price($item['price_per_meter']) ?>/m)</div>
                                        </td>
                                        <td>
                                            <div class="qty-stepper" style="max-width: 110px;">
                                                <button type="button" class="btn-qty-minus" data-cart-id="<?= $item['id'] ?>" aria-label="Decrease">-</button>
                                                <input type="number" id="cart_qty_<?= $item['id'] ?>" value="<?= $item['quantity'] ?>" min="1" readonly>
                                                <button type="button" class="btn-qty-plus" data-cart-id="<?= $item['id'] ?>" aria-label="Increase">+</button>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="fw-bold text-primary" id="line_total_<?= $item['id'] ?>">
                                                <?= format_price($item['line_total']) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-link text-danger p-0 btn-remove-cart-item" data-cart-id="<?= $item['id'] ?>" title="Remove Item">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="<?= BASE_URL ?>products.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Continue Browsing
                    </a>
                </div>
            </div>

            <!-- Right: Order Summary & Coupon Card -->
            <div class="col-lg-4">
                <!-- Promo Code Box -->
                <div class="card-custom p-4 mb-3">
                    <h6 class="fw-bold mb-3"><i class="fa-solid fa-ticket text-warning me-2"></i> Discount Promo Code</h6>
                    <?php if (!empty($totals['coupon_code'])): ?>
                        <div class="d-flex justify-content-between align-items-center p-2.5 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 mb-2">
                            <div>
                                <span class="badge bg-success me-1">APPLIED</span>
                                <strong class="text-success"><?= htmlspecialchars($totals['coupon_code']) ?></strong>
                            </div>
                            <button type="button" id="btnRemoveCoupon" class="btn btn-sm btn-outline-danger py-0 px-2" title="Remove Coupon">&times;</button>
                        </div>
                    <?php else: ?>
                        <div class="input-group">
                            <input type="text" id="couponCodeInput" class="form-control text-uppercase" placeholder="e.g. DWC10, INFRA500" aria-label="Coupon Code">
                            <button class="btn btn-primary fw-semibold" type="button" id="btnApplyCoupon">Apply</button>
                        </div>
                        <div class="form-text small mt-2">
                            Try <code>DWC10</code> (10% off > ₹5,000) or <code>INFRA500</code> (₹500 off > ₹10,000).
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Order Summary Breakdown -->
                <div class="card-custom p-4">
                    <h5 class="fw-bold mb-3">Order Price Summary</h5>

                    <div class="d-flex justify-content-between py-2 border-bottom text-muted small">
                        <span>Ex-Works Subtotal:</span>
                        <strong class="text-main" id="summarySubtotal"><?= format_price($totals['subtotal']) ?></strong>
                    </div>

                    <?php if ($totals['discount'] > 0): ?>
                        <div class="d-flex justify-content-between py-2 border-bottom text-success small">
                            <span>Discount (<?= htmlspecialchars($totals['coupon_code'] ?? 'Promo') ?>):</span>
                            <strong id="summaryDiscount">- <?= format_price($totals['discount']) ?></strong>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between py-2 border-bottom text-muted small">
                        <span>GST (<?= $totals['gst_rate'] ?>% Tax with ITC):</span>
                        <strong class="text-main" id="summaryGst"><?= format_price($totals['gst_amount']) ?></strong>
                    </div>

                    <div class="d-flex justify-content-between py-2 border-bottom text-muted small">
                        <span>Site Freight / Transit:</span>
                        <span class="text-success fw-bold">Standard Transport / Pickup</span>
                    </div>

                    <div class="d-flex justify-content-between py-3 mb-3">
                        <span class="h6 fw-bold mb-0">Grand Total (Incl. GST):</span>
                        <span class="h5 fw-bold text-primary mb-0" id="summaryGrandTotal"><?= format_price($totals['grand_total']) ?></span>
                    </div>

                    <a href="<?= BASE_URL ?>checkout.php" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                        <i class="fa-solid fa-lock me-2"></i> Proceed to Checkout
                    </a>

                    <div class="text-center small text-muted mt-3">
                        <i class="fa-solid fa-shield-halved text-success me-1"></i> 100% Secure Checkout & GST Invoicing
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
