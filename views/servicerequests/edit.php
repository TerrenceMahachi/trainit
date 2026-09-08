@extends('layouts.main')

<?php
use App\Models\Clientorganization;
use App\Models\Clientserviceplan;
use App\Models\User;
use App\Models\Prioritylevel;

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
                    
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> request_number: </label>
                    <input type="text" name="request_number" class="form-control form-control-lg" value="<?= htmlspecialchars($item->request_number ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Clientorganization: </label>
                    <select class="form-select form-select-lg f-sel" name="clientorganization" required>
                        <option value="">Select Clientorganization</option>
                        <?php foreach (Clientorganization::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>" <?= $data['record']->clientorganization == $selector->iD ? 'selected' : ''; ?>><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Clientserviceplan: </label>
                    <select class="form-select form-select-lg f-sel" name="clientserviceplan" required>
                        <option value="">Select Clientserviceplan</option>
                        <?php foreach (Clientserviceplan::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>" <?= $data['record']->clientserviceplan == $selector->iD ? 'selected' : ''; ?>><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Requester: </label>
                    <select class="form-select form-select-lg f-sel" name="requester" required>
                        <option value="">Select Requester</option>
                        <?php foreach (User::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>" <?= $data['record']->requester == $selector->iD ? 'selected' : ''; ?>><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group mb-4 col-md-12">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> Prioritylevel: </label>
                    <select class="form-select form-select-lg f-sel" name="prioritylevel" required>
                        <option value="">Select Prioritylevel</option>
                        <?php foreach (Prioritylevel::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>" <?= $data['record']->prioritylevel == $selector->iD ? 'selected' : ''; ?>><?= htmlspecialchars($selector->name ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> title: </label>
                    <input type="text" name="title" class="form-control form-control-lg" value="<?= htmlspecialchars($item->title ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> description: </label>
                    <input type="text" name="description" class="form-control form-control-lg" value="<?= htmlspecialchars($item->description ?? '', ENT_QUOTES) ?>" />
                </div>
                <div class="form-group col-md-12 mb-4">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> desired_due_date: </label>
                    <input type="date" name="desired_due_date" class="form-control form-control-lg" value="<?= htmlspecialchars($item->desired_due_date ?? '', ENT_QUOTES) ?>" />
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
