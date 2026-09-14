@extends('layouts.main')

<?php
global $siteConfig;
$alerts = $data['alerts'] ?? [];
$user   = $data['user'] ?? null;
?>

<main class="portal-dashboard">
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <p class="portal-kicker text-warning mb-1" style="font-size: 0.85rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                    <i class="fa fa-bell me-1"></i> Talent Pipeline
                </p>
                <h1 class="h2 fw-bold text-white mb-2">Candidate Vacancy Alerts &amp; Saved Searches</h1>
                <p class="mb-0 text-white-50" style="max-width: 650px;">
                    Monitor candidate market search subscriptions. Vacancy broadcasts trigger instantly to these talent profiles when matching roles are published.
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies" class="btn btn-outline-light rounded-pill px-3">
                    <i class="fa fa-arrow-left me-1"></i> Vacancies Console
                </a>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Active Alert Subscriptions</h5>
                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill"><?= count($alerts); ?> subscriptions</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Candidate</th>
                                <th>Target Keywords</th>
                                <th>Department</th>
                                <th>Basis</th>
                                <th>Status</th>
                                <th>Last Match</th>
                                <th class="pe-4 text-end">Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($alerts)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        No candidate alert subscriptions registered yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($alerts as $a): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($a['name'] ?: 'Subscriber'); ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($a['email']); ?></div>
                                        </td>
                                        <td>
                                            <?php if ($a['keywords']): ?>
                                                <span class="badge bg-purple text-white" style="background-color: #4A154B;"><?= htmlspecialchars($a['keywords']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">All Roles</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($a['dept_name'] ?: 'Any Department'); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary"><?= htmlspecialchars($a['basis_name'] ?: 'Any Basis'); ?></span>
                                        </td>
                                        <td>
                                            <?php if ($a['is_active']): ?>
                                                <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-muted px-3 py-1 rounded-pill">Unsubscribed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted">
                                            <?= $a['last_matched_at'] ? htmlspecialchars($a['last_matched_at']) : 'Never'; ?>
                                        </td>
                                        <td class="pe-4 text-end small text-muted">
                                            <?= htmlspecialchars($a['reg_date']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>
