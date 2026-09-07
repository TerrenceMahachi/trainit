@extends('layouts.main')

<?php
include __DIR__ . '/header.php';
use function App\Helpers\formatDateTime;
?>

<div>
    <!-- Page header -->
    <section class="hero-section">
        <div class="container text-start">
            <h3><?= $page_name ?> Details</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-capitalize"
                            href="<?= $siteConfig->siteUrl ?>/<?= $page ?>"><?= $page_name ?>s</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= htmlspecialchars($item->name ?? ('#' . $item->iD)) ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Record details -->
    <section class="py-4">
        <div class="container rounded-4 p-4 bg-white shadow-sm">

            <?php include __DIR__ . '/nav.php'; ?>

            <div class="table-responsive py-3 m-md-3">
                <table class="table table-borderless align-middle detail-table">
                    <tbody>
                        
                    <tr>
                        <th class="fw-bold">Rosterapplication</th>
                        <td class="text-muted"><?= htmlspecialchars($item->rosterapplication()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">National_id_number</th>
                        <td class="text-muted"><?= htmlspecialchars($item->national_id_number ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">National_id_doc</th>
                        <td class="text-muted"><?= htmlspecialchars($item->national_id_doc ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Passport_number</th>
                        <td class="text-muted"><?= htmlspecialchars($item->passport_number ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Passport_expiry</th>
                        <td class="text-muted"><?= htmlspecialchars($item->passport_expiry ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Street_address</th>
                        <td class="text-muted"><?= htmlspecialchars($item->street_address ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">City</th>
                        <td class="text-muted"><?= htmlspecialchars($item->city ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Country</th>
                        <td class="text-muted"><?= htmlspecialchars($item->country ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Bank_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->bank_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Bank_branch</th>
                        <td class="text-muted"><?= htmlspecialchars($item->bank_branch ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Account_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->account_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Account_number</th>
                        <td class="text-muted"><?= htmlspecialchars($item->account_number ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Bank_currency</th>
                        <td class="text-muted"><?= htmlspecialchars($item->bank_currency ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Emergency_contact_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->emergency_contact_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Emergency_contact_phone</th>
                        <td class="text-muted"><?= htmlspecialchars($item->emergency_contact_phone ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Emergency_contact_relationship</th>
                        <td class="text-muted"><?= htmlspecialchars($item->emergency_contact_relationship ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Nssa_number</th>
                        <td class="text-muted"><?= htmlspecialchars($item->nssa_number ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Police_clearance_doc</th>
                        <td class="text-muted"><?= htmlspecialchars($item->police_clearance_doc ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Police_clearance_date</th>
                        <td class="text-muted"><?= htmlspecialchars($item->police_clearance_date ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Signed_contract_doc</th>
                        <td class="text-muted"><?= htmlspecialchars($item->signed_contract_doc ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Signed_nda_doc</th>
                        <td class="text-muted"><?= htmlspecialchars($item->signed_nda_doc ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Odoo_applicant_id</th>
                        <td class="text-muted"><?= htmlspecialchars($item->odoo_applicant_id ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Odoo_employee_id</th>
                        <td class="text-muted"><?= htmlspecialchars($item->odoo_employee_id ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Synced_to_odoo_at</th>
                        <td class="text-muted"><?= htmlspecialchars($item->synced_to_odoo_at ?? ''); ?></td>
                    </tr>
                        <tr>
                            <th class="fw-bold">Date Registered</th>
                            <td class="text-muted"><?= formatDateTime($item->reg_date); ?></td>
                        </tr>
                        <tr>
                            <th class="fw-bold">Recorded By</th>
                            <td class="text-muted"><?= htmlspecialchars($item->creator()->name ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th class="fw-bold">Status</th>
                            <td class="text-muted"><?= htmlspecialchars($item->status()->name ?? ''); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>
</div>
