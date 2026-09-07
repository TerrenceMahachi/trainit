@extends('layouts.main')


<!-- Navigation -->

<?php use function APP\Helpers\formatDate; global $siteConfig; $item = $data['record'];?>
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
                    <a class="nav-link" aria-current="page" href="<?php echo $siteConfig->siteUrl; ?>/users/view/<?= $item->iD; ?>">View User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  active" href="<?php echo $siteConfig->siteUrl; ?>/users/edit/<?= $item->iD; ?>">Edit User</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  " href="<?php echo $siteConfig->siteUrl; ?>/users/login/<?= $item->iD; ?>">User Login</a>
                </li>
            </ul>
            <div class="row pt-4">
                <div class="col-md-8 offset-md-2">


                    <form class="form border rounded-5 p-5 bg-white shadow-sm" id="update_user_form">
                  
                        <div class="form-group mb-5">
                            <label class="text-muted fw-lighter fs-6" for="location">Name</label>
                            <input type="text" name="name" class="form-control form-control-lg" value="<?= $item->name; ?>" />
                        </div>
                        <div class="form-group mb-5">
                            <label for="location">Email</label>
                            <input type="text" name="email" class="form-control form-control-lg" value="<?= $item->email; ?>" />
                        </div>
                        <div class="form-group mb-5">
                            <label for="role">Role</label>
                            <select class="form-select form-select-lg f-sel" id="role" name="role" data-table="user_role"
                                data-property="role" required>
                                <option value="">Select role </option>
                                <?php foreach ($data['roles'] as $item): ?>
                                    <option value="<?php echo $item->iD; ?>" <?= $data['record']->role == $item->iD ? 'selected' : ''; ?>>
                                        <?php echo $item->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                       
                        <input type="hidden" name="user" value="<?= $item->iD; ?>" />

                        <p id="update_user_form_result"></p>
                        <hr><br>
                        <div class="d-flex justify-content-center">
                            <button type="button" id="btn_update_user"
                                class="btn button1 w-75 rounded-pill text-white mx-auto btn-lg btn-block">Update User</button>

                        </div>


                    </form>

                </div>
            </div>
           
        </div>
    </section>
</div>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/users/edit.js?id=<?php echo rand(); ?>"></script>
