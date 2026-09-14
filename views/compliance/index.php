@extends('layouts.main')

<?php
global $siteConfig;
$documents     = $data['documents'] ?? [];
$stats         = $data['stats'] ?? ['total' => 0, 'expired' => 0, 'critical' => 0, 'warning' => 0, 'valid' => 0];
$currentFilter = $data['currentFilter'] ?? 'actionable';
$logs          = $data['logs'] ?? [];
$user          = $data['user'] ?? null;
?>

<main class="portal-dashboard">
    <!-- Header -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <p class="portal-kicker text-warning mb-1" style="font-size: 0.85rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                    <i class="fa fa-shield-halved me-1"></i> Statutory &amp; Regulatory Safeguards
                </p>
                <h1 class="h2 fw-bold text-white mb-2">Compliance Radar &amp; Expiry Tracker</h1>
                <p class="mb-0 text-white-50" style="max-width: 650px;">
                    Proactively track staff qualifications, candidate work permits, passports, and police clearances. Automated email &amp; in-portal reminders run on scheduled cooldown cycles.
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= $siteConfig->siteUrl; ?>/admin" class="btn btn-outline-light rounded-pill px-3">
                    <i class="fa fa-arrow-left me-1"></i> Dashboard
                </a>
                <button id="btnRunScan" class="btn btn-warning text-dark fw-bold rounded-pill px-3" onclick="triggerScan(false)">
                    <i class="fa fa-rotate me-1"></i> Run Scan &amp; Remind
                </button>
                <button class="btn btn-outline-warning text-white rounded-pill px-3" onclick="triggerScan(true)" title="Bypasses 7-day cooldown">
                    <i class="fa fa-bolt me-1"></i> Force Scan
                </button>
            </div>
        </div>
    </section>

    <!-- Body -->
    <section class="py-4">
        <div class="container">

            <!-- KPI Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="text-muted small text-uppercase fw-bold">Monitored Credentials</div>
                        <div class="h2 fw-bold text-dark mb-0 mt-1"><?= number_format($stats['total']); ?></div>
                        <div class="small text-muted mt-2"><i class="fa fa-layer-group me-1"></i> Staff &amp; Associate Docs</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 h-100 <?= $stats['expired'] > 0 ? 'bg-danger text-white' : 'bg-white'; ?>">
                        <div class="small text-uppercase fw-bold <?= $stats['expired'] > 0 ? 'text-white-50' : 'text-muted'; ?>">Expired Credentials</div>
                        <div class="h2 fw-bold mb-0 mt-1"><?= number_format($stats['expired']); ?></div>
                        <div class="small mt-2 <?= $stats['expired'] > 0 ? 'text-white-50' : 'text-danger'; ?>">
                            <i class="fa fa-triangle-exclamation me-1"></i> Immediate action required
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="text-warning small text-uppercase fw-bold">Critical (≤ 30 Days)</div>
                        <div class="h2 fw-bold text-warning mb-0 mt-1"><?= number_format($stats['critical']); ?></div>
                        <div class="small text-muted mt-2"><i class="fa fa-clock me-1"></i> Urgently expiring</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="text-info small text-uppercase fw-bold">Warning (31–60 Days)</div>
                        <div class="h2 fw-bold text-info mb-0 mt-1"><?= number_format($stats['warning']); ?></div>
                        <div class="small text-muted mt-2"><i class="fa fa-bell me-1"></i> Automated alerts active</div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="btn-group rounded-pill p-1 bg-light">
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/compliance?filter=actionable" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold <?= $currentFilter === 'actionable' ? 'btn-dark' : 'text-muted'; ?>">
                            Actionable (≤ 60d) (<?= $stats['expired'] + $stats['critical'] + $stats['warning']; ?>)
                        </a>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/compliance?filter=expired" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold <?= $currentFilter === 'expired' ? 'btn-danger' : 'text-muted'; ?>">
                            Expired (<?= $stats['expired']; ?>)
                        </a>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/compliance?filter=critical" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold <?= $currentFilter === 'critical' ? 'btn-warning text-dark' : 'text-muted'; ?>">
                            Critical ≤ 30d (<?= $stats['critical']; ?>)
                        </a>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/compliance?filter=warning" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold <?= $currentFilter === 'warning' ? 'btn-info text-white' : 'text-muted'; ?>">
                            Warning 31–60d (<?= $stats['warning']; ?>)
                        </a>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/compliance?filter=all" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold <?= $currentFilter === 'all' ? 'btn-secondary' : 'text-muted'; ?>">
                            All Records (<?= $stats['total']; ?>)
                        </a>
                    </div>
                    <div class="text-muted small">
                        <i class="fa fa-info-circle me-1"></i> Auto-reminder throttling: 7-day cooldown per document
                    </div>
                </div>
            </div>

            <!-- Main Credentials Table -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-file-shield text-purple me-2" style="color: #4A154B;"></i> Credential Expiry Radar</h5>
                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill"><?= count($documents); ?> items matching filter</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Document / Credential</th>
                                <th>Owner / Recipient</th>
                                <th>Category</th>
                                <th>Expiry Date</th>
                                <th>Status / Countdown</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($documents)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa fa-shield-heart fa-3x mb-3 text-success d-block opacity-75"></i>
                                        <strong>No documents found in this compliance bracket!</strong>
                                        <p class="small mb-0">All monitored records meet or exceed active validity criteria.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($documents as $doc): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($doc['title']); ?></div>
                                            <div class="small text-muted"><span class="badge bg-light text-dark border"><?= htmlspecialchars($doc['document_type']); ?> #<?= $doc['document_id']; ?></span></div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($doc['recipient_name'] ?: 'N/A'); ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($doc['recipient_email'] ?: 'No email'); ?> &bull; <span class="badge bg-light text-secondary"><?= htmlspecialchars($doc['entity_type']); ?></span></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($doc['category']); ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($doc['expiry_date']); ?></div>
                                            <?php if (!empty($doc['issue_date'])): ?>
                                                <div class="small text-muted">Issued: <?= htmlspecialchars($doc['issue_date']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($doc['is_expired']): ?>
                                                <span class="badge bg-danger px-3 py-2 rounded-pill">
                                                    <i class="fa fa-circle-xmark me-1"></i> EXPIRED (<?= abs($doc['days_left']); ?> days ago)
                                                </span>
                                            <?php elseif ($doc['days_left'] <= 30): ?>
                                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                                    <i class="fa fa-triangle-exclamation me-1"></i> <?= $doc['days_left']; ?> days remaining
                                                </span>
                                            <?php elseif ($doc['days_left'] <= 60): ?>
                                                <span class="badge bg-info text-dark px-3 py-2 rounded-pill">
                                                    <i class="fa fa-clock me-1"></i> <?= $doc['days_left']; ?> days remaining
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success px-3 py-2 rounded-pill">
                                                    <i class="fa fa-check me-1"></i> <?= $doc['days_left']; ?> days (Valid)
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="<?= $siteConfig->siteUrl; ?><?= $doc['link']; ?>" class="btn btn-sm btn-outline-secondary rounded-pill">
                                                <i class="fa fa-arrow-up-right-from-square me-1"></i> View Profile
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Dispatch Logs -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-clock-rotate-left text-muted me-2"></i> Recent Reminder Dispatch Audit Trail</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Recipient</th>
                                <th>Document Reference</th>
                                <th>Days Left at Dispatch</th>
                                <th>Dispatch Channel</th>
                                <th class="pe-4 text-end">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No automated reminder dispatches recorded yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($log['recipient_name'] ?: $log['recipient_email']); ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($log['recipient_email']); ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($log['document_type']); ?> #<?= $log['document_id']; ?></span>
                                            <span class="small text-muted ms-1">Exp: <?= htmlspecialchars($log['expiry_date']); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $log['days_left'] < 0 ? 'bg-danger' : ($log['days_left'] <= 30 ? 'bg-warning text-dark' : 'bg-info text-white'); ?> rounded-pill">
                                                <?= $log['days_left'] < 0 ? 'Expired' : $log['days_left'] . 'd'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary rounded-pill"><i class="fa fa-envelope me-1"></i> <?= htmlspecialchars($log['channel']); ?></span>
                                        </td>
                                        <td class="pe-4 text-end text-muted small">
                                            <?= htmlspecialchars($log['sent_at']); ?>
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

<script>
function triggerScan(force) {
    const btn = document.getElementById('btnRunScan');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Scanning...';

    const formData = new FormData();
    formData.append('threshold', 60);
    formData.append('cooldown', 7);
    if (force) {
        formData.append('force', '1');
    }

    fetch('<?= $siteConfig->siteUrl; ?>/admin/compliance/run-scan', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-rotate me-1"></i> Run Scan &amp; Remind';
        if (data.status === 1) {
            alert(data.msg);
            window.location.reload();
        } else {
            alert('Scan Error: ' + (data.msg || 'Operation failed.'));
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-rotate me-1"></i> Run Scan &amp; Remind';
        alert('Network error while running scan: ' + err);
    });
}
</script>
