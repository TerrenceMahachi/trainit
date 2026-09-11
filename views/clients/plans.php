@extends('layouts.main')

<?php
global $siteConfig;
$client = $data['client'] ?? null;
$plans = $data['plans'] ?? [];
$activeTab = $data['activeTab'] ?? 'plans';
?>

<div class="container py-4 my-2">
    <?php include _BASE_PATH . '/views/clients/nav.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #2A114B;">
                <i class="fa fa-cubes me-2 text-warning"></i> Service Retainers &amp; Capacity
            </h4>
            <p class="text-muted small mb-0">Review active retainer contracts, monthly hours capacity, blended talent rate cards, and billing cycle policies.</p>
        </div>
        <a href="<?= $siteConfig->siteUrl ?>/contact?subject=Retainer+Plan+Inquiry" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold">
            <i class="fa fa-sliders-h me-1"></i> Modify Capacity / Retainer
        </a>
    </div>

    <?php if (empty($plans)): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center mb-4">
            <div class="rounded-circle bg-light d-inline-flex p-4 mb-3 text-muted">
                <i class="fa fa-folder-open fa-3x"></i>
            </div>
            <h5 class="fw-bold text-dark">No Service Retainers Configured</h5>
            <p class="text-muted small mb-4">Your organization does not have an active monthly service retainer assigned yet. Please contact Tsigiro Operations to activate your tailored retainer agreement.</p>
            <div>
                <a href="<?= $siteConfig->siteUrl ?>/contact" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark">
                    <i class="fa fa-envelope me-1"></i> Contact Operations Team
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4 mb-4">
            <?php foreach ($plans as $item): 
                $p = $item['plan'];
                $pId = is_object($p) ? $p->iD : $p['iD'];
                $planName = htmlspecialchars(is_object($p) ? $p->plan_name : $p['plan_name']);
                $currency = is_object($p) ? $p->currency : $p['currency'];
                $monthlyFee = (float)(is_object($p) ? $p->monthly_fee : $p['monthly_fee']);
                $incHours = (float)(is_object($p) ? $p->included_hours : $p['included_hours']);
                $assocRate = (float)(is_object($p) ? $p->associate_rate : $p['associate_rate']);
                $apprRate = (float)(is_object($p) ? $p->apprentice_rate : $p['apprentice_rate']);
                $billingCycleDay = (int)(is_object($p) ? $p->billing_cycle_day : $p['billing_cycle_day']);
                $startDate = is_object($p) ? $p->start_date : $p['start_date'];
                $isActive = $item['is_active'];
                $offering = $item['offering'];
                $offeringName = $offering ? htmlspecialchars(is_object($offering) ? $offering->name : $offering['name']) : 'Managed Services';
                $manager = $item['manager'];
                $managerName = $manager ? htmlspecialchars(is_object($manager) ? $manager->name : $manager['name']) : 'Tsigiro Operations Desk';
                $managerEmail = $manager ? htmlspecialchars(is_object($manager) ? $manager->email : $manager['email']) : 'support@tsigiro.co.zw';
                $excess = $item['excess_policy'];
                $excessName = $excess ? htmlspecialchars(is_object($excess) ? $excess->name : $excess['name']) : 'Excess Invoiced at Standard Rate';
            ?>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative <?= !$isActive ? 'opacity-75' : '' ?>">
                        <div class="card-header bg-white p-4 border-0 pb-0 d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge rounded-pill px-3 py-1 mb-2 <?= $isActive ? 'bg-success' : 'bg-secondary' ?>">
                                    <i class="fa <?= $isActive ? 'fa-check-circle' : 'fa-ban' ?> me-1"></i> <?= $isActive ? 'Active Retainer' : 'Terminated' ?>
                                </span>
                                <h4 class="fw-bold text-dark mb-1"><?= $planName ?></h4>
                                <div class="text-muted small"><i class="fa fa-layer-group me-1"></i> <?= $offeringName ?></div>
                            </div>
                            <div class="text-end">
                                <div class="fs-4 fw-bold text-dark">$<?= number_format($monthlyFee, 2) ?></div>
                                <div class="text-muted small">/ month (<?= $currency ?>)</div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <!-- Retainer Capacity Specs -->
                            <div class="p-3 bg-light rounded-4 mb-3 border">
                                <div class="row g-2 text-center">
                                    <div class="col-6 border-end">
                                        <div class="text-muted small">Included Hours</div>
                                        <div class="fs-4 fw-bold text-dark"><?= number_format($incHours, 1) ?> <small class="fs-6 text-muted">hrs/mo</small></div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small">Billing Renewal</div>
                                        <div class="fs-4 fw-bold text-dark">Day <?= $billingCycleDay ?> <small class="fs-6 text-muted">of month</small></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Rate Card Breakdown -->
                            <h6 class="fw-bold small text-muted text-uppercase mb-2"><i class="fa fa-tags me-1"></i> Blended Rate Card</h6>
                            <div class="list-group list-group-flush small mb-3 border rounded-3 overflow-hidden">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fa fa-user-tie me-2 text-primary"></i> Associate Hourly Rate (Senior Talent)</span>
                                    <strong class="text-dark">$<?= number_format($assocRate, 2) ?> / hr</strong>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fa fa-user-graduate me-2 text-success"></i> Apprentice Hourly Rate (Supported Execution)</span>
                                    <strong class="text-dark">$<?= number_format($apprRate, 2) ?> / hr</strong>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                    <span><i class="fa fa-info-circle me-2 text-muted"></i> Excess Hours Policy</span>
                                    <span class="text-muted fw-semibold"><?= $excessName ?></span>
                                </div>
                            </div>

                            <!-- Assigned Service Manager -->
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 border">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                     style="width: 40px; height: 40px; background-color: #2A114B; flex-shrink: 0;">
                                    <?= strtoupper(substr($managerName, 0, 1)) ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-muted">Dedicated Service Manager</div>
                                    <div class="fw-bold text-dark"><?= $managerName ?></div>
                                    <div class="small text-muted"><?= $managerEmail ?></div>
                                </div>
                            </div>
                        </div>

                        <?php if ($isActive): ?>
                            <div class="card-footer bg-white border-0 p-4 pt-0">
                                <a href="<?= $siteConfig->siteUrl ?>/client/requests/new?service_plan_id=<?= $pId ?>" 
                                   class="btn btn-warning w-100 rounded-pill py-2 fw-bold text-dark shadow-sm">
                                    <i class="fa fa-plus-circle me-1"></i> Request Work Under This Retainer
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Retainer Policy Explainer Banner -->
    <div class="card border-0 shadow-sm rounded-4 p-4" style="background: linear-gradient(135deg, #2A114B 0%, #431E76 100%); color: #fff;">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-1">Capacity Guarantee</span>
                    <h5 class="fw-bold text-white mb-0">How Tsigiro Retainers Work</h5>
                </div>
                <p class="text-white-50 mb-0 small">
                    Your monthly retainer pre-books dedicated talent bandwidth across engineering, data, design, and operations. Every work request draws from your monthly hours allowance with complete transparency. Supervised by experienced Associate mentors, deliverables undergo quality assurance before handover.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= $siteConfig->siteUrl ?>/contact?subject=Retainer+Upgrade+Request" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                    <i class="fa fa-arrow-up me-1"></i> Request Capacity Scale
                </a>
            </div>
        </div>
    </div>
</div>
