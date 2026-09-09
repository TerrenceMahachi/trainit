@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'] ?? null;
$pendingLeaves = $data['pendingLeaves'] ?? [];
$allLeaveStatuses = $data['allLeaveStatuses'] ?? [];
$activeTab = 'leave';
?>

<main class="portal-dashboard">
    <?php include __DIR__ . '/approvals_nav.php'; ?>

    <!-- Section Content: Leave Applications Queue -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-calendar-check text-success me-2"></i> Leave Requests Awaiting Authorization</h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-semibold">
                <?= count($pendingLeaves); ?> Pending Review
            </span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($pendingLeaves)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fa fa-umbrella-beach fa-3x mb-3 text-success" style="opacity: 0.6;"></i>
                    <h6 class="fw-bold text-dark">No Pending Leave Requests</h6>
                    <p class="small text-muted mb-0">All submitted employee leave applications have been reviewed.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">Employee</th>
                                <th>Leave Type</th>
                                <th>Period</th>
                                <th>Days</th>
                                <th>Reason & Handover</th>
                                <th class="text-end pe-4">Decision</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingLeaves as $item): 
                                $l = $item['leave'];
                                $staff = $item['staff'];
                                $type = $item['type'];
                                $att = $item['attachment'];
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <strong class="text-dark d-block"><?= htmlspecialchars($staff ? $staff->name : 'Staff'); ?></strong>
                                        <small class="text-muted"><?= htmlspecialchars($staff ? $staff->department : ''); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($type ? $type->name : 'Leave'); ?></span>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-semibold"><?= date('d M Y', strtotime($l->start_date)); ?></span>
                                        <i class="fa fa-arrow-right text-muted mx-1" style="font-size: 0.72rem;"></i>
                                        <span class="text-dark fw-semibold"><?= date('d M Y', strtotime($l->end_date)); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $l->days_requested; ?> Days</span>
                                    </td>
                                    <td>
                                        <span class="text-dark d-block text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($l->reason); ?>"><?= htmlspecialchars($l->reason); ?></span>
                                        <?php if ($att): ?>
                                            <a href="<?= $siteConfig->siteUrl . '/' . ltrim($att->file_path, '/'); ?>" target="_blank" class="small text-primary"><i class="fa fa-paperclip"></i> Certificate</a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-success text-white px-3" data-bs-toggle="modal" data-bs-target="#queueLeaveModal"
                                                onclick="prepareQueueLeave(<?= $l->iD; ?>, '<?= htmlspecialchars(addslashes($staff ? $staff->name : 'Staff')); ?>', '<?= htmlspecialchars(addslashes($type ? $type->name : 'Leave')); ?>', '<?= $l->days_requested; ?>')">
                                            <i class="fa fa-check-circle me-1"></i> Decide
                                        </button>
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

<!-- QUEUE MODAL: DECIDE LEAVE -->
<div class="modal fade" id="queueLeaveModal" tabindex="-1" aria-labelledby="queueLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <form action="<?= $siteConfig->siteUrl; ?>/admin/staff/leave/decide" method="POST">
                <input type="hidden" name="staffleave_id" id="q_leave_id" value="">
                <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals/leave">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="queueLeaveModalLabel"><i class="fa fa-calendar-check text-success me-2"></i> Review Leave Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Application Under Review</label>
                        <input type="text" id="q_leave_desc" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Manager Decision *</label>
                        <select name="leavestatus_id" class="form-select" required>
                            <?php foreach ($allLeaveStatuses as $ls): ?>
                                <option value="<?= $ls->iD; ?>" <?= ($ls->code === 'APPROVED') ? 'selected' : ''; ?>><?= htmlspecialchars($ls->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Comments & Handover Directives</label>
                        <textarea name="decision_notes" class="form-control" rows="3" placeholder="Approval feedback, handover coverage, or reason for decline..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success text-white px-4 fw-semibold">Save Decision</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function prepareQueueLeave(leaveId, staffName, leaveType, days) {
        document.getElementById('q_leave_id').value = leaveId;
        document.getElementById('q_leave_desc').value = staffName + ' — ' + leaveType + ' (' + days + ' days)';
    }
</script>
</main>
