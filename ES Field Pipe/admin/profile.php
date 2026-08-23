<?php
/**
 * Administrator Profile Settings - Esfield Pipe
 */
$pageTitle = "Administrator Profile";
require_once __DIR__ . '/includes/header.php';

$user = current_user();
?>

<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-black mb-1 text-dark">Staff Profile & Credentials</h4>
                <p class="text-muted small mb-0">Update your name, contact phone, and administrative account security password.</p>
            </div>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>

        <div class="row g-4">
            <!-- Account Info -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-id-badge me-2"></i> Profile Details</h5>
                    </div>
                    <div class="card-body p-4 border-top">
                        <form action="<?= BASE_URL ?>api/admin-profile.php" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="update_profile">

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Full Name *</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Email Address *</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Organization / Department</label>
                                <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($user['company_name'] ?? '') ?>">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted">Assigned System Role</label>
                                <input type="text" class="form-control bg-light" value="<?= strtoupper($user['role'] ?? 'ADMIN') ?>" readonly>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold rounded-3">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Profile Details
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Password Change -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-shield-keyhole me-2"></i> Change Password</h5>
                    </div>
                    <div class="card-body p-4 border-top">
                        <form action="<?= BASE_URL ?>api/admin-profile.php" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="change_password">

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Current Password *</label>
                                <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">New Password * (Min 6 chars)</label>
                                <input type="password" name="new_password" class="form-control" placeholder="••••••••" required minlength="6">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small">Confirm New Password *</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required minlength="6">
                            </div>

                            <div class="alert alert-light border small text-muted mb-4">
                                <i class="fa-solid fa-lock text-warning me-1"></i> Passwords are encrypted with standard <code>password_hash()</code> bcrypt security.
                            </div>

                            <button type="submit" class="btn btn-dark w-100 py-2.5 fw-bold rounded-3">
                                <i class="fa-solid fa-key me-2"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
