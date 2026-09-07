@extends('layouts.main')


<!-- Navigation -->
<?php

global $siteConfig;
use App\Models\User;

?>
<div>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1> Apply</h1>
            <p>Welcome, </p>



        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-5">
        <div class="container">

            <div class="row ">
                <div class="col-md-8 offset-md-2">
                    <form class="form border rounded-5 p-5 bg-white shadow-sm" id="create_item_form">



                        <div class="form-group mb-5">
                            <label for="name">Name of Fund</label>
                            <input type="text" name="name" class="form-control form-control-lg" />
                        </div>
                        <div class="form-group mb-5">
                            <label for="name">Description of fund</label>
                            <textarea name="description" class="form-control form-control-lg">.</textarea>
                        </div>

                        <div class="form-group mb-5">
                            <label for="role">Currency</label>
                            <select class="form-select form-select-lg f-sel" name="currency" data-property="role">
                                <option value="">Select currency </option>
                                <?php foreach ($data['currencies'] as $item): ?>
                                    <option value="<?php echo $item->iD; ?>">
                                        <?php echo $item->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-5">
                            <label for="name">Monthly contribution amount</label>
                            <input type="number" name="contribution" value="1.00"
                                class="form-control form-control-lg" />
                        </div>
                        <div class="form-group mb-5">
                            <label for="role">Fund Accessibility</label>
                            <select class="form-select form-select-lg f-sel" name="privacy" data-property="role">
                                <?php foreach ($data['privacy'] as $item): ?>
                                    <option value="<?php echo $item->iD; ?>">
                                        <?php echo $item->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if (!isset($_COOKIE['user'])): ?>
                            <p class="fw-bold">Authenticate</p><br>
                            <div class="form-group mb-5">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="accountType" id="existing"
                                        value="1" checked>
                                    <label class="form-check-label" for="existing">Registered User</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="accountType" id="new" value="2">
                                    <label class="form-check-label" for="new">New User</label>
                                </div>
                            </div>

                            <div class="form-group mb-5" id="nameField" style="display: none;">
                                <label for="name">Name</label>
                                <input type="text" class="form-control form-control-lg" name="name" placeholder="Enter your name">
                            </div>

                            <div class="form-group mb-5">
                                <label for="email">Email</label>
                                <input type="email" class="form-control form-control-lg" name="email" placeholder="Enter your email">
                            </div>

                            <div class="form-group mb-5">
                                <label for="password">Password</label>
                                <input type="password" class="form-control form-control-lg" name="password" placeholder="Enter your password">
                            </div>

                        <?php endif; ?>
                        <div class="mt-2" id="form_result"></div>

                        <hr>
                        <br>

                        <div class="d-flex justify-content-center">
                            <button type="button" id="btn_create_item"
                                class="btn button1 w-75 rounded-pill mx-auto btn-lg btn-block">Create fund</button>
                        </div>

                    </form>


                </div>
            </div>
        </div>
    </section>



    <!-- Search Section -->

</div>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/start-fund.js?id=<?php echo rand(); ?>"></script>