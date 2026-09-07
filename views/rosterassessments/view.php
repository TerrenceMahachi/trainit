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
                        <th class="fw-bold">Reviewer</th>
                        <td class="text-muted"><?= htmlspecialchars($item->reviewer()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Vettingrecommendation</th>
                        <td class="text-muted"><?= htmlspecialchars($item->vettingrecommendation()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Eligibility_gate_passed</th>
                        <td class="text-muted"><?= htmlspecialchars($item->eligibility_gate_passed ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Technical_fit_score</th>
                        <td class="text-muted"><?= htmlspecialchars($item->technical_fit_score ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Evidence_score</th>
                        <td class="text-muted"><?= htmlspecialchars($item->evidence_score ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Judgement_score</th>
                        <td class="text-muted"><?= htmlspecialchars($item->judgement_score ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Availability_score</th>
                        <td class="text-muted"><?= htmlspecialchars($item->availability_score ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Motivation_score</th>
                        <td class="text-muted"><?= htmlspecialchars($item->motivation_score ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Total_score</th>
                        <td class="text-muted"><?= htmlspecialchars($item->total_score ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Automated_red_flags</th>
                        <td class="text-muted"><?= htmlspecialchars($item->automated_red_flags ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Interview_notes</th>
                        <td class="text-muted"><?= htmlspecialchars($item->interview_notes ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Technical_test_result</th>
                        <td class="text-muted"><?= htmlspecialchars($item->technical_test_result ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Vetted_at</th>
                        <td class="text-muted"><?= htmlspecialchars($item->vetted_at ?? ''); ?></td>
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
