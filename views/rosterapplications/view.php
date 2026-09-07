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
                        <th class="fw-bold">User</th>
                        <td class="text-muted"><?= htmlspecialchars($item->user()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Applicationtrack</th>
                        <td class="text-muted"><?= htmlspecialchars($item->applicationtrack()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Applicationstatus</th>
                        <td class="text-muted"><?= htmlspecialchars($item->applicationstatus()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Primaryfunction</th>
                        <td class="text-muted"><?= htmlspecialchars($item->primaryfunction()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Legal_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->legal_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Preferred_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->preferred_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Email</th>
                        <td class="text-muted"><?= htmlspecialchars($item->email ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Mobile_number</th>
                        <td class="text-muted"><?= htmlspecialchars($item->mobile_number ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Whatsapp_number</th>
                        <td class="text-muted"><?= htmlspecialchars($item->whatsapp_number ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Date_of_birth</th>
                        <td class="text-muted"><?= htmlspecialchars($item->date_of_birth ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Gender</th>
                        <td class="text-muted"><?= htmlspecialchars($item->gender()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">City</th>
                        <td class="text-muted"><?= htmlspecialchars($item->city ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Suburb</th>
                        <td class="text-muted"><?= htmlspecialchars($item->suburb ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Zimprovince</th>
                        <td class="text-muted"><?= htmlspecialchars($item->zimprovince()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Country</th>
                        <td class="text-muted"><?= htmlspecialchars($item->country ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Nationality</th>
                        <td class="text-muted"><?= htmlspecialchars($item->nationality ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Workrightstatus</th>
                        <td class="text-muted"><?= htmlspecialchars($item->workrightstatus()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Work_permit_number</th>
                        <td class="text-muted"><?= htmlspecialchars($item->work_permit_number ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Work_permit_expiry</th>
                        <td class="text-muted"><?= htmlspecialchars($item->work_permit_expiry ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Has_disability_adjustment</th>
                        <td class="text-muted"><?= htmlspecialchars($item->has_disability_adjustment ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Adjustment_details</th>
                        <td class="text-muted"><?= htmlspecialchars($item->adjustment_details ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">How_heard</th>
                        <td class="text-muted"><?= htmlspecialchars($item->how_heard ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Referred_by</th>
                        <td class="text-muted"><?= htmlspecialchars($item->referred_by ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Consent_version</th>
                        <td class="text-muted"><?= htmlspecialchars($item->consent_version ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Consent_timestamp</th>
                        <td class="text-muted"><?= htmlspecialchars($item->consent_timestamp ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Consent_ip_address</th>
                        <td class="text-muted"><?= htmlspecialchars($item->consent_ip_address ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">E_signature</th>
                        <td class="text-muted"><?= htmlspecialchars($item->e_signature ?? ''); ?></td>
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
