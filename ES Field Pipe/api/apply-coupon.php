<?php
/**
 * AJAX API: Apply / Remove Coupon
 * Esfield Pipe Platform
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$action = $_POST['action'] ?? 'apply';
$db = get_db();

if ($action === 'remove') {
    unset($_SESSION['applied_coupon']);
    echo json_encode(['success' => true, 'message' => 'Coupon removed.']);
    exit;
}

$code = strtoupper(trim($_POST['coupon_code'] ?? ''));
if (empty($code)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid coupon code.']);
    exit;
}

$stmt = $db->prepare("SELECT * FROM `coupons` WHERE `code` = ? AND `status` = 'active'");
$stmt->execute([$code]);
$coupon = $stmt->fetch();

if (!$coupon) {
    echo json_encode(['success' => false, 'message' => 'Invalid or inactive promo code.']);
    exit;
}

// Check date validity
$today = date('Y-m-d');
if (!empty($coupon['start_date']) && $coupon['start_date'] > $today) {
    echo json_encode(['success' => false, 'message' => 'This promo code is not active yet.']);
    exit;
}
if (!empty($coupon['end_date']) && $coupon['end_date'] < $today) {
    echo json_encode(['success' => false, 'message' => 'This promo code has expired.']);
    exit;
}

// Check cart total requirement
$totals = get_cart_totals();
if ($totals['subtotal'] < (float)$coupon['min_order_amount']) {
    echo json_encode([
        'success' => false, 
        'message' => 'Minimum order amount for this coupon is ' . format_price($coupon['min_order_amount']) . '. Your current subtotal is ' . format_price($totals['subtotal']) . '.'
    ]);
    exit;
}

// Check usage limit
if ($coupon['usage_limit'] > 0 && $coupon['usage_count'] >= $coupon['usage_limit']) {
    echo json_encode(['success' => false, 'message' => 'Coupon usage limit has been reached.']);
    exit;
}

// Save in session
$_SESSION['applied_coupon'] = [
    'id' => $coupon['id'],
    'code' => $coupon['code'],
    'discount_type' => $coupon['discount_type'],
    'discount_value' => (float)$coupon['discount_value'],
    'min_order_amount' => (float)$coupon['min_order_amount'],
    'max_discount' => $coupon['max_discount'] ? (float)$coupon['max_discount'] : null
];

$newTotals = get_cart_totals();

echo json_encode([
    'success' => true,
    'message' => 'Coupon ' . $coupon['code'] . ' applied successfully! You saved ' . format_price($newTotals['discount']) . '.',
    'discount' => format_price($newTotals['discount']),
    'grand_total' => format_price($newTotals['grand_total'])
]);
