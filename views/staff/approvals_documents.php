@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'] ?? null;
$pendingDocs = $data['pendingDocs'] ?? [];
$allVerStatuses = $data['allVerStatuses'] ?? [];
$activeTab = 'documents';
?>

<main class="portal-dashboard">
    <?php include __DIR__ . '/approvals_nav.php'; ?>

    <!-- Section Content: Document Verification Queue -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-file-signature text-primary me-2"></i> Documents Awaiting Compliance Verification</h6>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill fw-semibold">
                <?= count($pendingDocs); ?> Pending Audit
            </span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($pendingDocs)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fa fa-check-double fa-3x mb-3 text-success" style="opacity: 0.6;"></i>
                    <h6 class="fw-bold text-dark">Queue Clear</h6>
                    <p class="small text-muted mb-0">All submitted staff documents have been audited and verified.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">Employee</th>
                                <th>Document Title</th>
                                <th>Category</th>
                                <th>Uploaded</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingDocs as $item): 
                                $d = $item['doc'];
                                $staff = $item['staff'];
                                $type = $item['type'];
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <strong class="text-dark d-block"><?= htmlspecialchars($staff ? $staff->name : 'Staff'); ?></strong>
                                        <small class="text-muted"><?= htmlspecialchars($staff ? $staff->employee_number : ''); ?></small>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-semibold"><?= htmlspecialchars($d->title); ?></span>
                                        <small class="text-muted d-block"><?= round($d->file_size / 1024, 1); ?> KB</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($type ? $type->name : 'Document'); ?></span>
                                    </td>
                                    <td>
                                        <span class="text-muted"><?= date('d M Y', strtotime($d->reg_date)); ?></span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= $siteConfig->siteUrl . '/' . ltrim($d->file_path, '/'); ?>" target="_blank" class="btn btn-outline-secondary">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#queueVerifyModal"
                                                    onclick="prepareQueueVerify(<?= $d->iD; ?>, '<?= htmlspecialchars(addslashes($d->title)); ?>', '<?= htmlspecialchars(addslashes($staff ? $staff->name : 'Staff')); ?>')">
                                                <i class="fa fa-stamp"></i> Audit
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    </div><!-- /.container -->
</section><!-- /.portal-dashboard-body -->

<!-- QUEUE MODAL: VERIFY DOCUMENT -->
<div class="modal fade" id="queueVerifyModal" tabindex="-1" aria-labelledby="queueVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <form action="<?= $siteConfig->siteUrl; ?>/admin/staff/documents/verify" method="POST">
                <input type="hidden" name="staffdocument_id" id="q_verify_doc_id" value="">
                <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals/documents">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="queueVerifyModalLabel"><i class="fa fa-stamp text-primary me-2"></i> Audit Compliance Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Staff Member</label>
                        <input type="text" id="q_verify_staff_name" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Document Title</label>
                        <input type="text" id="q_verify_doc_title" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Compliance Status *</label>
                        <select name="verificationstatus_id" class="form-select" required>
                            <?php foreach ($allVerStatuses as $vs): ?>
                                <option value="<?= $vs->iD; ?>" <?= ($vs->code === 'VERIFIED') ? 'selected' : ''; ?>><?= htmlspecialchars($vs->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Audit Verification Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Confirm inspection, validity checks, or rejection reasons..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Verification Audit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function prepareQueueVerify(docId, docTitle, staffName) {
        document.getElementById('q_verify_doc_id').value = docId;
        document.getElementById('q_verify_doc_title').value = docTitle;
        document.getElementById('q_verify_staff_name').value = staffName;
    }
</script>
</main>
