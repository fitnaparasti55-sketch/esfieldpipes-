<?php
/**
 * AJAX API: Cart Actions (Add, Update, Remove)
 * Esfield Pipe Platform
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$db = get_db();
$cartIdent = get_cart_identifier();

if ($action === 'add') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    if (!$productId) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        exit;
    }

    // Check product exists and is active
    $pStmt = $db->prepare("SELECT id, name, price_per_pipe, stock_quantity FROM `products` WHERE `id` = ? AND `status` = 'active'");
    $pStmt->execute([$productId]);
    $product = $pStmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found or unavailable']);
        exit;
    }

    // Check existing item in cart
    if ($cartIdent['user_id']) {
        $cStmt = $db->prepare("SELECT id, quantity FROM `cart` WHERE `user_id` = ? AND `product_id` = ?");
        $cStmt->execute([$cartIdent['user_id'], $productId]);
    } else {
        $cStmt = $db->prepare("SELECT id, quantity FROM `cart` WHERE `session_id` = ? AND `product_id` = ? AND `user_id` IS NULL");
        $cStmt->execute([$cartIdent['session_id'], $productId]);
    }
    $existing = $cStmt->fetch();

    if ($existing) {
        $newQty = $existing['quantity'] + $quantity;
        $uStmt = $db->prepare("UPDATE `cart` SET `quantity` = ? WHERE `id` = ?");
        $uStmt->execute([$newQty, $existing['id']]);
    } else {
        $iStmt = $db->prepare("INSERT INTO `cart` (`user_id`, `session_id`, `product_id`, `quantity`, `pipe_length_m`) VALUES (?, ?, ?, ?, 6.00)");
        $iStmt->execute([$cartIdent['user_id'], $cartIdent['session_id'], $productId, $quantity]);
    }

    $totals = get_cart_totals();
    echo json_encode([
        'success' => true,
        'message' => 'Added ' . $product['name'] . ' to cart!',
        'cart_count' => $totals['total_qty'],
        'subtotal' => format_price($totals['subtotal']),
        'grand_total' => format_price($totals['grand_total'])
    ]);
    exit;
}

if ($action === 'update') {
    $cartId = (int)($_POST['cart_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    if (!$cartId) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart item ID']);
        exit;
    }

    // Verify ownership
    if ($cartIdent['user_id']) {
        $stmt = $db->prepare("UPDATE `cart` SET `quantity` = ? WHERE `id` = ? AND `user_id` = ?");
        $stmt->execute([$quantity, $cartId, $cartIdent['user_id']]);
    } else {
        $stmt = $db->prepare("UPDATE `cart` SET `quantity` = ? WHERE `id` = ? AND `session_id` = ? AND `user_id` IS NULL");
        $stmt->execute([$quantity, $cartId, $cartIdent['session_id']]);
    }

    // Fetch line item
    $lineStmt = $db->prepare("SELECT c.quantity, p.price_per_pipe FROM `cart` c JOIN `products` p ON c.product_id = p.id WHERE c.id = ?");
    $lineStmt->execute([$cartId]);
    $line = $lineStmt->fetch();
    $lineTotal = $line ? format_price($line['quantity'] * $line['price_per_pipe']) : '₹0.00';

    $totals = get_cart_totals();
    echo json_encode([
        'success' => true,
        'message' => 'Cart updated',
        'line_total' => $lineTotal,
        'cart_count' => $totals['total_qty'],
        'subtotal' => format_price($totals['subtotal']),
        'discount' => format_price($totals['discount']),
        'gst_amount' => format_price($totals['gst_amount']),
        'grand_total' => format_price($totals['grand_total'])
    ]);
    exit;
}

if ($action === 'remove') {
    $cartId = (int)($_POST['cart_id'] ?? 0);
    if (!$cartId) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart item ID']);
        exit;
    }

    if ($cartIdent['user_id']) {
        $stmt = $db->prepare("DELETE FROM `cart` WHERE `id` = ? AND `user_id` = ?");
        $stmt->execute([$cartId, $cartIdent['user_id']]);
    } else {
        $stmt = $db->prepare("DELETE FROM `cart` WHERE `id` = ? AND `session_id` = ? AND `user_id` IS NULL");
        $stmt->execute([$cartId, $cartIdent['session_id']]);
    }

    $totals = get_cart_totals();
    echo json_encode([
        'success' => true,
        'message' => 'Item removed from cart',
        'cart_count' => $totals['total_qty'],
        'subtotal' => format_price($totals['subtotal']),
        'discount' => format_price($totals['discount']),
        'gst_amount' => format_price($totals['gst_amount']),
        'grand_total' => format_price($totals['grand_total'])
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
