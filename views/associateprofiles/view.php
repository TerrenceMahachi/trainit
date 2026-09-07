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
                        <th class="fw-bold">Employmentstatus</th>
                        <td class="text-muted"><?= htmlspecialchars($item->employmentstatus()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Years_experience</th>
                        <td class="text-muted"><?= htmlspecialchars($item->years_experience ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Donor_experience_years</th>
                        <td class="text-muted"><?= htmlspecialchars($item->donor_experience_years ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Donors_worked_with</th>
                        <td class="text-muted"><?= htmlspecialchars($item->donors_worked_with ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Largest_budget_handled</th>
                        <td class="text-muted"><?= htmlspecialchars($item->largest_budget_handled ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Largest_team_supervised</th>
                        <td class="text-muted"><?= htmlspecialchars($item->largest_team_supervised ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Largest_endpoints_supported</th>
                        <td class="text-muted"><?= htmlspecialchars($item->largest_endpoints_supported ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Largest_dataset_managed</th>
                        <td class="text-muted"><?= htmlspecialchars($item->largest_dataset_managed ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Supervised_juniors_before</th>
                        <td class="text-muted"><?= htmlspecialchars($item->supervised_juniors_before ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Led_audits_or_evaluations</th>
                        <td class="text-muted"><?= htmlspecialchars($item->led_audits_or_evaluations ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Rejected_work_experience</th>
                        <td class="text-muted"><?= htmlspecialchars($item->rejected_work_experience ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Day_rate_expectation</th>
                        <td class="text-muted"><?= htmlspecialchars($item->day_rate_expectation ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Capacity_days_per_month</th>
                        <td class="text-muted"><?= htmlspecialchars($item->capacity_days_per_month ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Notice_period</th>
                        <td class="text-muted"><?= htmlspecialchars($item->notice_period ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Invoiceentitytype</th>
                        <td class="text-muted"><?= htmlspecialchars($item->invoiceentitytype()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Has_tax_clearance_itf263</th>
                        <td class="text-muted"><?= htmlspecialchars($item->has_tax_clearance_itf263 ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Zimra_bp_number</th>
                        <td class="text-muted"><?= htmlspecialchars($item->zimra_bp_number ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Tax_clearance_doc</th>
                        <td class="text-muted"><?= htmlspecialchars($item->tax_clearance_doc ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Is_vat_registered</th>
                        <td class="text-muted"><?= htmlspecialchars($item->is_vat_registered ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Vat_number</th>
                        <td class="text-muted"><?= htmlspecialchars($item->vat_number ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Has_indemnity_insurance</th>
                        <td class="text-muted"><?= htmlspecialchars($item->has_indemnity_insurance ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Insurance_cover_amount</th>
                        <td class="text-muted"><?= htmlspecialchars($item->insurance_cover_amount ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Conflict_of_interest</th>
                        <td class="text-muted"><?= htmlspecialchars($item->conflict_of_interest ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Moonlighting_restrictions</th>
                        <td class="text-muted"><?= htmlspecialchars($item->moonlighting_restrictions ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Cv_bid_consent</th>
                        <td class="text-muted"><?= htmlspecialchars($item->cv_bid_consent ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Restricted_sectors_or_donors</th>
                        <td class="text-muted"><?= htmlspecialchars($item->restricted_sectors_or_donors ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Public_website_listing_consent</th>
                        <td class="text-muted"><?= htmlspecialchars($item->public_website_listing_consent ?? ''); ?></td>
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
