<?php
$title = "Client Organizations & Service Retainers — " . _SITE;
$navUser = \App\Helpers\Auth::user();
?>
<?php include _BASE_PATH . '/views/partials/header.php'; ?>
<?php include _BASE_PATH . '/views/partials/nav.php'; ?>

<main class="py-4" style="background-color: #fcfbfe; min-height: 85vh;">
    <div class="container-xl">

        <!-- Top Header & Breadcrumbs -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted small">
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard" class="text-decoration-none" style="color: #2A114B;">Admin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Client Accounts</li>
                    </ol>
                </nav>
                <h2 class="h3 fw-bold mb-0" style="color: #1C0D30;">
                    <i class="fa fa-building me-2" style="color: #FFCC00;"></i>Client Organizations & Retainers
                </h2>
                <p class="text-muted small mb-0">Manage corporate client partnerships, monthly retainers, and assigned service managers.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= $siteConfig->siteUrl ?>/admin/service-catalogue" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fa fa-list-check me-1"></i> Service Catalogue
                </a>
                <button type="button" class="btn text-white rounded-pill px-3 shadow-sm" style="background-color: #2A114B;" data-bs-toggle="modal" data-bs-target="#modalAddClient">
                    <i class="fa fa-plus-circle me-1"></i> Register Client
                </button>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #2A114B 0%, #431E76 100%); color: #fff;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase small fw-bold" style="letter-spacing: 0.5px; opacity: 0.8;">Total Clients</span>
                            <h3 class="display-6 fw-bold mb-0 mt-1"><?= count($clients) ?></h3>
                        </div>
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.15);">
                            <i class="fa fa-city fa-lg text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3 small" style="opacity: 0.9;">
                        <span>Active corporate contracts</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase text-muted small fw-bold">Active Plans</span>
                            <h3 class="display-6 fw-bold mb-0 mt-1" style="color: #2A114B;"><?= count($plans) ?></h3>
                        </div>
                        <div class="rounded-circle p-2 bg-light">
                            <i class="fa fa-shield-halved fa-lg text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        <span>Retainers in delivery</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase text-muted small fw-bold">Monthly Retainers</span>
                            <h3 class="display-6 fw-bold mb-0 mt-1 text-success">$<?= number_format($totalMonthlyRevenue, 2) ?></h3>
                        </div>
                        <div class="rounded-circle p-2 bg-light">
                            <i class="fa fa-file-invoice-dollar fa-lg text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        <span>Base recurring revenue</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase text-muted small fw-bold">Client Self-Service</span>
                            <h3 class="display-6 fw-bold mb-0 mt-1" style="color: #431E76;">Active</h3>
                        </div>
                        <div class="rounded-circle p-2 bg-light">
                            <i class="fa fa-users-gear fa-lg text-info"></i>
                        </div>
                    </div>
                    <div class="mt-3 small">
                        <a href="<?= $siteConfig->siteUrl ?>/client/portal" target="_blank" class="text-decoration-none fw-bold small text-primary">
                            Preview Portal <i class="fa fa-external-link-alt ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clients Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0" style="color: #2A114B;">Client Accounts Directory</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Organization</th>
                            <th>Registration / Tax</th>
                            <th>Billing Email</th>
                            <th>Location</th>
                            <th>Active Plans</th>
                            <th>Monthly Value</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clients)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa fa-building-circle-exclamation fa-3x mb-3 text-secondary" style="opacity: 0.4;"></i>
                                    <p class="mb-0">No client organizations registered yet.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clients as $c): ?>
                                <?php
                                $cId = is_object($c) ? $c->iD : $c['iD'];
                                $stats = $clientStats[$cId] ?? ['plans_count' => 0, 'requests_count' => 0, 'members_count' => 0, 'monthly_total' => 0];
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-3 p-2 me-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 40px; height: 40px; background-color: #2A114B;">
                                                <?= strtoupper(substr(is_object($c) ? $c->trading_name : $c['trading_name'], 0, 2)) ?>
                                            </div>
                                            <div>
                                                <a href="<?= $siteConfig->siteUrl ?>/admin/clients/view/<?= $cId ?>" class="fw-bold text-decoration-none" style="color: #1C0D30;">
                                                    <?= htmlspecialchars(is_object($c) ? $c->trading_name : $c['trading_name']) ?>
                                                </a>
                                                <div class="text-muted small"><?= htmlspecialchars(is_object($c) ? $c->legal_name : $c['legal_name']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars(is_object($c) ? $c->registration_number : $c['registration_number']) ?></span>
                                        <?php if (!empty(is_object($c) ? $c->tax_number : $c['tax_number'])): ?>
                                            <div class="text-muted small">TIN: <?= htmlspecialchars(is_object($c) ? $c->tax_number : $c['tax_number']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars(is_object($c) ? $c->billing_email : $c['billing_email']) ?>" class="text-decoration-none text-muted">
                                            <i class="fa fa-envelope me-1 text-primary"></i><?= htmlspecialchars(is_object($c) ? $c->billing_email : $c['billing_email']) ?>
                                        </a>
                                        <div class="text-muted small"><?= htmlspecialchars(is_object($c) ? $c->primary_phone : $c['primary_phone']) ?></div>
                                    </td>
                                    <td>
                                        <i class="fa fa-map-pin me-1 text-danger"></i><?= htmlspecialchars(is_object($c) ? $c->city : $c['city']) ?>, <?= htmlspecialchars(is_object($c) ? $c->country : $c['country']) ?>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-info-subtle text-info-emphasis px-2 py-1">
                                            <?= $stats['plans_count'] ?> Retainer<?= $stats['plans_count'] !== 1 ? 's' : '' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">$<?= number_format($stats['monthly_total'], 2) ?></span>
                                        <span class="text-muted small">/mo</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= $siteConfig->siteUrl ?>/admin/clients/view/<?= $cId ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            Manage <i class="fa fa-chevron-right ms-1"></i>
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

<!-- Modal: Register Client Organization -->
<div class="modal fade" id="modalAddClient" tabindex="-1" aria-labelledby="modalAddClientLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= $siteConfig->siteUrl ?>/admin/clients/create" method="POST">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalAddClientLabel" style="color: #2A114B;">
                        <i class="fa fa-building-circle-check me-2 text-warning"></i>Register Client Organization
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Legal Corporate Name <span class="text-danger">*</span></label>
                            <input type="text" name="legal_name" class="form-control rounded-3" placeholder="e.g. Econet Wireless Zimbabwe Ltd" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Trading / Brand Name</label>
                            <input type="text" name="trading_name" class="form-control rounded-3" placeholder="e.g. Econet Wireless">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Company Registration Number</label>
                            <input type="text" name="registration_number" class="form-control rounded-3" placeholder="e.g. 1423/98">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tax Number (BP / TIN)</label>
                            <input type="text" name="tax_number" class="form-control rounded-3" placeholder="e.g. 20018392">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Billing Email <span class="text-danger">*</span></label>
                            <input type="email" name="billing_email" class="form-control rounded-3" placeholder="accounts@client.co.zw" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Primary Phone</label>
                            <input type="tel" name="primary_phone" class="form-control rounded-3" placeholder="+263 242 ...">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Physical Address</label>
                            <input type="text" name="address" class="form-control rounded-3" placeholder="Street number, building, road">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">City / Town</label>
                            <input type="text" name="city" class="form-control rounded-3" value="Harare">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Country</label>
                            <input type="text" name="country" class="form-control rounded-3" value="Zimbabwe">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #2A114B;">
                        <i class="fa fa-check-circle me-1"></i> Save Organization
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include _BASE_PATH . '/views/partials/footer.php'; ?>
