@extends('layouts.main')

<?php
use App\Models\Rosterapplication;
use App\Models\Employmentstatus;
use App\Models\Invoiceentitytype;

include __DIR__ . '/header.php';
?>

<div>
    <!-- Page header -->
    <section class="hero-section">
        <div class="container text-start">
            <h3>New <?= $page_name ?></h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-capitalize"
                            href="<?= $siteConfig->siteUrl ?>/<?= $page ?>"><?= $page_name ?>s</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create New</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Create form -->
    <section class="py-4">
        <div class="container rounded-4 p-4 bg-white shadow-sm">

            <form class="form py-3 m-md-3" id="create_item_form">

                <div class="row">
                    
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Rosterapplication: </label>
                    <select class="form-select form-select-lg f-sel" name="rosterapplication" required>
                        <option value="">Select Rosterapplication</option>
                        <?php foreach (Rosterapplication::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Employmentstatus: </label>
                    <select class="form-select form-select-lg f-sel" name="employmentstatus" required>
                        <option value="">Select Employmentstatus</option>
                        <?php foreach (Employmentstatus::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> years_experience: </label>
                    <input type="text" name="years_experience" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> donor_experience_years: </label>
                    <input type="text" name="donor_experience_years" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> donors_worked_with: </label>
                    <input type="text" name="donors_worked_with" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> largest_budget_handled: </label>
                    <input type="text" name="largest_budget_handled" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> largest_team_supervised: </label>
                    <input type="number" name="largest_team_supervised" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> largest_endpoints_supported: </label>
                    <input type="number" name="largest_endpoints_supported" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> largest_dataset_managed: </label>
                    <input type="text" name="largest_dataset_managed" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> supervised_juniors_before: </label>
                    <input type="text" name="supervised_juniors_before" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> led_audits_or_evaluations: </label>
                    <input type="text" name="led_audits_or_evaluations" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> rejected_work_experience: </label>
                    <input type="text" name="rejected_work_experience" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> day_rate_expectation: </label>
                    <input type="number" name="day_rate_expectation" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> capacity_days_per_month: </label>
                    <input type="text" name="capacity_days_per_month" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> notice_period: </label>
                    <input type="text" name="notice_period" class="form-control form-control-lg" />
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Invoiceentitytype: </label>
                    <select class="form-select form-select-lg f-sel" name="invoiceentitytype" required>
                        <option value="">Select Invoiceentitytype</option>
                        <?php foreach (Invoiceentitytype::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> has_tax_clearance_itf263: </label>
                    <input type="text" name="has_tax_clearance_itf263" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> zimra_bp_number: </label>
                    <input type="text" name="zimra_bp_number" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> tax_clearance_doc: </label>
                    <input type="text" name="tax_clearance_doc" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> is_vat_registered: </label>
                    <input type="text" name="is_vat_registered" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> vat_number: </label>
                    <input type="text" name="vat_number" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> has_indemnity_insurance: </label>
                    <input type="text" name="has_indemnity_insurance" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> insurance_cover_amount: </label>
                    <input type="number" name="insurance_cover_amount" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> conflict_of_interest: </label>
                    <input type="text" name="conflict_of_interest" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> moonlighting_restrictions: </label>
                    <input type="text" name="moonlighting_restrictions" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> cv_bid_consent: </label>
                    <input type="text" name="cv_bid_consent" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> restricted_sectors_or_donors: </label>
                    <input type="text" name="restricted_sectors_or_donors" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> public_website_listing_consent: </label>
                    <input type="text" name="public_website_listing_consent" class="form-control form-control-lg" />
                </div>
                </div>

                <div class="mt-2" id="form_result"></div>

                <input type="hidden" name="Method" value="add_item">
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" id="btn_create_item" class="btn submit-btn btn-lg">Submit Record</button>
                    <a class="d-block my-3 text-start auth-link" href="<?php echo $siteConfig->siteUrl; ?>/<?= $page ?>">
                        <i class="fa fa-arrow-left me-2"></i> Back to <?= $page_name ?>s</a>
                </div>

            </form>

        </div>
    </section>
</div>
