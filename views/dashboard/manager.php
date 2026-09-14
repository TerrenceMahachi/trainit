@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'];

// Gather Service Delivery Metrics
$totalRequests = App\Models\Servicerequest::countAll();
$allClients = App\Models\Clientorganization::all();
$totalClients = count($allClients);
$activePlans = App\Models\Clientserviceplan::countAll();
$totalAssignments = App\Models\Workassignment::countAll();

// Recent Service Requests
$recentRequests = App\Models\Servicerequest::findByQuery(
    "SELECT * FROM servicerequest ORDER BY iD DESC LIMIT 8"
);
?>

<main class="portal-dashboard">
    <!-- Hero Header -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #0c2340 0%, #1d3557 100%);">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker" style="color: #60a5fa;"><i class="fa fa-handshake me-1"></i> Client Engagement &amp; Service Delivery</p>
                <h1>Service Delivery Dashboard</h1>
                <p class="portal-dashboard-intro">Welcome back, <?= htmlspecialchars($user->name ?? 'Manager'); ?>. Manage corporate client retainers, dispatch service requests, enforce SLAs, and oversee talent allocations.</p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <a href="<?= $siteConfig->siteUrl; ?>/admin/requests" class="btn btn-primary fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-tasks me-1"></i> Service Requests
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/clients" class="btn btn-dark border border-secondary text-white fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-building me-1 text-warning"></i> Client Accounts
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/clients/onboarding" class="btn btn-outline-light fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-user-plus me-1"></i> Onboarding Queue
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/service-catalogue" class="btn btn-outline-light fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-book me-1"></i> Service Catalogue
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/staff/portal" class="btn btn-warning text-dark fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-user-circle me-1"></i> Staff Portal
                </a>
            </div>
        </div>
    </section>

    <section class="portal-dashboard-body py-4">
        <div class="container">
            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: 10px;">
                    <i class="fa fa-check-circle me-2"></i> <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: 10px;">
                    <i class="fa fa-exclamation-circle me-2"></i> <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Metrics Counters Grid -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; border-left: 5px solid #3b82f6 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">Client Accounts</div>
                                    <h3 class="fw-bold mb-0 text-dark"><?= $totalClients; ?></h3>
                                    <small class="text-muted"><?= $activePlans; ?> Active Retainer Plans</small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-primary">
                                    <i class="fa fa-building fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; border-left: 5px solid #10b981 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">Service Tickets</div>
                                    <h3 class="fw-bold mb-0 text-success"><?= $totalRequests; ?></h3>
                                    <small class="text-muted">Total work requests logged</small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-success">
                                    <i class="fa fa-list-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; border-left: 5px solid #f59e0b !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">Team Allocations</div>
                                    <h3 class="fw-bold mb-0 text-warning"><?= $totalAssignments; ?></h3>
                                    <small class="text-muted">Active specialist assignments</small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-warning">
                                    <i class="fa fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; border-left: 5px solid #8b5cf6 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">Service Retainers</div>
                                    <h3 class="fw-bold mb-0 text-purple"><?= $activePlans; ?></h3>
                                    <small class="text-muted">Managed SLA contracts</small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-purple">
                                    <i class="fa fa-file-contract fa-2x" style="color: #8b5cf6;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operations Hub Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3" style="border-radius: 12px; background: #ffffff;">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #dbeafe; color: #2563eb;">
                            <i class="fa fa-tasks fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Service Requests &amp; Dispatch</h6>
                        <p class="text-muted small mb-3">Triage incoming client tickets, assign specialists, and enforce SLAs.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/requests" class="btn btn-sm btn-outline-primary fw-bold mt-auto">
                            Dispatch Desk <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3" style="border-radius: 12px; background: #ffffff;">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #dcfce7; color: #15803d;">
                            <i class="fa fa-building fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Client Organizations</h6>
                        <p class="text-muted small mb-3">Manage enterprise accounts, retainer quotas, and team rosters.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/clients" class="btn btn-sm btn-outline-success fw-bold mt-auto">
                            Client Accounts <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3" style="border-radius: 12px; background: #ffffff;">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #fef3c7; color: #d97706;">
                            <i class="fa fa-book-open fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Service Catalogue &amp; SLA</h6>
                        <p class="text-muted small mb-3">Configure standard service offerings, categories, and SLA tiers.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/service-catalogue" class="btn btn-sm btn-outline-warning text-dark fw-bold mt-auto">
                            Manage Catalogue <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3" style="border-radius: 12px; background: #ffffff;">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #e0e7ff; color: #4338ca;">
                            <i class="fa fa-user-check fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Talent Roster Review</h6>
                        <p class="text-muted small mb-3">Inspect verified apprentices and associates available for assignment.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/roster" class="btn btn-sm btn-outline-indigo fw-bold mt-auto" style="color: #4338ca; border-color: #4338ca;">
                            Browse Roster <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Active Service Requests Ledger -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-0">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fa fa-tasks me-2 text-primary"></i> Active Client Service Requests</h5>
                        <small class="text-muted">Live dispatch queue across all contracted client organizations.</small>
                    </div>
                    <a href="<?= $siteConfig->siteUrl; ?>/admin/requests" class="btn btn-sm btn-outline-dark fw-bold">
                        View All Requests <i class="fa fa-chevron-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Request #</th>
                                <th>Client Organization</th>
                                <th>Summary / Deliverable</th>
                                <th>Estimated Hours</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentRequests)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                                        No service requests found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentRequests as $req): 
                                    $clientOrg = App\Models\Clientorganization::find($req->clientorganization);
                                ?>
                                    <tr>
                                        <td class="ps-4 fw-bold font-monospace text-dark">
                                            #<?= htmlspecialchars($req->request_number ?? $req->iD); ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($clientOrg->legal_name ?? 'Client Organization'); ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($clientOrg->city ?? 'Harare'); ?></small>
                                        </td>
                                        <td>
                                            <div class="text-dark fw-semibold"><?= htmlspecialchars(substr($req->title ?? $req->description ?? 'Service Deliverable', 0, 60)); ?></div>
                                            <small class="text-muted">Submitted <?= date('d M Y', strtotime($req->reg_date ?? 'now')); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1"><?= number_format((float)($req->estimated_hours ?? 0), 1); ?> hrs</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?= $siteConfig->siteUrl; ?>/client/requests/view/<?= $req->iD; ?>" class="btn btn-sm btn-outline-primary fw-bold px-3">
                                                Workspace <i class="fa fa-arrow-right ms-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Manager Workspace Banner -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border-left: 6px solid #2563eb !important;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; background: #0c2340; color: #60a5fa; flex-shrink: 0;">
                            <i class="fa fa-user-tie fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Service Manager Workspace</h5>
                            <p class="text-muted mb-0 small">Logged in as <strong><?= htmlspecialchars($user->name ?? ''); ?></strong> &bull; Access your leave calendar, staff documents, and profile details.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= $siteConfig->siteUrl; ?>/staff/portal" class="btn btn-primary fw-bold px-3 py-2 shadow-sm">
                            <i class="fa fa-user-circle me-1"></i> Open Staff Portal
                        </a>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/clients" class="btn btn-outline-dark fw-bold px-3 py-2">
                            <i class="fa fa-building me-1"></i> Client Accounts Directory
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>
