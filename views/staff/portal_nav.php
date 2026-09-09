<?php
global $siteConfig;
$user = $data['user'] ?? null;
$profile = $data['profile'] ?? null;
$role = $data['role'] ?? null;
$roleName = $role ? $role->name : 'Staff Member';

$documents = $data['documents'] ?? [];
$leaves = $data['leaves'] ?? [];
$timeEntries = $data['timeEntries'] ?? [];
$activeTab = $data['activeTab'] ?? 'documents';

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

$sectionTitles = [
    'documents'  => 'My Compliance Documents',
    'leave'      => 'My Leave Applications',
    'timesheets' => 'My Operational Time Logs',
];
$currentSectionTitle = $sectionTitles[$activeTab] ?? 'Staff Hub';
?>

<!-- Header Section -->
<section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="text-white-50">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/staff/portal" class="text-white-50">Staff Hub</a></li>
                <li class="breadcrumb-item active text-warning" aria-current="page"><?= htmlspecialchars($currentSectionTitle); ?></li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 mb-2 fw-semibold rounded-pill">
                    <i class="fa fa-user-check me-1"></i> Internal Personnel Hub
                </span>
                <h1 class="h2 fw-bold text-white mb-1">Welcome, <?= htmlspecialchars($user ? $user->name : 'Staff'); ?></h1>
                <p class="text-white-50 mb-0">
                    <i class="fa fa-briefcase me-1 text-warning"></i> <?= htmlspecialchars($profile ? $profile->job_title : 'Personnel'); ?> &bull; 
                    <i class="fa fa-building ms-2 me-1 text-warning"></i> <?= htmlspecialchars($profile ? $profile->department : 'Operations'); ?> &bull; 
                    <i class="fa fa-map-marker-alt ms-2 me-1 text-warning"></i> <?= htmlspecialchars($profile ? $profile->station : 'Harare HQ'); ?>
                </p>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= $siteConfig->siteUrl; ?>/staff/portal/leave" class="btn btn-warning text-dark px-3 py-2 fw-bold rounded-3 shadow-sm">
                    <i class="fa fa-calendar-plus me-1"></i> Apply for Leave
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/staff/portal/documents" class="btn btn-primary px-3 py-2 fw-semibold rounded-3 shadow-sm">
                    <i class="fa fa-upload me-1"></i> Upload Document
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/staff/portal/timesheets" class="btn btn-outline-light px-3 py-2 fw-semibold rounded-3">
                    <i class="fa fa-clock me-1"></i> Log Hours
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/payroll" class="btn btn-outline-warning px-3 py-2 fw-semibold rounded-3">
                    <i class="fa fa-money-check-dollar me-1"></i> Payslips
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
                                <?= strtoupper(substr($user ? $user->name : 'S', 0, 1)); ?>
                            </div>
                            <div>
                                <h5 class="fw-bold text-white mb-0"><?= htmlspecialchars($user ? $user->name : 'Staff'); ?></h5>
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

            <!-- Right Column: Dedicated Section View Content -->
            <div class="col-lg-8">

                <!-- Real HTML Link Navigation (NO JS tabs!) -->
                <ul class="nav nav-pills nav-fill bg-white p-2 rounded-3 shadow-sm mb-4 border" id="portalNavLinks">
                    <li class="nav-item">
                        <a class="nav-link <?= ($activeTab === 'documents') ? 'active bg-primary text-white shadow-sm' : 'text-dark'; ?> fw-bold py-2" 
                           href="<?= $siteConfig->siteUrl; ?>/staff/portal/documents">
                            <i class="fa fa-folder-open <?= ($activeTab === 'documents') ? 'text-white' : 'text-primary'; ?> me-1"></i> 
                            My Documents (<?= count($documents); ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activeTab === 'leave') ? 'active bg-success text-white shadow-sm' : 'text-dark'; ?> fw-bold py-2" 
                           href="<?= $siteConfig->siteUrl; ?>/staff/portal/leave">
                            <i class="fa fa-calendar-alt <?= ($activeTab === 'leave') ? 'text-white' : 'text-success'; ?> me-1"></i> 
                            My Leave Applications (<?= count($leaves); ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($activeTab === 'timesheets') ? 'active bg-info text-white shadow-sm' : 'text-dark'; ?> fw-bold py-2" 
                           href="<?= $siteConfig->siteUrl; ?>/staff/portal/timesheets">
                            <i class="fa fa-clock <?= ($activeTab === 'timesheets') ? 'text-white' : 'text-info'; ?> me-1"></i> 
                            Logged Time Entries (<?= count($timeEntries); ?>)
                        </a>
                    </li>
                </ul>
