<?php
/**
 * Utility Functions & Helpers
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Sanitize user input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
}

/**
 * Currency Formatter
 */
function format_price($amount, $showCurrency = true): string {
    $num = (float)$amount;
    $formatted = number_format($num, 2, '.', ',');
    if ($showCurrency) {
        $currency = get_setting('site_currency', '₹');
        return $currency . ' ' . $formatted;
    }
    return $formatted;
}

/**
 * Flash Messaging
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // success, danger, warning, info
        'message' => $message
    ];
}

function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function has_flash(): bool {
    return isset($_SESSION['flash']);
}

/**
 * Render Flash Alert HTML
 */
function render_flash(): string {
    $flash = get_flash();
    if (!$flash) {
        return '';
    }
    $type = htmlspecialchars($flash['type']);
    $msg = htmlspecialchars($flash['message']);
    $icon = 'fa-info-circle';
    if ($type === 'success') $icon = 'fa-check-circle';
    if ($type === 'danger' || $type === 'error') {
        $type = 'danger';
        $icon = 'fa-exclamation-triangle';
    }
    if ($type === 'warning') $icon = 'fa-exclamation-circle';

    return "<div class='alert alert-{$type} alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm' role='alert'>
        <i class='fa-solid {$icon} me-2 fs-5'></i>
        <div class='flex-grow-1'>{$msg}</div>
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
}

/**
 * Get current user identifier for cart
 */
function get_cart_identifier(): array {
    $userId = $_SESSION['user_id'] ?? null;
    $sessionId = $_SESSION['guest_session_id'] ?? session_id();
    return ['user_id' => $userId, 'session_id' => $sessionId];
}

/**
 * Get Total Cart Item Count
 */
function get_cart_count(): int {
    $id = get_cart_identifier();
    $db = get_db();
    if ($id['user_id']) {
        $stmt = $db->prepare("SELECT SUM(quantity) as total_qty FROM `cart` WHERE `user_id` = ?");
        $stmt->execute([$id['user_id']]);
    } else {
        $stmt = $db->prepare("SELECT SUM(quantity) as total_qty FROM `cart` WHERE `session_id` = ? AND `user_id` IS NULL");
        $stmt->execute([$id['session_id']]);
    }
    $row = $stmt->fetch();
    return (int)($row['total_qty'] ?? 0);
}

/**
 * Get Cart Items with Product Details
 */
function get_cart_items(): array {
    $id = get_cart_identifier();
    $db = get_db();
    
    if ($id['user_id']) {
        $stmt = $db->prepare("
            SELECT c.*, p.name, p.slug, p.inner_diameter_mm, p.outer_diameter_mm, 
                   p.stiffness_class, p.price_per_pipe, p.price_per_meter, p.image, p.stock_quantity
            FROM `cart` c
            JOIN `products` p ON c.product_id = p.id
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$id['user_id']]);
    } else {
        $stmt = $db->prepare("
            SELECT c.*, p.name, p.slug, p.inner_diameter_mm, p.outer_diameter_mm, 
                   p.stiffness_class, p.price_per_pipe, p.price_per_meter, p.image, p.stock_quantity
            FROM `cart` c
            JOIN `products` p ON c.product_id = p.id
            WHERE c.session_id = ? AND c.user_id IS NULL
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$id['session_id']]);
    }
    
    $items = $stmt->fetchAll();
    foreach ($items as &$item) {
        $item['line_total'] = $item['price_per_pipe'] * $item['quantity'];
    }
    return $items;
}

/**
 * Calculate Cart Totals (Subtotal, GST, Discount, Total)
 */
function get_cart_totals(): array {
    $items = get_cart_items();
    $subtotal = 0.0;
    foreach ($items as $item) {
        $subtotal += (float)$item['line_total'];
    }

    $discount = 0.0;
    $couponCode = $_SESSION['applied_coupon']['code'] ?? null;
    if (!empty($_SESSION['applied_coupon'])) {
        $coupon = $_SESSION['applied_coupon'];
        if ($subtotal >= (float)$coupon['min_order_amount']) {
            if ($coupon['discount_type'] === 'percentage') {
                $discount = ($subtotal * (float)$coupon['discount_value']) / 100;
                if (!empty($coupon['max_discount']) && $discount > (float)$coupon['max_discount']) {
                    $discount = (float)$coupon['max_discount'];
                }
            } else {
                $discount = (float)$coupon['discount_value'];
            }
            if ($discount > $subtotal) {
                $discount = $subtotal;
            }
        } else {
            // Invalidate coupon if minimum amount not met
            unset($_SESSION['applied_coupon']);
            $couponCode = null;
        }
    }

    $gstRate = (float)get_setting('site_gst_rate', '18');
    $taxableAmount = max(0, $subtotal - $discount);
    $gstAmount = ($taxableAmount * $gstRate) / 100;
    $shipping = 0.00; // Free nationwide delivery above threshold / factory pickup
    $grandTotal = $taxableAmount + $gstAmount + $shipping;

    return [
        'subtotal' => $subtotal,
        'discount' => $discount,
        'coupon_code' => $couponCode,
        'gst_rate' => $gstRate,
        'gst_amount' => $gstAmount,
        'shipping' => $shipping,
        'grand_total' => $grandTotal,
        'item_count' => count($items),
        'total_qty' => array_sum(array_column($items, 'quantity'))
    ];
}

/**
 * Transfer guest cart to logged-in user upon login
 */
function transfer_guest_cart_to_user(int $userId, string $sessionId): void {
    $db = get_db();
    // Check if guest cart items exist
    $stmt = $db->prepare("SELECT * FROM `cart` WHERE `session_id` = ? AND `user_id` IS NULL");
    $stmt->execute([$sessionId]);
    $guestItems = $stmt->fetchAll();

    foreach ($guestItems as $item) {
        // Check if user already has this product in cart
        $check = $db->prepare("SELECT id, quantity FROM `cart` WHERE `user_id` = ? AND `product_id` = ?");
        $check->execute([$userId, $item['product_id']]);
        $existing = $check->fetch();

        if ($existing) {
            // Update quantity
            $update = $db->prepare("UPDATE `cart` SET `quantity` = `quantity` + ? WHERE `id` = ?");
            $update->execute([$item['quantity'], $existing['id']]);
            // Delete guest record
            $del = $db->prepare("DELETE FROM `cart` WHERE `id` = ?");
            $del->execute([$item['id']]);
        } else {
            // Assign to user
            $update = $db->prepare("UPDATE `cart` SET `user_id` = ? WHERE `id` = ?");
            $update->execute([$userId, $item['id']]);
        }
    }
}

/**
 * Order Status Badge Formatter
 */
function get_order_status_badge(string $status): string {
    $map = [
        'pending' => ['class' => 'bg-warning text-dark', 'icon' => 'fa-clock', 'text' => 'Pending Confirmation'],
        'confirmed' => ['class' => 'bg-info text-white', 'icon' => 'fa-clipboard-check', 'text' => 'Confirmed'],
        'processing' => ['class' => 'bg-primary text-white', 'icon' => 'fa-gears', 'text' => 'Manufacturing / Packing'],
        'shipped' => ['class' => 'bg-secondary text-white', 'icon' => 'fa-truck-fast', 'text' => 'In Transit / Shipped'],
        'delivered' => ['class' => 'bg-success text-white', 'icon' => 'fa-circle-check', 'text' => 'Delivered'],
        'cancelled' => ['class' => 'bg-danger text-white', 'icon' => 'fa-ban', 'text' => 'Cancelled'],
        'refunded' => ['class' => 'bg-dark text-white', 'icon' => 'fa-rotate-left', 'text' => 'Refunded']
    ];

    $info = $map[strtolower($status)] ?? ['class' => 'bg-secondary text-white', 'icon' => 'fa-circle-info', 'text' => ucfirst($status)];
    return "<span class='badge rounded-pill {$info['class']} px-3 py-2 fs-7'>
        <i class='fa-solid {$info['icon']} me-1'></i> {$info['text']}
    </span>";
}

/**
 * Payment Status Badge Formatter
 */
function get_payment_status_badge(string $status): string {
    $map = [
        'pending' => ['class' => 'badge-soft-warning', 'text' => 'Unpaid / Pending'],
        'paid' => ['class' => 'badge-soft-success', 'text' => 'Paid (Verified)'],
        'failed' => ['class' => 'badge-soft-danger', 'text' => 'Payment Failed'],
        'refunded' => ['class' => 'badge-soft-info', 'text' => 'Refunded']
    ];
    $info = $map[strtolower($status)] ?? ['class' => 'badge-soft-secondary', 'text' => ucfirst($status)];
    return "<span class='badge {$info['class']} px-2.5 py-1.5'>{$info['text']}</span>";
}

/**
 * Generate unique order tracking number
 */
function generate_order_number(): string {
    return 'ORD-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
}

/**
 * Safe File Upload Handler
 */
function handle_file_upload(array $file, string $targetDir, array $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'pdf'], int $maxSizeMb = 10): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Invalid file parameters.'];
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'No file was uploaded.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error code: ' . $file['error']];
    }

    if ($file['size'] > ($maxSizeMb * 1024 * 1024)) {
        return ['success' => false, 'error' => "File exceeds maximum permitted size of {$maxSizeMb}MB."];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) {
        return ['success' => false, 'error' => 'Unsupported file format. Allowed formats: ' . implode(', ', $allowedExts)];
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $newFilename = uniqid('esfield_', true) . '.' . $ext;
    $destination = rtrim($targetDir, '/') . '/' . $newFilename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'error' => 'Failed to save uploaded file on server disk.'];
    }

    return [
        'success' => true,
        'filename' => $newFilename,
        'path' => $destination,
        'extension' => $ext
    ];
}
