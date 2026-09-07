@extends('layouts.main')
<?php
include('header.php');
use function APP\Helpers\formatDateTime;

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
                    <li class="breadcrumb-item active" aria-current="page"><?= $item->name; ?></li>
                </ol>
            </nav>
        </div>
    </section>
    <section id="search" class="py-5 ">
        <div class="container rounded-3 p-4 border">

            <?php include("nav.php"); ?>

            <div class="table-responsive  py-5 m-3">
                <table class="table  table-borderless align-middle">
                    <tbody>
                        
                    <tr>
                        <th class="fw-bold">name</th>
                        <td class="text-muted"><?= $item->name; ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">user</th>
                        <td class="text-muted"><?= $item->user()->name; ?></td>
                    </tr>
                        <tr>
                            <th class="fw-bold">Date Registered</th>
                            <td class="text-muted"><?= formatDateTime($item->reg_date); ?></td>
                        </tr>
                        <tr>
                            <th class="fw-bold">Recorded By</th>
                            <td class="text-muted"><?= $item->creator()->name; ?></td>
                        </tr>
                        <tr>
                            <th class="fw-bold">Status</th>
                            <td class="text-muted"><?= $item->status()->name; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

</div>