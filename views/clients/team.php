@extends('layouts.main')

<?php
global $siteConfig;
$client = $data['client'] ?? null;
$members = $data['members'] ?? [];
$roles = $data['roles'] ?? [];
$activeTab = $data['activeTab'] ?? 'team';

$legalName = htmlspecialchars($client ? (is_object($client) ? $client->legal_name : $client['legal_name']) : 'Client Organization');
$tradingName = htmlspecialchars($client ? (is_object($client) ? $client->trading_name : $client['trading_name']) : $legalName);
$billingEmail = htmlspecialchars($client ? (is_object($client) ? $client->billing_email : $client['billing_email']) : '');
$regNumber = htmlspecialchars($client ? (is_object($client) ? $client->registration_number : $client['registration_number']) : 'N/A');
$taxNumber = htmlspecialchars($client ? (is_object($client) ? $client->tax_number : $client['tax_number']) : 'N/A');
$address = htmlspecialchars($client ? (is_object($client) ? $client->address : $client['address']) : '');
$city = htmlspecialchars($client ? (is_object($client) ? $client->city : $client['city']) : 'Harare');
$country = htmlspecialchars($client ? (is_object($client) ? $client->country : $client['country']) : 'Zimbabwe');
$phone = htmlspecialchars($client ? (is_object($client) ? $client->primary_phone : $client['primary_phone']) : '');
?>

<div class="container py-4 my-2">
    <?php include _BASE_PATH . '/views/clients/nav.php'; ?>

    <div class="row g-4 mb-4">
        <!-- Organization Legal Profile Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white p-4 border-0 pb-2">
                    <h5 class="fw-bold mb-1" style="color: #2A114B;">
                        <i class="fa fa-building me-2 text-warning"></i> Organization Profile
                    </h5>
                    <p class="text-muted small mb-0">Registered organizational details and billing credentials.</p>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="list-group list-group-flush small">
                        <div class="list-group-item px-0 py-2 border-0">
                            <span class="text-muted d-block">Trading Name</span>
                            <strong class="text-dark fs-6"><?= $tradingName ?></strong>
                        </div>
                        <div class="list-group-item px-0 py-2 border-0">
                            <span class="text-muted d-block">Legal Entity Name</span>
                            <span class="text-dark"><?= $legalName ?></span>
                        </div>
                        <div class="list-group-item px-0 py-2 border-0">
                            <span class="text-muted d-block">Billing Email</span>
                            <span class="text-dark"><i class="fa fa-envelope me-1 text-muted"></i> <?= $billingEmail ?></span>
                        </div>
                        <div class="list-group-item px-0 py-2 border-0">
                            <span class="text-muted d-block">Primary Telephone</span>
                            <span class="text-dark"><i class="fa fa-phone me-1 text-muted"></i> <?= $phone ?: 'Not specified' ?></span>
                        </div>
                        <div class="list-group-item px-0 py-2 border-0">
                            <span class="text-muted d-block">Registration &amp; Tax IDs</span>
                            <span class="text-dark font-monospace">Reg: <?= $regNumber ?> | Tax: <?= $taxNumber ?></span>
                        </div>
                        <div class="list-group-item px-0 py-2 border-0">
                            <span class="text-muted d-block">Address</span>
                            <span class="text-dark"><?= $address ? $address . ', ' : '' ?><?= $city ?>, <?= $country ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 p-4 rounded-bottom-4">
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <i class="fa fa-shield-alt text-success"></i>
                        <span>Verified Client Organization</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Authorized Team Members & Requesters -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white p-4 border-0 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: #2A114B;">
                            <i class="fa fa-users me-2 text-warning"></i> Authorized Requesters &amp; Team
                        </h5>
                        <p class="text-muted small mb-0">Colleagues authorized to submit work briefs, review deliverables, and collaborate with talent.</p>
                    </div>
                    <span class="badge bg-light text-muted border rounded-pill px-3 py-1">
                        <?= count($members) ?> Member(s)
                    </span>
                </div>

                <div class="card-body p-4 pt-2">
                    <?php if (empty($members)): ?>
                        <div class="p-4 text-center text-muted bg-light rounded-4">
                            <i class="fa fa-user-friends fa-2x mb-2 text-secondary"></i>
                            <p class="small mb-0">No active memberships logged. As an admin, you have full access to submit work requests.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="small text-uppercase text-muted">
                                        <th>Name &amp; Contact</th>
                                        <th>Role / Permission</th>
                                        <th>Status</th>
                                        <th>Member Since</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($members as $item): 
                                        $m = $item['membership'];
                                        $u = $item['user'];
                                        $r = $item['role'];
                                        $uName = htmlspecialchars($u ? (is_object($u) ? $u->name : $u['name']) : 'Team Member');
                                        $uEmail = htmlspecialchars($u ? (is_object($u) ? $u->email : $u['email']) : '');
                                        $roleName = htmlspecialchars($r ? (is_object($r) ? $r->name : $r['name']) : 'Authorized Requester');
                                        $regDate = is_object($m) ? $m->reg_date : $m['reg_date'];
                                    ?>
                                        <tr>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                                         style="width: 40px; height: 40px; background-color: #2A114B;">
                                                        <?= strtoupper(substr($uName, 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= $uName ?></div>
                                                        <small class="text-muted"><?= $uEmail ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1">
                                                    <i class="fa fa-id-badge me-1 text-primary"></i> <?= $roleName ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success rounded-pill px-3 py-1">
                                                    <i class="fa fa-check-circle me-1"></i> Active
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= $regDate ? date('M d, Y', strtotime($regDate)) : 'N/A' ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- Invite / Add Member Security Notice -->
                    <div class="mt-4 p-3 bg-light rounded-4 border">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa fa-info-circle text-primary mt-1"></i>
                            <div class="small">
                                <strong>Need to authorize additional team members or colleagues?</strong>
                                <p class="text-muted mb-0">To ensure strict organization data security, new authorized requesters must be verified by your dedicated Service Manager. Contact Tsigiro Operations to provision new seats.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
