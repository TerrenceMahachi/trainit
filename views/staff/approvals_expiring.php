@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'] ?? null;
$expiringDocs = $data['expiringDocs'] ?? [];
$activeTab = 'expiring';
?>

<main class="portal-dashboard">
    <?php include __DIR__ . '/approvals_nav.php'; ?>

    <!-- Section Content: Expiring Credentials Radar -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-stopwatch text-danger me-2"></i> Credentials Expiring Within 90 Days or Surpassed</h6>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-semibold">
                <?= count($expiringDocs); ?> At Risk / Expired
            </span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($expiringDocs)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fa fa-shield-alt fa-3x mb-3 text-success" style="opacity: 0.6;"></i>
                    <h6 class="fw-bold text-dark">All Credentials in Legal Standing</h6>
                    <p class="small text-muted mb-0">No driver's licences, defensive driving certificates, or medicals expiring in the next 90 days.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">Employee</th>
                                <th>Document Title</th>
                                <th>Category</th>
                                <th>Expiry Date</th>
                                <th>Urgency Status</th>
                                <th class="text-end pe-4">Dossier</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expiringDocs as $item): 
                                $v = $item['validity'];
                                $d = $item['doc'];
                                $staff = $item['staff'];
                                $type = $item['type'];
                                $isExpired = $item['isExpired'];
                                $daysLeft = $item['daysLeft'];
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <strong class="text-dark d-block"><?= htmlspecialchars($staff ? $staff->name : 'Staff'); ?></strong>
                                        <small class="text-muted"><?= htmlspecialchars($staff ? $staff->department : ''); ?></small>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-semibold"><?= htmlspecialchars($d ? $d->title : 'Document'); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($type ? $type->name : 'Certificate'); ?></span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark"><?= date('d M Y', strtotime($v->expiry_date)); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($isExpired): ?>
                                            <span class="badge bg-danger text-white"><i class="fa fa-times-circle me-1"></i> Expired (<?= abs($daysLeft); ?> days ago)</span>
                                        <?php elseif ($daysLeft <= 30): ?>
                                            <span class="badge bg-warning text-dark"><i class="fa fa-exclamation-triangle me-1"></i> Critical: <?= $daysLeft; ?> days remaining</span>
                                        <?php else: ?>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="fa fa-clock me-1"></i> <?= $daysLeft; ?> days left</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= $siteConfig->siteUrl; ?>/admin/staff/view/<?= (int)($staff ? $staff->user : 0); ?>#tab-docs" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-folder-open me-1"></i> View Dossier
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    </div><!-- /.container -->
</section><!-- /.portal-dashboard-body -->
</main>
