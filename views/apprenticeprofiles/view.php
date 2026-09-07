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
                        <th class="fw-bold">Apprenticestatus</th>
                        <td class="text-muted"><?= htmlspecialchars($item->apprenticestatus()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Institution_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->institution_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Degree_programme</th>
                        <td class="text-muted"><?= htmlspecialchars($item->degree_programme ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Study_level</th>
                        <td class="text-muted"><?= htmlspecialchars($item->study_level ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Student_reg_number</th>
                        <td class="text-muted"><?= htmlspecialchars($item->student_reg_number ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Expected_completion_date</th>
                        <td class="text-muted"><?= htmlspecialchars($item->expected_completion_date ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Is_wrl_attachment</th>
                        <td class="text-muted"><?= htmlspecialchars($item->is_wrl_attachment ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Wrl_start_date</th>
                        <td class="text-muted"><?= htmlspecialchars($item->wrl_start_date ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Wrl_end_date</th>
                        <td class="text-muted"><?= htmlspecialchars($item->wrl_end_date ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Wrl_duration_months</th>
                        <td class="text-muted"><?= htmlspecialchars($item->wrl_duration_months ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Wrl_coordinator_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->wrl_coordinator_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Wrl_coordinator_email</th>
                        <td class="text-muted"><?= htmlspecialchars($item->wrl_coordinator_email ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Wrl_coordinator_phone</th>
                        <td class="text-muted"><?= htmlspecialchars($item->wrl_coordinator_phone ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Requires_placement_letter</th>
                        <td class="text-muted"><?= htmlspecialchars($item->requires_placement_letter ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Requires_host_mou</th>
                        <td class="text-muted"><?= htmlspecialchars($item->requires_host_mou ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Requires_logbook_visits</th>
                        <td class="text-muted"><?= htmlspecialchars($item->requires_logbook_visits ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Requires_host_insurance</th>
                        <td class="text-muted"><?= htmlspecialchars($item->requires_host_insurance ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Min_stipend_required</th>
                        <td class="text-muted"><?= htmlspecialchars($item->min_stipend_required ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Engagementmodel</th>
                        <td class="text-muted"><?= htmlspecialchars($item->engagementmodel()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Worklocationpreference</th>
                        <td class="text-muted"><?= htmlspecialchars($item->worklocationpreference()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Proof_of_registration_doc</th>
                        <td class="text-muted"><?= htmlspecialchars($item->proof_of_registration_doc ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Transcript_doc</th>
                        <td class="text-muted"><?= htmlspecialchars($item->transcript_doc ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Current_average_grade</th>
                        <td class="text-muted"><?= htmlspecialchars($item->current_average_grade ?? ''); ?></td>
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
