<?php
/**
 * Admin Homepage Settings Action
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_editor_or_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'homepage.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'homepage.php');
    exit;
}

$db = get_db();
$uploadDir = __DIR__ . '/../assets/uploads/homepage/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

try {
    $db->beginTransaction();
    $stmt = $db->prepare("INSERT INTO `settings` (`key_name`, `key_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`)");

    $fields = [
        'home_hero_badge', 'home_hero_heading', 'home_hero_subheading',
        'home_hero_btn1_text', 'home_hero_btn1_url', 'home_hero_btn2_text', 'home_hero_btn2_url',
        'home_stat1_number', 'home_stat1_label', 'home_stat2_number', 'home_stat2_label',
        'home_stat3_number', 'home_stat3_label',
        'home_company_heading', 'home_company_subheading', 'home_company_desc',
        'home_products_heading', 'home_products_desc', 'home_products_count',
        'home_feature1_title', 'home_feature1_desc', 'home_feature1_icon',
        'home_feature2_title', 'home_feature2_desc', 'home_feature2_icon',
        'home_feature3_title', 'home_feature3_desc', 'home_feature3_icon',
        'home_feature4_title', 'home_feature4_desc', 'home_feature4_icon',
        'home_cta_heading', 'home_cta_desc', 'home_cta_btn_text', 'home_cta_btn_url'
    ];

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $stmt->execute([$field, trim($_POST[$field])]);
        }
    }

    // Hero Image Upload
    if (!empty($_FILES['hero_image_file']) && $_FILES['hero_image_file']['error'] === UPLOAD_ERR_OK) {
        $res = handle_file_upload($_FILES['hero_image_file'], $uploadDir, ['jpg', 'jpeg', 'png', 'webp', 'svg'], 10);
        if ($res['success']) {
            $path = 'assets/uploads/homepage/' . $res['filename'];
            $stmt->execute(['home_hero_image', $path]);
        }
    } elseif (!empty($_POST['home_hero_image'])) {
        $stmt->execute(['home_hero_image', trim($_POST['home_hero_image'])]);
    }

    // Company Image Upload
    if (!empty($_FILES['company_image_file']) && $_FILES['company_image_file']['error'] === UPLOAD_ERR_OK) {
        $res = handle_file_upload($_FILES['company_image_file'], $uploadDir, ['jpg', 'jpeg', 'png', 'webp', 'svg'], 10);
        if ($res['success']) {
            $path = 'assets/uploads/homepage/' . $res['filename'];
            $stmt->execute(['home_company_image', $path]);
        }
    } elseif (!empty($_POST['home_company_image'])) {
        $stmt->execute(['home_company_image', trim($_POST['home_company_image'])]);
    }

    $db->commit();
    set_flash('success', 'Homepage content and layout updated successfully.');

} catch (Exception $e) {
    $db->rollBack();
    set_flash('danger', 'Error updating homepage settings: ' . $e->getMessage());
}

header('Location: ' . ADMIN_URL . 'homepage.php');
exit;
