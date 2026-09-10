@extends('layouts.main')

<?php
global $siteConfig;
$staffRecords = $data['staffRecords'] ?? [];
$pendingInvites = $data['pendingInvites'] ?? [];
$search = $data['search'] ?? '';

// Compute Counts
$totalStaff = count($staffRecords);
$adminCount = 0;
$managerCount = 0;
$billingCount = 0;
$vettingCount = 0;

foreach ($staffRecords as $r) {
    $roleId = (int)$r['user']->role;
    if ($roleId === 1) $adminCount++;
    elseif ($roleId === 6) $managerCount++;
    elseif ($roleId === 7) $billingCount++;
    elseif ($roleId === 8) $vettingCount++;
}
?>

<main class="portal-dashboard">
    <!-- Hero Header -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container portal-dashboard-header-inner d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <p class="portal-kicker text-warning mb-1" style="font-size: 0.85rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                    <i class="fa fa-users-cog me-1"></i> Operations & Governance
                </p>
                <h1 class="h2 fw-bold text-white mb-2">Staff & Operations Directory</h1>
                <p class="portal-dashboard-intro mb-0 text-white-50" style="max-width: 650px;">
                    Manage internal team members, provision operational roles, issue statutory onboarding invitations, and generate NSSA Form P4 employee registers.
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= $siteConfig->siteUrl; ?>/admin/staff/export-p4" class="btn btn-outline-light px-3 py-2 fw-semibold rounded-3 shadow-sm">
                    <i class="fa fa-file-excel text-success me-1"></i> Export NSSA P4
                </a>
                <button type="button" class="btn btn-warning text-dark fw-bold px-3 py-2 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#inviteStaffModal">
                    <i class="fa fa-paper-plane me-1"></i> Invite Staff
                </button>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/staff/create" class="btn btn-primary px-3 py-2 fw-bold rounded-3 shadow-sm" style="background: #FFCC00; border-color: #FFCC00; color: #1C0D30;">
                    <i class="fa fa-user-plus me-1"></i> Register Staff
                </a>
            </div>
        </div>
    </section>

    <section class="portal-dashboard-body py-4">
        <div class="container">

            <!-- KPI Metric Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #2A114B !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold text-uppercase">Total Staff</span>
                                <i class="fa fa-id-badge text-purple fa-lg" style="color: #2A114B;"></i>
                            </div>
                            <h2 class="h3 fw-bold mb-1 text-dark"><?= $totalStaff; ?></h2>
                            <small class="text-muted">Registered internal personnel</small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #007bff !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold text-uppercase">Administrators</span>
                                <i class="fa fa-shield-alt text-primary fa-lg"></i>
                            </div>
                            <h2 class="h3 fw-bold mb-1 text-dark"><?= $adminCount; ?></h2>
                            <small class="text-muted">Command & Governance</small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #28a745 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold text-uppercase">Service & Vetting</span>
                                <i class="fa fa-user-check text-success fa-lg"></i>
                            </div>
                            <h2 class="h3 fw-bold mb-1 text-dark"><?= ($managerCount + $vettingCount); ?></h2>
                            <small class="text-muted"><?= $managerCount; ?> Managers &bull; <?= $vettingCount; ?> Vetting Officers</small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #ffc107 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold text-uppercase">Pending Invites</span>
                                <i class="fa fa-envelope-open-text text-warning fa-lg"></i>
                            </div>
                            <h2 class="h3 fw-bold mb-1 text-dark"><?= count($pendingInvites); ?></h2>
                            <small class="text-muted">Awaiting onboarding completion</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <form method="GET" action="<?= $siteConfig->siteUrl; ?>/admin/staff" class="row g-2 align-items-center">
                        <div class="col-md-8 col-lg-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search staff by name, email, or job title..." value="<?= htmlspecialchars($search, ENT_QUOTES); ?>">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3 d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100 fw-semibold" style="background: #2A114B;">Filter</button>
                            <?php if ($search !== ''): ?>
                                <a href="<?= $siteConfig->siteUrl; ?>/admin/staff" class="btn btn-outline-secondary">Clear</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Staff Members Table -->
            <div class="card border-0 shadow-sm mb-5" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-address-book text-primary me-2"></i> Active Staff Members</h5>
                    <span class="badge bg-secondary"><?= count($staffRecords); ?> Personnel</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Staff Member</th>
                                <th>Role</th>
                                <th>Job Title / Department</th>
                                <th>Works Number</th>
                                <th>NSSA SSR #</th>
                                <th>Station</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($staffRecords)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fa fa-user-slash fa-3x mb-3 text-secondary"></i>
                                        <p class="mb-0">No staff records found matching your query.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($staffRecords as $item): ?>
                                    <?php
                                    $u = $item['user'];
                                    $p = $item['profile'];
                                    $role = $item['role'];
                                    $roleName = $role ? $role->name : 'Staff';
                                    
                                    // Badge color
                                    $badgeClass = 'bg-primary';
                                    if ($u->role == 1) $badgeClass = 'bg-dark';
                                    elseif ($u->role == 6) $badgeClass = 'bg-info text-dark';
                                    elseif ($u->role == 7) $badgeClass = 'bg-warning text-dark';
                                    elseif ($u->role == 8) $badgeClass = 'bg-success';
                                    ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle rounded-circle bg-light d-flex align-items-center justify-content-center me-3 fw-bold text-dark border" style="width: 42px; height: 42px; background-color: #F8F5FC !important; border-color: #E6DCF2 !important; color: #2A114B !important;">
                                                    <?= strtoupper(substr($u->name, 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($u->name); ?></div>
                                                    <small class="text-muted"><i class="fa fa-envelope me-1"></i><?= htmlspecialchars($u->email); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge <?= $badgeClass; ?> px-2 py-1"><?= htmlspecialchars($roleName); ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($p ? $p->job_title : 'Staff Member'); ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($p ? $p->department : 'Operations'); ?></small>
                                        </td>
                                        <td>
                                            <code class="text-muted fw-bold"><?= htmlspecialchars($p ? ($p->employee_number ?: 'TRN-' . str_pad($u->iD, 3, '0', STR_PAD_LEFT)) : '-'); ?></code>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($p ? ($p->ssr_number ?: 'Pending') : 'Pending'); ?></span>
                                        </td>
                                        <td>
                                            <span class="small text-muted"><?= htmlspecialchars($p ? $p->station : 'Harare HQ'); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?= $siteConfig->siteUrl; ?>/admin/staff/view/<?= $u->iD; ?>" class="btn btn-sm btn-outline-primary fw-semibold me-1">
                                                <i class="fa fa-id-card me-1"></i> Dossier
                                            </a>
                                            <a href="<?= $siteConfig->siteUrl; ?>/admin/staff/edit/<?= $u->iD; ?>" class="btn btn-sm btn-outline-secondary" title="Edit Profile">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pending Invitations Section -->
            <?php if (!empty($pendingInvites)): ?>
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-clock text-warning me-2"></i> Pending Staff Onboarding Invitations</h6>
                        <span class="badge bg-warning text-dark"><?= count($pendingInvites); ?> Awaiting Setup</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th class="ps-4">Recipient</th>
                                    <th>Work Email</th>
                                    <th>Proposed Role</th>
                                    <th>Department / Job</th>
                                    <th>Expires At</th>
                                    <th class="text-end pe-4">Direct Onboarding Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingInvites as $inv): ?>
                                    <?php
                                    $invRole = App\Models\Role::find($inv->role);
                                    $invRoleName = $invRole ? $invRole->name : 'Staff';
                                    $inviteUrl = $siteConfig->siteUrl . "/staff/onboard?token=" . urlencode($inv->token);
                                    ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($inv->name); ?></td>
                                        <td><?= htmlspecialchars($inv->email); ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($invRoleName); ?></span></td>
                                        <td><?= htmlspecialchars($inv->job_title); ?> (<?= htmlspecialchars($inv->department); ?>)</td>
                                        <td><small class="text-muted"><?= date('M d, Y H:i', strtotime($inv->expires_at)); ?></small></td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-outline-secondary copy-link-btn" data-url="<?= htmlspecialchars($inviteUrl); ?>" onclick="copyInviteLink(this)">
                                                <i class="fa fa-copy me-1"></i> Copy Link
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</main>

<!-- Invite Staff Modal -->
<div class="modal fade" id="inviteStaffModal" tabindex="-1" aria-labelledby="inviteStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header text-white" style="background: #2A114B; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title fw-bold" id="inviteStaffModalLabel"><i class="fa fa-paper-plane text-warning me-2"></i> Issue Staff Invitation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="inviteStaffForm">
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        The recipient will receive an onboarding invitation email with a secure token to set their password and complete their statutory NSSA Form P4 profile.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Staff Member's Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Sandra Ndlovu" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Official Work Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="e.g. sndlovu@tsigiro.co.zw" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Operational Staff Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="">Select Staff Role...</option>
                            <option value="1">Administrator (Command & Governance)</option>
                            <option value="6">Service Manager (Client Requests & Time Approval)</option>
                            <option value="7">Billing Officer (Billing & External Payments)</option>
                            <option value="8">Vetting Officer (Talent Vetting & Screening)</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Job Title / Designation <span class="text-danger">*</span></label>
                            <input type="text" name="job_title" class="form-control" placeholder="e.g. Senior Vetting Officer" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-dark">Department</label>
                            <select name="department" class="form-select">
                                <option value="Operations">Operations</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Finance & Billing">Finance & Billing</option>
                                <option value="Vetting & Talent">Vetting & Talent</option>
                                <option value="Executive">Executive</option>
                            </select>
                        </div>
                    </div>

                    <div id="inviteFormAlert" class="alert d-none mt-2"></div>
                </div>
                <div class="modal-footer bg-light border-0 px-4 py-3" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnSendInviteSubmit" class="btn btn-dark fw-bold px-4" style="background: #2A114B;">
                        <i class="fa fa-paper-plane me-1"></i> Dispatch Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function copyInviteLink(btn) {
    const url = btn.getAttribute('data-url');
    navigator.clipboard.writeText(url).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-check text-success me-1"></i> Copied!';
        btn.classList.replace('btn-outline-secondary', 'btn-success');
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.replace('btn-success', 'btn-outline-secondary');
        }, 2000);
    });
}

document.getElementById('inviteStaffForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const btn = document.getElementById('btnSendInviteSubmit');
    const alertBox = document.getElementById('inviteFormAlert');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';
    alertBox.className = 'alert d-none';

    const formData = new FormData(form);

    fetch('<?= $siteConfig->siteUrl; ?>/admin/staff/invite', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-paper-plane me-1"></i> Dispatch Invitation';
        
        if (data.status === 1) {
            alertBox.className = 'alert alert-success mt-2';
            alertBox.textContent = data.msg;
            form.reset();
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            alertBox.className = 'alert alert-danger mt-2';
            alertBox.textContent = data.msg || 'Error sending invitation.';
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-paper-plane me-1"></i> Dispatch Invitation';
        alertBox.className = 'alert alert-danger mt-2';
        alertBox.textContent = 'Server communication error. Please try again.';
    });
});
</script>
