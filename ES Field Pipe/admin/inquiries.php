<?php
/**
 * Inquiries & Quote Requests Management - Esfield Pipe
 */
$pageTitle = "Inquiries & Quotes";
require_once __DIR__ . '/includes/header.php';

$db = get_db();

$status = trim($_GET['status'] ?? '');
$query = "SELECT * FROM `support_inquiries` WHERE 1=1";
$params = [];

if (!empty($status)) {
    $query .= " AND `status` = ?";
    $params[] = $status;
}

$query .= " ORDER BY `created_at` DESC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$inquiries = $stmt->fetchAll();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-black mb-1 text-dark">Technical Inquiries & Bulk Quotation RFQs</h4>
        <p class="text-muted small mb-0">Review project inquiries submitted via the website contact form and pipe calculator.</p>
    </div>
</div>

<!-- Filter Tabs -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="inquiries.php" class="btn btn-sm <?= empty($status) ? 'btn-primary' : 'btn-light border' ?>">All Inquiries</a>
            <a href="inquiries.php?status=new" class="btn btn-sm <?= $status === 'new' ? 'btn-primary' : 'btn-light border' ?>">New / Unanswered</a>
            <a href="inquiries.php?status=in_progress" class="btn btn-sm <?= $status === 'in_progress' ? 'btn-primary' : 'btn-light border' ?>">In Progress</a>
            <a href="inquiries.php?status=resolved" class="btn btn-sm <?= $status === 'resolved' ? 'btn-primary' : 'btn-light border' ?>">Resolved / Quoted</a>
        </div>
    </div>
</div>

<!-- Inquiries Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Client Details</th>
                        <th>Subject & Requirements</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inquiries)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No inquiries found.</td></tr>
                    <?php else: ?>
                        <?php foreach($inquiries as $inq): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?= htmlspecialchars($inq['name']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($inq['company'] ?? 'Individual') ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($inq['email']) ?> &bull; <?= htmlspecialchars($inq['phone'] ?? '') ?></div>
                            </td>
                            <td style="max-width: 320px;">
                                <div class="fw-bold text-dark text-truncate"><?= htmlspecialchars($inq['subject']) ?></div>
                                <?php if (!empty($inq['pipe_requirement'])): ?>
                                    <div class="badge bg-light text-primary border mb-1"><?= htmlspecialchars($inq['pipe_requirement']) ?></div>
                                <?php endif; ?>
                                <div class="small text-muted text-truncate"><?= htmlspecialchars($inq['message']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-secondary text-uppercase" style="font-size: 0.7rem;">
                                    <?= $inq['inquiry_type'] ?>
                                </span>
                            </td>
                            <td class="small text-muted">
                                <?= date('d M Y, H:i', strtotime($inq['created_at'])) ?>
                            </td>
                            <td>
                                <span class="badge <?= $inq['status'] === 'new' ? 'bg-danger' : ($inq['status'] === 'in_progress' ? 'bg-warning text-dark' : 'bg-success text-white') ?>">
                                    <?= ucfirst(str_replace('_', ' ', $inq['status'])) ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openReplyModal(<?= htmlspecialchars(json_encode($inq)) ?>)">
                                    <i class="fa-solid fa-reply me-1"></i> Review & Reply
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Review & Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= BASE_URL ?>api/admin-inquiries.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="reply">
                <input type="hidden" name="inquiry_id" id="modalInqId">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-envelope-open-text text-primary me-2"></i> Inquiry Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <div class="row g-2 small">
                            <div class="col-sm-6"><strong>From:</strong> <span id="modalSender"></span></div>
                            <div class="col-sm-6"><strong>Company:</strong> <span id="modalCompany"></span></div>
                            <div class="col-sm-6"><strong>Email:</strong> <span id="modalEmail"></span></div>
                            <div class="col-sm-6"><strong>Phone:</strong> <span id="modalPhone"></span></div>
                            <div class="col-12 mt-2"><strong>Subject:</strong> <span id="modalSubject" class="fw-bold text-dark"></span></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Customer Message</label>
                        <div class="p-3 border rounded bg-white text-dark small" id="modalMessage" style="white-space: pre-wrap;"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Update Inquiry Status</label>
                            <select name="status" id="modalStatus" class="form-select">
                                <option value="new">New / Unanswered</option>
                                <option value="in_progress">In Progress (Reviewing)</option>
                                <option value="resolved">Resolved / Formal Quote Dispatched</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Internal Staff Reply / Quotation Notes</label>
                            <textarea name="admin_reply" id="modalReply" class="form-control" rows="3" placeholder="Enter quotation reference number or notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openReplyModal(inq) {
    document.getElementById('modalInqId').value = inq.id;
    document.getElementById('modalSender').textContent = inq.name;
    document.getElementById('modalCompany').textContent = inq.company || '—';
    document.getElementById('modalEmail').textContent = inq.email;
    document.getElementById('modalPhone').textContent = inq.phone || '—';
    document.getElementById('modalSubject').textContent = inq.subject;
    document.getElementById('modalMessage').textContent = inq.message;
    document.getElementById('modalStatus').value = inq.status;
    document.getElementById('modalReply').value = inq.admin_reply || '';
    new bootstrap.Modal(document.getElementById('replyModal')).show();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
