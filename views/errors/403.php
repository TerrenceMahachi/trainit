@extends('layouts.main')

<?php
global $siteConfig;
$siteUrl = $siteConfig ? $siteConfig->siteUrl : _BASEURL;
$user = \App\Helpers\Auth::user();
$userName = $user ? htmlspecialchars(is_object($user) ? $user->name : $user['name']) : 'User';
$roleObj = $user ? (is_object($user) ? $user->role() : null) : null;
$roleName = $roleObj ? htmlspecialchars(is_object($roleObj) ? $roleObj->name : $roleObj['name']) : 'Portal User';
$title = "403 — Access Restricted — " . _SITE;
$customMessage = $message ?? ($data['message'] ?? 'You do not have access to this page.');
?>

<div class="py-5" style="background-color: #fcfbfe; min-height: 80vh;">
    <div class="container py-md-4">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center p-4 p-md-5 bg-white">
                    <div class="mb-4">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm"
                             style="width: 88px; height: 88px; background: linear-gradient(135deg, #FFF3CD 0%, #FFE69C 100%); color: #B25E00; font-size: 38px;">
                            <i class="fa fa-shield-halved"></i>
                        </div>
                    </div>

                    <div class="d-inline-block align-self-center mb-3">
                        <span class="badge bg-warning-subtle text-warning-emphasis fw-bold rounded-pill px-3 py-2" style="font-size: 13px;">
                            <i class="fa fa-lock me-1"></i> HTTP 403 &bull; Access Restricted
                        </span>
                    </div>

                    <h2 class="fw-bold mb-2" style="color: #2A114B;">
                        You do not have access to this page
                    </h2>

                    <p class="text-muted fs-6 mb-4 px-md-3">
                        Your account (<strong><?= $userName ?></strong> &bull; <span class="badge bg-light text-dark border"><?= $roleName ?></span>) does not have authorization to view this section or perform this action.
                    </p>

                    <div class="p-3 bg-light rounded-4 mb-4 text-start border">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa fa-circle-info text-primary mt-1"></i>
                            <div class="small">
                                <strong class="text-dark">Need permissions for this module?</strong>
                                <p class="text-muted mb-0">If your role requires access to this console, contact your Tsigiro Administrator at <a href="mailto:support@tsigiro.co.zw" class="text-decoration-none fw-semibold" style="color: #2A114B;">support@tsigiro.co.zw</a>.</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="<?= $siteUrl ?>/dashboard" class="btn text-white rounded-pill px-4 py-2 fw-bold shadow-sm" style="background-color: #2A114B;">
                            <i class="fa fa-house me-2" style="color: #FFCC00;"></i>Click here to go to the dashboard
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                            <i class="fa fa-arrow-left me-1"></i> Return to Previous Page
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
