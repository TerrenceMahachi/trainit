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
                        <th class="fw-bold">Sectortype</th>
                        <td class="text-muted"><?= htmlspecialchars($item->sectortype()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Engagementbasis</th>
                        <td class="text-muted"><?= htmlspecialchars($item->engagementbasis()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Organization_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->organization_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Position_title</th>
                        <td class="text-muted"><?= htmlspecialchars($item->position_title ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Start_date</th>
                        <td class="text-muted"><?= htmlspecialchars($item->start_date ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">End_date</th>
                        <td class="text-muted"><?= htmlspecialchars($item->end_date ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Is_current</th>
                        <td class="text-muted"><?= htmlspecialchars($item->is_current ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Key_deliverables</th>
                        <td class="text-muted"><?= htmlspecialchars($item->key_deliverables ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Reason_for_leaving</th>
                        <td class="text-muted"><?= htmlspecialchars($item->reason_for_leaving ?? ''); ?></td>
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
