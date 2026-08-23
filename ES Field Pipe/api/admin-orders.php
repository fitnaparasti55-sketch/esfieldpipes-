<?php
/**
 * Admin Orders Action Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_editor_or_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'orders.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'orders.php');
    exit;
}

$action = $_POST['action'] ?? '';
$db = get_db();

try {
    if ($action === 'update_order') {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $orderStatus = $_POST['order_status'] ?? 'pending';
        $paymentStatus = $_POST['payment_status'] ?? 'pending';
        $trackingNumber = trim($_POST['tracking_number'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        $stmt = $db->prepare("
            UPDATE `orders` SET 
                `order_status` = ?, 
                `payment_status` = ?, 
                `tracking_number` = ?, 
                `notes` = ? 
            WHERE `id` = ?
        ");
        $stmt->execute([$orderStatus, $paymentStatus, $trackingNumber, $notes, $orderId]);

        set_flash('success', 'Order status and dispatch details updated successfully.');
        header('Location: ' . ADMIN_URL . 'order-detail.php?id=' . $orderId);
        exit;
    }
} catch (Exception $e) {
    set_flash('danger', 'Error updating order: ' . $e->getMessage());
    header('Location: ' . ADMIN_URL . 'orders.php');
    exit;
}
