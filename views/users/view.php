@extends('layouts.main')


<!-- Navigation -->

<?php 
use function APP\Helpers\formatDate;
use function APP\Helpers\formatDateTime;
global $siteConfig; $item = $data['record'];?>
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
                    <a class="nav-link active" aria-current="page" href="<?php echo $siteConfig->siteUrl; ?>/users/view/<?= $item->iD; ?>">View User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="<?php echo $siteConfig->siteUrl; ?>/users/edit/<?= $item->iD; ?>">Edit User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  " href="<?php echo $siteConfig->siteUrl; ?>/users/login/<?= $item->iD; ?>">User Login</a>
                </li>
            </ul>
            <div class="row pt-4">
                <div class="col-md-8 offset-md-2">


                    <form class="form" id="update_user_form">
                  
                        <div class="form-group mb-4 border rounded-2 p-3 ps-5 bg-white shadow-sm">
                            <label class="text-muted fw-lighter fs-6" for="location">Name: </label>
                            <span class="view-text"><?= $item->name; ?></span>
                        </div>
                        <div class="form-group mb-4 border rounded-2 p-3 ps-5 bg-white shadow-sm">
                            <label class="text-muted fw-lighter fs-6" for="location">Email: </label>
                            <span class="view-text"><?= $item->email; ?></span>
                        </div>
                        <div class="form-group mb-4 border rounded-2 p-3 ps-5 bg-white shadow-sm">
                            <label class="text-muted fw-lighter fs-6" for="location">Role: </label>
                            <span class="view-text"><?= $item->role()->name;  ?></span>
                        </div>
                        <div class="form-group mb-4 border rounded-2 p-3 ps-5 bg-white shadow-sm">
                            <label class="text-muted fw-lighter fs-6" for="location">Date Registered: </label>
                            <span class="view-text"><?= formatDate($item->reg_date); ?></span>
                        </div>
                        <div class="form-group mb-4 border rounded-2 p-3 ps-5 bg-white shadow-sm">
                            <label class="text-muted fw-lighter fs-6" for="location">Recorded By: </label>
                            <span class="view-text"><?= $item->creator()->name; ?></span>
                        </div>
                        <div class="form-group mb-4 border rounded-2 p-3 ps-5 bg-white shadow-sm">
                            <label class="text-muted fw-lighter fs-6" for="location">Login Status: </label>
                            <span class="view-text"><?= $item->login()[0]->status()->name; ?></span>
                        </div>
                        <div class="form-group mb-4 border rounded-2 p-3 ps-5 bg-white shadow-sm">
                            <label class="text-muted fw-lighter fs-6" for="location">Last Login: </label>
                            <span class="view-text"><?= formatDateTime($item->login()[0]->last_login); ?></span>
                        </div>
                        <div class="form-group mb-5 border rounded-2 p-3 ps-5 bg-white shadow-sm">
                            <label class="text-muted fw-lighter fs-6" for="location">Login Count: </label>
                            <span class="view-text"><?= $item->login()[0]->login_count; ?></span>
                        </div>
                        
                       

                      

                    </form>

                </div>
            </div>
           
        </div>
    </section>
    
</div>