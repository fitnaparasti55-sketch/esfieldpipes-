<?php
/**
 * Admin SEO Action Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_editor_or_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'seo.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'seo.php');
    exit;
}

$db = get_db();

try {
    $db->beginTransaction();
    $stmt = $db->prepare("INSERT INTO `settings` (`key_name`, `key_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`)");

    $seoFields = [
        'meta_title', 'meta_description', 'meta_keywords',
        'home_seo_title', 'home_seo_description', 'og_image',
        'robots_indexing'
    ];

    foreach ($seoFields as $field) {
        if (isset($_POST[$field])) {
            $stmt->execute([$field, trim($_POST[$field])]);
        }
    }

    $db->commit();
    set_flash('success', 'Search Engine Optimization (SEO) metadata updated successfully.');

} catch (Exception $e) {
    $db->rollBack();
    set_flash('danger', 'Error updating SEO settings: ' . $e->getMessage());
}

header('Location: ' . ADMIN_URL . 'seo.php');
exit;
