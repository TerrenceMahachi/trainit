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
                        <th class="fw-bold">Legal_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->legal_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Trading_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->trading_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Registration_number</th>
                        <td class="text-muted"><?= htmlspecialchars($item->registration_number ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Tax_number</th>
                        <td class="text-muted"><?= htmlspecialchars($item->tax_number ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Billing_email</th>
                        <td class="text-muted"><?= htmlspecialchars($item->billing_email ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Address</th>
                        <td class="text-muted"><?= htmlspecialchars($item->address ?? ''); ?></td>
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
                        <th class="fw-bold">Primary_phone</th>
                        <td class="text-muted"><?= htmlspecialchars($item->primary_phone ?? ''); ?></td>
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
