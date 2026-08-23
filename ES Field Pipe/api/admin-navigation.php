<?php
/**
 * Admin Navigation Action Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_editor_or_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'navigation.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'navigation.php');
    exit;
}

$db = get_db();

try {
    $db->beginTransaction();
    $stmt = $db->prepare("INSERT INTO `settings` (`key_name`, `key_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`)");

    for ($i = 1; $i <= 5; $i++) {
        if (isset($_POST["nav_label_{$i}"])) {
            $stmt->execute(["nav_label_{$i}", trim($_POST["nav_label_{$i}"])]);
        }
        if (isset($_POST["nav_url_{$i}"])) {
            $stmt->execute(["nav_url_{$i}", trim($_POST["nav_url_{$i}"])]);
        }
    }

    $db->commit();
    set_flash('success', 'Navigation menu links updated successfully.');

} catch (Exception $e) {
    $db->rollBack();
    set_flash('danger', 'Error updating navigation: ' . $e->getMessage());
}

header('Location: ' . ADMIN_URL . 'navigation.php');
exit;
