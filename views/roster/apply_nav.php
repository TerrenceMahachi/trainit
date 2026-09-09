<?php
/**
 * Shared Multi-Stage Application Navigation Bar.
 * Renders real HTML <a> links with step states (completed, active, pending).
 * Strictly avoids client-side JavaScript tab toggling.
 */
global $siteConfig;

$step = $currentStep ?? 1;
$appId = isset($application) && $application ? (int)$application->iD : (isset($appId) ? (int)$appId : 0);
$trackCode = isset($track) && $track ? (is_object($track) ? $track->code : $track) : ($trackCode ?? 'apprentice');
$trackName = $trackCode === 'associate' ? 'Associate Specialist' : 'Apprentice';

$steps = [
    1 => [
        'title' => 'Express Intake',
        'subtitle' => 'Basic Profile',
        'icon' => 'fa-user',
        'url' => $appId > 0 ? ($siteConfig->siteUrl . "/opportunities/apply/{$trackCode}?id={$appId}") : ($siteConfig->siteUrl . "/opportunities/apply/{$trackCode}"),
    ],
    2 => [
        'title' => 'Credentials',
        'subtitle' => 'Docs & Qualifications',
        'icon' => 'fa-certificate',
        'url' => $appId > 0 ? ($siteConfig->siteUrl . "/roster/apply/credentials?id={$appId}") : '#',
    ],
    3 => [
        'title' => 'Skills Matrix',
        'subtitle' => 'Core Competencies',
        'icon' => 'fa-code',
        'url' => $appId > 0 ? ($siteConfig->siteUrl . "/roster/apply/skills?id={$appId}") : '#',
    ],
    4 => [
        'title' => 'Experience',
        'subtitle' => 'History & Referees',
        'icon' => 'fa-briefcase',
        'url' => $appId > 0 ? ($siteConfig->siteUrl . "/roster/apply/experience?id={$appId}") : '#',
    ],
    5 => [
        'title' => 'Review & Submit',
        'subtitle' => 'Declarations',
        'icon' => 'fa-check-double',
        'url' => $appId > 0 ? ($siteConfig->siteUrl . "/roster/apply/review?id={$appId}") : '#',
    ],
];
?>

<div class="bg-white border-bottom shadow-sm mb-4 sticky-top" style="top: 0; z-index: 1020;">
    <div class="container py-2">
        <!-- Header Strip -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
                <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fa fa-arrow-left me-1"></i> Opportunities
                </a>
                <span class="badge <?= $trackCode === 'associate' ? 'bg-primary' : 'bg-success'; ?> px-3 py-2 rounded-pill">
                    <i class="fa <?= $trackCode === 'associate' ? 'fa-user-tie' : 'fa-graduation-cap'; ?> me-1"></i>
                    <?= htmlspecialchars($trackName); ?> Track
                </span>
                <?php if ($appId > 0): ?>
                    <span class="badge bg-light text-dark border px-2 py-1 small">
                        App #<?= $appId; ?>
                    </span>
                <?php endif; ?>
            </div>
            <div>
                <span class="text-muted small">
                    Step <strong class="text-dark"><?= is_numeric($step) ? $step : 'Final'; ?></strong> of 5
                </span>
            </div>
        </div>

        <!-- Real <a> Navigation Links (No JS Tabs) -->
        <nav aria-label="Onboarding Progress" class="overflow-auto py-1">
            <ol class="list-unstyled d-flex flex-nowrap align-items-center gap-1 mb-0" style="min-width: 640px;">
                <?php foreach ($steps as $idx => $s): 
                    $isActive = ($step === $idx);
                    $isCompleted = ($step > $idx) || ($step === 'status');
                    $isClickable = ($appId > 0 && ($isCompleted || $idx <= $step + 1));
                ?>
                    <li class="flex-fill">
                        <?php if ($isClickable && !$isActive): ?>
                            <a href="<?= $s['url']; ?>" class="d-flex align-items-center gap-2 p-2 rounded text-decoration-none border transition-all text-secondary" style="background: #f8fafc;">
                                <span class="badge <?= $isCompleted ? 'bg-success' : 'bg-secondary'; ?> rounded-circle p-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                    <?php if ($isCompleted): ?>
                                        <i class="fa fa-check small text-white"></i>
                                    <?php else: ?>
                                        <?= $idx; ?>
                                    <?php endif; ?>
                                </span>
                                <div class="lh-1 text-truncate">
                                    <span class="d-block fw-semibold text-dark small text-truncate"><?= $s['title']; ?></span>
                                    <span class="text-muted" style="font-size: 0.75rem;"><?= $s['subtitle']; ?></span>
                                </div>
                            </a>
                        <?php elseif ($isActive): ?>
                            <div class="d-flex align-items-center gap-2 p-2 rounded border border-primary bg-primary bg-opacity-10 text-primary">
                                <span class="badge bg-primary text-white rounded-circle p-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                    <?= $idx; ?>
                                </span>
                                <div class="lh-1 text-truncate">
                                    <span class="d-block fw-bold text-primary small text-truncate"><?= $s['title']; ?></span>
                                    <span class="text-primary-emphasis" style="font-size: 0.75rem;"><?= $s['subtitle']; ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="d-flex align-items-center gap-2 p-2 rounded border bg-light text-muted opacity-50">
                                <span class="badge bg-secondary text-white rounded-circle p-2" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                    <?= $idx; ?>
                                </span>
                                <div class="lh-1 text-truncate">
                                    <span class="d-block fw-medium small text-truncate"><?= $s['title']; ?></span>
                                    <span class="text-muted" style="font-size: 0.75rem;"><?= $s['subtitle']; ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </li>
                    <?php if ($idx < 5): ?>
                        <li class="text-muted px-1" aria-hidden="true">
                            <i class="fa fa-chevron-right small opacity-50"></i>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>
</div>
