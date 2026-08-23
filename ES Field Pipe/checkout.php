<?php
/**
 * Checkout Page
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$cartItems = get_cart_items();
$totals = get_cart_totals();

if (empty($cartItems)) {
    set_flash('warning', 'Your cart is empty. Please add DWC pipes before proceeding to checkout.');
    header('Location: ' . BASE_URL . 'products.php');
    exit;
}

$user = current_user();
$errors = [];

// Handle Order Placement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please refresh the page and try again.';
    }

    $name = sanitize($_POST['customer_name'] ?? '');
    $email = filter_var(trim($_POST['customer_email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $phone = sanitize($_POST['customer_phone'] ?? '');
    $company = sanitize($_POST['company_name'] ?? '');
    $gstin = sanitize($_POST['gstin'] ?? '');
    $address = sanitize($_POST['shipping_address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $state = sanitize($_POST['state'] ?? '');
    $pincode = sanitize($_POST['pincode'] ?? '');
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'bank_transfer');
    $notes = sanitize($_POST['notes'] ?? '');

    if (empty($name)) $errors[] = 'Customer full name is required.';
    if (!$email) $errors[] = 'A valid email address is required for order confirmation and invoice.';
    if (empty($phone)) $errors[] = 'Phone number is required for dispatch communication.';
    if (empty($address)) $errors[] = 'Site delivery address is required.';
    if (empty($city)) $errors[] = 'City is required.';
    if (empty($state)) $errors[] = 'State is required.';
    if (empty($pincode)) $errors[] = 'Pincode / Postal code is required.';

    if (!in_array($paymentMethod, ['razorpay', 'stripe', 'bank_transfer', 'cod'])) {
        $paymentMethod = 'bank_transfer';
    }

    if (empty($errors)) {
        $db = get_db();
        try {
            $db->beginTransaction();

            $orderNumber = generate_order_number();
            $paymentStatus = in_array($paymentMethod, ['razorpay', 'stripe']) ? 'paid' : 'pending';
            $transactionId = in_array($paymentMethod, ['razorpay', 'stripe']) ? ('TXN-' . strtoupper(bin2hex(random_bytes(6)))) : null;
            $orderStatus = 'confirmed';

            // Insert into orders
            $orderStmt = $db->prepare("
                INSERT INTO `orders` (
                    `order_number`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, 
                    `company_name`, `gstin`, `shipping_address`, `city`, `state`, `pincode`, 
                    `subtotal`, `tax_amount`, `discount_amount`, `shipping_charge`, `total_amount`, 
                    `coupon_code`, `payment_method`, `payment_status`, `transaction_id`, 
                    `order_status`, `notes`
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $orderStmt->execute([
                $orderNumber,
                $user ? $user['id'] : null,
                $name,
                $email,
                $phone,
                $company,
                $gstin,
                $address,
                $city,
                $state,
                $pincode,
                $totals['subtotal'],
                $totals['gst_amount'],
                $totals['discount'],
                $totals['shipping'],
                $totals['grand_total'],
                $totals['coupon_code'],
                $paymentMethod,
                $paymentStatus,
                $transactionId,
                $orderStatus,
                $notes
            ]);

            $orderId = $db->lastInsertId();

            // Insert order items & adjust stock
            $itemStmt = $db->prepare("
                INSERT INTO `order_items` (
                    `order_id`, `product_id`, `product_name`, `inner_diameter_mm`, `outer_diameter_mm`, 
                    `stiffness_class`, `unit_price`, `quantity`, `pipe_length_m`, `total_price`
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stockStmt = $db->prepare("UPDATE `products` SET `stock_quantity` = GREATEST(0, `stock_quantity` - ?) WHERE `id` = ?");

            foreach ($cartItems as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['name'],
                    $item['inner_diameter_mm'],
                    $item['outer_diameter_mm'],
                    $item['stiffness_class'],
                    $item['price_per_pipe'],
                    $item['quantity'],
                    $item['pipe_length_m'] ?? 6.00,
                    $item['line_total']
                ]);

                $stockStmt->execute([$item['quantity'], $item['product_id']]);
            }

            // If coupon used, increment coupon usage
            if (!empty($totals['coupon_code'])) {
                $cUpdate = $db->prepare("UPDATE `coupons` SET `usage_count` = `usage_count` + 1 WHERE `code` = ?");
                $cUpdate->execute([$totals['coupon_code']]);
            }

            // Clear Cart
            $cartIdent = get_cart_identifier();
            if ($cartIdent['user_id']) {
                $db->prepare("DELETE FROM `cart` WHERE `user_id` = ?")->execute([$cartIdent['user_id']]);
            } else {
                $db->prepare("DELETE FROM `cart` WHERE `session_id` = ? AND `user_id` IS NULL")->execute([$cartIdent['session_id']]);
            }
            unset($_SESSION['applied_coupon']);

            $db->commit();

            header('Location: ' . BASE_URL . 'order-confirmation.php?order_number=' . urlencode($orderNumber));
            exit;

        } catch (Exception $e) {
            $db->rollBack();
            $errors[] = 'Failed to place order: ' . $e->getMessage();
        }
    }
}

$pageTitle = "Checkout & Payment";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>"><i class="fa-solid fa-house me-1"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>cart.php">Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>

    <h1 class="h3 fw-bold mb-4"><i class="fa-solid fa-truck-ramp-box text-primary me-2"></i> Delivery & Payment Checkout</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>checkout.php" method="POST" id="checkoutForm">
        <?= csrf_field() ?>

        <div class="row g-4">
            <!-- Left: Delivery Address & Billing Info -->
            <div class="col-lg-7">
                <!-- Section 1: Customer Details -->
                <div class="card-custom p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-user-tie text-primary me-2"></i> 1. Contact & B2B Information</h5>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="customer_name" class="form-control" value="<?= htmlspecialchars($_POST['customer_name'] ?? ($user['name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address * (For Tax Invoice)</label>
                            <input type="email" name="customer_email" class="form-control" value="<?= htmlspecialchars($_POST['customer_email'] ?? ($user['email'] ?? '')) ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Phone Number * (Site Coordinator)</label>
                            <input type="tel" name="customer_phone" class="form-control" placeholder="+91 98765 00000" value="<?= htmlspecialchars($_POST['customer_phone'] ?? ($user['phone'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company / Contractor Name</label>
                            <input type="text" name="company_name" class="form-control" placeholder="e.g. Apex Infra Pvt Ltd" value="<?= htmlspecialchars($_POST['company_name'] ?? ($user['company_name'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">GSTIN Number (Optional - For Input Tax Credit)</label>
                        <input type="text" name="gstin" class="form-control text-uppercase" placeholder="e.g. 07AABCE9876F1Z4" value="<?= htmlspecialchars($_POST['gstin'] ?? ($user['gstin'] ?? '')) ?>">
                        <div class="form-text small text-muted">A valid GSTIN ensures 18% Input Tax Credit claim on your business tax filing.</div>
                    </div>
                </div>

                <!-- Section 2: Site Shipping Address -->
                <div class="card-custom p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-location-dot text-primary me-2"></i> 2. Site Delivery Address</h5>

                    <div class="mb-3">
                        <label class="form-label">Detailed Site / Yard Address *</label>
                        <textarea name="shipping_address" class="form-control" rows="2.5" placeholder="Plot / Road / Trench stretch / Landmark" required><?= htmlspecialchars($_POST['shipping_address'] ?? ($user['address'] ?? '')) ?></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">City *</label>
                            <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($_POST['city'] ?? ($user['city'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State *</label>
                            <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($_POST['state'] ?? ($user['state'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode *</label>
                            <input type="text" name="pincode" class="form-control" value="<?= htmlspecialchars($_POST['pincode'] ?? ($user['pincode'] ?? '')) ?>" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Special Delivery / Unloading Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Site has trailer access, crane available between 9am-5pm..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Section 3: Payment Method Selection -->
                <div class="card-custom p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-credit-card text-primary me-2"></i> 3. Select Payment Gateway / Method</h5>

                    <div class="d-flex flex-column gap-2.5">
                        <!-- Option 1: Razorpay -->
                        <div class="form-check p-3 border rounded-3 bg-surface d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="radio" name="payment_method" id="pay_razorpay" value="razorpay" checked>
                            <label class="form-check-label flex-grow-1 cursor-pointer" for="pay_razorpay">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Razorpay (UPI, Cards, NetBanking, QR)</strong>
                                    <span class="badge bg-primary">Instant</span>
                                </div>
                                <div class="small text-muted">Supports Google Pay, PhonePe, Paytm, RuPay, Visa, Mastercard & Corporate NetBanking.</div>
                            </label>
                        </div>

                        <!-- Option 2: Stripe -->
                        <div class="form-check p-3 border rounded-3 bg-surface d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="radio" name="payment_method" id="pay_stripe" value="stripe">
                            <label class="form-check-label flex-grow-1 cursor-pointer" for="pay_stripe">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Stripe Secure Card Payment</strong>
                                    <span class="badge bg-info text-white">Global Cards</span>
                                </div>
                                <div class="small text-muted">Direct credit & debit card processing with 256-bit SSL encryption.</div>
                            </label>
                        </div>

                        <!-- Option 3: NEFT / RTGS Bank Transfer -->
                        <div class="form-check p-3 border rounded-3 bg-surface d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="radio" name="payment_method" id="pay_bank" value="bank_transfer">
                            <label class="form-check-label flex-grow-1 cursor-pointer" for="pay_bank">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Direct NEFT / RTGS / Proforma Invoice</strong>
                                    <span class="badge bg-secondary">B2B Favorite</span>
                                </div>
                                <div class="small text-muted">Generate instant Proforma Tax Invoice for accounts clearance before dispatch.</div>
                            </label>
                        </div>

                        <!-- Option 4: COD -->
                        <div class="form-check p-3 border rounded-3 bg-surface d-flex align-items-center gap-3">
                            <input class="form-check-input ms-0 mt-0" type="radio" name="payment_method" id="pay_cod" value="cod">
                            <label class="form-check-label flex-grow-1 cursor-pointer" for="pay_cod">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Pay upon Site Delivery (COD)</strong>
                                    <span class="badge bg-dark">Site Payment</span>
                                </div>
                                <div class="small text-muted">Pay driver/site executive after pipe inspection upon trailer arrival.</div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Order Review & Submit -->
            <div class="col-lg-5">
                <div class="card-custom p-4 sticky-top" style="top: 80px; z-index: 10;">
                    <h5 class="fw-bold mb-3">Order Items (<?= count($cartItems) ?>)</h5>

                    <!-- Order Line Items Preview -->
                    <div class="overflow-auto mb-3" style="max-height: 240px;">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom small">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary px-2 py-1"><?= $item['quantity'] ?>x</span>
                                    <div>
                                        <div class="fw-bold text-truncate" style="max-width: 180px;"><?= htmlspecialchars($item['name']) ?></div>
                                        <div class="text-muted" style="font-size: 0.72rem;">ID: <?= $item['inner_diameter_mm'] ?>mm | <?= $item['stiffness_class'] ?></div>
                                    </div>
                                </div>
                                <strong class="text-main"><?= format_price($item['line_total']) ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Price Calculations -->
                    <div class="d-flex justify-content-between py-2 border-bottom text-muted small">
                        <span>Ex-Works Subtotal:</span>
                        <strong class="text-main"><?= format_price($totals['subtotal']) ?></strong>
                    </div>

                    <?php if ($totals['discount'] > 0): ?>
                        <div class="d-flex justify-content-between py-2 border-bottom text-success small">
                            <span>Coupon Discount (<?= htmlspecialchars($totals['coupon_code'] ?? '') ?>):</span>
                            <strong>- <?= format_price($totals['discount']) ?></strong>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between py-2 border-bottom text-muted small">
                        <span>GST (<?= $totals['gst_rate'] ?>% Tax with ITC):</span>
                        <strong class="text-main"><?= format_price($totals['gst_amount']) ?></strong>
                    </div>

                    <div class="d-flex justify-content-between py-3 mb-3">
                        <span class="h6 fw-bold mb-0">Total Payable:</span>
                        <span class="h4 fw-bold text-primary mb-0"><?= format_price($totals['grand_total']) ?></span>
                    </div>

                    <button type="submit" name="place_order" class="btn btn-primary w-100 py-3 fw-bold fs-6 shadow">
                        <i class="fa-solid fa-circle-check me-2"></i> Confirm & Place Order
                    </button>

                    <div class="p-3 bg-subtle rounded-3 small text-muted mt-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-file-invoice text-primary"></i>
                            <span>GST Tax Invoice automatically generated</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-truck text-success"></i>
                            <span>Nationwide trailer freight dispatch</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
