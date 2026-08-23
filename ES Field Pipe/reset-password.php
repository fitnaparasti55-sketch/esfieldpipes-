<?php
/**
 * Reset Password - Esfield Pipe
 */
$pageTitle = "Set New Password";
require_once __DIR__ . '/includes/header.php';

$token = trim($_GET['token'] ?? '');
$email = trim($_GET['email'] ?? '');
$error = null;
$success = false;

$db = get_db();

if (empty($token) || empty($email)) {
    $error = 'Invalid or missing password reset link.';
} else {
    $stmt = $db->prepare("SELECT id FROM `users` WHERE `email` = ? AND `reset_token` = ? AND `reset_expires` > NOW() AND `status` = 'active'");
    $stmt->execute([$email, $token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = 'This password reset link is invalid or has expired. Please request a new link.';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $error = 'Security verification failed. Please try again.';
        } else {
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters long.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Passwords do not match.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $upStmt = $db->prepare("UPDATE `users` SET `password` = ?, `reset_token` = NULL, `reset_expires` = NULL WHERE `id` = ?");
                $upStmt->execute([$hashedPassword, $user['id']]);

                set_flash('success', 'Your password has been successfully reset! You can now log in with your new password.');
                header('Location: ' . BASE_URL . 'login.php');
                exit;
            }
        }
    }
}
$siteLogo = get_setting('site_logo', 'assets/images/logo.svg');
?>

<div class="auth-page py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <a href="<?= BASE_URL ?>">
                                <img src="<?= BASE_URL . $siteLogo ?>" alt="Esfield" style="height: 45px;" class="mb-3">
                            </a>
                            <h3 class="fw-black text-dark">Set New Password</h3>
                            <p class="text-muted small">Choose a strong password for your account</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger mb-4">
                                <i class="fa-solid fa-circle-exclamation me-2"></i> <?= htmlspecialchars($error) ?>
                            </div>
                            <a href="<?= BASE_URL ?>forgot-password.php" class="btn btn-outline-primary w-100 py-2.5 fw-bold">
                                Request New Reset Link
                            </a>
                        <?php else: ?>
                            <form action="<?= BASE_URL ?>reset-password.php?token=<?= urlencode($token) ?>&email=<?= urlencode($email) ?>" method="POST">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                        <input type="password" name="password" class="form-control border-start-0 ps-0 bg-light" placeholder="••••••••" required minlength="6" autofocus>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-shield-halved"></i></span>
                                        <input type="password" name="confirm_password" class="form-control border-start-0 ps-0 bg-light" placeholder="••••••••" required minlength="6">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 fw-black rounded-3 shadow-sm mb-3">
                                    Update Password <i class="fa-solid fa-key ms-2"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
