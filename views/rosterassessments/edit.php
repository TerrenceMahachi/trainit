@extends('layouts.main')

<?php
use App\Models\Rosterapplication;
use App\Models\User;
use App\Models\Vettingrecommendation;

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
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Reviewer: </label>
                    <select class="form-select form-select-lg f-sel" name="reviewer" required>
                        <option value="">Select Reviewer</option>
                        <?php foreach (User::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>" <?= $data['record']->reviewer == $selector->iD ? 'selected' : ''; ?>><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Vettingrecommendation: </label>
                    <select class="form-select form-select-lg f-sel" name="vettingrecommendation" required>
                        <option value="">Select Vettingrecommendation</option>
                        <?php foreach (Vettingrecommendation::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>" <?= $data['record']->vettingrecommendation == $selector->iD ? 'selected' : ''; ?>><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> eligibility_gate_passed: </label>
                    <input type="text" name="eligibility_gate_passed" class="form-control form-control-lg" value="<?= htmlspecialchars($item->eligibility_gate_passed ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> technical_fit_score: </label>
                    <input type="number" name="technical_fit_score" class="form-control form-control-lg" value="<?= htmlspecialchars($item->technical_fit_score ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> evidence_score: </label>
                    <input type="number" name="evidence_score" class="form-control form-control-lg" value="<?= htmlspecialchars($item->evidence_score ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> judgement_score: </label>
                    <input type="number" name="judgement_score" class="form-control form-control-lg" value="<?= htmlspecialchars($item->judgement_score ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> availability_score: </label>
                    <input type="number" name="availability_score" class="form-control form-control-lg" value="<?= htmlspecialchars($item->availability_score ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> motivation_score: </label>
                    <input type="number" name="motivation_score" class="form-control form-control-lg" value="<?= htmlspecialchars($item->motivation_score ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> total_score: </label>
                    <input type="number" name="total_score" class="form-control form-control-lg" value="<?= htmlspecialchars($item->total_score ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> automated_red_flags: </label>
                    <input type="text" name="automated_red_flags" class="form-control form-control-lg" value="<?= htmlspecialchars($item->automated_red_flags ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> interview_notes: </label>
                    <input type="text" name="interview_notes" class="form-control form-control-lg" value="<?= htmlspecialchars($item->interview_notes ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> technical_test_result: </label>
                    <input type="text" name="technical_test_result" class="form-control form-control-lg" value="<?= htmlspecialchars($item->technical_test_result ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> vetted_at: </label>
                    <input type="datetime-local" name="vetted_at" class="form-control form-control-lg" value="<?= htmlspecialchars($item->vetted_at ?? '', ENT_QUOTES) ?>" />
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
