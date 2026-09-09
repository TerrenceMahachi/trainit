@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'] ?? null;
$pendingTime = $data['pendingTime'] ?? [];
$activeTab = 'timesheets';
?>

<main class="portal-dashboard">
    <?php include __DIR__ . '/approvals_nav.php'; ?>

    <!-- Section Content: Timesheet Sign-Offs Queue -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-tasks text-info me-2"></i> Operational Hours Awaiting Supervisor Sign-Off</h6>
            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill fw-semibold">
                <?= count($pendingTime); ?> Entries Awaiting Sign-Off
            </span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($pendingTime)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fa fa-user-clock fa-3x mb-3 text-success" style="opacity: 0.6;"></i>
                    <h6 class="fw-bold text-dark">All Timesheets Signed Off</h6>
                    <p class="small text-muted mb-0">No operational hours currently awaiting manager review.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">Employee</th>
                                <th>Work Date</th>
                                <th>Category</th>
                                <th>Hours</th>
                                <th>Deliverables Summary</th>
                                <th class="text-end pe-4">Sign-Off</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingTime as $item): 
                                $t = $item['entry'];
                                $staff = $item['staff'];
                                $cat = $item['category'];
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <strong class="text-dark d-block"><?= htmlspecialchars($staff ? $staff->name : 'Staff'); ?></strong>
                                        <small class="text-muted"><?= htmlspecialchars($staff ? $staff->department : ''); ?></small>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-semibold"><?= date('d M Y', strtotime($t->work_date)); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($cat ? $cat->name : 'Activity'); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary text-white fs-6"><?= number_format($t->hours, 1); ?> hrs</span>
                                    </td>
                                    <td>
                                        <span class="text-dark d-block" style="max-width: 250px;"><?= htmlspecialchars($t->task_summary); ?></span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-sm btn-info text-white px-3" data-bs-toggle="modal" data-bs-target="#queueTimeModal"
                                                onclick="prepareQueueTime(<?= $t->iD; ?>, '<?= htmlspecialchars(addslashes($staff ? $staff->name : 'Staff')); ?>', '<?= number_format($t->hours, 1); ?>', '<?= date('d M Y', strtotime($t->work_date)); ?>')">
                                            <i class="fa fa-check-double me-1"></i> Sign-Off
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

<!-- QUEUE MODAL: SIGNOFF TIME -->
<div class="modal fade" id="queueTimeModal" tabindex="-1" aria-labelledby="queueTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <form action="<?= $siteConfig->siteUrl; ?>/admin/staff/time/signoff" method="POST">
                <input type="hidden" name="stafftimeentry_id" id="q_time_id" value="">
                <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals/timesheets">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="queueTimeModalLabel"><i class="fa fa-check-double text-info me-2"></i> Timesheet Sign-Off</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Time Entry</label>
                        <input type="text" id="q_time_desc" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Decision *</label>
                        <select name="is_approved" class="form-select" required>
                            <option value="1">Approve & Sign-Off</option>
                            <option value="0">Reject Hours</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Supervisor Verification Notes</label>
                        <textarea name="review_notes" class="form-control" rows="3" placeholder="Deliverables verified compliant..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white px-4 fw-semibold">Save Sign-Off</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function prepareQueueTime(timeId, staffName, hours, workDate) {
        document.getElementById('q_time_id').value = timeId;
        document.getElementById('q_time_desc').value = staffName + ' — ' + hours + ' hrs on ' + workDate;
    }
</script>
</main>
