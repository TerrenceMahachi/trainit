@extends('layouts.main')

<?php
global $siteConfig;
$success = $data['success'] ?? false;
$message = $data['message'] ?? '';
$oppUrl  = $data['opportunitiesUrl'] ?? '/opportunities';
?>

<main class="trainit-page py-5" style="background: #f8fafc; min-height: 70vh;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5 text-center">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                    <div class="mb-4">
                        <?php if ($success): ?>
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success" style="width: 72px; height: 72px; font-size: 32px;">
                                <i class="fa fa-circle-check"></i>
                            </span>
                        <?php else: ?>
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger" style="width: 72px; height: 72px; font-size: 32px;">
                                <i class="fa fa-triangle-exclamation"></i>
                            </span>
                        <?php endif; ?>
                    </div>

                    <h2 class="fw-bold text-dark mb-3"><?= htmlspecialchars($data['title']); ?></h2>
                    <p class="text-muted mb-4" style="line-height: 1.6;">
                        <?= htmlspecialchars($message); ?>
                    </p>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="<?= $oppUrl; ?>" class="btn btn-warning text-dark fw-bold rounded-pill px-4">
                            <i class="fa fa-arrow-left me-1"></i> Back to Opportunities
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
