@extends('layouts.main')

<?php
include __DIR__ . '/header.php';
use function App\Helpers\formatDateTime;
?>

<div>
    <!-- Page header -->
    <section class="hero-section">
        <div class="container text-start">
            <h3><?= $page_name ?> Details</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-capitalize"
                            href="<?= $siteConfig->siteUrl ?>/<?= $page ?>"><?= $page_name ?>s</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= htmlspecialchars($item->name ?? ('#' . $item->iD)) ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Record details -->
    <section class="py-4">
        <div class="container rounded-4 p-4 bg-white shadow-sm">

            <?php include __DIR__ . '/nav.php'; ?>

            <div class="table-responsive py-3 m-md-3">
                <table class="table table-borderless align-middle detail-table">
                    <tbody>
                        
                    <tr>
                        <th class="fw-bold">Statutoryreturn</th>
                        <td class="text-muted"><?= htmlspecialchars($item->statutoryreturn()->name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">File_path</th>
                        <td class="text-muted"><?= htmlspecialchars($item->file_path ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">File_name</th>
                        <td class="text-muted"><?= htmlspecialchars($item->file_name ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">File_size</th>
                        <td class="text-muted"><?= htmlspecialchars($item->file_size ?? ''); ?></td>
                    </tr>
                    <tr>
                        <th class="fw-bold">Mime_type</th>
                        <td class="text-muted"><?= htmlspecialchars($item->mime_type ?? ''); ?></td>
                    </tr>
                        <tr>
                            <th class="fw-bold">Date Registered</th>
                            <td class="text-muted"><?= formatDateTime($item->reg_date); ?></td>
                        </tr>
                        <tr>
                            <th class="fw-bold">Recorded By</th>
                            <td class="text-muted"><?= htmlspecialchars($item->creator()->name ?? ''); ?></td>
                        </tr>
                        <tr>
                            <th class="fw-bold">Status</th>
                            <td class="text-muted"><?= htmlspecialchars($item->status()->name ?? ''); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>
</div>
