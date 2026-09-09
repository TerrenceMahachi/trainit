@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'] ?? null;
$profile = $data['profile'] ?? null;
$timeEntries = $data['timeEntries'] ?? [];
$allActivityCats = $data['allActivityCats'] ?? [];
$activeTab = 'timesheets';
?>

<main class="portal-dashboard">
    <?php include __DIR__ . '/portal_nav.php'; ?>

    <!-- Section Content: Logged Time Entries -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-clock text-info me-2"></i> My Operational Time Logs</h6>
                <small class="text-muted">Logged hours for vetting reviews, request triage, and IT engineering</small>
            </div>
            <button class="btn btn-info btn-sm px-3 py-2 fw-semibold rounded-3 shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#selfTimeModal">
                <i class="fa fa-plus me-1"></i> Log Hours
            </button>
        </div>
        <div class="card-body p-0">
            <?php if (empty($timeEntries)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fa fa-user-clock fa-3x mb-3 text-light-purple" style="color: #D6C2EB;"></i>
                    <h6 class="fw-bold text-dark">No Operational Hours Logged</h6>
                    <p class="small text-muted mb-3">Keep track of your daily tasks by logging hours against your operational activities.</p>
                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#selfTimeModal">
                        <i class="fa fa-clock me-1"></i> Log First Entry
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">Work Date</th>
                                <th>Category</th>
                                <th>Hours</th>
                                <th>Summary of Tasks</th>
                                <th class="text-end pe-4">Sign-Off</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($timeEntries as $timeItem): 
                                $t = $timeItem['entry'];
                                $cat = $timeItem['category'];
                                $appr = $timeItem['approval'];
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <strong class="text-dark"><?= date('d M Y', strtotime($t->work_date)); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($cat ? $cat->name : 'Activity'); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary text-white fs-6"><?= number_format($t->hours, 1); ?> hrs</span>
                                    </td>
                                    <td>
                                        <span class="text-dark d-block" style="max-width: 280px;"><?= htmlspecialchars($t->task_summary); ?></span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if ($appr && $appr->is_approved): ?>
                                            <span class="badge bg-success text-white"><i class="fa fa-check-double me-1"></i> Signed Off</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark"><i class="fa fa-hourglass-half me-1"></i> Pending Sign-Off</span>
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

<!-- SELF MODAL: LOG TIME -->
<div class="modal fade" id="selfTimeModal" tabindex="-1" aria-labelledby="selfTimeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <form action="<?= $siteConfig->siteUrl; ?>/staff/time/log" method="POST">
                <input type="hidden" name="staffprofile_id" value="<?= (int)($profile ? $profile->iD : 0); ?>">
                <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/staff/portal/timesheets">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="selfTimeModalLabel"><i class="fa fa-clock text-info me-2"></i> Log Operational Time</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Activity Category *</label>
                        <select name="activitycategory_id" class="form-select" required>
                            <option value="">-- Select Operational Activity --</option>
                            <?php foreach ($allActivityCats as $ac): ?>
                                <option value="<?= $ac->iD; ?>"><?= htmlspecialchars($ac->name); ?> (<?= $ac->is_billable ? 'Billable' : 'Internal Ops'; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Work Date *</label>
                            <input type="date" name="work_date" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Hours *</label>
                            <input type="number" step="0.25" min="0.25" max="24" name="hours" class="form-control fw-bold" placeholder="e.g., 4.5" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Task Summary / Deliverables *</label>
                        <textarea name="task_summary" class="form-control" rows="3" placeholder="Summary of deliverables accomplished during this time..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white px-4 fw-semibold">Save Time Log</button>
                </div>
            </form>
        </div>
    </div>
</div>
</main>
