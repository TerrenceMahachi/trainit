@extends('layouts.main')

<?php
use function APP\Helpers\formatDate;
global $siteConfig;
?>
<div>
    <section class="hero-section">
        <div class="container text-center">
            <h3>Confirm your account</h3>
        </div>
    </section>

    <section id="categories" class="py-5">
        <div class="container">
            <div class="row mt-4">
                <div class="col-md-8 offset-md-2">

                    <form method="POST" action="<?= $siteConfig->siteUrl; ?>/confirm"
                        class="border rounded-4 p-4 p-md-5 bg-white shadow-sm">
                        <div class="form-group mb-3">
                            <label for="location">Your email address</label>
                            <input type="text" name="email" value="" class="form-control" required disabled />
                        </div>
                        <div class="form-group mb-3">
                            <label for="location">Name</label>
                            <input type="text" name="name" class="form-control" required />
                        </div>
                        <div class="form-group mb-3">
                            <label for="location">Phone</label>
                            <input type="text" name="phone" class="form-control" required />
                        </div>
                        <div class="form-group mb-4">
                            <label for="location">Password</label>
                            <input type="password" name="password" class="form-control" required />
                        </div>

                        <input type="hidden" name="token" value='<?php echo $_GET['token']; ?>' />

                        <hr>
                        <div class="d-flex justify-content-center">
                            <button type="submit" id="btn_submit"
                                class="btn button1 w-75 rounded-pill mx-auto btn-lg">Submit confirmation</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>

</div>
