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
                        <th class="fw-bold">Motivation_narrative</th>
                        <td class="text-muted"><?= htmlspecialchars($item->motivation_narrative ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Primary_function_evidence</th>
                        <td class="text-muted"><?= htmlspecialchars($item->primary_function_evidence ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Shared_client_management_plan</th>
                        <td class="text-muted"><?= htmlspecialchars($item->shared_client_management_plan ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Error_discovery_resolution</th>
                        <td class="text-muted"><?= htmlspecialchars($item->error_discovery_resolution ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Urgent_friday_deadline_dilemma</th>
                        <td class="text-muted"><?= htmlspecialchars($item->urgent_friday_deadline_dilemma ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Associate_apprentice_qa_methodology</th>
                        <td class="text-muted"><?= htmlspecialchars($item->associate_apprentice_qa_methodology ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Associate_unethical_client_solution</th>
                        <td class="text-muted"><?= htmlspecialchars($item->associate_unethical_client_solution ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Apprentice_twelve_month_goal</th>
                        <td class="text-muted"><?= htmlspecialchars($item->apprentice_twelve_month_goal ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Additional_notes</th>
                        <td class="text-muted"><?= htmlspecialchars($item->additional_notes ?? ''); ?></td>
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
