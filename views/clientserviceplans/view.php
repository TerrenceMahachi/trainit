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
                        <th class="fw-bold">Clientorganization</th>
                        <td class="text-muted"><?= htmlspecialchars($item->clientorganization()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Serviceoffering</th>
                        <td class="text-muted"><?= htmlspecialchars($item->serviceoffering()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Plan_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->plan_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Currency</th>
                        <td class="text-muted"><?= htmlspecialchars($item->currency ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Monthly_fee</th>
                        <td class="text-muted"><?= htmlspecialchars($item->monthly_fee ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Included_hours</th>
                        <td class="text-muted"><?= htmlspecialchars($item->included_hours ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Associate_rate</th>
                        <td class="text-muted"><?= htmlspecialchars($item->associate_rate ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Apprentice_rate</th>
                        <td class="text-muted"><?= htmlspecialchars($item->apprentice_rate ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Billing_cycle_day</th>
                        <td class="text-muted"><?= htmlspecialchars($item->billing_cycle_day ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Service_manager</th>
                        <td class="text-muted"><?= htmlspecialchars($item->service_manager()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Billing_owner</th>
                        <td class="text-muted"><?= htmlspecialchars($item->billing_owner()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Excesspolicy</th>
                        <td class="text-muted"><?= htmlspecialchars($item->excesspolicy()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Start_date</th>
                        <td class="text-muted"><?= htmlspecialchars($item->start_date ?? ''); ?></td>
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
