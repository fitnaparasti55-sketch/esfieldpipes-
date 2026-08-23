<?php
/**
 * Update Admin Settings Action
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Access Control
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'admin/settings.php');
    exit;
}

// CSRF Verification
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . BASE_URL . 'admin/settings.php');
    exit;
}

$db = get_db();
$postData = $_POST;

// Remove non-setting fields
unset($postData['csrf_token']);

try {
    $db->beginTransaction();

    $stmt = $db->prepare("INSERT INTO `settings` (`key_name`, `key_value`) VALUES (?, ?)
                          ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`)");

    foreach ($postData as $key => $value) {
        $stmt->execute([$key, $value]);
    }

    $db->commit();
    set_flash('success', 'Website settings have been updated successfully.');

} catch (Exception $e) {
    $db->rollBack();
    set_flash('danger', 'Failed to update settings: ' . $e->getMessage());
}

header('Location: ' . BASE_URL . 'admin/settings.php');
exit;
