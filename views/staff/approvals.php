@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'];
$pendingDocs = $data['pendingDocs'] ?? [];
$expiringDocs = $data['expiringDocs'] ?? [];
$pendingLeaves = $data['pendingLeaves'] ?? [];
$pendingTime = $data['pendingTime'] ?? [];
$allVerStatuses = $data['allVerStatuses'] ?? [];
$allLeaveStatuses = $data['allLeaveStatuses'] ?? [];
?>

<main class="portal-dashboard">
    <!-- Header Section -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="text-white-50">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/admin/staff" class="text-white-50">Staff Directory</a></li>
                    <li class="breadcrumb-item active text-warning" aria-current="page">Compliance & Approvals Queue</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge bg-warning text-dark px-3 py-1 mb-2 fw-semibold rounded-pill">
                        <i class="fa fa-shield-alt me-1"></i> Governance & Operations Control
                    </span>
                    <h1 class="h2 fw-bold text-white mb-1">Compliance & Approvals Command Center</h1>
                    <p class="text-white-50 mb-0">Audit pending compliance documents, track expiring credentials, and process leave and timesheets</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= $siteConfig->siteUrl; ?>/admin/staff" class="btn btn-outline-light px-3 py-2 fw-semibold rounded-3">
                        <i class="fa fa-users me-1"></i> Staff Directory
                    </a>
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

            <!-- KPI Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #0d6efd !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Pending Documents</span>
                                    <h3 class="fw-bold mb-0 text-dark"><?= count($pendingDocs); ?></h3>
                                </div>
                                <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 48px; height: 48px;">
                                    <i class="fa fa-folder-open fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #dc3545 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Expiring Credentials</span>
                                    <h3 class="fw-bold mb-0 text-danger"><?= count($expiringDocs); ?></h3>
                                </div>
                                <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 48px; height: 48px;">
                                    <i class="fa fa-id-card fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #198754 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Leave Requests</span>
                                    <h3 class="fw-bold mb-0 text-dark"><?= count($pendingLeaves); ?></h3>
                                </div>
                                <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 48px; height: 48px;">
                                    <i class="fa fa-calendar-check fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 4px solid #0dcaf0 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold">Unapproved Timesheets</span>
                                    <h3 class="fw-bold mb-0 text-dark"><?= count($pendingTime); ?></h3>
                                </div>
                                <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center bg-info-subtle text-info" style="width: 48px; height: 48px;">
                                    <i class="fa fa-clock fs-5"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Queue Tab Navigation -->
            <ul class="nav nav-pills nav-fill bg-white p-2 rounded-3 shadow-sm mb-4 border" id="queueTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-dark py-2" id="qtab-docs-nav" data-bs-toggle="pill" data-bs-target="#qtab-docs" type="button" role="tab" aria-controls="qtab-docs" aria-selected="true">
                        <i class="fa fa-file-signature text-primary me-1"></i> Document Verification Queue (<?= count($pendingDocs); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-dark py-2" id="qtab-radar-nav" data-bs-toggle="pill" data-bs-target="#qtab-radar" type="button" role="tab" aria-controls="qtab-radar" aria-selected="false">
                        <i class="fa fa-stopwatch text-danger me-1"></i> Expiring Credentials Radar (<?= count($expiringDocs); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-dark py-2" id="qtab-leaves-nav" data-bs-toggle="pill" data-bs-target="#qtab-leaves" type="button" role="tab" aria-controls="qtab-leaves" aria-selected="false">
                        <i class="fa fa-calendar-alt text-success me-1"></i> Leave Applications (<?= count($pendingLeaves); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-dark py-2" id="qtab-time-nav" data-bs-toggle="pill" data-bs-target="#qtab-time" type="button" role="tab" aria-controls="qtab-time" aria-selected="false">
                        <i class="fa fa-tasks text-info me-1"></i> Timesheet Sign-Offs (<?= count($pendingTime); ?>)
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="queueTabsContent">

                <!-- TAB 1: PENDING DOCUMENTS -->
                <div class="tab-pane fade show active" id="qtab-docs" role="tabpanel" aria-labelledby="qtab-docs-nav">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-file-signature text-primary me-2"></i> Documents Awaiting Compliance Verification</h6>
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
                </div>

                <!-- TAB 2: EXPIRING CREDENTIALS RADAR -->
                <div class="tab-pane fade" id="qtab-radar" role="tabpanel" aria-labelledby="qtab-radar-nav">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-stopwatch text-danger me-2"></i> Credentials Expiring Within 90 Days or Surpassed</h6>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($expiringDocs)): ?>
                                <div class="p-5 text-center text-muted">
                                    <i class="fa fa-shield-alt fa-3x mb-3 text-success" style="opacity: 0.6;"></i>
                                    <h6 class="fw-bold text-dark">All Credentials in Legal Standing</h6>
                                    <p class="small text-muted mb-0">No driver's licences, defensive driving certificates, or medicals expiring in the next 90 days.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                        <thead class="bg-light text-muted">
                                            <tr>
                                                <th class="ps-4">Employee</th>
                                                <th>Document Title</th>
                                                <th>Category</th>
                                                <th>Expiry Date</th>
                                                <th>Urgency Status</th>
                                                <th class="text-end pe-4">Dossier</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($expiringDocs as $item): 
                                                $v = $item['validity'];
                                                $d = $item['doc'];
                                                $staff = $item['staff'];
                                                $type = $item['type'];
                                                $isExpired = $item['isExpired'];
                                                $daysLeft = $item['daysLeft'];
                                            ?>
                                                <tr>
                                                    <td class="ps-4">
                                                        <strong class="text-dark d-block"><?= htmlspecialchars($staff ? $staff->name : 'Staff'); ?></strong>
                                                        <small class="text-muted"><?= htmlspecialchars($staff ? $staff->department : ''); ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="text-dark fw-semibold"><?= htmlspecialchars($d ? $d->title : 'Document'); ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($type ? $type->name : 'Certificate'); ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold text-dark"><?= date('d M Y', strtotime($v->expiry_date)); ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if ($isExpired): ?>
                                                            <span class="badge bg-danger text-white"><i class="fa fa-times-circle me-1"></i> Expired (<?= abs($daysLeft); ?> days ago)</span>
                                                        <?php elseif ($daysLeft <= 30): ?>
                                                            <span class="badge bg-warning text-dark"><i class="fa fa-exclamation-triangle me-1"></i> Critical: <?= $daysLeft; ?> days remaining</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="fa fa-clock me-1"></i> <?= $daysLeft; ?> days left</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        <a href="<?= $siteConfig->siteUrl; ?>/admin/staff/view/<?= (int)($staff ? $staff->user : 0); ?>#tab-docs" class="btn btn-sm btn-outline-primary">
                                                            <i class="fa fa-folder-open me-1"></i> View Dossier
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

                <!-- TAB 3: PENDING LEAVE APPLICATIONS -->
                <div class="tab-pane fade" id="qtab-leaves" role="tabpanel" aria-labelledby="qtab-leaves-nav">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-calendar-check text-success me-2"></i> Leave Requests Awaiting Authorization</h6>
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
                </div>

                <!-- TAB 4: TIMESHEET SIGN-OFFS -->
                <div class="tab-pane fade" id="qtab-time" role="tabpanel" aria-labelledby="qtab-time-nav">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-tasks text-info me-2"></i> Operational Hours Awaiting Supervisor Sign-Off</h6>
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
                </div>

            </div>

        </div>
    </section>

    <!-- QUEUE MODAL 1: VERIFY DOCUMENT -->
    <div class="modal fade" id="queueVerifyModal" tabindex="-1" aria-labelledby="queueVerifyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <form action="<?= $siteConfig->siteUrl; ?>/admin/staff/documents/verify" method="POST">
                    <input type="hidden" name="staffdocument_id" id="q_verify_doc_id" value="">
                    <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals#qtab-docs">
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

    <!-- QUEUE MODAL 2: DECIDE LEAVE -->
    <div class="modal fade" id="queueLeaveModal" tabindex="-1" aria-labelledby="queueLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <form action="<?= $siteConfig->siteUrl; ?>/admin/staff/leave/decide" method="POST">
                    <input type="hidden" name="staffleave_id" id="q_leave_id" value="">
                    <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals#qtab-leaves">
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

    <!-- QUEUE MODAL 3: SIGNOFF TIME -->
    <div class="modal fade" id="queueTimeModal" tabindex="-1" aria-labelledby="queueTimeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <form action="<?= $siteConfig->siteUrl; ?>/admin/staff/time/signoff" method="POST">
                    <input type="hidden" name="stafftimeentry_id" id="q_time_id" value="">
                    <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals#qtab-time">
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

    <!-- Scripts -->
    <script>
        function prepareQueueVerify(docId, docTitle, staffName) {
            document.getElementById('q_verify_doc_id').value = docId;
            document.getElementById('q_verify_doc_title').value = docTitle;
            document.getElementById('q_verify_staff_name').value = staffName;
        }

        function prepareQueueLeave(leaveId, staffName, leaveType, days) {
            document.getElementById('q_leave_id').value = leaveId;
            document.getElementById('q_leave_desc').value = staffName + ' — ' + leaveType + ' (' + days + ' days)';
        }

        function prepareQueueTime(timeId, staffName, hours, workDate) {
            document.getElementById('q_time_id').value = timeId;
            document.getElementById('q_time_desc').value = staffName + ' — ' + hours + ' hrs on ' + workDate;
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
