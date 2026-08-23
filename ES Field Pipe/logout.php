<?php
/**
 * Logout Action Handler
 * Esfield Pipe Platform
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

logout_user();

set_flash('info', 'You have been successfully signed out of your account.');
header('Location: ' . BASE_URL . 'login.php');
exit;
