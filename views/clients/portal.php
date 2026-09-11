@extends('layouts.main')

<?php
global $siteConfig;
$clientObj = $data['client'] ?? $client ?? null;
$clientName = htmlspecialchars($clientObj ? (is_object($clientObj) ? $clientObj->trading_name : $clientObj['trading_name']) : 'Client Workspace');
$activePlans = $data['activePlans'] ?? $activePlans ?? [];
$recentRequests = $data['recentRequests'] ?? $recentRequests ?? [];
$totalRequests = $data['totalRequests'] ?? $totalRequests ?? 0;
$openRequests = $data['openRequests'] ?? $openRequests ?? 0;
$reviewRequests = $data['reviewRequests'] ?? $reviewRequests ?? 0;
$closedRequests = $data['closedRequests'] ?? $closedRequests ?? 0;
$totalIncludedHours = $data['totalIncludedHours'] ?? $totalIncludedHours ?? 0;
$totalMonthlyFee = $data['totalMonthlyFee'] ?? $totalMonthlyFee ?? 0;
$activeTab = 'overview';
?>

<main class="py-4" style="background-color: #fcfbfe; min-height: 85vh;">
    <div class="container-xl">

        <?php include _VIEWS_PATH . '/clients/nav.php'; ?>

        <?php if (!empty($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                <i class="fa fa-check-circle me-2"></i>
                <?php if ($_GET['msg'] === 'request_submitted'): ?>
                    Your work request has been logged successfully! Our service desk is triaging your requirements.
                <?php else: ?>
                    <?= htmlspecialchars($_GET['msg']); ?>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- KPI Metrics Grid -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <span class="text-muted small d-block">Active Retainers</span>
                    <h3 class="fw-bold mb-0 mt-1" style="color: #2A114B;"><?= count($activePlans); ?></h3>
                    <span class="badge bg-primary-subtle text-primary rounded-pill mt-2 w-auto align-self-start">
                        $<?= number_format($totalMonthlyFee, 0); ?>/mo &bull; <?= $totalIncludedHours; ?> hrs
                    </span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <span class="text-muted small d-block">Active Requests</span>
                    <h3 class="fw-bold mb-0 mt-1 text-primary"><?= $openRequests; ?></h3>
                    <span class="badge bg-info-subtle text-info-emphasis rounded-pill mt-2 w-auto align-self-start">In Execution</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100" style="border-left: 4px solid #ffc107 !important;">
                    <span class="text-muted small d-block">Delivered / In Review</span>
                    <h3 class="fw-bold mb-0 mt-1 text-warning-emphasis"><?= $reviewRequests; ?></h3>
                    <span class="badge bg-warning-subtle text-dark rounded-pill mt-2 w-auto align-self-start">Awaiting Sign-off</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <span class="text-muted small d-block">Completed Engagements</span>
                    <h3 class="fw-bold mb-0 mt-1 text-success"><?= $closedRequests; ?></h3>
                    <span class="badge bg-success-subtle text-success rounded-pill mt-2 w-auto align-self-start">Archived &amp; Satisfied</span>
                </div>
            </div>
        </div>

        <!-- Active Retainers & Hours Usage Meter -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0" style="color: #2A114B;">
                            <i class="fa fa-cubes me-2 text-primary"></i>Active Service Retainers
                        </h5>
                        <a href="<?= $siteConfig->siteUrl ?>/client/plans" class="small fw-semibold text-decoration-none">
                            View All Details <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <?php if (empty($activePlans)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fa fa-layer-group fa-2x mb-2 text-secondary opacity-50"></i>
                            <p class="mb-2">No active retainers assigned to your organization profile.</p>
                            <a href="<?= $siteConfig->siteUrl ?>/contact" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                Inquire About a Retainer Plan
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($activePlans as $pItem): ?>
                                <?php
                                $plan = $pItem['plan'];
                                $manager = $pItem['manager'];
                                $incHours = (float)($pItem['included_hours'] ?? 0);
                                $planName = htmlspecialchars(is_object($plan) ? $plan->plan_name : $plan['plan_name']);
                                $offeringName = htmlspecialchars($pItem['offering'] ? (is_object($pItem['offering']) ? $pItem['offering']->name : $pItem['offering']['name']) : 'Managed Services');
                                ?>
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 bg-light h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="fw-bold mb-0 text-dark"><?= $planName ?></h6>
                                                <span class="badge bg-primary rounded-pill">$<?= number_format(is_object($plan) ? $plan->monthly_fee : $plan['monthly_fee'], 0) ?>/mo</span>
                                            </div>
                                            <div class="small text-muted mb-3"><?= $offeringName ?></div>

                                            <div class="d-flex justify-content-between small text-muted mb-1">
                                                <span>Monthly Retainer Hours</span>
                                                <span class="fw-bold text-dark"><?= $incHours ?> hrs / month</span>
                                            </div>
                                            <div class="progress rounded-pill mb-3" style="height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 30%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top small">
                                            <span class="text-muted">Account Manager:</span>
                                            <span class="fw-bold text-dark"><?= htmlspecialchars($manager ? (is_object($manager) ? $manager->name : $manager['name']) : 'Tsigiro Operations') ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Service Desk Help / SLA Target -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white" style="background: linear-gradient(135deg, #F8F5FC 0%, #ffffff 100%);">
                    <h5 class="fw-bold mb-3" style="color: #2A114B;">
                        <i class="fa fa-headset me-2 text-warning"></i>Service Desk Support
                    </h5>
                    <p class="small text-muted mb-3">Our vetted talent practitioners, senior associates, and delivery managers are assigned on-demand based on your service requirements.</p>

                    <div class="border rounded-3 p-3 bg-white mb-3 shadow-sm">
                        <div class="small fw-bold text-dark mb-1">SLA Turnaround Commitments</div>
                        <div class="small text-muted mb-1"><i class="fa fa-bolt me-1 text-danger"></i> <strong>Critical Incidents:</strong> 15-minute response target</div>
                        <div class="small text-muted"><i class="fa fa-clock me-1 text-warning"></i> <strong>Standard Tasks:</strong> 24-hour sprint allocation</div>
                    </div>

                    <a href="<?= $siteConfig->siteUrl ?>/client/requests/new" class="btn btn-warning text-dark fw-bold rounded-pill w-100 shadow-sm">
                        <i class="fa fa-paper-plane me-1"></i> Submit Work Request
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Service Requests Ledger -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0" style="color: #2A114B;">
                        <i class="fa fa-ticket me-2 text-primary"></i>Recent Service Engagements
                    </h5>
                    <small class="text-muted">Showing latest 5 work briefs submitted by your organization.</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= $siteConfig->siteUrl ?>/client/requests" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        View All (<?= $totalRequests ?>)
                    </a>
                    <a href="<?= $siteConfig->siteUrl ?>/client/requests/new" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="fa fa-plus me-1"></i> New Request
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Ticket Ref</th>
                            <th>Engagement Title</th>
                            <th>Priority</th>
                            <th>Target Due</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentRequests)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa fa-inbox fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
                                    <p class="mb-2">No service requests logged yet.</p>
                                    <a href="<?= $siteConfig->siteUrl ?>/client/requests/new" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold text-dark shadow-sm">
                                        Submit First Request
                                    </a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentRequests as $item): ?>
                                <?php
                                $r = $item['request'];
                                $reqId = is_object($r) ? $r->iD : $r['iD'];
                                $statusCode = $item['status_code'];
                                $statusName = $item['status_name'];
                                $priority = $item['priority'];
                                $priCode = $priority ? (is_object($priority) ? $priority->code : $priority['code']) : 'MEDIUM';

                                $badgeClass = 'bg-secondary';
                                if ($statusCode === 'NEW') $badgeClass = 'bg-primary';
                                elseif ($statusCode === 'TRIAGED') $badgeClass = 'bg-info text-dark';
                                elseif ($statusCode === 'IN_PROGRESS') $badgeClass = 'bg-warning text-dark';
                                elseif ($statusCode === 'RESOLVED' || $statusCode === 'WAITING_CLIENT') $badgeClass = 'bg-danger text-white';
                                elseif ($statusCode === 'CLOSED') $badgeClass = 'bg-success';

                                $priClass = 'bg-secondary';
                                if ($priCode === 'URGENT') $priClass = 'bg-danger text-white';
                                elseif ($priCode === 'HIGH') $priClass = 'bg-warning text-dark';
                                elseif ($priCode === 'MEDIUM') $priClass = 'bg-info text-dark';
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold" style="color: #2A114B;">
                                        <a href="<?= $siteConfig->siteUrl ?>/client/requests/view/<?= $reqId ?>" class="text-decoration-none fw-bold" style="color: #2A114B;">
                                            <?= htmlspecialchars(is_object($r) ? $r->request_number : $r['request_number']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars(is_object($r) ? $r->title : $r['title']) ?></div>
                                        <div class="text-muted small text-truncate" style="max-width: 420px;"><?= htmlspecialchars(is_object($r) ? $r->description : $r['description']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge <?= $priClass ?> rounded-pill px-2 py-1">
                                            <?= htmlspecialchars($priority ? (is_object($priority) ? $priority->name : $priority['name']) : 'Normal') ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <i class="fa fa-calendar me-1 text-primary"></i><?= htmlspecialchars(is_object($r) ? $r->desired_due_date : $r['desired_due_date']) ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $badgeClass ?> rounded-pill px-2 py-1">
                                            <?= htmlspecialchars($statusName) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= $siteConfig->siteUrl ?>/client/requests/view/<?= $reqId ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fa fa-folder-open me-1"></i> Open Workspace
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>
