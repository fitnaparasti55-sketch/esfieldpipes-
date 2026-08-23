<?php
/**
 * Sign In Page - Esfield Pipe
 */
$pageTitle = "Sign In to Your Account";
require_once __DIR__ . '/includes/header.php';

// Redirect if already logged in
if (is_logged_in()) {
    if (is_editor_or_admin()) {
        header('Location: ' . ADMIN_URL . 'dashboard.php');
    } else {
        header('Location: ' . BASE_URL . 'index.php');
    }
    exit;
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
                            <h3 class="fw-black text-dark">Welcome Back</h3>
                            <p class="text-muted small">Access your infrastructure project dashboard</p>
                        </div>

                        <form action="<?= BASE_URL ?>api/login.php" method="POST">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 ps-0 bg-light" placeholder="engineer@project.com" required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Password</label>
                                    <a href="<?= BASE_URL ?>forgot-password.php" class="small text-decoration-none fw-semibold">Forgot?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control border-start-0 ps-0 bg-light" placeholder="••••••••" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe">
                                    <label class="form-check-label text-muted small" for="rememberMe">
                                        Keep me signed in on this device
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-black rounded-3 shadow-sm mb-3">
                                Sign In <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                            </button>

                            <div class="text-center mt-4 pt-2 border-top">
                                <p class="mb-0 text-muted small">Don't have an account yet?</p>
                                <a href="<?= BASE_URL ?>register.php" class="fw-bold text-primary text-decoration-none">Create Project Account</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?= BASE_URL ?>" class="text-secondary text-decoration-none small">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
