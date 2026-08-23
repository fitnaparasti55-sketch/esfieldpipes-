<?php
/**
 * Authentication & Authorization Helpers
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

/**
 * Check if a user is logged in
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Get current logged in user record
 */
function current_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }
    static $user = null;
    if ($user === null) {
        try {
            $db = get_db();
            $stmt = $db->prepare("SELECT * FROM `users` WHERE `id` = ? AND `status` = 'active'");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch() ?: null;
            if (!$user) {
                // User was blocked or deleted
                logout_user();
            }
        } catch (Exception $e) {
            return null;
        }
    }
    return $user;
}

/**
 * Role checking helpers
 */
function is_admin(): bool {
    $user = current_user();
    return $user && ($user['role'] === 'admin');
}

function is_editor_or_admin(): bool {
    $user = current_user();
    return $user && in_array($user['role'], ['admin', 'editor']);
}

/**
 * Guard: Require logged in user
 */
function require_login(string $redirect = ''): void {
    if (!is_logged_in()) {
        $target = $redirect ?: $_SERVER['REQUEST_URI'] ?? 'index.php';
        $_SESSION['login_redirect'] = $target;
        set_flash('warning', 'Please sign in to your Esfield account to proceed.');
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

/**
 * Guard: Require Admin role
 */
function require_admin(): void {
    if (!is_logged_in()) {
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'] ?? (ADMIN_URL . 'dashboard.php');
        set_flash('warning', 'Administrator authentication required. Please sign in.');
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
    if (!is_admin()) {
        set_flash('danger', 'Access denied. Administrative privileges required.');
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
}

/**
 * Guard: Require Editor or Admin role
 */
function require_editor_or_admin(): void {
    if (!is_logged_in()) {
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'] ?? (ADMIN_URL . 'dashboard.php');
        set_flash('warning', 'Administrative access required. Please sign in with staff credentials.');
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
    if (!is_editor_or_admin()) {
        set_flash('danger', 'Access denied. Staff privileges required.');
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
}

/**
 * Perform login session setup
 */
function login_user(array $user): void {
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];

    // Transfer guest cart items to this user
    if (!empty($_SESSION['guest_session_id'])) {
        transfer_guest_cart_to_user((int)$user['id'], $_SESSION['guest_session_id']);
    }
}

/**
 * Perform logout
 */
function logout_user(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['guest_session_id'] = bin2hex(random_bytes(16));
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
