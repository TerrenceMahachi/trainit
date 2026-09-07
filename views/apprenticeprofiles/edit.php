@extends('layouts.main')

<?php
use App\Models\Rosterapplication;
use App\Models\Apprenticestatus;
use App\Models\Engagementmodel;
use App\Models\Worklocationpreference;

include __DIR__ . '/header.php';
?>

<div>
    <!-- Page header -->
    <section class="hero-section">
        <div class="container text-start">
            <h3>Edit <?= $page_name ?></h3>
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

    <!-- Edit form -->
    <section class="py-4">
        <div class="container rounded-4 p-4 bg-white shadow-sm">

            <?php include __DIR__ . '/nav.php'; ?>

            <form class="form py-3 m-md-3" id="update_item_form">
                <div class="row">
                    
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Rosterapplication: </label>
                    <select class="form-select form-select-lg f-sel" name="rosterapplication" required>
                        <option value="">Select Rosterapplication</option>
                        <?php foreach (Rosterapplication::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>" <?= $data['record']->rosterapplication == $selector->iD ? 'selected' : ''; ?>><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Apprenticestatus: </label>
                    <select class="form-select form-select-lg f-sel" name="apprenticestatus" required>
                        <option value="">Select Apprenticestatus</option>
                        <?php foreach (Apprenticestatus::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>" <?= $data['record']->apprenticestatus == $selector->iD ? 'selected' : ''; ?>><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> institution_name: </label>
                    <input type="text" name="institution_name" class="form-control form-control-lg" value="<?= htmlspecialchars($item->institution_name ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> degree_programme: </label>
                    <input type="text" name="degree_programme" class="form-control form-control-lg" value="<?= htmlspecialchars($item->degree_programme ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> study_level: </label>
                    <input type="text" name="study_level" class="form-control form-control-lg" value="<?= htmlspecialchars($item->study_level ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> student_reg_number: </label>
                    <input type="text" name="student_reg_number" class="form-control form-control-lg" value="<?= htmlspecialchars($item->student_reg_number ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> expected_completion_date: </label>
                    <input type="date" name="expected_completion_date" class="form-control form-control-lg" value="<?= htmlspecialchars($item->expected_completion_date ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> is_wrl_attachment: </label>
                    <input type="text" name="is_wrl_attachment" class="form-control form-control-lg" value="<?= htmlspecialchars($item->is_wrl_attachment ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> wrl_start_date: </label>
                    <input type="date" name="wrl_start_date" class="form-control form-control-lg" value="<?= htmlspecialchars($item->wrl_start_date ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> wrl_end_date: </label>
                    <input type="date" name="wrl_end_date" class="form-control form-control-lg" value="<?= htmlspecialchars($item->wrl_end_date ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> wrl_duration_months: </label>
                    <input type="number" name="wrl_duration_months" class="form-control form-control-lg" value="<?= htmlspecialchars($item->wrl_duration_months ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> wrl_coordinator_name: </label>
                    <input type="text" name="wrl_coordinator_name" class="form-control form-control-lg" value="<?= htmlspecialchars($item->wrl_coordinator_name ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> wrl_coordinator_email: </label>
                    <input type="text" name="wrl_coordinator_email" class="form-control form-control-lg" value="<?= htmlspecialchars($item->wrl_coordinator_email ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> wrl_coordinator_phone: </label>
                    <input type="text" name="wrl_coordinator_phone" class="form-control form-control-lg" value="<?= htmlspecialchars($item->wrl_coordinator_phone ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> requires_placement_letter: </label>
                    <input type="text" name="requires_placement_letter" class="form-control form-control-lg" value="<?= htmlspecialchars($item->requires_placement_letter ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> requires_host_mou: </label>
                    <input type="text" name="requires_host_mou" class="form-control form-control-lg" value="<?= htmlspecialchars($item->requires_host_mou ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> requires_logbook_visits: </label>
                    <input type="text" name="requires_logbook_visits" class="form-control form-control-lg" value="<?= htmlspecialchars($item->requires_logbook_visits ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> requires_host_insurance: </label>
                    <input type="text" name="requires_host_insurance" class="form-control form-control-lg" value="<?= htmlspecialchars($item->requires_host_insurance ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> min_stipend_required: </label>
                    <input type="number" name="min_stipend_required" class="form-control form-control-lg" value="<?= htmlspecialchars($item->min_stipend_required ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Engagementmodel: </label>
                    <select class="form-select form-select-lg f-sel" name="engagementmodel" required>
                        <option value="">Select Engagementmodel</option>
                        <?php foreach (Engagementmodel::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>" <?= $data['record']->engagementmodel == $selector->iD ? 'selected' : ''; ?>><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Worklocationpreference: </label>
                    <select class="form-select form-select-lg f-sel" name="worklocationpreference" required>
                        <option value="">Select Worklocationpreference</option>
                        <?php foreach (Worklocationpreference::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>" <?= $data['record']->worklocationpreference == $selector->iD ? 'selected' : ''; ?>><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> proof_of_registration_doc: </label>
                    <input type="text" name="proof_of_registration_doc" class="form-control form-control-lg" value="<?= htmlspecialchars($item->proof_of_registration_doc ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> transcript_doc: </label>
                    <input type="text" name="transcript_doc" class="form-control form-control-lg" value="<?= htmlspecialchars($item->transcript_doc ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> current_average_grade: </label>
                    <input type="text" name="current_average_grade" class="form-control form-control-lg" value="<?= htmlspecialchars($item->current_average_grade ?? '', ENT_QUOTES) ?>" />
                </div>

                    <div class="form-group col-sm-12 mb-4">
                        <label class="mb-2" for="status">Status: </label>
                        <select class="form-select form-select-lg f-sel" name="status">
                            <option value="1" <?= $item->status == "1" ? 'selected' : ''; ?>>Active</option>
                            <option value="2" <?= $item->status == "2" ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="mt-2" id="form_result"></div>
                    <input type="hidden" name="itemiD" value="<?= $item->iD; ?>" />

                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" id="btn_edit_item" class="btn submit-btn btn-lg">Update Record</button>
                        <a class="d-block my-3 text-start auth-link"
                            href="<?php echo $siteConfig->siteUrl; ?>/<?= $page ?>">
                            <i class="fa fa-arrow-left me-2"></i> Back to <?= $page_name ?>s</a>
                    </div>
                </div>
            </form>

        </div>
    </section>
</div>
