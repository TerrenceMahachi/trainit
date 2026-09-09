@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'] ?? null;
$profile = $data['profile'] ?? null;
$documents = $data['documents'] ?? [];
$allDocTypes = $data['allDocTypes'] ?? [];
$activeTab = 'documents';
?>

<main class="portal-dashboard">
    <?php include __DIR__ . '/portal_nav.php'; ?>

    <!-- Section Content: My Documents -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-folder-open text-primary me-2"></i> Compliance Documents On File</h6>
                <small class="text-muted">Ensure your National ID, Driver's Licence, and certificates remain current</small>
            </div>
            <button class="btn btn-primary btn-sm px-3 py-2 fw-semibold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#selfDocModal">
                <i class="fa fa-upload me-1"></i> Upload New Document
            </button>
        </div>
        <div class="card-body p-0">
            <?php if (empty($documents)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fa fa-file-upload fa-3x mb-3 text-light-purple" style="color: #D6C2EB;"></i>
                    <h6 class="fw-bold text-dark">No Documents Filed</h6>
                    <p class="small text-muted mb-3">Please upload a certified scan of your National ID, Driver's Licence, or Proof of Residence.</p>
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#selfDocModal">
                        <i class="fa fa-upload me-1"></i> Upload Document Now
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">Document Title</th>
                                <th>Category</th>
                                <th>Validity</th>
                                <th>Compliance Status</th>
                                <th class="text-end pe-4">File</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documents as $docItem): 
                                $d = $docItem['doc'];
                                $type = $docItem['type'];
                                $ver = $docItem['verification'];
                                $status = $docItem['status'];
                                $validity = $docItem['validity'];
                                $statusCode = $status ? $status->code : 'PENDING';
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <strong class="text-dark d-block"><?= htmlspecialchars($d->title); ?></strong>
                                        <small class="text-muted"><?= round($d->file_size / 1024, 1); ?> KB &bull; Uploaded <?= date('d M Y', strtotime($d->reg_date)); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($type ? $type->name : 'Document'); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($validity): 
                                            $isExpired = ($validity->expiry_date < date('Y-m-d'));
                                            $daysRemaining = (int)ceil((strtotime($validity->expiry_date) - time()) / 86400);
                                        ?>
                                            <?php if ($isExpired): ?>
                                                <span class="badge bg-danger text-white"><i class="fa fa-exclamation-circle me-1"></i> Expired</span>
                                            <?php elseif ($daysRemaining <= 30): ?>
                                                <span class="badge bg-warning text-dark"><i class="fa fa-clock me-1"></i> Expires in <?= $daysRemaining; ?> days</span>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">Valid until <?= date('d M Y', strtotime($validity->expiry_date)); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">&mdash; Non-expiring</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($statusCode === 'VERIFIED'): ?>
                                            <span class="badge bg-success text-white"><i class="fa fa-check-circle me-1"></i> Verified</span>
                                        <?php elseif ($statusCode === 'REJECTED'): ?>
                                            <span class="badge bg-danger text-white" title="<?= htmlspecialchars($ver->notes ?? ''); ?>"><i class="fa fa-times-circle me-1"></i> Rejected</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark"><i class="fa fa-hourglass-half me-1"></i> Pending Review</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= $siteConfig->siteUrl . '/' . ltrim($d->file_path, '/'); ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa fa-download"></i> View
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

            </div><!-- /.col-lg-8 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section><!-- /.portal-dashboard-body -->

<!-- SELF MODAL: UPLOAD DOCUMENT -->
<div class="modal fade" id="selfDocModal" tabindex="-1" aria-labelledby="selfDocModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <form action="<?= $siteConfig->siteUrl; ?>/admin/staff/documents/upload" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="staffprofile_id" value="<?= (int)($profile ? $profile->iD : 0); ?>">
                <input type="hidden" name="redirect_url" value="<?= $siteConfig->siteUrl; ?>/staff/portal/documents">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title fw-bold text-dark" id="selfDocModalLabel"><i class="fa fa-upload text-primary me-2"></i> Upload Personal Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Document Category *</label>
                        <select name="documenttype_id" class="form-select" required onchange="toggleSelfExpiryFields(this)">
                            <option value="">-- Select Document Category --</option>
                            <?php foreach ($allDocTypes as $dt): ?>
                                <option value="<?= $dt->iD; ?>" data-expiry="<?= $dt->requires_expiry ? '1' : '0'; ?>"><?= htmlspecialchars($dt->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Document Title *</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g., My Valid Class 4 Driver's Licence" required>
                    </div>
                    <div class="row g-2 mb-3" id="selfExpiryFieldsDiv" style="display: none;">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Issue Date</label>
                            <input type="date" name="issue_date" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark small">Expiry Date *</label>
                            <input type="date" name="expiry_date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Select File *</label>
                        <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                        <small class="text-muted">Supported formats: PDF, JPG, PNG (Max 10MB)</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSelfExpiryFields(selectElem) {
        var selectedOption = selectElem.options[selectElem.selectedIndex];
        var requiresExpiry = selectedOption.getAttribute('data-expiry') === '1';
        var div = document.getElementById('selfExpiryFieldsDiv');
        if (div) {
            div.style.display = requiresExpiry ? 'flex' : 'none';
        }
    }
</script>
</main>
