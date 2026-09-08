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

                <!-- Right Column: Statutory & Details -->
                <div class="col-lg-8">
                    
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

            </div>

        </div>
    </section>
</main>
