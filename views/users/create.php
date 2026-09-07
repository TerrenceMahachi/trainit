@extends('layouts.main')


<!-- Navigation -->
<?php global $siteConfig; ?>
<div>
    <!-- Hero Section -->


    <!-- Categories Section -->
    <section class="hero-section" style="background-size: 100% auto; ">

        <div class="container text-start">

            <p>
            <nav aria-label="breadcrumb border rounded-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" onclick="history.back()">Back</a></li>
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/users">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>


                </ol>
            </nav>
            </p>



        </div>
    </section>

    <!-- Search Section -->
    <section id="search" class="py-5 bg-light">
        <div class="container">

            <div class="row mt-4">
                <div class="col-md-8 offset-md-2">
                    <form class="form border rounded-3 p-5 bg-white shadow-sm" id="create_user_form">

                       

                        <div class="form-group mb-5">
                            <label for="role">Role</label>
                            <select class="form-select form-select-lg f-sel" id="role" name="role" data-table="user_role"
                                data-property="role" required>
                                <option value="">Select role </option>
                                <?php foreach ($data['roles'] as $item): ?>
                                    <option value="<?php echo $item->iD; ?>" <?= isset($_POST['role']) && $_POST['role'] == $item->iD ? 'selected' : ''; ?>>
                                        <?php echo $item->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-5">
                            <label for="name">Name of user</label>
                            <input type="text" name="name" class="form-control form-control-lg"
                                value="<?= isset($_POST['name']) ? $_POST['name'] : ''; ?>" required />
                        </div>

                        <div class="form-group mb-5">
                            <label for="email">User's email address</label>
                            <input type="email" name="email" class="form-control form-control-lg"
                                value="<?= isset($_POST['email']) ? $_POST['email'] : ''; ?>" required />
                        </div>

                        <div class="form-group mb-5">
                            <label for="password">Password</label>
                            <input type="text" name="password" class="form-control form-control-lg" value="<?= $data['password']; ?>"
                                required />
                        </div>

                        <p id="form_result"></p>
                        <hr>

                        <div class="d-flex justify-content-center">
                            <button type="button" id="btn_create_user"
                                class="btn button1 w-75 rounded-pill mx-auto btn-lg btn-block">Add user</button>
                        </div>

                    </form>


                </div>
            </div>
        </div>
    </section>

</div>

<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/users.js?id=<?php echo rand(); ?>"></script>