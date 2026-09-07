@extends('layouts.main')

<?php
include('header.php');
use App\Models\UserProfile;
use App\Models\User;

use function APP\Helpers\formatDateTime;

?>

<!-- Navigation -->

<div>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h3><?= $page_name ?>s</h3>
            <nav aria-label="breadcrumb border rounded-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $page_name ?>s</li>

                </ol>
            </nav>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-2">
        <div class="container  rounded-3 p-4 border">
            <div class=" p-3">

                <div class="row justify-content-between">
                    <div class="col-auto">
                        <form id="searchForm" class="">
                            <div class="d-flex">
                                <input type="search" name="search" class="form-control search-input"
                                    placeholder="Search ">
                                <button class="btn search-btn ms-2" type="button" onclick="loadData()">Search</button>

                            </div>
                        </form>
                    </div>

                    <div class="col-auto mb-3 text-end">
                        <a class="btn create-btn" href="<?= $siteConfig->siteUrl; ?>/new-<?= $table ?>">+
                            Create New Record</a>
                    </div>

                </div>
                <div class="row mt-3 search justify-content-start">
                    <div class="col-auto" id="viewToggle">
                        <span class="listView p-2"><i class="fas fa-list fa-2x"></i></span>
                        <span class="tableView p-2"><i class="fas fa-table fa-2x"></i></span>
                    </div>
                    <div class="col-auto text-end ">
                        <select class="selectpicker" id="order_filter" data-style="btn-outline-light" data-width="200px"
                            data-dropup-auto="false">
                            <option value="reg_date DESC">Newest First</option>
                            <option value="reg_date ASC">Oldest First</option>
                            <option value="name ASC">Name A - Z</option>
                            <option value="name DESC">Name Z - A</option>
                        </select>
                    </div>
                    
                <div class='mb-3 col-auto '>
                    <select class='form-select select save-state f-sel' name='user'>
                        <option value="">All Users</option>
                        <?php foreach (User::all() as $selector): ?>
                            <option value="<?= $selector->iD; ?>"><?= $selector->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                </div>
                <br>
            </div>

            <br>
            <div class="row mt-4 mx-2">

                <div class="col-sm-12">
                    <div class="d-flex justify-content-between">
                        <div class="px-3 stats-label-text"></div>
                        <div class="px-3 stats-label-text">Total Records : <span class="stats-label"
                                id="total_records_label"></span>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-5" id="results"></div>
                    <div id="pagination-controls" class="d-flex justify-content-center mt-4"></div>
                    <?php include($siteConfig->assetsLoc . '/nav/ajax-pagination.php'); ?>
                </div>
                <br>
                <!-- Pagination -->



            </div>
    </section>
    <div id="list_template" style="display: none;">
        <div class="mb-3 p-2 py-3 text-muted ps-5 ">
            <h5>@item_name</h5>
            <p class="fw-light">

                <span class="fw-normal">Status:</span> @status_name
                                 |  <span class="fw-normal text-black">name:</span> @name_name
                 |  <span class="fw-normal text-black">user:</span> @user_name

            </p>
            <a class="btn btn-sm  table-button" href="<?= $siteConfig->siteUrl; ?>/view-<?= $table ?>/@item_id">
                <span>View Details</span>
            </a>

        </div>
        <hr>
    </div>


    <!-- Template for Table Item (Row) -->
    <template id="table_template">
        <tr>
            <td>@count</td>

                        <td>@name_name</td>
            <td>@user_name</td>

            <td>@status_name</td>
            <td>
                <a class="btn btn-sm  table-button" href="<?= $siteConfig->siteUrl; ?>/view-<?= $table ?>/@item_id">
                    <span>View</span>
                </a>

            </td>
        </tr>
    </template>

</div>
<input type="hidden" id="home_input" />
<input type="hidden" id="current_user" value="<?php if (isset($_COOKIE['user'])) {
    echo $_COOKIE['user'];
} ?>" />