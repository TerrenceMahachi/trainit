@extends('layouts.main')

<?php
use App\Models\Rosterapplication;

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
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> motivation_narrative: </label>
                    <input type="text" name="motivation_narrative" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> primary_function_evidence: </label>
                    <input type="text" name="primary_function_evidence" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> shared_client_management_plan: </label>
                    <input type="text" name="shared_client_management_plan" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> error_discovery_resolution: </label>
                    <input type="text" name="error_discovery_resolution" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> urgent_friday_deadline_dilemma: </label>
                    <input type="text" name="urgent_friday_deadline_dilemma" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> associate_apprentice_qa_methodology: </label>
                    <input type="text" name="associate_apprentice_qa_methodology" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> associate_unethical_client_solution: </label>
                    <input type="text" name="associate_unethical_client_solution" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> apprentice_twelve_month_goal: </label>
                    <input type="text" name="apprentice_twelve_month_goal" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> additional_notes: </label>
                    <input type="text" name="additional_notes" class="form-control form-control-lg" />
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
