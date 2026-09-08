@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'];
$profile = $data['profile'];
$role = $data['role'];
$roleName = $role ? $role->name : 'Staff Member';

$documents = $data['documents'] ?? [];
$leaves = $data['leaves'] ?? [];
$timeEntries = $data['timeEntries'] ?? [];

// Calculate leave days taken this year
$annualLeaveDays = 0;
$sickLeaveDays = 0;
foreach ($leaves as $lItem) {
    $l = $lItem['leave'];
    $st = $lItem['status'];
    $code = $st ? $st->code : '';
    if ($code === 'APPROVED') {
        $ltCode = $lItem['type'] ? $lItem['type']->code : '';
        if ($ltCode === 'ANNUAL') $annualLeaveDays += (float)$l->days_requested;
        elseif ($ltCode === 'SICK') $sickLeaveDays += (float)$l->days_requested;
    }
}
$availableAnnual = max(0, 22 - $annualLeaveDays);

// Calculate total hours logged this month
$totalHoursMonth = 0;
$currentMonth = date('Y-m');
foreach ($timeEntries as $tItem) {
    $t = $tItem['entry'];
    if (strpos($t->work_date, $currentMonth) === 0) {
        $totalHoursMonth += (float)$t->hours;
    }
}
?>

<main class="portal-dashboard">
    <!-- Header Section -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="text-white-50">Dashboard</a></li>
                    <li class="breadcrumb-item active text-warning" aria-current="page">Staff Self-Service Hub</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge bg-warning text-dark px-3 py-1 mb-2 fw-semibold rounded-pill">
                        <i class="fa fa-user-check me-1"></i> Internal Personnel Hub
                    </span>
                    <h1 class="h2 fw-bold text-white mb-1">Welcome, <?= htmlspecialchars($user->name); ?></h1>
                    <p class="text-white-50 mb-0">
                        <i class="fa fa-briefcase me-1 text-warning"></i> <?= htmlspecialchars($profile ? $profile->job_title : 'Personnel'); ?> &bull; 
                        <i class="fa fa-building ms-2 me-1 text-warning"></i> <?= htmlspecialchars($profile ? $profile->department : 'Operations'); ?> &bull; 
                        <i class="fa fa-map-marker-alt ms-2 me-1 text-warning"></i> <?= htmlspecialchars($profile ? $profile->station : 'Harare HQ'); ?>
                    </p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-warning text-dark px-3 py-2 fw-bold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#selfLeaveModal">
                        <i class="fa fa-calendar-plus me-1"></i> Apply for Leave
                    </button>
                    <button class="btn btn-primary px-3 py-2 fw-semibold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#selfDocModal">
                        <i class="fa fa-upload me-1"></i> Upload Document
                    </button>
                    <button class="btn btn-outline-light px-3 py-2 fw-semibold rounded-3" data-bs-toggle="modal" data-bs-target="#selfTimeModal">
                        <i class="fa fa-clock me-1"></i> Log Hours
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Body Section -->
    <section class="portal-dashboard-body py-4">
        <div class="container">

            <!-- Flash Messages -->
            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fa fa-check-circle me-2"></i> <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fa fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                
                <!-- Left Sidebar: Digital Staff ID Card & Balances -->
                <div class="col-lg-4">

                    <!-- Digital Staff ID Card -->
                    <div class="card border-0 shadow mb-4 text-white position-relative overflow-hidden" 
                         style="border-radius: 16px; background: linear-gradient(145deg, #1C0D30 0%, #2A114B 60%, #471E7A 100%); min-height: 250px;">
                        
                        <!-- Card Watermark / Accent -->
                        <div class="position-absolute end-0 bottom-0 text-white" style="opacity: 0.05; transform: translate(20%, 20%); pointer-events: none;">
                            <i class="fa fa-id-badge" style="font-size: 15rem;"></i>
                        </div>

                        <div class="card-body p-4 d-flex flex-column justify-content-between position-relative">
                            <!-- Card Header -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning text-dark fw-bold px-2 py-1">TSIGIRO</span>
                                    <span class="small fw-semibold text-white-50 text-uppercase tracking-wider" style="font-size: 0.72rem; letter-spacing: 1px;">STAFF CREDENTIAL</span>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                            </div>

                            <!-- Card Middle: Photo + Identification -->
                            <div class="d-flex align-items-center gap-3 my-3">
                                <div class="avatar-circle rounded-3 d-flex align-items-center justify-content-center fw-bold border shadow-sm flex-shrink-0" 
                                     style="width: 64px; height: 64px; background: #FFCC00; color: #2A114B; font-size: 1.6rem; border-color: #FFE680;">
                                    <?= strtoupper(substr($user->name, 0, 1)); ?>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-white mb-0"><?= htmlspecialchars($user->name); ?></h5>
                                    <div class="text-warning small fw-semibold"><?= htmlspecialchars($profile ? $profile->job_title : 'Staff Member'); ?></div>
                                    <code class="text-white-50 small">Works No: <?= htmlspecialchars($profile ? ($profile->employee_number ?: 'TRN-' . str_pad($user->iD, 3, '0', STR_PAD_LEFT)) : 'Pending'); ?></code>
                                </div>
                            </div>

                            <!-- Card Footer: Details & Microprint -->
                            <div class="pt-2 border-top border-secondary border-opacity-25" style="font-size: 0.78rem;">
                                <div class="d-flex justify-content-between text-white-50 mb-1">
                                    <span>Department: <strong class="text-white"><?= htmlspecialchars($profile ? $profile->department : 'Operations'); ?></strong></span>
                                    <span>Station: <strong class="text-white"><?= htmlspecialchars($profile ? $profile->station : 'Harare HQ'); ?></strong></span>
                                </div>
                                <div class="d-flex justify-content-between text-white-50">
                                    <span>National ID: <strong class="text-white"><?= htmlspecialchars($profile ? ($profile->national_id_number ?: 'On File') : 'On File'); ?></strong></span>
                                    <span>SSR No: <strong class="text-white"><?= htmlspecialchars($profile ? ($profile->ssr_number ?: 'Registered') : 'Registered'); ?></strong></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Entitlements & Fast Stats -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-chart-pie text-warning me-2"></i> Leave & Activity Balances</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong class="text-dark d-block">Annual Leave Available</strong>
                                    <small class="text-muted">Statutory 22 days/year</small>
                                </div>
                                <span class="badge bg-success fs-6 fw-bold"><?= $availableAnnual; ?> Days</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong class="text-dark d-block">Annual Leave Taken</strong>
                                    <small class="text-muted">Year to date</small>
                                </div>
                                <span class="badge bg-light text-dark border fs-6"><?= $annualLeaveDays; ?> Days</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong class="text-dark d-block">Sick Leave Taken</strong>
                                    <small class="text-muted">Certified medical days</small>
                                </div>
                                <span class="badge bg-light text-dark border fs-6"><?= $sickLeaveDays; ?> Days</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <div>
                                    <strong class="text-dark d-block">Hours Logged (<?= date('M Y'); ?>)</strong>
                                    <small class="text-muted">Operational hours logged</small>
                                </div>
                                <span class="badge bg-primary fs-6 fw-bold"><?= number_format($totalHoursMonth, 1); ?> hrs</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Tabs for Documents, Leaves, and Timesheets -->
                <div class="col-lg-8">

                    <!-- Tab Navigation -->
                    <ul class="nav nav-pills nav-fill bg-white p-2 rounded-3 shadow-sm mb-4 border" id="portalTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-dark py-2" id="ptab-docs-nav" data-bs-toggle="pill" data-bs-target="#ptab-docs" type="button" role="tab" aria-controls="ptab-docs" aria-selected="true">
                                <i class="fa fa-folder-open text-primary me-1"></i> My Documents (<?= count($documents); ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-dark py-2" id="ptab-leaves-nav" data-bs-toggle="pill" data-bs-target="#ptab-leaves" type="button" role="tab" aria-controls="ptab-leaves" aria-selected="false">
                                <i class="fa fa-calendar-alt text-success me-1"></i> My Leave Applications (<?= count($leaves); ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-dark py-2" id="ptab-time-nav" data-bs-toggle="pill" data-bs-target="#ptab-time" type="button" role="tab" aria-controls="ptab-time" aria-selected="false">
                                <i class="fa fa-clock text-info me-1"></i> Logged Time Entries (<?= count($timeEntries); ?>)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="portalTabsContent">

                        <!-- TAB 1: MY DOCUMENTS -->
                        <div class="tab-pane fade show active" id="ptab-docs" role="tabpanel" aria-labelledby="ptab-docs-nav">
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-folder-open text-primary me-2"></i> Compliance Documents On File</h6>
                                        <small class="text-muted">Ensure your National ID, Driver's Licence, and certificates remain current</small>
                                    </div>
                                    <button class="btn btn-primary btn-sm px-3 py-2 fw-semibold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#selfDocModal">
                                        <i class="fa fa-upload me-1"></i> Upload New Document
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <?php if (empty($documents)): ?>
                                        <div class="p-5 text-center text-muted">
                                            <i class="fa fa-file-upload fa-3x mb-3 text-light-purple" style="color: #D6C2EB;"></i>
                                            <h6 class="fw-bold text-dark">No Documents Filed</h6>
                                            <p class="small text-muted mb-3">Please upload a certified scan of your National ID, Driver's Licence, or Proof of Residence.</p>
                                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#selfDocModal">
                                                <i class="fa fa-upload me-1"></i> Upload Document Now
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                                <thead class="bg-light text-muted">
                                                    <tr>
                                                        <th class="ps-4">Document Title</th>
                                                        <th>Category</th>
                                                        <th>Validity</th>
                                                        <th>Compliance Status</th>
                                                        <th class="text-end pe-4">File</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($documents as $docItem): 
                                                        $d = $docItem['doc'];
                                                        $type = $docItem['type'];
                                                        $ver = $docItem['verification'];
                                                        $status = $docItem['status'];
                                                        $validity = $docItem['validity'];
                                                        $statusCode = $status ? $status->code : 'PENDING';
                                                    ?>
                                                        <tr>
                                                            <td class="ps-4">
                                                                <strong class="text-dark d-block"><?= htmlspecialchars($d->title); ?></strong>
                                                                <small class="text-muted"><?= round($d->file_size / 1024, 1); ?> KB &bull; Uploaded <?= date('d M Y', strtotime($d->reg_date)); ?></small>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($type ? $type->name : 'Document'); ?></span>
                                                            </td>
                                                            <td>
                                                                <?php if ($validity): 
                                                                    $isExpired = ($validity->expiry_date < date('Y-m-d'));
                                                                    $daysRemaining = (int)ceil((strtotime($validity->expiry_date) - time()) / 86400);
                                                                ?>
                                                                    <?php if ($isExpired): ?>
                                                                        <span class="badge bg-danger text-white"><i class="fa fa-exclamation-circle me-1"></i> Expired</span>
                                                                    <?php elseif ($daysRemaining <= 30): ?>
                                                                        <span class="badge bg-warning text-dark"><i class="fa fa-clock me-1"></i> Expires in <?= $daysRemaining; ?> days</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Valid until <?= date('d M Y', strtotime($validity->expiry_date)); ?></span>
                                                                    <?php endif; ?>
                                                                <?php else: ?>
                                                                    <span class="text-muted small">&mdash; Non-expiring</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($statusCode === 'VERIFIED'): ?>
                                                                    <span class="badge bg-success text-white"><i class="fa fa-check-circle me-1"></i> Verified</span>
                                                                <?php elseif ($statusCode === 'REJECTED'): ?>
                                                                    <span class="badge bg-danger text-white" title="<?= htmlspecialchars($ver->notes ?? ''); ?>"><i class="fa fa-times-circle me-1"></i> Rejected</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-warning text-dark"><i class="fa fa-hourglass-half me-1"></i> Pending Review</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-end pe-4">
                                                                <a href="<?= $siteConfig->siteUrl . '/' . ltrim($d->file_path, '/'); ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                                    <i class="fa fa-download"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: MY LEAVES -->
                        <div class="tab-pane fade" id="ptab-leaves" role="tabpanel" aria-labelledby="ptab-leaves-nav">
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
                        </div>

                        <!-- TAB 3: LOGGED TIME -->
                        <div class="tab-pane fade" id="ptab-time" role="tabpanel" aria-labelledby="ptab-time-nav">
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
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- SELF MODAL 1: UPLOAD DOCUMENT -->
    <div class="modal fade" id="selfDocModal" tabindex="-1" aria-labelledby="selfDocModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <form action="<?= $siteConfig->siteUrl; ?>/admin/staff/documents/upload" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="staffprofile_id" value="<?= (int)($profile ? $profile->iD : 0); ?>">
                    <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/staff/portal#ptab-docs">
                    <div class="modal-header bg-white border-bottom">
                        <h5 class="modal-title fw-bold text-dark" id="selfDocModalLabel"><i class="fa fa-upload text-primary me-2"></i> Upload Personal Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Document Category *</label>
                            <select name="documenttype_id" class="form-select" required onchange="toggleSelfExpiryFields(this)">
                                <option value="">-- Select Document Category --</option>
                                <?php foreach ($data['allDocTypes'] as $dt): ?>
                                    <option value="<?= $dt->iD; ?>" data-expiry="<?= $dt->requires_expiry ? '1' : '0'; ?>"><?= htmlspecialchars($dt->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Document Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g., My Valid Class 4 Driver's Licence" required>
                        </div>
                        <div class="row g-2 mb-3" id="selfExpiryFieldsDiv" style="display: none;">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small">Issue Date</label>
                                <input type="date" name="issue_date" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small">Expiry Date *</label>
                                <input type="date" name="expiry_date" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Select File *</label>
                            <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                            <small class="text-muted">Supported formats: PDF, JPG, PNG (Max 10MB)</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">Upload Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SELF MODAL 2: APPLY LEAVE -->
    <div class="modal fade" id="selfLeaveModal" tabindex="-1" aria-labelledby="selfLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <form action="<?= $siteConfig->siteUrl; ?>/staff/leave/apply" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="staffprofile_id" value="<?= (int)($profile ? $profile->iD : 0); ?>">
                    <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/staff/portal#ptab-leaves">
                    <div class="modal-header bg-white border-bottom">
                        <h5 class="modal-title fw-bold text-dark" id="selfLeaveModalLabel"><i class="fa fa-calendar-plus text-success me-2"></i> Apply for Leave</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Leave Category *</label>
                            <select name="leavetype_id" class="form-select" required>
                                <option value="">-- Select Leave Category --</option>
                                <?php foreach ($data['allLeaveTypes'] as $lt): ?>
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

    <!-- SELF MODAL 3: LOG TIME -->
    <div class="modal fade" id="selfTimeModal" tabindex="-1" aria-labelledby="selfTimeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <form action="<?= $siteConfig->siteUrl; ?>/staff/time/log" method="POST">
                    <input type="hidden" name="staffprofile_id" value="<?= (int)($profile ? $profile->iD : 0); ?>">
                    <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/staff/portal#ptab-time">
                    <div class="modal-header bg-white border-bottom">
                        <h5 class="modal-title fw-bold text-dark" id="selfTimeModalLabel"><i class="fa fa-clock text-info me-2"></i> Log Operational Time</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Activity Category *</label>
                            <select name="activitycategory_id" class="form-select" required>
                                <option value="">-- Select Operational Activity --</option>
                                <?php foreach ($data['allActivityCats'] as $ac): ?>
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

    <!-- Client-side Scripts -->
    <script>
        function toggleSelfExpiryFields(selectElem) {
            var selectedOption = selectElem.options[selectElem.selectedIndex];
            var requiresExpiry = selectedOption.getAttribute('data-expiry') === '1';
            var div = document.getElementById('selfExpiryFieldsDiv');
            if (div) {
                div.style.display = requiresExpiry ? 'flex' : 'none';
            }
        }

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

        document.addEventListener('DOMContentLoaded', function() {
            var hash = window.location.hash;
            if (hash) {
                var tabTrigger = document.querySelector('button[data-bs-target="' + hash + '"]');
                if (tabTrigger) {
                    var tab = new bootstrap.Tab(tabTrigger);
                    tab.show();
                }
            }
        });
    </script>
</main>
