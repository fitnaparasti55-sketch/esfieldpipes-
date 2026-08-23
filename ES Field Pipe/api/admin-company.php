<?php
/**
 * Admin Company Information Action
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_editor_or_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'company.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'company.php');
    exit;
}

$db = get_db();

try {
    $db->beginTransaction();
    $stmt = $db->prepare("INSERT INTO `settings` (`key_name`, `key_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`)");

    $fields = [
        'site_name', 'site_tagline', 'site_phone', 'site_phone_alt', 'site_whatsapp',
        'site_email', 'site_address', 'site_url', 'gstin', 'pan_number', 'cin_number',
        'bis_info', 'footer_about', 'facebook_url', 'linkedin_url', 'twitter_url',
        'instagram_url', 'youtube_url'
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $stmt->execute([$field, trim($_POST[$field])]);
        }
    }

    $db->commit();
    set_flash('success', 'Company information and business details updated successfully.');

} catch (Exception $e) {
    $db->rollBack();
    set_flash('danger', 'Error updating company info: ' . $e->getMessage());
}

header('Location: ' . ADMIN_URL . 'company.php');
exit;
