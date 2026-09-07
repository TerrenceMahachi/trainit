@extends('layouts.main')

<?php
use App\Models\User;
use App\Models\Applicationtrack;
use App\Models\Applicationstatus;
use App\Models\Servicefunction;
use App\Models\Gender;
use App\Models\Zimprovince;
use App\Models\Workrightstatus;

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
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> User: </label>
                    <select class="form-select form-select-lg f-sel" name="user" required>
                        <option value="">Select User</option>
                        <?php foreach (User::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Applicationtrack: </label>
                    <select class="form-select form-select-lg f-sel" name="applicationtrack" required>
                        <option value="">Select Applicationtrack</option>
                        <?php foreach (Applicationtrack::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Applicationstatus: </label>
                    <select class="form-select form-select-lg f-sel" name="applicationstatus" required>
                        <option value="">Select Applicationstatus</option>
                        <?php foreach (Applicationstatus::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Primaryfunction: </label>
                    <select class="form-select form-select-lg f-sel" name="primaryfunction" required>
                        <option value="">Select Primaryfunction</option>
                        <?php foreach (Servicefunction::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> legal_name: </label>
                    <input type="text" name="legal_name" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> preferred_name: </label>
                    <input type="text" name="preferred_name" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> email: </label>
                    <input type="text" name="email" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> mobile_number: </label>
                    <input type="text" name="mobile_number" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> whatsapp_number: </label>
                    <input type="text" name="whatsapp_number" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> date_of_birth: </label>
                    <input type="date" name="date_of_birth" class="form-control form-control-lg" />
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Gender: </label>
                    <select class="form-select form-select-lg f-sel" name="gender" required>
                        <option value="">Select Gender</option>
                        <?php foreach (Gender::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> city: </label>
                    <input type="text" name="city" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> suburb: </label>
                    <input type="text" name="suburb" class="form-control form-control-lg" />
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Zimprovince: </label>
                    <select class="form-select form-select-lg f-sel" name="zimprovince" required>
                        <option value="">Select Zimprovince</option>
                        <?php foreach (Zimprovince::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> country: </label>
                    <input type="text" name="country" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> nationality: </label>
                    <input type="text" name="nationality" class="form-control form-control-lg" />
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Workrightstatus: </label>
                    <select class="form-select form-select-lg f-sel" name="workrightstatus" required>
                        <option value="">Select Workrightstatus</option>
                        <?php foreach (Workrightstatus::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> work_permit_number: </label>
                    <input type="text" name="work_permit_number" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> work_permit_expiry: </label>
                    <input type="date" name="work_permit_expiry" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> has_disability_adjustment: </label>
                    <input type="text" name="has_disability_adjustment" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> adjustment_details: </label>
                    <input type="text" name="adjustment_details" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> how_heard: </label>
                    <input type="text" name="how_heard" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> referred_by: </label>
                    <input type="text" name="referred_by" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> consent_version: </label>
                    <input type="text" name="consent_version" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> consent_timestamp: </label>
                    <input type="datetime-local" name="consent_timestamp" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> consent_ip_address: </label>
                    <input type="text" name="consent_ip_address" class="form-control form-control-lg" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> e_signature: </label>
                    <input type="text" name="e_signature" class="form-control form-control-lg" />
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
