@extends('layouts.main')


<!-- Navigation -->
<?php global $siteConfig; ?>
<div>
    <!-- Hero Section -->

    <section class="hero-section">

        <div class="container text-start">

            <p>
            <nav aria-label="breadcrumb border rounded-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo $siteConfig->siteUrl; ?>/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo $siteConfig->siteUrl; ?>/edit-profile">Profile</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Basic details</li>
                </ol>
            </nav>
            </p>



        </div>
    </section>
    <!-- Categories Section -->


    <!-- Search Section -->
    <section id="search" class="py-5 bg-light">
        <div class="container">

            <ul class="nav nav-tabs my-4">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?php echo $siteConfig->siteUrl; ?>/edit-profile">Basic details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $siteConfig->siteUrl; ?>/edit-password">Password</a>
                </li>
            </ul>
            <div class="row pt-4">
                <div class="col-md-8 offset-md-2">


                    <form class="form border rounded-5 p-5 bg-white shadow" id="update_profile_form">

                        <div class="form-group mb-5">
                            <label for="location">Name</label>
                            <input type="text" name="name" value="<?= $data['user']->name; ?>" class="form-control"
                                required />
                        </div>
                        <div class="form-group mb-5">
                            <label for="location">Email address</label>
                            <input type="text" name="email" class="form-control" value="<?= $data['user']->email; ?>"
                                required />
                        </div>

                        <div class="form-group mb-5">
                            <label for="location">Current password</label>
                            <input type="password" name="password" class="form-control" />
                        </div>

                        <p id="update_profile_form_result"></p>
                        <hr>
                        <br>
                        <div class="d-flex justify-content-center">
                            <button type="button" id="btn_submit_profile"
                                class="btn button1 w-75 rounded-pill mx-auto btn-lg btn-block">Update
                                Profile</button>

                        </div>


                    </form>

                </div>
            </div>
           
        </div>
    </section>
</div>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/account/edit-profile.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script>
    $(document).ready(function () {
    });
</script>