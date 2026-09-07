@extends('layouts.main')
<?php
use App\Models\User;


include('header.php');
?>

<div>
    <!-- Hero Section -->


    <section class="hero-section" style="background-size: 100% auto; ">

        <div class="container text-start">

            <p>
            <nav aria-label="breadcrumb border rounded-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-capitalize"
                            href="<?= $siteConfig->siteUrl ?>/<?= $page ?>"><?= $page_name ?>s</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $item->name; ?></li>
                </ol>
            </nav>
            </p>



        </div>
    </section>

    <section id="search" class="py-5 ">
        <div class="container  rounded-3 p-4 border">

            <?php include("nav.php"); ?>

            <form class="form  py-5 m-3 " id="update_item_form">
                <div class="row">
                    
                <div class="form-group col-md-6 mb-4">
                    <label class="mb-3"> name: </label>
                    <input type="text" name="name" class="form-control form-control-lg" value="<?= $item->name ?>" />
                </div>
                <div class="form-group mb-4 col-md-6">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> User: </label>
                    <select class="form-select form-select-lg f-sel" name="user" required>
                        <option value="">Select User</option>
                        <?php foreach (User::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>" <?= $data['record']->user == $selector->iD ? 'selected' : ''; ?>><?= $selector->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>


                    <div class="form-group col-md-6 mb-4">
                        <label class="mb-3" for="location">Status: </label>
                        <select class="form-select  form-select-lg  f-sel" name="status">
                            <option value="1" <?= $item->status == "1" ? 'selected' : ''; ?>>Active</option>
                            <option value="2" <?= $item->status == "2" ? 'selected' : ''; ?>>inActive
                            </option>

                        </select>
                    </div>
                    <p id="form_result"></p>
                    <br>
                    <input type="hidden" name="itemiD" value="<?= $item->iD; ?>" />

                    <div class="d-flex justify-content-between">
                        <button type="button" id="btn_edit_item" class="btn submit-btn btn-lg ">Update
                            Record</button>
                        <a class="d-block my-3 text-start  text-black"
                            href="<?php echo $siteConfig->siteUrl; ?>/<?= $page ?>"> <i
                                class="fa fa-arrow-left me-2"></i> Back to <?= $page_name ?>s</a>
                    </div>

                </div>
            </form>


        </div>
    </section>

</div>