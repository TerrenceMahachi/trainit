<?php
$title = "Service Requests Delivery Desk — " . _SITE;
$navUser = \App\Helpers\Auth::user();
?>
<?php include _BASE_PATH . '/views/partials/header.php'; ?>
<?php include _BASE_PATH . '/views/partials/nav.php'; ?>

<main class="py-4" style="background-color: #fcfbfe; min-height: 85vh;">
    <div class="container-xl">

        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted small">
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard" class="text-decoration-none" style="color: #2A114B;">Admin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Service Requests</li>
                    </ol>
                </nav>
                <h2 class="h3 fw-bold mb-0" style="color: #1C0D30;">
                    <i class="fa fa-ticket me-2" style="color: #FFCC00;"></i>Service Delivery & Dispatch Desk
                </h2>
                <p class="text-muted small mb-0">Triage client tickets, manage SLA deadlines, and dispatch vetted Associates & Apprentices.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= $siteConfig->siteUrl ?>/admin/clients" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fa fa-building me-1"></i> Client Accounts
                </a>
                <a href="<?= $siteConfig->siteUrl ?>/admin/service-catalogue" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fa fa-list-check me-1"></i> Service Catalogue
                </a>
            </div>
        </div>

        <!-- 4 KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #FF6B6B 0%, #EE5253 100%); color: #fff;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase small fw-bold" style="opacity: 0.85;">Awaiting Triage</span>
                            <h3 class="display-6 fw-bold mb-0 mt-1"><?= count($newQueue) ?></h3>
                        </div>
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fa fa-inbox fa-lg text-white"></i>
                        </div>
                    </div>
                    <div class="mt-3 small" style="opacity: 0.9;">
                        <span>New client submissions</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase text-muted small fw-bold">Active Delivery</span>
                            <h3 class="display-6 fw-bold mb-0 mt-1" style="color: #2A114B;"><?= count($activeQueue) ?></h3>
                        </div>
                        <div class="rounded-circle p-2 bg-light">
                            <i class="fa fa-person-digging fa-lg text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        <span>Work assignments in progress</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase text-muted small fw-bold">Ready / Review</span>
                            <h3 class="display-6 fw-bold mb-0 mt-1 text-warning"><?= count($resolvedQueue) ?></h3>
                        </div>
                        <div class="rounded-circle p-2 bg-light">
                            <i class="fa fa-clipboard-check fa-lg text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        <span>Deliverables awaiting sign-off</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase text-muted small fw-bold">Completed & Closed</span>
                            <h3 class="display-6 fw-bold mb-0 mt-1 text-success"><?= count($closedQueue) ?></h3>
                        </div>
                        <div class="rounded-circle p-2 bg-light">
                            <i class="fa fa-circle-check fa-lg text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        <span>Fulfilled client engagements</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Triage Queues Tabs -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 pt-3 px-4">
                <ul class="nav nav-pills gap-2 border-0" id="queueTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4 py-2 fw-bold" id="tab-active" data-bs-toggle="pill" data-bs-target="#content-active" type="button" role="tab">
                            <i class="fa fa-person-running me-1"></i> Active Work (<?= count($activeQueue) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4 py-2 fw-bold text-danger" id="tab-new" data-bs-toggle="pill" data-bs-target="#content-new" type="button" role="tab">
                            <i class="fa fa-bell me-1"></i> New Triage (<?= count($newQueue) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4 py-2 fw-bold text-warning" id="tab-review" data-bs-toggle="pill" data-bs-target="#content-review" type="button" role="tab">
                            <i class="fa fa-clock-rotate-left me-1"></i> Review & Client (<?= count($resolvedQueue) ?>)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4 py-2 fw-bold text-success" id="tab-closed" data-bs-toggle="pill" data-bs-target="#content-closed" type="button" role="tab">
                            <i class="fa fa-check-double me-1"></i> Closed (<?= count($closedQueue) ?>)
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content" id="queueTabsContent">

                    <!-- Tab 1: Active Work Queue -->
                    <div class="tab-pane fade show active" id="content-active" role="tabpanel">
                        <?php $renderTable = function($queueList, $emptyMsg) use ($siteConfig) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-muted small text-uppercase">
                                        <tr>
                                            <th class="ps-4">Ticket</th>
                                            <th>Client Organization</th>
                                            <th>Objective / Title</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Assigned Talent</th>
                                            <th class="text-end pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($queueList)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center py-5 text-muted">
                                                    <i class="fa fa-folder-open fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
                                                    <p class="mb-0"><?= $emptyMsg ?></p>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($queueList as $item): ?>
                                                <?php
                                                $req = $item['request'];
                                                $reqId = is_object($req) ? $req->iD : $req['iD'];
                                                $client = $item['client'];
                                                $priority = $item['priority'];
                                                ?>
                                                <tr>
                                                    <td class="ps-4 fw-bold font-monospace" style="color: #2A114B;">
                                                        <?= htmlspecialchars(is_object($req) ? $req->request_number : $req['request_number']) ?>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold text-dark"><?= htmlspecialchars($client ? (is_object($client) ? $client->trading_name : $client['trading_name']) : 'Client') ?></span>
                                                    </td>
                                                    <td>
                                                        <a href="<?= $siteConfig->siteUrl ?>/admin/requests/view/<?= $reqId ?>" class="fw-bold text-decoration-none" style="color: #1C0D30;">
                                                            <?= htmlspecialchars(is_object($req) ? $req->title : $req['title']) ?>
                                                        </a>
                                                        <div class="text-muted small">Target: <?= htmlspecialchars(is_object($req) ? $req->desired_due_date : $req['desired_due_date']) ?></div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-danger-subtle text-danger border">
                                                            <?= htmlspecialchars($priority ? (is_object($priority) ? $priority->name : $priority['name']) : 'Normal') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary-subtle text-primary border">
                                                            <?= htmlspecialchars($item['status_name']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-dark border">
                                                            <i class="fa fa-user me-1 text-secondary"></i><?= $item['assignments_count'] ?> Assigned
                                                        </span>
                                                    </td>
                                                    <td class="text-end pe-4">
                                                        <a href="<?= $siteConfig->siteUrl ?>/admin/requests/view/<?= $reqId ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                            Workspace <i class="fa fa-chevron-right ms-1"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php }; ?>
                        <?php $renderTable($activeQueue, "No active engagements in delivery right now."); ?>
                    </div>

                    <!-- Tab 2: New Triage Queue -->
                    <div class="tab-pane fade" id="content-new" role="tabpanel">
                        <?php $renderTable($newQueue, "No new tickets awaiting triage."); ?>
                    </div>

                    <!-- Tab 3: Review & Client Verification Queue -->
                    <div class="tab-pane fade" id="content-review" role="tabpanel">
                        <?php $renderTable($resolvedQueue, "No deliverables currently awaiting client verification."); ?>
                    </div>

                    <!-- Tab 4: Closed Engagements -->
                    <div class="tab-pane fade" id="content-closed" role="tabpanel">
                        <?php $renderTable($closedQueue, "No closed engagements archived yet."); ?>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>

<?php include _BASE_PATH . '/views/partials/footer.php'; ?>
