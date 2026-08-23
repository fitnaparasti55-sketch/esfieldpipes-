<?php
/**
 * Admin Profile Update Action Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_editor_or_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'profile.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'profile.php');
    exit;
}

$action = $_POST['action'] ?? '';
$db = get_db();
$userId = (int)$_SESSION['user_id'];

try {
    // 1. UPDATE PROFILE DETAILS
    if ($action === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $company = trim($_POST['company_name'] ?? '');

        if (empty($name) || empty($email)) {
            set_flash('danger', 'Name and email are required.');
            header('Location: ' . ADMIN_URL . 'profile.php');
            exit;
        }

        // Check if email taken by someone else
        $check = $db->prepare("SELECT id FROM `users` WHERE `email` = ? AND `id` != ?");
        $check->execute([$email, $userId]);
        if ($check->fetch()) {
            set_flash('danger', 'Email address is already in use by another account.');
            header('Location: ' . ADMIN_URL . 'profile.php');
            exit;
        }

        $stmt = $db->prepare("UPDATE `users` SET `name` = ?, `email` = ?, `phone` = ?, `company_name` = ? WHERE `id` = ?");
        $stmt->execute([$name, $email, $phone, $company, $userId]);

        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        set_flash('success', 'Profile details updated successfully.');
        header('Location: ' . ADMIN_URL . 'profile.php');
        exit;
    }

    // 2. CHANGE PASSWORD
    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($newPassword) < 6) {
            set_flash('danger', 'New password must be at least 6 characters long.');
            header('Location: ' . ADMIN_URL . 'profile.php');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            set_flash('danger', 'New passwords do not match.');
            header('Location: ' . ADMIN_URL . 'profile.php');
            exit;
        }

        $stmt = $db->prepare("SELECT `password` FROM `users` WHERE `id` = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            set_flash('danger', 'Incorrect current password entered.');
            header('Location: ' . ADMIN_URL . 'profile.php');
            exit;
        }

        $newHashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $upStmt = $db->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ?");
        $upStmt->execute([$newHashed, $userId]);

        set_flash('success', 'Your password has been changed successfully.');
        header('Location: ' . ADMIN_URL . 'profile.php');
        exit;
    }

} catch (Exception $e) {
    set_flash('danger', 'Error updating profile: ' . $e->getMessage());
    header('Location: ' . ADMIN_URL . 'profile.php');
    exit;
}
