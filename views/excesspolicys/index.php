@extends('layouts.main')

<?php
include __DIR__ . '/header.php';
use App\Models\Excesspolicy;

use function App\Helpers\formatDateTime;
?>

<div>
    <!-- Page header -->
    <section class="hero-section">
        <div class="container">
            <h3><?= $page_name ?>s</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $page_name ?>s</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Records -->
    <section id="records" class="py-4">
        <div class="container rounded-4 p-4 bg-white shadow-sm">

            <!-- Toolbar: search on the left, create on the right -->
            <div class="row justify-content-between align-items-center g-2">
                <div class="col-12 col-md-auto">
                    <form id="searchForm" onsubmit="current_page = 1; loadData(); return false;">
                        <div class="d-flex">
                            <input type="search" name="search" class="form-control search-input"
                                placeholder="Search <?= strtolower($page_name) ?>s...">
                            <button class="btn search-btn ms-2" type="submit">Search</button>
                        </div>
                    </form>
                </div>

                <div class="col-12 col-md-auto text-md-end">
                    <a class="btn create-btn" href="<?= $siteConfig->siteUrl; ?>/new-<?= $table ?>">
                        <i class="fa fa-plus me-1"></i> New <?= $page_name ?></a>
                </div>
            </div>

            <!-- Filters: view toggle, sort, per-module filters -->
            <div class="row mt-3 search align-items-center g-2">
                <div class="col-auto">
                    <div class="view-toggle" id="viewToggle" role="group" aria-label="Switch view">
                        <span class="listView p-2" title="List view"><i class="fas fa-list"></i></span>
                        <span class="tableView p-2" title="Table view"><i class="fas fa-table"></i></span>
                    </div>
                </div>
                <div class="col-auto">
                    <select class="form-select" id="order_filter" aria-label="Sort order">
                        <option value="reg_date DESC" selected>Newest First</option>
                        <option value="reg_date ASC">Oldest First</option>
                        <option value="name ASC">Name A - Z</option>
                        <option value="name DESC">Name Z - A</option>
                    </select>
                </div>
                
            </div>

            <hr>

            <!-- Results -->
            <div class="d-flex justify-content-between">
                <div class="stats-label-text"></div>
                <div class="stats-label-text">Total Records: <span class="stats-label"
                        id="total_records_label"></span></div>
            </div>

            <div class="my-4" id="results"></div>

            <div id="pagination-controls" class="d-flex justify-content-center mt-4"></div>
            <?php include($siteConfig->assetsLoc . '/nav/ajax-pagination.php'); ?>

        </div>
    </section>
</div>

<input type="hidden" id="home_input" />
