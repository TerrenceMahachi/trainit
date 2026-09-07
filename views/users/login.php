@extends('layouts.main')


<!-- Navigation -->

<?php use function APP\Helpers\formatDate;
global $siteConfig;
$item = $data['record']; ?>
<div>
    <!-- Hero Section -->

    <section class="hero-section" style="background-size: 100% auto; ">

        <div class="container text-start">

            <p>
            <nav aria-label="breadcrumb border rounded-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" onclick="history.back()">Back</a></li>
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/users">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $item->name; ?></li>


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
                    <a class="nav-link" aria-current="page"
                        href="<?php echo $siteConfig->siteUrl; ?>/users/view/<?= $item->iD; ?>">View User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  " href="<?php echo $siteConfig->siteUrl; ?>/users/edit/<?= $item->iD; ?>">Edit
                        User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  active"
                        href="<?php echo $siteConfig->siteUrl; ?>/users/login/<?= $item->iD; ?>">User Login</a>
                </li>
            </ul>
            <div class="row pt-4">
                <div class="col-md-8 offset-md-2">


                    <form class="form border rounded-5 p-5 bg-white shadow-sm" id="update_user_form">


                        <div class="form-group mb-5">
                        <label class="text-muted fw-lighter fs-6" for="location">Login Status: </label>

                            <div class="row">
                            <div class="col-sm-8">
                            <?php if($data['allow']=='1'): ?>
                            <span class="view-text">Allow Login</span>
                            <?php else: ?>
                                <span class="view-text"><?= $item->login()[0]->status()->name; ?></span>
                            <?php endif; ?>


                            </div>
                            <div class="col-sm-4">
                            <?php if($data['allow']=='1'): ?>
                                <button class="btn button1" type="button" id="btn_stop_login">Block Login</button>

                            <?php else: ?>
                                <button class="btn button1" type="button" id="btn_allow_login">Activate Login</button>

                            <?php endif; ?>

                            </div>
                            </div>
                        </div>
                        <div class="input-group input-group-lg">
                            <input type="text" name="password" id="password" class="form-control" placeholder="New Password"
                                value="">
                            <button class="btn button1" type="button" id="btn_update_password">Update Password</button>
                           
                        </div>


                        <input type="hidden" name="user" id="user" value="<?= $item->iD; ?>" />
                        <br>

                        <p id="update_user_form_result"></p>
                       

                    </form>

                </div>
            </div>

        </div>
    </section>
</div>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/users/login.js?id=<?php echo rand(); ?>"></script>