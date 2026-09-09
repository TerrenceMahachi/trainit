@extends('layouts.main')

<?php
global $siteConfig;

$application = $data['application'];
$appId = (int)$application->iD;
$track = $application->applicationtrack();
$trackCode = $track ? $track->code : 'apprentice';
$primaryFunction = $application->primaryfunction();
$skillItems = $data['skillItems'] ?? [];
$proficiencyLevels = $data['proficiencyLevels'] ?? [];
$existingSkills = $data['existingSkillsMap'] ?? []; // skillitem => proficiencylevel
$currentStep = 3;
?>

<main class="portal-dashboard">
    <?php include _VIEWS_PATH . '/roster/apply_nav.php'; ?>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h3 fw-bold mb-1">Step 3: Core Competencies & Skills Matrix</h2>
                        <p class="text-muted mb-0">
                            Rate your hands-on proficiency in key functional skills for 
                            <strong class="text-dark"><?= htmlspecialchars($primaryFunction ? $primaryFunction->name : 'Your Practice Area'); ?></strong>.
                        </p>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2">
                        Application #<?= $appId; ?>
                    </span>
                </div>

                <div id="skills_alert" style="display:none;" class="alert mb-4"></div>

                <form id="skills_form" action="<?= $siteConfig->siteUrl; ?>/roster/apply/skills" method="POST" class="card border-0 shadow-sm rounded-4 mb-4">
                    <input type="hidden" name="application_id" value="<?= $appId; ?>">

                    <div class="card-body p-4 p-md-5">
                        <div class="alert bg-light border mb-4">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="fa fa-circle-info text-primary"></i>
                                <span class="fw-semibold">Proficiency Guidelines:</span>
                            </div>
                            <div class="row g-2 small text-muted">
                                <div class="col-sm-6 col-md-3"><strong>Beginner:</strong> Conceptual knowledge, academic theory.</div>
                                <div class="col-sm-6 col-md-3"><strong>Intermediate:</strong> Practical execution under supervision.</div>
                                <div class="col-sm-6 col-md-3"><strong>Advanced:</strong> Autonomous delivery on complex briefs.</div>
                                <div class="col-sm-6 col-md-3"><strong>Expert:</strong> Architect, audit, and mentor others.</div>
                            </div>
                        </div>

                        <?php if (empty($skillItems)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fa fa-layer-group fa-2x mb-2 text-secondary opacity-50"></i>
                                <p>Standard skill items for this discipline are being compiled. You may proceed to Step 4.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="min-width: 220px;">Skill / Functional Competency</th>
                                            <th class="text-center" style="min-width: 320px;">Your Self-Assessed Proficiency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($skillItems as $item): 
                                            $selectedLevel = $existingSkills[$item->iD] ?? null;
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($item->name); ?></div>
                                                    <span class="badge bg-light text-muted border small"><?= htmlspecialchars($item->code ?? ''); ?></span>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-around align-items-center flex-wrap gap-2">
                                                        <div class="form-check form-check-inline m-0">
                                                            <input class="form-check-input" type="radio" name="skill_<?= $item->iD; ?>" id="sk_<?= $item->iD; ?>_none" value="0" <?= empty($selectedLevel) ? 'checked' : ''; ?>>
                                                            <label class="form-check-label small text-muted" for="sk_<?= $item->iD; ?>_none">N/A</label>
                                                        </div>
                                                        <?php foreach ($proficiencyLevels as $lvl): ?>
                                                            <div class="form-check form-check-inline m-0">
                                                                <input class="form-check-input" type="radio" name="skill_<?= $item->iD; ?>" id="sk_<?= $item->iD; ?>_<?= $lvl->iD; ?>" value="<?= $lvl->iD; ?>" <?= ((int)$selectedLevel === (int)$lvl->iD) ? 'checked' : ''; ?>>
                                                                <label class="form-check-label small" for="sk_<?= $item->iD; ?>_<?= $lvl->iD; ?>">
                                                                    <?= htmlspecialchars($lvl->name); ?>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- Additional Custom Skills / Tools -->
                        <div class="mt-4 p-3 bg-light rounded-3 border">
                            <label class="form-label fw-semibold">Other Relevant Tools, Technologies & Methodologies</label>
                            <textarea name="additional_tools" rows="2" class="form-control" placeholder="e.g. Docker, Figma, SAP, QuickBooks, PowerBI, SPSS, Python, Git..."></textarea>
                            <div class="form-text">List any specialized frameworks, libraries, ERP systems, or certifications not listed above.</div>
                        </div>

                        <!-- Form Actions (Native Links) -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-4 border-top mt-4">
                            <a href="<?= $siteConfig->siteUrl; ?>/roster/apply/credentials?id=<?= $appId; ?>" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fa fa-arrow-left me-1"></i> Back: Step 2
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                Save & Continue to Step 4: Experience <i class="fa fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
