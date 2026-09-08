<?php
$cName = htmlspecialchars(is_object($client) ? $client->trading_name : $client['trading_name']);
$title = "{$cName} — Client Dossier & Retainers — " . _SITE;
$navUser = \App\Helpers\Auth::user();
$cId = is_object($client) ? $client->iD : $client['iD'];
?>
<?php include _BASE_PATH . '/views/partials/header.php'; ?>
<?php include _BASE_PATH . '/views/partials/nav.php'; ?>

<main class="py-4" style="background-color: #fcfbfe; min-height: 85vh;">
    <div class="container-xl">

        <!-- Breadcrumbs & Actions -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted small">
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard" class="text-decoration-none" style="color: #2A114B;">Admin</a></li>
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/admin/clients" class="text-decoration-none" style="color: #2A114B;">Clients</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $cName ?></li>
                    </ol>
                </nav>
                <h2 class="h3 fw-bold mb-0" style="color: #1C0D30;">
                    <i class="fa fa-building me-2" style="color: #FFCC00;"></i><?= $cName ?>
                </h2>
                <div class="text-muted small"><?= htmlspecialchars(is_object($client) ? $client->legal_name : $client['legal_name']) ?> &bull; Ref: <?= htmlspecialchars(is_object($client) ? $client->registration_number : $client['registration_number']) ?></div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= $siteConfig->siteUrl ?>/admin/clients" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fa fa-arrow-left me-1"></i> All Clients
                </a>
                <button type="button" class="btn text-white rounded-pill px-3 shadow-sm" style="background-color: #2A114B;" data-bs-toggle="modal" data-bs-target="#modalAssignPlan">
                    <i class="fa fa-plus-circle me-1"></i> Subscribe Retainer Plan
                </button>
            </div>
        </div>

        <?php if (!empty($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                <i class="fa fa-check-circle me-2"></i>
                <?php if ($_GET['msg'] === 'plan_assigned'): ?>Retainer plan successfully assigned to client.<?php endif; ?>
                <?php if ($_GET['msg'] === 'plan_terminated'): ?>Retainer plan deactivated successfully.<?php endif; ?>
                <?php if ($_GET['msg'] === 'client_created'): ?>Client organization created successfully.<?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Column: Company Details & Billing Info -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                    <h5 class="fw-bold mb-3" style="color: #2A114B;">
                        <i class="fa fa-circle-info me-2 text-primary"></i>Company Profile
                    </h5>
                    <div class="list-group list-group-flush small">
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Legal Entity</span>
                            <span class="fw-bold text-end"><?= htmlspecialchars(is_object($client) ? $client->legal_name : $client['legal_name']) ?></span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Trading Name</span>
                            <span class="fw-bold"><?= htmlspecialchars(is_object($client) ? $client->trading_name : $client['trading_name']) ?></span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Reg Number</span>
                            <span class="badge bg-light text-dark border"><?= htmlspecialchars(is_object($client) ? $client->registration_number : $client['registration_number']) ?></span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Tax ID (TIN)</span>
                            <span class="fw-bold"><?= htmlspecialchars(is_object($client) ? $client->tax_number : $client['tax_number'] ?: 'N/A') ?></span>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Billing Email</span>
                            <a href="mailto:<?= htmlspecialchars(is_object($client) ? $client->billing_email : $client['billing_email']) ?>" class="fw-bold text-decoration-none text-primary">
                                <?= htmlspecialchars(is_object($client) ? $client->billing_email : $client['billing_email']) ?>
                            </a>
                        </div>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                            <span class="text-muted">Primary Phone</span>
                            <span class="fw-bold"><?= htmlspecialchars(is_object($client) ? $client->primary_phone : $client['primary_phone'] ?: 'N/A') ?></span>
                        </div>
                        <div class="list-group-item px-0 py-2">
                            <span class="text-muted d-block mb-1">Physical Address</span>
                            <span class="fw-bold"><?= htmlspecialchars(is_object($client) ? $client->address : $client['address'] ?: 'N/A') ?>, <?= htmlspecialchars(is_object($client) ? $client->city : $client['city']) ?>, <?= htmlspecialchars(is_object($client) ? $client->country : $client['country']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Client Team Members -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0" style="color: #2A114B;">
                            <i class="fa fa-users me-2 text-warning"></i>Team Members
                        </h5>
                        <span class="badge bg-secondary-subtle text-dark"><?= count($members) ?> Members</span>
                    </div>
                    <?php if (empty($members)): ?>
                        <p class="text-muted small mb-0">No dedicated client member accounts linked yet. Contact billing officer to provision accounts.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush small">
                            <?php foreach ($members as $m): ?>
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($m['user'] ? (is_object($m['user']) ? $m['user']->name : $m['user']['name']) : 'User #' . (is_object($m['membership']) ? $m['membership']->user : $m['membership']['user'])) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($m['user'] ? (is_object($m['user']) ? $m['user']->email : $m['user']['email']) : '') ?></div>
                                    </div>
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($m['role'] ? (is_object($m['role']) ? $m['role']->name : $m['role']['name']) : 'Member') ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Subscribed Retainer Plans & Service History -->
            <div class="col-lg-8">
                <!-- Retainers Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0" style="color: #2A114B;">
                            <i class="fa fa-cubes me-2 text-primary"></i>Service Retainer Subscriptions
                        </h5>
                        <button type="button" class="btn btn-sm text-white rounded-pill px-3" style="background-color: #2A114B;" data-bs-toggle="modal" data-bs-target="#modalAssignPlan">
                            <i class="fa fa-plus me-1"></i> Add Retainer
                        </button>
                    </div>
                    <div class="p-4 pt-0">
                        <?php if (empty($plans)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fa fa-file-contract fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
                                <p class="mb-2">No service plans configured for this client yet.</p>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalAssignPlan">
                                    Subscribe First Retainer Plan
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($plans as $pData): ?>
                                    <?php
                                    $p = $pData['plan'];
                                    $pId = is_object($p) ? $p->iD : $p['iD'];
                                    $isTerm = $pData['is_terminated'];
                                    ?>
                                    <div class="col-12">
                                        <div class="border rounded-4 p-3 <?= $isTerm ? 'bg-light opacity-75' : 'bg-white shadow-sm' ?>">
                                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-2">
                                                <div>
                                                    <span class="badge <?= $isTerm ? 'bg-secondary' : 'bg-success' ?> me-2">
                                                        <?= $isTerm ? 'TERMINATED' : 'ACTIVE RETAINER' ?>
                                                    </span>
                                                    <span class="fw-bold h5 mb-0 text-dark"><?= htmlspecialchars(is_object($p) ? $p->plan_name : $p['plan_name']) ?></span>
                                                </div>
                                                <div class="d-flex align-items-baseline gap-1">
                                                    <span class="h4 fw-bold mb-0 text-success">$<?= number_format(is_object($p) ? $p->monthly_fee : $p['monthly_fee'], 2) ?></span>
                                                    <span class="text-muted small">/month</span>
                                                </div>
                                            </div>

                                            <div class="row g-2 text-muted small mt-2 pt-2 border-top">
                                                <div class="col-sm-6 col-md-3">
                                                    <span class="d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Offering</span>
                                                    <span class="text-dark fw-bold"><?= htmlspecialchars($pData['offering'] ? (is_object($pData['offering']) ? $pData['offering']->name : $pData['offering']['name']) : 'General') ?></span>
                                                </div>
                                                <div class="col-sm-6 col-md-3">
                                                    <span class="d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Included Hours</span>
                                                    <span class="text-dark fw-bold"><?= number_format(is_object($p) ? $p->included_hours : $p['included_hours'], 1) ?> hrs/mo</span>
                                                </div>
                                                <div class="col-sm-6 col-md-3">
                                                    <span class="d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Excess Rates</span>
                                                    <span class="text-dark">Assoc: $<?= number_format(is_object($p) ? $p->associate_rate : $p['associate_rate'], 0) ?> | Appr: $<?= number_format(is_object($p) ? $p->apprentice_rate : $p['apprentice_rate'], 0) ?></span>
                                                </div>
                                                <div class="col-sm-6 col-md-3">
                                                    <span class="d-block text-uppercase fw-bold" style="font-size: 0.7rem;">Service Manager</span>
                                                    <span class="text-dark fw-bold"><?= htmlspecialchars($pData['manager'] ? (is_object($pData['manager']) ? $pData['manager']->name : $pData['manager']['name']) : 'Lead Manager') ?></span>
                                                </div>
                                            </div>

                                            <?php if ($isTerm): ?>
                                                <div class="mt-2 alert alert-warning py-1 px-3 mb-0 small rounded-3">
                                                    <i class="fa fa-ban me-1"></i> Terminated on <?= htmlspecialchars($pData['termination']->end_date ?? '') ?> &bull; <?= htmlspecialchars($pData['termination']->reason ?? '') ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="mt-3 text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTerminatePlan_<?= $pId ?>">
                                                        <i class="fa fa-power-off me-1"></i> Terminate Retainer
                                                    </button>
                                                </div>

                                                <!-- Modal: Terminate Plan -->
                                                <div class="modal fade" id="modalTerminatePlan_<?= $pId ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content rounded-4 border-0 shadow">
                                                            <form action="<?= $siteConfig->siteUrl ?>/admin/clients/terminate-plan" method="POST">
                                                                <input type="hidden" name="plan_id" value="<?= $pId ?>">
                                                                <input type="hidden" name="client_id" value="<?= $cId ?>">
                                                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                                                    <h5 class="modal-title fw-bold text-danger">
                                                                        <i class="fa fa-triangle-exclamation me-2"></i>Terminate Retainer Plan
                                                                    </h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body px-4 py-3">
                                                                    <p class="small text-muted mb-3">You are deactivating <strong><?= htmlspecialchars(is_object($p) ? $p->plan_name : $p['plan_name']) ?></strong>. This is recorded as an immutable zero-null termination event in the database.</p>
                                                                    <div class="mb-3">
                                                                        <label class="form-label small fw-bold">Effective End Date</label>
                                                                        <input type="date" name="end_date" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label small fw-bold">Reason for Cancellation</label>
                                                                        <textarea name="reason" class="form-control rounded-3" rows="2" placeholder="e.g. Replaced with tier-2 retainer, or contract non-renewal" required></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                                                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-danger rounded-pill px-4">Confirm Termination</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Service Requests Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0" style="color: #2A114B;">
                            <i class="fa fa-ticket me-2 text-warning"></i>Recent Service Requests
                        </h5>
                        <a href="<?= $siteConfig->siteUrl ?>/admin/requests" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            View All Requests
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Ticket</th>
                                    <th>Title</th>
                                    <th>Desired Date</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($requests)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            No requests logged for this client yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($requests as $req): ?>
                                        <?php $rId = is_object($req) ? $req->iD : $req['iD']; ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark">
                                                <?= htmlspecialchars(is_object($req) ? $req->request_number : $req['request_number']) ?>
                                            </td>
                                            <td><?= htmlspecialchars(is_object($req) ? $req->title : $req['title']) ?></td>
                                            <td class="text-muted small"><?= htmlspecialchars(is_object($req) ? $req->desired_due_date : $req['desired_due_date']) ?></td>
                                            <td><span class="badge bg-primary-subtle text-primary border">ACTIVE</span></td>
                                            <td class="text-end pe-4">
                                                <a href="<?= $siteConfig->siteUrl ?>/admin/requests/view/<?= $rId ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    Workspace
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
        </div>

    </div>
</main>

<!-- Modal: Assign Retainer Plan -->
<div class="modal fade" id="modalAssignPlan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= $siteConfig->siteUrl ?>/admin/clients/assign-plan" method="POST">
                <input type="hidden" name="client_id" value="<?= $cId ?>">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" style="color: #2A114B;">
                        <i class="fa fa-file-signature me-2 text-warning"></i>Subscribe New Retainer Plan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Plan Title <span class="text-danger">*</span></label>
                            <input type="text" name="plan_name" class="form-control rounded-3" placeholder="e.g. IT Cloud & Infrastructure Care Retainer" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Service Offering <span class="text-danger">*</span></label>
                            <select name="service_offering_id" class="form-select rounded-3" required>
                                <?php foreach ($offerings as $off): ?>
                                    <option value="<?= is_object($off) ? $off->iD : $off['iD'] ?>">
                                        <?= htmlspecialchars(is_object($off) ? $off->name : $off['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Monthly Retainer Fee ($ USD) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="monthly_fee" class="form-control rounded-3" value="1200.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Included Monthly Hours <span class="text-danger">*</span></label>
                            <input type="number" step="0.5" name="included_hours" class="form-control rounded-3" value="40.0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Billing Cycle Day</label>
                            <input type="number" min="1" max="28" name="billing_cycle_day" class="form-control rounded-3" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Associate Excess Hourly Rate ($ USD)</label>
                            <input type="number" step="0.5" name="associate_rate" class="form-control rounded-3" value="35.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Apprentice Excess Hourly Rate ($ USD)</label>
                            <input type="number" step="0.5" name="apprentice_rate" class="form-control rounded-3" value="18.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Assigned Service Manager <span class="text-danger">*</span></label>
                            <select name="service_manager" class="form-select rounded-3">
                                <?php foreach ($staffUsers as $su): ?>
                                    <option value="<?= is_object($su) ? $su->iD : $su['iD'] ?>">
                                        <?= htmlspecialchars(is_object($su) ? $su->name : $su['name']) ?> (<?= htmlspecialchars(is_object($su) ? $su->email : $su['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Excess Hours Policy</label>
                            <select name="excess_policy_id" class="form-select rounded-3">
                                <?php foreach ($excessPolicies as $ep): ?>
                                    <option value="<?= is_object($ep) ? $ep->iD : $ep['iD'] ?>">
                                        <?= htmlspecialchars(is_object($ep) ? $ep->name : $ep['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #2A114B;">
                        <i class="fa fa-check me-1"></i> Subscribe Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include _BASE_PATH . '/views/partials/footer.php'; ?>
