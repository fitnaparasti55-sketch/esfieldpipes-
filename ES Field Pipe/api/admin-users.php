<?php
/**
 * Admin User Management Action Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin(); // Strict Admin Only

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ADMIN_URL . 'users.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed.');
    header('Location: ' . ADMIN_URL . 'users.php');
    exit;
}

$action = $_POST['action'] ?? '';
$db = get_db();

try {
    // 1. ADD USER
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = in_array($_POST['role'] ?? '', ['admin', 'editor', 'customer']) ? $_POST['role'] : 'customer';
        $phone = trim($_POST['phone'] ?? '');
        $company = trim($_POST['company_name'] ?? '');
        $status = in_array($_POST['status'] ?? '', ['active', 'blocked']) ? $_POST['status'] : 'active';

        if (empty($name) || empty($email) || empty($password)) {
            set_flash('danger', 'Please provide name, email, and password.');
            header('Location: ' . ADMIN_URL . 'users.php');
            exit;
        }

        // Check if email already exists
        $check = $db->prepare("SELECT id FROM `users` WHERE `email` = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            set_flash('danger', 'A user with this email address already exists.');
            header('Location: ' . ADMIN_URL . 'users.php');
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone`, `company_name`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $role, $phone, $company, $status]);

        set_flash('success', "User '{$name}' ({$email}) created successfully.");
        header('Location: ' . ADMIN_URL . 'users.php');
        exit;
    }

    // 2. EDIT USER
    if ($action === 'edit') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = in_array($_POST['role'] ?? '', ['admin', 'editor', 'customer']) ? $_POST['role'] : 'customer';
        $phone = trim($_POST['phone'] ?? '');
        $company = trim($_POST['company_name'] ?? '');
        $status = in_array($_POST['status'] ?? '', ['active', 'blocked']) ? $_POST['status'] : 'active';

        // Check if updating self role/status
        if ($userId === (int)$_SESSION['user_id'] && $status === 'blocked') {
            set_flash('danger', 'You cannot block your own currently logged in account.');
            header('Location: ' . ADMIN_URL . 'users.php');
            exit;
        }

        $stmt = $db->prepare("UPDATE `users` SET `name` = ?, `email` = ?, `role` = ?, `phone` = ?, `company_name` = ?, `status` = ? WHERE `id` = ?");
        $stmt->execute([$name, $email, $role, $phone, $company, $status, $userId]);

        set_flash('success', "User '{$name}' profile updated.");
        header('Location: ' . ADMIN_URL . 'users.php');
        exit;
    }

    // 3. RESET PASSWORD
    if ($action === 'reset_password') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $newPassword = $_POST['new_password'] ?? '';

        if (strlen($newPassword) < 6) {
            set_flash('danger', 'Password must be at least 6 characters long.');
            header('Location: ' . ADMIN_URL . 'users.php');
            exit;
        }

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ?");
        $stmt->execute([$hashed, $userId]);

        set_flash('success', 'User password has been securely reset.');
        header('Location: ' . ADMIN_URL . 'users.php');
        exit;
    }

    // 4. TOGGLE STATUS
    if ($action === 'toggle_status') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId === (int)$_SESSION['user_id']) {
            set_flash('danger', 'You cannot change status of your own account.');
        } else {
            $stmt = $db->prepare("UPDATE `users` SET `status` = IF(`status`='active', 'blocked', 'active') WHERE `id` = ?");
            $stmt->execute([$userId]);
            set_flash('success', 'User status updated.');
        }
        header('Location: ' . ADMIN_URL . 'users.php');
        exit;
    }

    // 5. DELETE USER
    if ($action === 'delete') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId === (int)$_SESSION['user_id']) {
            set_flash('danger', 'You cannot delete your own account.');
        } else {
            $stmt = $db->prepare("DELETE FROM `users` WHERE `id` = ?");
            $stmt->execute([$userId]);
            set_flash('success', 'User deleted.');
        }
        header('Location: ' . ADMIN_URL . 'users.php');
        exit;
    }

} catch (Exception $e) {
    set_flash('danger', 'User operation error: ' . $e->getMessage());
    header('Location: ' . ADMIN_URL . 'users.php');
    exit;
}
