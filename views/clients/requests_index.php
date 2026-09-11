@extends('layouts.main')

<?php
global $siteConfig;
$client = $data['client'] ?? null;
$requests = $data['requests'] ?? [];
$counts = $data['counts'] ?? ['all' => 0, 'open' => 0, 'review' => 0, 'closed' => 0];
$currentFilter = $data['currentFilter'] ?? 'all';
$search = $data['search'] ?? '';
$activeTab = $data['activeTab'] ?? 'requests';
?>

<div class="container py-4 my-2">
    <?php include _BASE_PATH . '/views/clients/nav.php'; ?>

    <!-- Feedback Alerts -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            <?php if ($_GET['msg'] === 'request_submitted'): ?>
                Your service work brief has been logged successfully! Internal operations have been notified.
            <?php elseif ($_GET['msg'] === 'signed_off'): ?>
                Engagement approved and closed. Thank you for your review and rating!
            <?php else: ?>
                <?= htmlspecialchars($_GET['msg']) ?>
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fa fa-exclamation-triangle me-2"></i>
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Header Filter & Search Toolbar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center justify-content-between">
                <!-- Status Filter Pills -->
                <div class="col-lg-7">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted small fw-semibold me-1"><i class="fa fa-filter me-1"></i> Filter:</span>
                        <a href="<?= $siteConfig->siteUrl ?>/client/requests?status=all<?= $search ? '&search=' . urlencode($search) : '' ?>" 
                           class="btn btn-sm rounded-pill px-3 py-1 <?= $currentFilter === 'all' ? 'btn-dark' : 'btn-outline-secondary' ?>">
                            All <span class="badge bg-secondary ms-1"><?= $counts['all'] ?></span>
                        </a>
                        <a href="<?= $siteConfig->siteUrl ?>/client/requests?status=open<?= $search ? '&search=' . urlencode($search) : '' ?>" 
                           class="btn btn-sm rounded-pill px-3 py-1 <?= $currentFilter === 'open' ? 'btn-primary' : 'btn-outline-primary' ?>">
                            <i class="fa fa-spinner me-1"></i> Active / Open <span class="badge bg-primary ms-1"><?= $counts['open'] ?></span>
                        </a>
                        <a href="<?= $siteConfig->siteUrl ?>/client/requests?status=review<?= $search ? '&search=' . urlencode($search) : '' ?>" 
                           class="btn btn-sm rounded-pill px-3 py-1 <?= $currentFilter === 'review' ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning text-dark' ?>">
                            <i class="fa fa-check-circle me-1"></i> Ready for Review <span class="badge bg-warning text-dark ms-1"><?= $counts['review'] ?></span>
                        </a>
                        <a href="<?= $siteConfig->siteUrl ?>/client/requests?status=closed<?= $search ? '&search=' . urlencode($search) : '' ?>" 
                           class="btn btn-sm rounded-pill px-3 py-1 <?= $currentFilter === 'closed' ? 'btn-success' : 'btn-outline-success' ?>">
                            <i class="fa fa-archive me-1"></i> Signed Off <span class="badge bg-success ms-1"><?= $counts['closed'] ?></span>
                        </a>
                    </div>
                </div>

                <!-- Search & Quick Action -->
                <div class="col-lg-5">
                    <form method="GET" action="<?= $siteConfig->siteUrl ?>/client/requests" class="d-flex gap-2">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($currentFilter) ?>">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 rounded-start-pill text-muted">
                                <i class="fa fa-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0 rounded-end-pill" 
                                   placeholder="Search ticket, title, scope..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <?php if ($search): ?>
                            <a href="<?= $siteConfig->siteUrl ?>/client/requests?status=<?= htmlspecialchars($currentFilter) ?>" 
                               class="btn btn-outline-secondary rounded-pill px-3" title="Clear search">
                                <i class="fa fa-times"></i>
                            </a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-dark rounded-pill px-3">Search</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests Ledger List -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white p-4 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1" style="color: #2A114B;">
                    <i class="fa fa-list-ul me-2 text-warning"></i> Service Requests &amp; Engagements
                </h5>
                <p class="text-muted small mb-0">Showing <?= count($requests) ?> engagement(s) under <?= htmlspecialchars($client ? (is_object($client) ? $client->trading_name : $client['trading_name']) : 'Organization') ?></p>
            </div>
            <a href="<?= $siteConfig->siteUrl ?>/client/requests/new" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                <i class="fa fa-plus-circle me-1"></i> New Work Request
            </a>
        </div>

        <?php if (empty($requests)): ?>
            <div class="card-body p-5 text-center">
                <div class="rounded-circle bg-light d-inline-flex p-4 mb-3 text-muted">
                    <i class="fa fa-clipboard-list fa-3x"></i>
                </div>
                <h5 class="fw-bold text-dark">No work requests found</h5>
                <p class="text-muted small mb-4">
                    <?= $search ? 'No tickets matched your search query "' . htmlspecialchars($search) . '".' : 'There are currently no service requests under this filter.' ?>
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <?php if ($search || $currentFilter !== 'all'): ?>
                        <a href="<?= $siteConfig->siteUrl ?>/client/requests" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fa fa-redo me-1"></i> Reset Filters
                        </a>
                    <?php endif; ?>
                    <a href="<?= $siteConfig->siteUrl ?>/client/requests/new" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">
                        <i class="fa fa-plus-circle me-1"></i> Submit Work Request
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-uppercase text-muted">
                            <th class="ps-4">Ticket &amp; Title</th>
                            <th>Retainer Plan</th>
                            <th>Priority</th>
                            <th>Assigned Talent</th>
                            <th>Status</th>
                            <th>Target Date</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $item): 
                            $r = $item['request'];
                            $rId = is_object($r) ? $r->iD : $r['iD'];
                            $reqNum = htmlspecialchars(is_object($r) ? $r->request_number : $r['request_number']);
                            $title = htmlspecialchars(is_object($r) ? $r->title : $r['title']);
                            $desc = htmlspecialchars(is_object($r) ? $r->description : $r['description']);
                            $dueDate = is_object($r) ? $r->desired_due_date : $r['desired_due_date'];
                            $regDate = is_object($r) ? $r->reg_date : $r['reg_date'];
                            $statusCode = $item['status_code'];
                            $statusName = $item['status_name'];
                            $priority = $item['priority'];
                            $prioName = $priority ? htmlspecialchars(is_object($priority) ? $priority->name : $priority['name']) : 'Normal';
                            $prioCode = $priority ? (is_object($priority) ? $priority->code : $priority['code']) : 'MEDIUM';
                            $plan = $item['plan'];
                            $planName = $plan ? htmlspecialchars(is_object($plan) ? $plan->plan_name : $plan['plan_name']) : 'Standard Retainer';
                            $talCount = (int)$item['assignments_count'];
                        ?>
                            <tr>
                                <td class="ps-4 py-3" style="max-width: 320px;">
                                    <div class="d-flex align-items-start gap-2">
                                        <div>
                                            <span class="badge bg-light text-dark font-monospace border mb-1"><?= $reqNum ?></span>
                                            <a href="<?= $siteConfig->siteUrl ?>/client/requests/view/<?= $rId ?>" class="fw-bold text-decoration-none text-dark d-block text-truncate" style="max-width: 280px;" title="<?= $title ?>">
                                                <?= $title ?>
                                            </a>
                                            <small class="text-muted d-block text-truncate" style="max-width: 280px;"><?= $desc ?></small>
                                            <span class="text-muted" style="font-size: 0.75rem;">
                                                <i class="fa fa-clock me-1"></i> Logged <?= date('M d, Y', strtotime($regDate)) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border">
                                        <i class="fa fa-cubes me-1 text-secondary"></i> <?= $planName ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($prioCode === 'URGENT' || $prioCode === 'HIGH'): ?>
                                        <span class="badge bg-danger rounded-pill px-3 py-1">
                                            <i class="fa fa-bolt me-1"></i> <?= $prioName ?>
                                        </span>
                                    <?php elseif ($prioCode === 'LOW'): ?>
                                        <span class="badge bg-secondary rounded-pill px-3 py-1">
                                            <?= $prioName ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark rounded-pill px-3 py-1">
                                            <?= $prioName ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($talCount > 0): ?>
                                        <span class="badge rounded-pill bg-purple text-white px-3 py-1" style="background-color: #431E76;">
                                            <i class="fa fa-user-check me-1 text-warning"></i> <?= $talCount ?> Assigned
                                        </span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-light text-muted border px-2 py-1 small">
                                            <i class="fa fa-hourglass-half me-1"></i> In Triage
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item['is_closed'] || $statusCode === 'CLOSED'): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-1">
                                            <i class="fa fa-check-double me-1"></i> Signed Off
                                        </span>
                                    <?php elseif ($item['is_review']): ?>
                                        <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1">
                                            <i class="fa fa-clipboard-check me-1"></i> Ready for Review
                                        </span>
                                    <?php elseif ($statusCode === 'IN_PROGRESS'): ?>
                                        <span class="badge rounded-pill px-3 py-1 text-white" style="background-color: #2A114B;">
                                            <i class="fa fa-cog fa-spin me-1 text-warning"></i> In Progress
                                        </span>
                                    <?php elseif ($statusCode === 'TRIAGED'): ?>
                                        <span class="badge bg-info text-dark rounded-pill px-3 py-1">
                                            <i class="fa fa-calendar-check me-1"></i> Triaged &amp; Scheduled
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary rounded-pill px-3 py-1">
                                            <i class="fa fa-sparkles me-1"></i> New
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($dueDate): ?>
                                        <div class="small fw-semibold text-dark">
                                            <i class="fa fa-calendar me-1 text-muted"></i> <?= date('M d, Y', strtotime($dueDate)) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Standard SLA</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= $siteConfig->siteUrl ?>/client/requests/view/<?= $rId ?>" 
                                       class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-semibold">
                                        Workspace <i class="fa fa-arrow-right ms-1"></i>
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
