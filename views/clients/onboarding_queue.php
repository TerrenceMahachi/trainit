@extends('layouts.main')

<?php
global $siteConfig;
$requests       = $data['requests'] ?? [];
$currentFilter  = $data['currentFilter'] ?? 'pending';
$pendingCount   = $data['pendingCount'] ?? 0;
$approvedCount  = $data['approvedCount'] ?? 0;
$rejectedCount  = $data['rejectedCount'] ?? 0;
$user           = $data['user'] ?? null;
?>

<main class="portal-dashboard">
    <!-- Header -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <p class="portal-kicker text-warning mb-1" style="font-size: 0.85rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                    <i class="fa fa-handshake me-1"></i> Corporate Partnerships &amp; Retainers
                </p>
                <h1 class="h2 fw-bold text-white mb-2">Client Self-Onboarding Approvals</h1>
                <p class="mb-0 text-white-50" style="max-width: 650px;">
                    Review prospective client organization intake requests, verify corporate records, and provision accounts with 1-click execution.
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= $siteConfig->siteUrl; ?>/admin/clients" class="btn btn-outline-light rounded-pill px-3">
                    <i class="fa fa-building me-1"></i> Client Accounts
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/clients/onboard" class="btn btn-warning text-dark fw-bold rounded-pill px-3" target="_blank">
                    <i class="fa fa-arrow-up-right-from-square me-1"></i> Public Form
                </a>
            </div>
        </div>
    </section>

    <!-- Body -->
    <section class="py-4">
        <div class="container">

            <!-- Filter Tabs & Stats -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="btn-group rounded-pill p-1 bg-light">
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/clients/onboarding?filter=pending" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold <?= $currentFilter === 'pending' ? 'btn-warning text-dark' : 'text-muted'; ?>">
                            Pending Verification (<?= $pendingCount; ?>)
                        </a>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/clients/onboarding?filter=approved" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold <?= $currentFilter === 'approved' ? 'btn-success' : 'text-muted'; ?>">
                            Approved (<?= $approvedCount; ?>)
                        </a>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/clients/onboarding?filter=rejected" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold <?= $currentFilter === 'rejected' ? 'btn-danger' : 'text-muted'; ?>">
                            Rejected (<?= $rejectedCount; ?>)
                        </a>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/clients/onboarding?filter=all" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold <?= $currentFilter === 'all' ? 'btn-dark' : 'text-muted'; ?>">
                            All
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <?php if (empty($requests)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                    <i class="fa fa-building fa-3x text-secondary opacity-50 mb-3"></i>
                    <h5 class="fw-bold text-dark">No Client Onboarding Requests</h5>
                    <p class="text-muted mb-0 small">There are currently no requests in this status view.</p>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>#ID</th>
                                    <th>Company / Entity</th>
                                    <th>Primary Contact</th>
                                    <th>Service &amp; Tier</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $r): 
                                    $sector = $r->sectortype();
                                    $offering = $r->serviceoffering();
                                    $model = $r->engagementmodel();
                                    $statusBadge = match($r->status) {
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-warning text-dark',
                                    };
                                ?>
                                    <tr>
                                        <td class="fw-bold text-muted">#REQ-<?= str_pad((string)$r->iD, 4, '0', STR_PAD_LEFT); ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($r->company_name); ?></div>
                                            <div class="small text-muted">
                                                <?= htmlspecialchars($sector ? $sector->name : 'Corporate'); ?> &bull; 
                                                <?= htmlspecialchars($r->city . ', ' . $r->country); ?>
                                                <?php if ($r->tax_number): ?>
                                                    &bull; <code class="small"><?= htmlspecialchars($r->tax_number); ?></code>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($r->contact_name); ?></div>
                                            <div class="small text-muted">
                                                <a href="mailto:<?= htmlspecialchars($r->contact_email); ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($r->contact_email); ?></a>
                                                &bull; <?= htmlspecialchars($r->contact_phone); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($offering): ?>
                                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($offering->name); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted">General Retainer</span>
                                            <?php endif; ?>
                                            <div class="small text-muted mt-1"><?= (int)$r->estimated_monthly_hours; ?> hrs/mo &bull; <?= htmlspecialchars($r->currency_preference); ?></div>
                                        </td>
                                        <td>
                                            <span class="badge <?= $statusBadge; ?> text-capitalize px-2 py-1">
                                                <?= htmlspecialchars($r->status); ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted">
                                            <?= date('M j, Y', strtotime($r->reg_date)); ?>
                                        </td>
                                        <td class="text-end">
                                            <?php if ($r->status === 'pending'): ?>
                                                <button class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm"
                                                        onclick="approveClientOnboarding(<?= $r->iD; ?>, '<?= htmlspecialchars(addslashes($r->company_name)); ?>')">
                                                    <i class="fa fa-check me-1"></i> Approve &amp; Provision
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger rounded-pill px-2"
                                                        onclick="rejectClientOnboarding(<?= $r->iD; ?>, '<?= htmlspecialchars(addslashes($r->company_name)); ?>')">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            <?php elseif ($r->status === 'approved'): ?>
                                                <?php if ($r->provisioned_clientorganization): ?>
                                                    <a href="<?= $siteConfig->siteUrl; ?>/admin/clients/view/<?= $r->provisioned_clientorganization; ?>" 
                                                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                        <i class="fa fa-building me-1"></i> View Client 360&deg;
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="small text-muted fst-italic">Rejected</span>
                                            <?php endif; ?>
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

<script>
function approveClientOnboarding(id, company) {
    if (!confirm('Approve and provision client organization "' + company + '"?\nThis will create corporate membership, generate temporary credentials, and dispatch the welcome email.')) {
        return;
    }
    const notes = prompt('Enter optional approval notes or instructions:', 'Corporate identity and tax credentials verified.');
    if (notes === null) return;

    const fd = new FormData();
    fd.append('review_notes', notes);

    fetch('<?= $siteConfig->siteUrl; ?>/admin/clients/onboarding/' + id + '/approve', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        alert(res.msg);
        if (res.status === 1) {
            window.location.reload();
        }
    });
}

function rejectClientOnboarding(id, company) {
    const reason = prompt('Please enter the rejection reason to notify ' + company + ':', 'Corporate tax documentation could not be verified.');
    if (!reason) return;

    const fd = new FormData();
    fd.append('review_notes', reason);

    fetch('<?= $siteConfig->siteUrl; ?>/admin/clients/onboarding/' + id + '/reject', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        alert(res.msg);
        if (res.status === 1) {
            window.location.reload();
        }
    });
}
</script>
