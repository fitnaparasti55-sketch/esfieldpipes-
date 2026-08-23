<?php
/**
 * Registration Page - Esfield Pipe
 */
$pageTitle = "Create New Account";
require_once __DIR__ . '/includes/header.php';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}
?>

<div class="auth-page py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6 col-xl-5">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <a href="<?= BASE_URL ?>">
                                <img src="<?= ASSETS_URL ?>images/logo.svg" alt="Esfield" style="height: 40px;" class="mb-3">
                            </a>
                            <h3 class="fw-black text-dark">Create Account</h3>
                            <p class="text-muted small">Join India's premier DWC pipe procurement platform</p>
                        </div>

                        <form action="<?= BASE_URL ?>api/register.php" method="POST">
                            <?= csrf_field() ?>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                                        <input type="text" name="name" class="form-control border-start-0 ps-0 bg-light" placeholder="John Doe" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 ps-0 bg-light" placeholder="engineer@project.com" required>
                                </div>
                                <div class="form-text small">We'll use this for order updates and invoices.</div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                        <input type="password" name="password" class="form-control border-start-0 ps-0 bg-light" placeholder="••••••••" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider">Confirm</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-shield-check"></i></span>
                                        <input type="password" name="confirm_password" class="form-control border-start-0 ps-0 bg-light" placeholder="••••••••" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label text-muted small" for="terms">
                                        I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>.
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-black rounded-3 shadow-sm mb-3">
                                Create Account <i class="fa-solid fa-user-plus ms-2"></i>
                            </button>

                            <div class="text-center mt-4 pt-2 border-top">
                                <p class="mb-0 text-muted small">Already have an account?</p>
                                <a href="<?= BASE_URL ?>login.php" class="fw-bold text-primary text-decoration-none">Sign In Instead</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
