@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'];
$profile = $data['profile'];
$role = $data['role'];
$roleName = $role ? $role->name : 'Staff Member';

$badgeClass = 'bg-primary';
if ($user->role == 1) $badgeClass = 'bg-dark';
elseif ($user->role == 6) $badgeClass = 'bg-info text-dark';
elseif ($user->role == 7) $badgeClass = 'bg-warning text-dark';
elseif ($user->role == 8) $badgeClass = 'bg-success';
?>

<main class="portal-dashboard">
    <!-- Header Section -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="text-white-50">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/admin/staff" class="text-white-50">Staff Directory</a></li>
                    <li class="breadcrumb-item active text-warning" aria-current="page"><?= htmlspecialchars($user->name); ?></li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center me-3 fw-bold border shadow-sm" style="width: 64px; height: 64px; background-color: #F8F5FC; border-color: #E6DCF2; color: #2A114B; font-size: 1.5rem;">
                        <?= strtoupper(substr($user->name, 0, 1)); ?>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h1 class="h3 fw-bold text-white mb-0"><?= htmlspecialchars($user->name); ?></h1>
                            <span class="badge <?= $badgeClass; ?> px-2 py-1"><?= htmlspecialchars($roleName); ?></span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                        </div>
                        <p class="text-white-50 mb-0">
                            <i class="fa fa-briefcase me-1 text-warning"></i> <?= htmlspecialchars($profile ? $profile->job_title : 'Staff Member'); ?> &bull; 
                            <i class="fa fa-building ms-2 me-1 text-warning"></i> <?= htmlspecialchars($profile ? $profile->department : 'Operations'); ?> &bull; 
                            <i class="fa fa-envelope ms-2 me-1 text-warning"></i> <?= htmlspecialchars($user->email); ?>
                        </p>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="<?= $siteConfig->siteUrl; ?>/admin/staff" class="btn btn-outline-light px-3 py-2 fw-semibold rounded-3">
                        <i class="fa fa-arrow-left me-1"></i> Staff Directory
                    </a>
                    <button onclick="window.print()" class="btn btn-light px-3 py-2 fw-semibold rounded-3 shadow-sm">
                        <i class="fa fa-print me-1"></i> Print Dossier
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Body Section -->
    <section class="portal-dashboard-body py-4">
        <div class="container">

            <div class="row g-4">
                
                <!-- Left Sidebar: Employment Highlights -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-id-badge text-primary me-2"></i> Employment Metadata</h6>
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Employee Number:</span>
                                    <code class="fw-bold text-dark"><?= htmlspecialchars($profile ? ($profile->employee_number ?: 'TRN-' . str_pad($user->iD, 3, '0', STR_PAD_LEFT)) : 'Pending'); ?></code>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Official Designation:</span>
                                    <strong class="text-dark"><?= htmlspecialchars($profile ? $profile->job_title : 'Staff'); ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Assigned Department:</span>
                                    <span class="text-dark"><?= htmlspecialchars($profile ? $profile->department : 'Operations'); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Station / Base Office:</span>
                                    <span class="text-dark"><?= htmlspecialchars($profile ? $profile->station : 'Harare HQ'); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Nature of Employment:</span>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($profile ? $profile->nature_of_employment : 'ORDINARY'); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Date of Appointment:</span>
                                    <span class="text-dark"><?= htmlspecialchars($profile && $profile->date_of_employment ? date('d M Y', strtotime($profile->date_of_employment)) : 'Pending'); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Primary Work Phone:</span>
                                    <span class="text-dark"><?= htmlspecialchars($profile ? ($profile->telephone_number ?: 'Not specified') : 'Not specified'); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Disbursal Banking Card -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-university text-success me-2"></i> Remuneration & Banking</h6>
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Bank Name:</span>
                                    <strong class="text-dark"><?= htmlspecialchars($profile ? ($profile->bank_name ?: 'Pending') : 'Pending'); ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Branch:</span>
                                    <span class="text-dark"><?= htmlspecialchars($profile ? ($profile->bank_branch ?: 'Pending') : 'Pending'); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Account Number:</span>
                                    <code class="text-dark fw-bold"><?= htmlspecialchars($profile ? ($profile->account_number ?: 'Pending') : 'Pending'); ?></code>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Currency:</span>
                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($profile ? $profile->bank_currency : 'USD'); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Tabbed Modules -->
                <div class="col-lg-8">

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

                    <!-- Tab Navigation Header -->
                    <ul class="nav nav-pills nav-fill bg-white p-2 rounded-3 shadow-sm mb-4 border" id="dossierTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-dark py-2" id="tab-statutory-nav" data-bs-toggle="pill" data-bs-target="#tab-statutory" type="button" role="tab" aria-controls="tab-statutory" aria-selected="true">
                                <i class="fa fa-shield-alt text-warning me-1"></i> Statutory P4
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-dark py-2" id="tab-docs-nav" data-bs-toggle="pill" data-bs-target="#tab-docs" type="button" role="tab" aria-controls="tab-docs" aria-selected="false">
                                <i class="fa fa-folder-open text-primary me-1"></i> Document Vault <span class="badge bg-primary-subtle text-primary rounded-pill ms-1"><?= count($data['documents'] ?? []); ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-dark py-2" id="tab-leave-nav" data-bs-toggle="pill" data-bs-target="#tab-leave" type="button" role="tab" aria-controls="tab-leave" aria-selected="false">
                                <i class="fa fa-calendar-check text-success me-1"></i> Leave & Absence <span class="badge bg-success-subtle text-success rounded-pill ms-1"><?= count($data['leaves'] ?? []); ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-dark py-2" id="tab-time-nav" data-bs-toggle="pill" data-bs-target="#tab-time" type="button" role="tab" aria-controls="tab-time" aria-selected="false">
                                <i class="fa fa-clock text-info me-1"></i> Operational Time <span class="badge bg-info-subtle text-info rounded-pill ms-1"><?= count($data['timeEntries'] ?? []); ?></span>
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content" id="dossierTabsContent">

                        <!-- TAB 1: STATUTORY P4 & ADDRESS -->
                        <div class="tab-pane fade show active" id="tab-statutory" role="tabpanel" aria-labelledby="tab-statutory-nav">
                            
                            <!-- Statutory Compliance Card (NSSA Form P4) -->
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="fa fa-shield-alt text-warning me-2"></i> Statutory Identification (NSSA Form P4 Compliant)
                                    </h6>
                                    <span class="badge bg-warning text-dark">Official Register</span>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-sm-6 col-md-4">
                                            <small class="text-muted d-block">Full Legal Name</small>
                                            <strong class="text-dark"><?= htmlspecialchars(($profile ? ($profile->title ? $profile->title . ' ' : '') : '') . $user->name); ?></strong>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <small class="text-muted d-block">National ID Number</small>
                                            <span class="badge bg-light text-dark border fs-6 fw-bold"><?= htmlspecialchars($profile ? ($profile->national_id_number ?: 'Pending') : 'Pending'); ?></span>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <small class="text-muted d-block">NSSA SSR Number</small>
                                            <span class="badge bg-light text-dark border fs-6 fw-bold"><?= htmlspecialchars($profile ? ($profile->ssr_number ?: 'Pending') : 'Pending'); ?></span>
                                        </div>

                                        <div class="col-sm-6 col-md-4">
                                            <small class="text-muted d-block">Date of Birth</small>
                                            <span class="text-dark"><?= htmlspecialchars($profile && $profile->date_of_birth ? date('d M Y', strtotime($profile->date_of_birth)) : 'Not specified'); ?></span>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <small class="text-muted d-block">Gender</small>
                                            <span class="text-dark"><?= htmlspecialchars($profile ? ($profile->gender ?: 'Not specified') : 'Not specified'); ?></span>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <small class="text-muted d-block">Marital Status</small>
                                            <span class="text-dark"><?= htmlspecialchars($profile ? ($profile->marital_status ?: 'Single') : 'Single'); ?></span>
                                        </div>

                                        <div class="col-sm-6 col-md-4">
                                            <small class="text-muted d-block">Drivers Licence Number</small>
                                            <span class="text-dark"><?= htmlspecialchars($profile ? ($profile->drivers_licence_number ?: 'None on file') : 'None on file'); ?></span>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <small class="text-muted d-block">Passport Number</small>
                                            <span class="text-dark"><?= htmlspecialchars($profile ? ($profile->passport_number ?: 'None on file') : 'None on file'); ?></span>
                                        </div>
                                        <div class="col-sm-6 col-md-4">
                                            <small class="text-muted d-block">Nationality / Citizenship</small>
                                            <span class="text-dark"><?= htmlspecialchars($profile ? ($profile->nationality . ' (' . $profile->citizenship . ')') : 'Zimbabwean (ZW)'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Residential Address & Emergency Contact -->
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="fa fa-map-marker-alt text-danger me-2"></i> Residential Location & Emergency Contact
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Residential Street Address</small>
                                            <span class="text-dark fw-semibold"><?= htmlspecialchars($profile ? trim(($profile->street_number ? $profile->street_number . ' ' : '') . ($profile->street_name ?: '')) ?: 'Not specified' : 'Not specified'); ?></span>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Suburb & City</small>
                                            <span class="text-dark fw-semibold"><?= htmlspecialchars($profile ? trim(($profile->suburb ? $profile->suburb . ', ' : '') . ($profile->town ?: 'HARARE')) : 'HARARE'); ?> (<?= htmlspecialchars($profile ? $profile->region : 'HRE'); ?>)</span>
                                        </div>
                                    </div>

                                    <hr class="my-3 text-muted">

                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Emergency Contact Name</small>
                                            <strong class="text-dark"><?= htmlspecialchars($profile ? ($profile->emergency_contact_name ?: 'Pending') : 'Pending'); ?></strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Relationship</small>
                                            <span class="text-dark"><?= htmlspecialchars($profile ? ($profile->emergency_contact_relationship ?: 'Pending') : 'Pending'); ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Emergency Phone</small>
                                            <span class="text-dark fw-bold"><?= htmlspecialchars($profile ? ($profile->emergency_contact_phone ?: 'Pending') : 'Pending'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- System Access & Audit Information -->
                            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h6 class="fw-bold mb-0 text-dark">
                                        <i class="fa fa-lock text-info me-2"></i> System Credentials & Security Scope
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-sm-6 col-md-3">
                                            <small class="text-muted d-block">System User ID</small>
                                            <code>#<?= (int)$user->iD; ?></code>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <small class="text-muted d-block">Role ID</small>
                                            <span><?= (int)$user->role; ?> (<?= htmlspecialchars($roleName); ?>)</span>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <small class="text-muted d-block">Account Status</small>
                                            <span class="badge bg-success">Active</span>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <small class="text-muted d-block">Created On</small>
                                            <span class="text-muted"><?= date('d M Y', strtotime($user->reg_date)); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- TAB 2: DOCUMENT & COMPLIANCE VAULT -->
                        <div class="tab-pane fade" id="tab-docs" role="tabpanel" aria-labelledby="tab-docs-nav">
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-folder-open text-primary me-2"></i> Employee Document Vault</h6>
                                        <small class="text-muted">Statutory filings, certified IDs, licenses, and employment agreements</small>
                                    </div>
                                    <button class="btn btn-primary btn-sm px-3 py-2 fw-semibold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                                        <i class="fa fa-upload me-1"></i> Upload Document
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <?php if (empty($data['documents'])): ?>
                                        <div class="p-5 text-center text-muted">
                                            <i class="fa fa-file-invoice fa-3x mb-3 text-light-purple" style="color: #D6C2EB;"></i>
                                            <h6 class="fw-bold text-dark">No Documents Filed Yet</h6>
                                            <p class="small text-muted mb-3">Upload statutory IDs, driver's licences, academic credentials, or contracts for compliance audit.</p>
                                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                                                <i class="fa fa-plus me-1"></i> Add First Document
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                                <thead class="bg-light text-muted">
                                                    <tr>
                                                        <th class="ps-4">Document Title</th>
                                                        <th>Category</th>
                                                        <th>Expiry Status</th>
                                                        <th>Compliance Audit</th>
                                                        <th class="text-end pe-4">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($data['documents'] as $docItem): 
                                                        $d = $docItem['doc'];
                                                        $type = $docItem['type'];
                                                        $ver = $docItem['verification'];
                                                        $status = $docItem['status'];
                                                        $validity = $docItem['validity'];
                                                        $statusCode = $status ? $status->code : 'PENDING';
                                                    ?>
                                                        <tr>
                                                            <td class="ps-4">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-2 text-primary fs-5">
                                                                        <i class="fa <?= (strpos($d->mime_type, 'pdf') !== false) ? 'fa-file-pdf text-danger' : 'fa-file-alt text-primary'; ?>"></i>
                                                                    </div>
                                                                    <div>
                                                                        <strong class="text-dark d-block"><?= htmlspecialchars($d->title); ?></strong>
                                                                        <small class="text-muted"><?= round($d->file_size / 1024, 1); ?> KB &bull; Filed <?= date('d M Y', strtotime($d->reg_date)); ?></small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($type ? $type->name : 'General Document'); ?></span>
                                                            </td>
                                                            <td>
                                                                <?php if ($validity): 
                                                                    $isExpired = ($validity->expiry_date < date('Y-m-d'));
                                                                    $daysRemaining = (int)ceil((strtotime($validity->expiry_date) - time()) / 86400);
                                                                ?>
                                                                    <?php if ($isExpired): ?>
                                                                        <span class="badge bg-danger text-white"><i class="fa fa-exclamation-circle me-1"></i> Expired (<?= date('d M Y', strtotime($validity->expiry_date)); ?>)</span>
                                                                    <?php elseif ($daysRemaining <= 30): ?>
                                                                        <span class="badge bg-warning text-dark"><i class="fa fa-clock me-1"></i> Expiring in <?= $daysRemaining; ?> days</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Valid until <?= date('d M Y', strtotime($validity->expiry_date)); ?></span>
                                                                    <?php endif; ?>
                                                                <?php else: ?>
                                                                    <span class="text-muted small">&mdash; Non-expiring</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($statusCode === 'VERIFIED'): ?>
                                                                    <span class="badge bg-success text-white" title="<?= htmlspecialchars($ver->notes ?? ''); ?>">
                                                                        <i class="fa fa-check-circle me-1"></i> Verified
                                                                    </span>
                                                                    <small class="d-block text-muted" style="font-size: 0.75rem;">by <?= htmlspecialchars($docItem['auditor'] ? $docItem['auditor']->name : 'Admin'); ?></small>
                                                                <?php elseif ($statusCode === 'REJECTED'): ?>
                                                                    <span class="badge bg-danger text-white" title="<?= htmlspecialchars($ver->notes ?? ''); ?>">
                                                                        <i class="fa fa-times-circle me-1"></i> Rejected
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-warning text-dark">
                                                                        <i class="fa fa-hourglass-half me-1"></i> Pending Audit
                                                                    </span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-end pe-4">
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="<?= $siteConfig->siteUrl . '/' . ltrim($d->file_path, '/'); ?>" target="_blank" class="btn btn-outline-secondary" title="View Document">
                                                                        <i class="fa fa-external-link-alt"></i> View
                                                                    </a>
                                                                    <?php if (\App\Helpers\Auth::isAdmin()): ?>
                                                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#verifyDocModal" 
                                                                                onclick="prepareVerifyModal(<?= $d->iD; ?>, '<?= htmlspecialchars(addslashes($d->title)); ?>')">
                                                                            <i class="fa fa-stamp"></i> Audit
                                                                        </button>
                                                                    <?php endif; ?>
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

                        <!-- TAB 3: LEAVE & ABSENCE MANAGEMENT -->
                        <div class="tab-pane fade" id="tab-leave" role="tabpanel" aria-labelledby="tab-leave-nav">
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-calendar-check text-success me-2"></i> Leave & Absence Register</h6>
                                        <small class="text-muted">Statutory annual leave, sick leave, compassionate leave applications</small>
                                    </div>
                                    <button class="btn btn-success btn-sm px-3 py-2 fw-semibold rounded-3 shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
                                        <i class="fa fa-plus me-1"></i> Apply for Leave
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <?php if (empty($data['leaves'])): ?>
                                        <div class="p-5 text-center text-muted">
                                            <i class="fa fa-calendar-times fa-3x mb-3 text-light-purple" style="color: #D6C2EB;"></i>
                                            <h6 class="fw-bold text-dark">No Leave Applications On Record</h6>
                                            <p class="small text-muted mb-3">Record planned vacation leave, medical sick leave, or study leave for this employee.</p>
                                            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
                                                <i class="fa fa-calendar-plus me-1"></i> Apply for Leave
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                                <thead class="bg-light text-muted">
                                                    <tr>
                                                        <th class="ps-4">Leave Type</th>
                                                        <th>Date Range</th>
                                                        <th>Duration</th>
                                                        <th>Reason & Evidence</th>
                                                        <th>Approval Status</th>
                                                        <th class="text-end pe-4">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($data['leaves'] as $leaveItem): 
                                                        $l = $leaveItem['leave'];
                                                        $lt = $leaveItem['type'];
                                                        $appr = $leaveItem['approval'];
                                                        $st = $leaveItem['status'];
                                                        $att = $leaveItem['attachment'];
                                                        $lStatusCode = $st ? $st->code : 'PENDING';
                                                    ?>
                                                        <tr>
                                                            <td class="ps-4">
                                                                <strong class="text-dark d-block"><?= htmlspecialchars($lt ? $lt->name : 'Leave'); ?></strong>
                                                                <small class="text-muted">Applied <?= date('d M Y', strtotime($l->reg_date)); ?></small>
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
                                                                <span class="text-dark d-block text-truncate" style="max-width: 180px;" title="<?= htmlspecialchars($l->reason); ?>"><?= htmlspecialchars($l->reason); ?></span>
                                                                <?php if ($att): ?>
                                                                    <a href="<?= $siteConfig->siteUrl . '/' . ltrim($att->file_path, '/'); ?>" target="_blank" class="small text-primary"><i class="fa fa-paperclip"></i> View Medical/Note</a>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($lStatusCode === 'APPROVED'): ?>
                                                                    <span class="badge bg-success text-white"><i class="fa fa-check me-1"></i> Approved</span>
                                                                    <small class="d-block text-muted" style="font-size: 0.75rem;">by <?= htmlspecialchars($leaveItem['manager'] ? $leaveItem['manager']->name : 'Manager'); ?></small>
                                                                <?php elseif ($lStatusCode === 'REJECTED'): ?>
                                                                    <span class="badge bg-danger text-white"><i class="fa fa-times me-1"></i> Declined</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-warning text-dark"><i class="fa fa-clock me-1"></i> Awaiting Approval</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-end pe-4">
                                                                <?php if (\App\Helpers\Auth::isAdmin()): ?>
                                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#decideLeaveModal"
                                                                            onclick="prepareLeaveModal(<?= $l->iD; ?>, '<?= htmlspecialchars(addslashes($lt ? $lt->name : 'Leave')); ?>', '<?= $l->days_requested; ?>')">
                                                                        <i class="fa fa-tasks"></i> Review
                                                                    </button>
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

                        <!-- TAB 4: OPERATIONAL TIME & ACTIVITY -->
                        <div class="tab-pane fade" id="tab-time" role="tabpanel" aria-labelledby="tab-time-nav">
                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-clock text-info me-2"></i> Operational Activity & Logged Hours</h6>
                                        <small class="text-muted">Daily operational work: vetting evaluations, triage, engineering, and client deliverables</small>
                                    </div>
                                    <button class="btn btn-info btn-sm px-3 py-2 fw-semibold rounded-3 shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#logTimeModal">
                                        <i class="fa fa-plus me-1"></i> Log Hours
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <?php if (empty($data['timeEntries'])): ?>
                                        <div class="p-5 text-center text-muted">
                                            <i class="fa fa-user-clock fa-3x mb-3 text-light-purple" style="color: #D6C2EB;"></i>
                                            <h6 class="fw-bold text-dark">No Operational Hours Logged</h6>
                                            <p class="small text-muted mb-3">Staff can log daily hours against talent vetting reviews, client request triage, or IT maintenance.</p>
                                            <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#logTimeModal">
                                                <i class="fa fa-plus me-1"></i> Log Operational Time
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                                <thead class="bg-light text-muted">
                                                    <tr>
                                                        <th class="ps-4">Work Date</th>
                                                        <th>Category</th>
                                                        <th>Hours Logged</th>
                                                        <th>Task Deliverables</th>
                                                        <th class="text-end pe-4">Sign-Off Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($data['timeEntries'] as $timeItem): 
                                                        $t = $timeItem['entry'];
                                                        $cat = $timeItem['category'];
                                                        $appr = $timeItem['approval'];
                                                    ?>
                                                        <tr>
                                                            <td class="ps-4">
                                                                <strong class="text-dark"><?= date('d M Y', strtotime($t->work_date)); ?></strong>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($cat ? $cat->name : 'General Activity'); ?></span>
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
                                                                    <small class="d-block text-muted" style="font-size: 0.75rem;">by <?= htmlspecialchars($timeItem['supervisor'] ? $timeItem['supervisor']->name : 'Supervisor'); ?></small>
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

    <!-- MODAL 1: UPLOAD DOCUMENT -->
    <div class="modal fade" id="uploadDocModal" tabindex="-1" aria-labelledby="uploadDocModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <form action="<?= $siteConfig->siteUrl; ?>/admin/staff/documents/upload" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="staffprofile_id" value="<?= (int)($profile ? $profile->iD : 0); ?>">
                    <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/admin/staff/view/<?= (int)$user->iD; ?>#tab-docs">
                    <div class="modal-header bg-white border-bottom">
                        <h5 class="modal-title fw-bold text-dark" id="uploadDocModalLabel"><i class="fa fa-upload text-primary me-2"></i> File Compliance Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Document Category *</label>
                            <select name="documenttype_id" class="form-select" required onchange="toggleExpiryFields(this)">
                                <option value="">-- Select Document Category --</option>
                                <?php foreach ($data['allDocTypes'] as $dt): ?>
                                    <option value="<?= $dt->iD; ?>" data-expiry="<?= $dt->requires_expiry ? '1' : '0'; ?>"><?= htmlspecialchars($dt->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Document Title *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g., Valid Class 4 Driver's Licence Scan" required>
                        </div>
                        <div class="row g-2 mb-3" id="expiryFieldsDiv" style="display: none;">
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
                            <small class="text-muted">Accepted formats: PDF, JPG, PNG, DOC, DOCX (Max 10MB)</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">Upload & File</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: AUDIT & VERIFY DOCUMENT -->
    <div class="modal fade" id="verifyDocModal" tabindex="-1" aria-labelledby="verifyDocModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <form action="<?= $siteConfig->siteUrl; ?>/admin/staff/documents/verify" method="POST">
                    <input type="hidden" name="staffdocument_id" id="verify_doc_id" value="">
                    <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/admin/staff/view/<?= (int)$user->iD; ?>#tab-docs">
                    <div class="modal-header bg-white border-bottom">
                        <h5 class="modal-title fw-bold text-dark" id="verifyDocModalLabel"><i class="fa fa-stamp text-primary me-2"></i> Compliance Document Audit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Document Under Audit</label>
                            <input type="text" id="verify_doc_title" class="form-control bg-light" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Audit Decision *</label>
                            <select name="verificationstatus_id" class="form-select" required>
                                <?php foreach ($data['allVerStatuses'] as $vs): ?>
                                    <option value="<?= $vs->iD; ?>" <?= ($vs->code === 'VERIFIED') ? 'selected' : ''; ?>><?= htmlspecialchars($vs->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Compliance Audit Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Confirm original inspection, registrar confirmation, or reason for decision..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Audit Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: APPLY LEAVE -->
    <div class="modal fade" id="applyLeaveModal" tabindex="-1" aria-labelledby="applyLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <form action="<?= $siteConfig->siteUrl; ?>/staff/leave/apply" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="staffprofile_id" value="<?= (int)($profile ? $profile->iD : 0); ?>">
                    <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/admin/staff/view/<?= (int)$user->iD; ?>#tab-leave">
                    <div class="modal-header bg-white border-bottom">
                        <h5 class="modal-title fw-bold text-dark" id="applyLeaveModalLabel"><i class="fa fa-calendar-plus text-success me-2"></i> Submit Leave Application</h5>
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
                                <input type="date" name="start_date" id="leave_start" class="form-control" required onchange="calculateLeaveDays()">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small">Resumption Date *</label>
                                <input type="date" name="end_date" id="leave_end" class="form-control" required onchange="calculateLeaveDays()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Working Days Requested *</label>
                            <input type="number" step="0.5" min="0.5" name="days_requested" id="leave_days" class="form-control fw-bold" placeholder="e.g., 5.0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Reason / Work Handover *</label>
                            <textarea name="reason" class="form-control" rows="2" placeholder="Brief handover note or reason for leave..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Supporting Certificate (Optional)</label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Doctor's sick certificate or exam timetable (PDF/JPG)</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success text-white px-4 fw-semibold">Submit Leave</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 4: DECIDE LEAVE APPLICATION -->
    <div class="modal fade" id="decideLeaveModal" tabindex="-1" aria-labelledby="decideLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <form action="<?= $siteConfig->siteUrl; ?>/admin/staff/leave/decide" method="POST">
                    <input type="hidden" name="staffleave_id" id="decide_leave_id" value="">
                    <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/admin/staff/view/<?= (int)$user->iD; ?>#tab-leave">
                    <div class="modal-header bg-white border-bottom">
                        <h5 class="modal-title fw-bold text-dark" id="decideLeaveModalLabel"><i class="fa fa-tasks text-primary me-2"></i> Review Leave Application</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Application Under Review</label>
                            <input type="text" id="decide_leave_desc" class="form-control bg-light" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Manager Decision *</label>
                            <select name="leavestatus_id" class="form-select" required>
                                <?php foreach ($data['allLeaveStatuses'] as $ls): ?>
                                    <option value="<?= $ls->iD; ?>" <?= ($ls->code === 'APPROVED') ? 'selected' : ''; ?>><?= htmlspecialchars($ls->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Manager Comments / Handover Stipulations</label>
                            <textarea name="decision_notes" class="form-control" rows="3" placeholder="Notes on approval, handover coverage, or reasons for decline..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Decision</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 5: LOG TIME ENTRY -->
    <div class="modal fade" id="logTimeModal" tabindex="-1" aria-labelledby="logTimeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <form action="<?= $siteConfig->siteUrl; ?>/staff/time/log" method="POST">
                    <input type="hidden" name="staffprofile_id" value="<?= (int)($profile ? $profile->iD : 0); ?>">
                    <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/admin/staff/view/<?= (int)$user->iD; ?>#tab-time">
                    <div class="modal-header bg-white border-bottom">
                        <h5 class="modal-title fw-bold text-dark" id="logTimeModalLabel"><i class="fa fa-clock text-info me-2"></i> Log Operational Time</h5>
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

    <!-- Client-side Scripts for Modals and Tabs -->
    <script>
        function toggleExpiryFields(selectElem) {
            var selectedOption = selectElem.options[selectElem.selectedIndex];
            var requiresExpiry = selectedOption.getAttribute('data-expiry') === '1';
            var div = document.getElementById('expiryFieldsDiv');
            if (div) {
                div.style.display = requiresExpiry ? 'flex' : 'none';
            }
        }

        function prepareVerifyModal(docId, docTitle) {
            document.getElementById('verify_doc_id').value = docId;
            document.getElementById('verify_doc_title').value = docTitle;
        }

        function prepareLeaveModal(leaveId, leaveType, days) {
            document.getElementById('decide_leave_id').value = leaveId;
            document.getElementById('decide_leave_desc').value = leaveType + ' (' + days + ' days)';
        }

        function calculateLeaveDays() {
            var start = document.getElementById('leave_start').value;
            var end = document.getElementById('leave_end').value;
            if (start && end) {
                var d1 = new Date(start);
                var d2 = new Date(end);
                if (d2 >= d1) {
                    var diffDays = Math.round((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
                    document.getElementById('leave_days').value = diffDays;
                }
            }
        }

        // Keep active tab on reload if URL hash is present
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

