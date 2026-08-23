<?php
/**
 * Admin Inquiries Action Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_editor_or_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'inquiries.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'inquiries.php');
    exit;
}

$action = $_POST['action'] ?? '';
$db = get_db();

try {
    if ($action === 'reply') {
        $id = (int)($_POST['inquiry_id'] ?? 0);
        $status = $_POST['status'] ?? 'resolved';
        $reply = trim($_POST['admin_reply'] ?? '');

        $stmt = $db->prepare("UPDATE `support_inquiries` SET `status` = ?, `admin_reply` = ? WHERE `id` = ?");
        $stmt->execute([$status, $reply, $id]);

        set_flash('success', 'Inquiry status and reply updated.');
        header('Location: ' . ADMIN_URL . 'inquiries.php');
        exit;
    }
} catch (Exception $e) {
    set_flash('danger', 'Error updating inquiry: ' . $e->getMessage());
    header('Location: ' . ADMIN_URL . 'inquiries.php');
    exit;
}
