@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'] ?? null;
$profile = $data['profile'] ?? null;
$leaves = $data['leaves'] ?? [];
$allLeaveTypes = $data['allLeaveTypes'] ?? [];
$activeTab = 'leave';
?>

<main class="portal-dashboard">
    <?php include __DIR__ . '/portal_nav.php'; ?>

    <!-- Section Content: My Leave Applications -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-calendar-check text-success me-2"></i> My Leave Applications</h6>
                <small class="text-muted">Track status of your vacation and medical leave requests</small>
            </div>
            <button class="btn btn-success btn-sm px-3 py-2 fw-semibold rounded-3 shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#selfLeaveModal">
                <i class="fa fa-plus me-1"></i> Apply for Leave
            </button>
        </div>
        <div class="card-body p-0">
            <?php if (empty($leaves)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fa fa-calendar-times fa-3x mb-3 text-light-purple" style="color: #D6C2EB;"></i>
                    <h6 class="fw-bold text-dark">No Leave Applications Found</h6>
                    <p class="small text-muted mb-3">Planning time off? Submit your leave request for manager approval.</p>
                    <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#selfLeaveModal">
                        <i class="fa fa-calendar-plus me-1"></i> Submit Leave Request
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">Leave Type</th>
                                <th>Period</th>
                                <th>Duration</th>
                                <th>Status & Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaves as $leaveItem): 
                                $l = $leaveItem['leave'];
                                $lt = $leaveItem['type'];
                                $appr = $leaveItem['approval'];
                                $st = $leaveItem['status'];
                                $lStatusCode = $st ? $st->code : 'PENDING';
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <strong class="text-dark d-block"><?= htmlspecialchars($lt ? $lt->name : 'Leave'); ?></strong>
                                        <small class="text-muted">Reason: <?= htmlspecialchars($l->reason); ?></small>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-semibold"><?= date('d M Y', strtotime($l->start_date)); ?></span>
                                        <i class="fa fa-arrow-right text-muted mx-1" style="font-size: 0.75rem;"></i>
                                        <span class="text-dark fw-semibold"><?= date('d M Y', strtotime($l->end_date)); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $l->days_requested; ?> Days</span>
                                    </td>
                                    <td>
                                        <?php if ($lStatusCode === 'APPROVED'): ?>
                                            <span class="badge bg-success text-white"><i class="fa fa-check me-1"></i> Approved</span>
                                            <?php if ($appr && !empty($appr->decision_notes)): ?>
                                                <small class="d-block text-muted" style="font-size: 0.75rem;">"<?= htmlspecialchars($appr->decision_notes); ?>"</small>
                                            <?php endif; ?>
                                        <?php elseif ($lStatusCode === 'REJECTED'): ?>
                                            <span class="badge bg-danger text-white"><i class="fa fa-times me-1"></i> Declined</span>
                                            <?php if ($appr && !empty($appr->decision_notes)): ?>
                                                <small class="d-block text-danger" style="font-size: 0.75rem;">"<?= htmlspecialchars($appr->decision_notes); ?>"</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark"><i class="fa fa-clock me-1"></i> Awaiting Manager Review</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

            </div><!-- /.col-lg-8 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section><!-- /.portal-dashboard-body -->

<!-- SELF MODAL: APPLY LEAVE -->
<div class="modal fade" id="selfLeaveModal" tabindex="-1" aria-labelledby="selfLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <form action="<?= $siteConfig->siteUrl; ?>/staff/leave/apply" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="staffprofile_id" value="<?= (int)($profile ? $profile->iD : 0); ?>">
                <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/staff/portal/leave">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="selfLeaveModalLabel"><i class="fa fa-calendar-plus text-success me-2"></i> Apply for Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Leave Category *</label>
                        <select name="leavetype_id" class="form-select" required>
                            <option value="">-- Select Leave Category --</option>
                            <?php foreach ($allLeaveTypes as $lt): ?>
                                <option value="<?= $lt->iD; ?>"><?= htmlspecialchars($lt->name); ?> (<?= $lt->annual_days; ?> days/yr)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Commencement Date *</label>
                            <input type="date" name="start_date" id="self_leave_start" class="form-control" required onchange="calcSelfLeaveDays()">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Resumption Date *</label>
                            <input type="date" name="end_date" id="self_leave_end" class="form-control" required onchange="calcSelfLeaveDays()">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Working Days Requested *</label>
                        <input type="number" step="0.5" min="0.5" name="days_requested" id="self_leave_days" class="form-control fw-bold" placeholder="e.g., 5.0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Reason / Handover Coverage *</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="Brief handover note or reason for leave..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Supporting Certificate (Optional)</label>
                        <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Doctor's certificate or exam timetable (PDF/JPG)</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success text-white px-4 fw-semibold">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function calcSelfLeaveDays() {
        var start = document.getElementById('self_leave_start').value;
        var end = document.getElementById('self_leave_end').value;
        if (start && end) {
            var d1 = new Date(start);
            var d2 = new Date(end);
            if (d2 >= d1) {
                var diffDays = Math.round((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('self_leave_days').value = diffDays;
            }
        }
    }
</script>
</main>
