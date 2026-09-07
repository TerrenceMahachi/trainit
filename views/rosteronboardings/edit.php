@extends('layouts.main')

<?php
use App\Models\Rosterapplication;

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
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> national_id_number: </label>
                    <input type="text" name="national_id_number" class="form-control form-control-lg" value="<?= htmlspecialchars($item->national_id_number ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> national_id_doc: </label>
                    <input type="text" name="national_id_doc" class="form-control form-control-lg" value="<?= htmlspecialchars($item->national_id_doc ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> passport_number: </label>
                    <input type="text" name="passport_number" class="form-control form-control-lg" value="<?= htmlspecialchars($item->passport_number ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> passport_expiry: </label>
                    <input type="date" name="passport_expiry" class="form-control form-control-lg" value="<?= htmlspecialchars($item->passport_expiry ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> street_address: </label>
                    <input type="text" name="street_address" class="form-control form-control-lg" value="<?= htmlspecialchars($item->street_address ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> city: </label>
                    <input type="text" name="city" class="form-control form-control-lg" value="<?= htmlspecialchars($item->city ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> country: </label>
                    <input type="text" name="country" class="form-control form-control-lg" value="<?= htmlspecialchars($item->country ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> bank_name: </label>
                    <input type="text" name="bank_name" class="form-control form-control-lg" value="<?= htmlspecialchars($item->bank_name ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> bank_branch: </label>
                    <input type="text" name="bank_branch" class="form-control form-control-lg" value="<?= htmlspecialchars($item->bank_branch ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> account_name: </label>
                    <input type="text" name="account_name" class="form-control form-control-lg" value="<?= htmlspecialchars($item->account_name ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> account_number: </label>
                    <input type="text" name="account_number" class="form-control form-control-lg" value="<?= htmlspecialchars($item->account_number ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> bank_currency: </label>
                    <input type="text" name="bank_currency" class="form-control form-control-lg" value="<?= htmlspecialchars($item->bank_currency ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> emergency_contact_name: </label>
                    <input type="text" name="emergency_contact_name" class="form-control form-control-lg" value="<?= htmlspecialchars($item->emergency_contact_name ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> emergency_contact_phone: </label>
                    <input type="text" name="emergency_contact_phone" class="form-control form-control-lg" value="<?= htmlspecialchars($item->emergency_contact_phone ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> emergency_contact_relationship: </label>
                    <input type="text" name="emergency_contact_relationship" class="form-control form-control-lg" value="<?= htmlspecialchars($item->emergency_contact_relationship ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> nssa_number: </label>
                    <input type="text" name="nssa_number" class="form-control form-control-lg" value="<?= htmlspecialchars($item->nssa_number ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> police_clearance_doc: </label>
                    <input type="text" name="police_clearance_doc" class="form-control form-control-lg" value="<?= htmlspecialchars($item->police_clearance_doc ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> police_clearance_date: </label>
                    <input type="date" name="police_clearance_date" class="form-control form-control-lg" value="<?= htmlspecialchars($item->police_clearance_date ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> signed_contract_doc: </label>
                    <input type="text" name="signed_contract_doc" class="form-control form-control-lg" value="<?= htmlspecialchars($item->signed_contract_doc ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> signed_nda_doc: </label>
                    <input type="text" name="signed_nda_doc" class="form-control form-control-lg" value="<?= htmlspecialchars($item->signed_nda_doc ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> odoo_applicant_id: </label>
                    <input type="number" name="odoo_applicant_id" class="form-control form-control-lg" value="<?= htmlspecialchars($item->odoo_applicant_id ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> odoo_employee_id: </label>
                    <input type="number" name="odoo_employee_id" class="form-control form-control-lg" value="<?= htmlspecialchars($item->odoo_employee_id ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> synced_to_odoo_at: </label>
                    <input type="datetime-local" name="synced_to_odoo_at" class="form-control form-control-lg" value="<?= htmlspecialchars($item->synced_to_odoo_at ?? '', ENT_QUOTES) ?>" />
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
