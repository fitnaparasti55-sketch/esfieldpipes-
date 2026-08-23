<?php
/**
 * Admin Appearance & Theme Action Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_editor_or_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'appearance.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'appearance.php');
    exit;
}

$db = get_db();

try {
    $db->beginTransaction();
    $stmt = $db->prepare("INSERT INTO `settings` (`key_name`, `key_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`)");

    $themeFields = [
        'theme_primary_color',
        'theme_primary_hover',
        'theme_secondary_color',
        'theme_secondary_hover',
        'theme_accent_color',
        'theme_bg_body',
        'theme_text_main',
        'theme_header_bg',
        'theme_topbar_bg',
        'theme_footer_bg',
        'theme_btn_color',
        'theme_btn_hover_color',
        'theme_border_radius',
        'theme_font_family'
    ];

    foreach ($themeFields as $field) {
        if (isset($_POST[$field])) {
            $stmt->execute([$field, trim($_POST[$field])]);
        }
    }

    $db->commit();
    set_flash('success', 'Visual theme and color scheme updated successfully across the entire website.');

} catch (Exception $e) {
    $db->rollBack();
    set_flash('danger', 'Error updating theme: ' . $e->getMessage());
}

header('Location: ' . ADMIN_URL . 'appearance.php');
exit;
