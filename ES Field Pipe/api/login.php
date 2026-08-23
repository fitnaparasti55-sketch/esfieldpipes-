<?php
/**
 * Login Action Handler
 * Esfield Pipe Platform - Secure Authentication
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

// CSRF Verification
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('danger', 'Security verification failed. Please refresh the page and try again.');
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    set_flash('danger', 'Please provide both your registered email address and password.');
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

try {
    $db = get_db();
    $stmt = $db->prepare("SELECT * FROM `users` WHERE `email` = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Check if account is suspended / blocked
        if ($user['status'] === 'blocked') {
            set_flash('danger', 'Your account has been suspended. Please contact customer support.');
            header('Location: ' . BASE_URL . 'login.php');
            exit;
        }

        $passwordValid = false;

        // 1. Primary check: standard password_verify()
        if (password_verify($password, $user['password'])) {
            $passwordValid = true;

            // Rehash if algorithm/cost has updated
            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $upStmt = $db->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ?");
                $upStmt->execute([$newHash, $user['id']]);
            }
        }
        // 2. Legacy fallback: auto-upgrade plain-text password if found
        elseif ($user['password'] === $password) {
            $passwordValid = true;
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $upStmt = $db->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ?");
            $upStmt->execute([$newHash, $user['id']]);
        }

        if ($passwordValid) {
            // Initialize User Session
            login_user($user);

            // Determine redirection target
            if (!empty($_SESSION['login_redirect'])) {
                $target = $_SESSION['login_redirect'];
                unset($_SESSION['login_redirect']);
            } elseif (in_array($user['role'], ['admin', 'editor'])) {
                $target = ADMIN_URL . 'dashboard.php';
            } else {
                $target = BASE_URL . 'profile.php';
            }

            $firstName = htmlspecialchars(explode(' ', $user['name'])[0]);
            set_flash('success', "Access Granted. Welcome back, {$firstName}!");
            header('Location: ' . $target);
            exit;
        }
    }

    // Generic error message to prevent account enumeration
    set_flash('danger', 'Invalid credentials. The email address or password you entered is incorrect.');
    header('Location: ' . BASE_URL . 'login.php');
    exit;

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    set_flash('danger', 'A system error occurred during authentication. Please try again later.');
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
