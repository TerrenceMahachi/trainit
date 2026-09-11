<?php
global $siteConfig;
$activeTab = $activeTab ?? 'overview';
$clientObj = $client ?? null;
$clientName = htmlspecialchars($clientObj ? (is_object($clientObj) ? $clientObj->trading_name : $clientObj['trading_name']) : 'Client Workspace');
?>

<!-- Client Portal Header Banner -->
<div class="card border-0 rounded-4 p-4 mb-4 text-white shadow-sm position-relative overflow-hidden"
     style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 60%, #431E76 100%);">
    <div class="row align-items-center position-relative" style="z-index: 2;">
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge rounded-pill px-3 py-1 text-dark fw-bold" style="background-color: #FFCC00;">
                    <i class="fa fa-handshake me-1"></i> Client Partner Workspace
                </span>
                <span class="badge bg-light bg-opacity-25 rounded-pill px-3 py-1 text-white small">
                    <i class="fa fa-building me-1"></i> <?= $clientName ?>
                </span>
            </div>
            <h2 class="display-6 fw-bold mb-1"><?= $clientName ?></h2>
            <p class="mb-0 text-white-50">Manage your active service retainers, submit structured work briefs, track delivery milestones, and collaborate with assigned talent in real time.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="d-flex justify-content-lg-end gap-2 flex-wrap">
                <a href="<?= $siteConfig->siteUrl ?>/client/requests/new" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                    <i class="fa fa-paper-plane me-1"></i> Request Work
                </a>
                <a href="<?= $siteConfig->siteUrl ?>/contact" class="btn btn-outline-light rounded-pill px-3 py-2 fw-semibold">
                    <i class="fa fa-headset me-1"></i> Support
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Dedicated Client Subnavigation Tabs (Real Links & Distinct Routes) -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
    <div class="card-body p-2">
        <ul class="nav nav-pills nav-fill flex-column flex-sm-row gap-2">
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-semibold py-2 px-3 <?= $activeTab === 'overview' ? 'active shadow-sm text-white' : 'text-dark' ?>" 
                   style="<?= $activeTab === 'overview' ? 'background-color: #2A114B;' : '' ?>"
                   href="<?= $siteConfig->siteUrl ?>/client/portal">
                    <i class="fa fa-chart-pie me-1 <?= $activeTab === 'overview' ? 'text-warning' : 'text-primary' ?>"></i> Overview
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-semibold py-2 px-3 <?= $activeTab === 'request_new' ? 'active shadow-sm text-white' : 'text-dark' ?>" 
                   style="<?= $activeTab === 'request_new' ? 'background-color: #2A114B;' : '' ?>"
                   href="<?= $siteConfig->siteUrl ?>/client/requests/new">
                    <i class="fa fa-plus-circle me-1 <?= $activeTab === 'request_new' ? 'text-warning' : 'text-success' ?>"></i> Request Work
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-semibold py-2 px-3 <?= $activeTab === 'requests' ? 'active shadow-sm text-white' : 'text-dark' ?>" 
                   style="<?= $activeTab === 'requests' ? 'background-color: #2A114B;' : '' ?>"
                   href="<?= $siteConfig->siteUrl ?>/client/requests">
                    <i class="fa fa-ticket me-1 <?= $activeTab === 'requests' ? 'text-warning' : 'text-info' ?>"></i> Work Requests
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-semibold py-2 px-3 <?= $activeTab === 'plans' ? 'active shadow-sm text-white' : 'text-dark' ?>" 
                   style="<?= $activeTab === 'plans' ? 'background-color: #2A114B;' : '' ?>"
                   href="<?= $siteConfig->siteUrl ?>/client/plans">
                    <i class="fa fa-cubes me-1 <?= $activeTab === 'plans' ? 'text-warning' : 'text-secondary' ?>"></i> Retainers &amp; Plans
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-semibold py-2 px-3 <?= $activeTab === 'team' ? 'active shadow-sm text-white' : 'text-dark' ?>" 
                   style="<?= $activeTab === 'team' ? 'background-color: #2A114B;' : '' ?>"
                   href="<?= $siteConfig->siteUrl ?>/client/team">
                    <i class="fa fa-users me-1 <?= $activeTab === 'team' ? 'text-warning' : 'text-dark' ?>"></i> Team &amp; Access
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-semibold py-2 px-3 <?= $activeTab === 'invoices' ? 'active shadow-sm text-white' : 'text-dark' ?>" 
                   style="<?= $activeTab === 'invoices' ? 'background-color: #2A114B;' : '' ?>"
                   href="<?= $siteConfig->siteUrl ?>/client/invoices">
                    <i class="fa fa-file-invoice-dollar me-1 <?= $activeTab === 'invoices' ? 'text-warning' : 'text-success' ?>"></i> Invoices &amp; Billing
                </a>
            </li>
        </ul>
    </div>
</div>
