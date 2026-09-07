
@extends('layouts.main')

<?php
use App\Models\User;

include('header.php');
?>

<div>
    <!-- Hero Section -->


    <section class="hero-section">
        <div class="container text-start">
            <h3><?= $page_name ?>s</h3>
            <nav aria-label="breadcrumb border rounded-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-capitalize"
                            href="<?= $siteConfig->siteUrl ?>/<?= $page ?>"><?= $page_name ?>s</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create New</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Search Section -->
    <section class="py-5 ">
        <div class="container rounded-3 p-4 border">

            <form class="form  py-5 m-3 " id="create_item_form">
                
                <div class="row">
                   
                    
                <div class="form-group col-md-6 mb-4">
                    <label class="mb-3"> name: </label>
                    <input type="text" name="name" class="form-control form-control-lg" />
                </div>
                <div class="form-group mb-4 col-md-6">
                    <label class="text-muted fw-lighter fs-6 mb-3 text-muted"> User: </label>
                    <select class="form-select form-select-lg f-sel" name="user" required>
                        <option value="">Select User</option>
                        <?php foreach (User::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= $selector->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                </div>


                <div class="mt-2" id="form_result"></div>

                <br>

                <input type="hidden" name="Method" value="add_item">
                <div class="d-flex justify-content-between">
                    <button type="button" id="btn_create_item" class="btn submit-btn btn-lg ">Submit Record</button>
                    <a class="d-block my-3 text-start  text-black"
                        href="<?php echo $siteConfig->siteUrl; ?>/<?= $page ?>"> <i
                            class="fa fa-arrow-left me-2"></i> Back to <?= $page_name ?>s</a>
                </div>
                <p class="mb-1 text-center">

                </p>

            </form>


        </div>


    </section>
    <!-- Search Section -->
  

</div>

