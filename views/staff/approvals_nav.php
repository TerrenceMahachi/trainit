<?php
global $siteConfig;
$activeTab = $data['activeTab'] ?? 'documents';
$user = $data['user'] ?? null;
$pendingDocs = $data['pendingDocs'] ?? [];
$expiringDocs = $data['expiringDocs'] ?? [];
$pendingLeaves = $data['pendingLeaves'] ?? [];
$pendingTime = $data['pendingTime'] ?? [];

$sectionTitles = [
    'documents'  => 'Document Verification Queue',
    'expiring'   => 'Expiring Credentials Radar',
    'leave'      => 'Leave Applications Queue',
    'timesheets' => 'Timesheet Sign-Offs Queue',
];
$currentSectionTitle = $sectionTitles[$activeTab] ?? 'Compliance Queue';
?>

<!-- Header Section -->
<section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="text-white-50">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/admin/staff" class="text-white-50">Staff Directory</a></li>
                <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals" class="text-white-50">Compliance & Approvals</a></li>
                <li class="breadcrumb-item active text-warning" aria-current="page"><?= htmlspecialchars($currentSectionTitle); ?></li>
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

<!-- Body Section Wrapper -->
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
                <a href="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals/documents" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 <?= ($activeTab === 'documents') ? 'border-primary' : ''; ?>" style="border-radius: 12px; border-left: 4px solid #0d6efd !important;">
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
                </a>
            </div>

            <div class="col-sm-6 col-lg-3">
                <a href="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals/expiring" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 <?= ($activeTab === 'expiring') ? 'border-danger' : ''; ?>" style="border-radius: 12px; border-left: 4px solid #dc3545 !important;">
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
                </a>
            </div>

            <div class="col-sm-6 col-lg-3">
                <a href="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals/leave" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 <?= ($activeTab === 'leave') ? 'border-success' : ''; ?>" style="border-radius: 12px; border-left: 4px solid #198754 !important;">
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
                </a>
            </div>

            <div class="col-sm-6 col-lg-3">
                <a href="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals/timesheets" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 <?= ($activeTab === 'timesheets') ? 'border-info' : ''; ?>" style="border-radius: 12px; border-left: 4px solid #0dcaf0 !important;">
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
                </a>
            </div>
        </div>

        <!-- Section Navigation Links (Actual HTML links with distinct routes) -->
        <ul class="nav nav-pills nav-fill bg-white p-2 rounded-3 shadow-sm mb-4 border" id="queueNavLinks">
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'documents') ? 'active bg-primary text-white shadow-sm' : 'text-dark'; ?> fw-bold py-2" 
                   href="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals/documents">
                    <i class="fa fa-file-signature <?= ($activeTab === 'documents') ? 'text-white' : 'text-primary'; ?> me-1"></i> 
                    Document Verification Queue (<?= count($pendingDocs); ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'expiring') ? 'active bg-danger text-white shadow-sm' : 'text-dark'; ?> fw-bold py-2" 
                   href="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals/expiring">
                    <i class="fa fa-stopwatch <?= ($activeTab === 'expiring') ? 'text-white' : 'text-danger'; ?> me-1"></i> 
                    Expiring Credentials Radar (<?= count($expiringDocs); ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'leave') ? 'active bg-success text-white shadow-sm' : 'text-dark'; ?> fw-bold py-2" 
                   href="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals/leave">
                    <i class="fa fa-calendar-alt <?= ($activeTab === 'leave') ? 'text-white' : 'text-success'; ?> me-1"></i> 
                    Leave Applications (<?= count($pendingLeaves); ?>)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activeTab === 'timesheets') ? 'active bg-info text-white shadow-sm' : 'text-dark'; ?> fw-bold py-2" 
                   href="<?= $siteConfig->siteUrl; ?>/admin/staff-approvals/timesheets">
                    <i class="fa fa-tasks <?= ($activeTab === 'timesheets') ? 'text-white' : 'text-info'; ?> me-1"></i> 
                    Timesheet Sign-Offs (<?= count($pendingTime); ?>)
                </a>
            </li>
        </ul>
