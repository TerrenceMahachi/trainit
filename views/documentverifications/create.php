@extends('layouts.main')

<?php
use App\Models\Staffdocument;
use App\Models\Verificationstatus;

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
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Staffdocument: </label>
                    <select class="form-select form-select-lg f-sel" name="staffdocument" required>
                        <option value="">Select Staffdocument</option>
                        <?php foreach (Staffdocument::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Verificationstatus: </label>
                    <select class="form-select form-select-lg f-sel" name="verificationstatus" required>
                        <option value="">Select Verificationstatus</option>
                        <?php foreach (Verificationstatus::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> notes: </label>
                    <input type="text" name="notes" class="form-control form-control-lg" />
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
