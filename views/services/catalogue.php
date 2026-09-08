<?php
$title = "Service Catalogue & SLA Governance — " . _SITE;
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
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/admin/clients" class="text-decoration-none" style="color: #2A114B;">Clients</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Service Catalogue</li>
                    </ol>
                </nav>
                <h2 class="h3 fw-bold mb-0" style="color: #1C0D30;">
                    <i class="fa fa-list-check me-2" style="color: #FFCC00;"></i>Service Catalogue & SLA Framework
                </h2>
                <p class="text-muted small mb-0">Standardized service deliverables, capabilities, and guaranteed response/resolution SLA benchmarks.</p>
            </div>
            <div>
                <a href="<?= $siteConfig->siteUrl ?>/admin/clients" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fa fa-building me-1"></i> Client Organizations
                </a>
            </div>
        </div>

        <!-- Service Categories & Offerings -->
        <div class="row g-4">
            <?php foreach ($groupedOfferings as $catGroup): ?>
                <?php
                $cat = $catGroup['category'];
                $catName = htmlspecialchars(is_object($cat) ? $cat->name : $cat['name']);
                $catCode = htmlspecialchars(is_object($cat) ? $cat->code : $cat['code']);
                $catDesc = htmlspecialchars(is_object($cat) ? $cat->description : $cat['description']);
                $offeringsList = $catGroup['offerings'];
                ?>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge rounded-pill px-3 py-2 text-white fw-bold me-2" style="background-color: #2A114B;">
                                <?= $catCode ?>
                            </span>
                            <h4 class="fw-bold mb-0" style="color: #2A114B;"><?= $catName ?></h4>
                        </div>
                        <p class="text-muted small mb-4"><?= $catDesc ?></p>

                        <div class="list-group list-group-flush">
                            <?php if (empty($offeringsList)): ?>
                                <div class="text-muted small py-2">No offerings listed under this category.</div>
                            <?php else: ?>
                                <?php foreach ($offeringsList as $off): ?>
                                    <div class="list-group-item px-0 py-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars(is_object($off) ? $off->name : $off['name']) ?></h6>
                                            <span class="badge bg-light text-muted border font-monospace"><?= htmlspecialchars(is_object($off) ? $off->code : $off['code']) ?></span>
                                        </div>
                                        <p class="small text-muted mb-2"><?= htmlspecialchars(is_object($off) ? $off->description : $off['description']) ?></p>

                                        <!-- SLA Benchmark Indicators -->
                                        <div class="d-flex gap-2 flex-wrap mt-2">
                                            <span class="badge bg-danger-subtle text-danger border" style="font-size: 0.72rem;">
                                                <i class="fa fa-bolt me-1"></i>Urgent: 15m Resp / 4h Res
                                            </span>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border" style="font-size: 0.72rem;">
                                                <i class="fa fa-clock me-1"></i>High: 30m Resp / 8h Res
                                            </span>
                                            <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.72rem;">
                                                <i class="fa fa-calendar-check me-1"></i>Standard: 2h Resp / 24h Res
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</main>

<?php include _BASE_PATH . '/views/partials/footer.php'; ?>
