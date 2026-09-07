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
                        <th class="fw-bold">Refereecontacttiming</th>
                        <td class="text-muted"><?= htmlspecialchars($item->refereecontacttiming()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Refereeverificationstatus</th>
                        <td class="text-muted"><?= htmlspecialchars($item->refereeverificationstatus()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Referee_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->referee_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Organization</th>
                        <td class="text-muted"><?= htmlspecialchars($item->organization ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Position</th>
                        <td class="text-muted"><?= htmlspecialchars($item->position ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Relationship</th>
                        <td class="text-muted"><?= htmlspecialchars($item->relationship ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Email</th>
                        <td class="text-muted"><?= htmlspecialchars($item->email ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Phone</th>
                        <td class="text-muted"><?= htmlspecialchars($item->phone ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Verification_notes</th>
                        <td class="text-muted"><?= htmlspecialchars($item->verification_notes ?? ''); ?></td>
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
