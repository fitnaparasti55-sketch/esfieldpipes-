<?php
/**
 * Forgot Password - Esfield Pipe
 */
$pageTitle = "Reset Your Password";
require_once __DIR__ . '/includes/header.php';

$sent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $email = trim($_POST['email'] ?? '');
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $db = get_db();
            $stmt = $db->prepare("SELECT id, name FROM `users` WHERE `email` = ? AND `status` = 'active'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate a secure reset token valid for 1 hour
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', time() + 3600);

                $upStmt = $db->prepare("UPDATE `users` SET `reset_token` = ?, `reset_expires` = ? WHERE `id` = ?");
                $upStmt->execute([$token, $expires, $user['id']]);

                // In local/demo environment, save reset link in session flash for testing convenience
                $resetUrl = BASE_URL . "reset-password.php?token=" . urlencode($token) . "&email=" . urlencode($email);
                $_SESSION['demo_reset_link'] = $resetUrl;
            }
        }
        // Always show the same generic response to prevent account enumeration
        $sent = true;
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
                            <h3 class="fw-black text-dark">Password Assistance</h3>
                            <p class="text-muted small">Enter your email to receive password reset instructions</p>
                        </div>

                        <?php if ($sent): ?>
                            <div class="alert alert-success d-flex align-items-start gap-2 mb-4">
                                <i class="fa-solid fa-circle-check fs-5 mt-1"></i>
                                <div>
                                    If an active account exists with that email, a password reset link has been dispatched. Please check your inbox.
                                    <?php if (!empty($_SESSION['demo_reset_link'])): ?>
                                        <div class="mt-2 pt-2 border-top border-success-subtle small">
                                            <strong>Direct Reset Link (Local Demo):</strong><br>
                                            <a href="<?= $_SESSION['demo_reset_link'] ?>" class="text-break fw-semibold">Click here to reset password</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="<?= BASE_URL ?>login.php" class="btn btn-outline-primary w-100 py-2.5 fw-bold">
                                <i class="fa-solid fa-arrow-left me-1"></i> Return to Sign In
                            </a>
                        <?php else: ?>
                            <form action="<?= BASE_URL ?>forgot-password.php" method="POST">
                                <?= csrf_field() ?>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0 ps-0 bg-light" placeholder="engineer@project.com" required autofocus>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 fw-black rounded-3 shadow-sm mb-3">
                                    Send Reset Instructions <i class="fa-solid fa-paper-plane ms-2"></i>
                                </button>

                                <div class="text-center mt-3 pt-2 border-top">
                                    <a href="<?= BASE_URL ?>login.php" class="text-secondary text-decoration-none small">
                                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Sign In
                                    </a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
