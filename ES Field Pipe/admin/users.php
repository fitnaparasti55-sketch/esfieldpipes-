<?php
/**
 * User Account Management - Esfield Pipe
 */
$pageTitle = "User & Account Management";
require_once __DIR__ . '/includes/header.php';

require_admin(); // Strict Admin Only

$db = get_db();

$search = trim($_GET['search'] ?? '');
$role = trim($_GET['role'] ?? '');
$status = trim($_GET['status'] ?? '');

$query = "SELECT * FROM `users` WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (`name` LIKE ? OR `email` LIKE ? OR `phone` LIKE ? OR `company_name` LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($role)) {
    $query .= " AND `role` = ?";
    $params[] = $role;
}

if (!empty($status)) {
    $query .= " AND `status` = ?";
    $params[] = $status;
}

$query .= " ORDER BY `created_at` DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-black mb-1 text-dark">User Accounts & Roles</h4>
        <p class="text-muted small mb-0">Manage registered engineering contractors, client accounts, editors, and administrative staff.</p>
    </div>
    <button type="button" class="btn btn-primary px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fa-solid fa-user-plus me-1"></i> Add User / Admin
    </button>
</div>

<!-- Search and Filter Bar -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="users.php" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, company or phone..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-2">
                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="editor" <?= $role === 'editor' ? 'selected' : '' ?>>Editor</option>
                    <option value="customer" <?= $role === 'customer' ? 'selected' : '' ?>>Customer</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active Only</option>
                    <option value="blocked" <?= $status === 'blocked' ? 'selected' : '' ?>>Blocked Only</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-dark w-100 fw-bold">Filter</button>
                <?php if (!empty($search) || !empty($role) || !empty($status)): ?>
                    <a href="users.php" class="btn btn-sm btn-outline-secondary" title="Reset Filters"><i class="fa-solid fa-rotate-left"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Users Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">User Details</th>
                        <th>Role</th>
                        <th>Company / Phone</th>
                        <th>Registered Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No users found.</td></tr>
                    <?php else: ?>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                        <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($u['name']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($u['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?= $u['role'] === 'admin' ? 'bg-danger' : ($u['role'] === 'editor' ? 'bg-warning text-dark' : 'bg-light text-dark border') ?> text-uppercase px-2.5 py-1.5" style="font-size: 0.72rem;">
                                    <i class="fa-solid <?= $u['role'] === 'admin' ? 'fa-shield-halved' : ($u['role'] === 'editor' ? 'fa-pen-to-square' : 'fa-user') ?> me-1"></i>
                                    <?= $u['role'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark small"><?= htmlspecialchars($u['company_name'] ?? 'Individual') ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($u['phone'] ?? '—') ?></div>
                            </td>
                            <td class="small text-muted">
                                <?= date('d M Y, H:i', strtotime($u['created_at'])) ?>
                            </td>
                            <td>
                                <form action="<?= BASE_URL ?>api/admin-users.php" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="badge rounded-pill border-0 <?= $u['status'] === 'active' ? 'bg-success text-white' : 'bg-danger text-white' ?>" style="cursor: pointer;">
                                        <?= ucfirst($u['status']) ?>
                                    </button>
                                </form>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEditUserModal(<?= htmlspecialchars(json_encode($u)) ?>)" title="Edit Profile">
                                        <i class="fa-solid fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning text-dark" onclick="openResetPasswordModal(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['name'])) ?>')" title="Reset Password">
                                        <i class="fa-solid fa-key"></i>
                                    </button>
                                    <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteUser(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['name'])) ?>')" title="Delete User">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= BASE_URL ?>api/admin-users.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-user-plus text-primary me-2"></i> Add New User / Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Full Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Rajesh Kumar" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Email Address *</label>
                        <input type="email" name="email" class="form-control" placeholder="user@company.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Password * (Min 6 chars)</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required minlength="6">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Role *</label>
                            <select name="role" class="form-select">
                                <option value="customer" selected>Customer / Contractor</option>
                                <option value="editor">Editor (Staff)</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" selected>Active</option>
                                <option value="blocked">Blocked</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Company Name</label>
                        <input type="text" name="company_name" class="form-control" placeholder="e.g. Apex Civil Infra">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="+91 98765 43210">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= BASE_URL ?>api/admin-users.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="user_id" id="editUserId">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-user-pen text-primary me-2"></i> Edit User Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Full Name *</label>
                        <input type="text" name="name" id="editUserName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Email Address *</label>
                        <input type="email" name="email" id="editUserEmail" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">Role</label>
                            <select name="role" id="editUserRole" class="form-select">
                                <option value="customer">Customer</option>
                                <option value="editor">Editor</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Status</label>
                            <select name="status" id="editUserStatus" class="form-select">
                                <option value="active">Active</option>
                                <option value="blocked">Blocked</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Company Name</label>
                        <input type="text" name="company_name" id="editUserCompany" class="form-control">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small">Phone Number</label>
                        <input type="text" name="phone" id="editUserPhone" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= BASE_URL ?>api/admin-users.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="user_id" id="resetPwdUserId">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-key text-warning me-2"></i> Reset Password for <span id="resetPwdUserName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Enter New Password (Min 6 chars)</label>
                        <input type="password" name="new_password" class="form-control" placeholder="••••••••" required minlength="6">
                    </div>
                    <p class="text-muted small mb-0">The password will be safely encrypted using bcrypt algorithm before storage.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark px-4">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?= BASE_URL ?>api/admin-users.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" id="deleteUserId">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i> Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="mb-0">Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold">Delete Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditUserModal(user) {
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editUserName').value = user.name;
    document.getElementById('editUserEmail').value = user.email;
    document.getElementById('editUserRole').value = user.role;
    document.getElementById('editUserStatus').value = user.status;
    document.getElementById('editUserCompany').value = user.company_name || '';
    document.getElementById('editUserPhone').value = user.phone || '';
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function openResetPasswordModal(id, name) {
    document.getElementById('resetPwdUserId').value = id;
    document.getElementById('resetPwdUserName').textContent = name;
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}

function confirmDeleteUser(id, name) {
    document.getElementById('deleteUserId').value = id;
    document.getElementById('deleteUserName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
