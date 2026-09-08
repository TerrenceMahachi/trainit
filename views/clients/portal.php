<?php
$clientName = htmlspecialchars(is_object($client) ? $client->trading_name : $client['trading_name']);
$title = "Client Portal — {$clientName} — " . _SITE;
$navUser = \App\Helpers\Auth::user();
?>
<?php include _BASE_PATH . '/views/partials/header.php'; ?>
<?php include _BASE_PATH . '/views/partials/nav.php'; ?>

<main class="py-4" style="background-color: #fcfbfe; min-height: 85vh;">
    <div class="container-xl">

        <!-- Welcome Banner -->
        <div class="card border-0 rounded-4 p-4 mb-4 text-white shadow-sm position-relative overflow-hidden"
             style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 60%, #431E76 100%);">
            <div class="row align-items-center position-relative" style="z-index: 2;">
                <div class="col-lg-8">
                    <span class="badge rounded-pill px-3 py-1 mb-2 text-dark fw-bold" style="background-color: #FFCC00;">
                        <i class="fa fa-handshake me-1"></i> Client Partner Workspace
                    </span>
                    <h2 class="display-6 fw-bold mb-1"><?= $clientName ?></h2>
                    <p class="mb-0 text-white-50">Manage your active service retainers, submit priority service requests, and track project deliverables in real time.</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <button type="button" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#modalClientNewRequest">
                        <i class="fa fa-paper-plane me-1"></i> Submit Service Request
                    </button>
                </div>
            </div>
        </div>

        <!-- Active Retainers & Hours Meter -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0" style="color: #2A114B;">
                            <i class="fa fa-cubes me-2 text-primary"></i>Active Service Retainers
                        </h5>
                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-bold">Active Subscriptions</span>
                    </div>

                    <?php if (empty($activePlans)): ?>
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">No active retainers found. Please contact your account lead.</p>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($activePlans as $pItem): ?>
                                <?php
                                $plan = $pItem['plan'];
                                $manager = $pItem['manager'];
                                $includedHours = (float)(is_object($plan) ? $plan->included_hours : $plan['included_hours']);
                                ?>
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 bg-light h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars(is_object($plan) ? $plan->plan_name : $plan['plan_name']) ?></h6>
                                            <span class="badge bg-primary">$<?= number_format(is_object($plan) ? $plan->monthly_fee : $plan['monthly_fee'], 0) ?>/mo</span>
                                        </div>
                                        <div class="small text-muted mb-3">
                                            <?= htmlspecialchars($pItem['offering'] ? (is_object($pItem['offering']) ? $pItem['offering']->name : $pItem['offering']['name']) : 'Managed Services') ?>
                                        </div>

                                        <!-- Monthly Hours Progress -->
                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                            <span>Monthly Retainer Allowance</span>
                                            <span class="fw-bold text-dark"><?= $includedHours ?> hrs / month</span>
                                        </div>
                                        <div class="progress rounded-pill mb-3" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top small">
                                            <span class="text-muted">Account Lead:</span>
                                            <span class="fw-bold text-dark"><?= htmlspecialchars($manager ? (is_object($manager) ? $manager->name : $manager['name']) : 'Tsigiro Operations') ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Help / Support Desk -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100" style="background: linear-gradient(135deg, #F8F5FC 0%, #fff 100%);">
                    <h5 class="fw-bold mb-3" style="color: #2A114B;">
                        <i class="fa fa-headset me-2 text-warning"></i>Service Desk Support
                    </h5>
                    <p class="small text-muted mb-3">Your dedicated service managers and talent associates are on standby to triage urgent incidents and planned project milestones.</p>

                    <div class="border rounded-3 p-3 bg-white mb-3">
                        <div class="small fw-bold text-dark mb-1">Emergency Service SLA</div>
                        <div class="small text-muted"><i class="fa fa-clock me-1 text-danger"></i> <strong>Critical:</strong> 15-minute response target</div>
                        <div class="small text-muted"><i class="fa fa-clock me-1 text-warning"></i> <strong>Standard:</strong> 2-hour response target</div>
                    </div>

                    <button type="button" class="btn btn-outline-primary rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#modalClientNewRequest">
                        <i class="fa fa-plus me-1"></i> Create Service Ticket
                    </button>
                </div>
            </div>
        </div>

        <!-- Service Requests Ledger -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0" style="color: #2A114B;">
                    <i class="fa fa-ticket me-2 text-primary"></i>Service Requests & Engagements
                </h5>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalClientNewRequest">
                    <i class="fa fa-plus me-1"></i> New Request
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Ticket Ref</th>
                            <th>Engagement Title</th>
                            <th>Desired Date</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fa fa-inbox fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
                                    <p class="mb-2">No service requests active right now.</p>
                                    <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalClientNewRequest">
                                        Submit First Request
                                    </button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($requests as $r): ?>
                                <?php $reqId = is_object($r) ? $r->iD : $r['iD']; ?>
                                <tr>
                                    <td class="ps-4 fw-bold" style="color: #2A114B;">
                                        <?= htmlspecialchars(is_object($r) ? $r->request_number : $r['request_number']) ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars(is_object($r) ? $r->title : $r['title']) ?></div>
                                        <div class="text-muted small text-truncate" style="max-width: 400px;"><?= htmlspecialchars(is_object($r) ? $r->description : $r['description']) ?></div>
                                    </td>
                                    <td class="text-muted small">
                                        <i class="fa fa-calendar me-1"></i><?= htmlspecialchars(is_object($r) ? $r->desired_due_date : $r['desired_due_date']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border px-2 py-1">IN PROGRESS</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= $siteConfig->siteUrl ?>/admin/requests/view/<?= $reqId ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            View Workspace <i class="fa fa-chevron-right ms-1"></i>
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

<!-- Modal: Submit Service Request -->
<div class="modal fade" id="modalClientNewRequest" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= $siteConfig->siteUrl ?>/client/requests/submit" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="client_id" value="<?= is_object($client) ? $client->iD : $client['iD'] ?>">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" style="color: #2A114B;">
                        <i class="fa fa-paper-plane me-2 text-warning"></i>Submit New Service Request
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Select Active Retainer Plan <span class="text-danger">*</span></label>
                            <select name="service_plan_id" class="form-select rounded-3" required>
                                <?php foreach ($activePlans as $pItem): ?>
                                    <?php $pl = $pItem['plan']; ?>
                                    <option value="<?= is_object($pl) ? $pl->iD : $pl['iD'] ?>">
                                        <?= htmlspecialchars(is_object($pl) ? $pl->plan_name : $pl['plan_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Priority Urgency <span class="text-danger">*</span></label>
                            <select name="priority_id" class="form-select rounded-3" required>
                                <?php foreach ($priorities as $pri): ?>
                                    <option value="<?= is_object($pri) ? $pri->iD : $pri['iD'] ?>">
                                        <?= htmlspecialchars(is_object($pri) ? $pri->name : $pri['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Request Title / Task Objective <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Server security hardening & database optimization" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Detailed Requirements & Scope <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control rounded-3" rows="4" placeholder="Describe the outcome, specifications, credentials, or instructions..." required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Desired Delivery Date <span class="text-danger">*</span></label>
                            <input type="date" name="desired_due_date" class="form-control rounded-3" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Optional Attachment (Brief, Spec, File)</label>
                            <input type="file" name="attachment" class="form-control rounded-3">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #2A114B;">
                        <i class="fa fa-check-circle me-1"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include _BASE_PATH . '/views/partials/footer.php'; ?>
