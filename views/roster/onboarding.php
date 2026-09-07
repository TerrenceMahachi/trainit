@extends('layouts.main')

<?php
global $siteConfig;
$app = $data['app'];
$onboarding = $data['onboarding'];
?>

<main class="portal-dashboard">
    <section class="portal-dashboard-header">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker"><a href="<?= $siteConfig->siteUrl; ?>/dashboard/application?id=<?= $app->iD; ?>" style="color:rgba(255,255,255,0.7); text-decoration:none;"><i class="fa fa-arrow-left me-1"></i> Application #<?= $app->iD; ?></a></p>
                <h1>Stage 3: Statutory Onboarding</h1>
                <p class="portal-dashboard-intro">Complete your formal statutory and banking details for client placement contracting and payment processing.</p>
            </div>
            <div class="portal-account-summary">
                <span>Admission Stage</span>
                <strong>STAGE 3 ONBOARDING</strong>
                <small id="onboarding_autosave_header"><i class="fa fa-cloud me-1"></i> Auto-save ready</small>
            </div>
        </div>
    </section>

    <section class="portal-dashboard-body py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    
    <!-- Compact & Sticky Progress Tracker -->
    <div class="sticky-top bg-white border-bottom shadow-sm mb-4" style="top: 0; z-index: 1025;">
        <div class="container py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark small"><i class="fa fa-chart-line text-success me-1"></i> Onboarding:</span>
                    <strong class="text-success small" id="onboarding_percent_label">0%</strong>
                    <span class="text-muted small" id="onboarding_counts_label">(0/0)</span>
                </div>
                <div>
                    <span id="onboarding_autosave_badge" class="badge bg-light text-muted border px-2 py-1 small fw-normal">
                        <i class="fa fa-cloud me-1"></i> Ready
                    </span>
                </div>
            </div>
            <div class="progress" style="height: 4px; border-radius: 2px; background-color: #e9ecef;">
                <div id="onboarding_progress_bar" class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
            </div>
        </div>
    </div>

                    <div id="onboarding_alert" style="display:none;" class="alert mb-4"></div>

                    <form id="onboarding_form" enctype="multipart/form-data" method="POST">
                        <input type="hidden" name="rosterapplication" value="<?= $app->iD; ?>">

                        <!-- Statutory ID & Address -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-id-card text-primary me-2"></i> 1. Statutory Identification & Physical Address</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Zimbabwe National ID Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control track-field" name="national_id_number" value="<?= htmlspecialchars($onboarding->national_id_number ?? ''); ?>" required placeholder="e.g. 63-1234567-X-42">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Upload National ID Copy (PDF/JPG) <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" name="national_id_doc" <?= $onboarding ? '' : 'required'; ?>>
                                        <?php if ($onboarding && $onboarding->national_id_doc): ?>
                                            <small class="text-success"><i class="fa fa-check"></i> File on record: <?= htmlspecialchars($onboarding->national_id_doc); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Passport Number (if available)</label>
                                        <input type="text" class="form-control track-field" name="passport_number" value="<?= htmlspecialchars($onboarding->passport_number ?? ''); ?>" placeholder="e.g. FN123456">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Passport Expiry Date</label>
                                        <input type="date" class="form-control track-field" name="passport_expiry" value="<?= htmlspecialchars($onboarding->passport_expiry ?? ''); ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold">Full Physical Street Address <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control track-field" name="street_address" value="<?= htmlspecialchars($onboarding->street_address ?? ''); ?>" required placeholder="House number, Street name, Suburb">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">City <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control track-field" name="city" value="<?= htmlspecialchars($onboarding->city ?? $app->city ?? 'Harare'); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">NSSA Social Security Number (if registered)</label>
                                        <input type="text" class="form-control track-field" name="nssa_number" value="<?= htmlspecialchars($onboarding->nssa_number ?? ''); ?>" placeholder="NSSA Number">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Banking Details -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-university text-primary me-2"></i> 2. Banking Details for Stipend / Fee Payments</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Bank Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control track-field" name="bank_name" value="<?= htmlspecialchars($onboarding->bank_name ?? ''); ?>" required placeholder="e.g. Stanbic, CABS, Nedbank, FBC">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Bank Branch <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control track-field" name="bank_branch" value="<?= htmlspecialchars($onboarding->bank_branch ?? ''); ?>" required placeholder="e.g. Samora Machel, Nelson Mandela">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Account Holder Legal Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control track-field" name="account_name" value="<?= htmlspecialchars($onboarding->account_name ?? $app->legal_name ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Account Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control track-field" name="account_number" value="<?= htmlspecialchars($onboarding->account_number ?? ''); ?>" required placeholder="Account Number">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Currency</label>
                                        <select class="form-select track-field" name="bank_currency">
                                            <option value="USD" selected>USD (Nostro)</option>
                                            <option value="ZWG">ZWG</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-phone-square-alt text-primary me-2"></i> 3. Emergency Contact Details</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Contact Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control track-field" name="emergency_contact_name" value="<?= htmlspecialchars($onboarding->emergency_contact_name ?? ''); ?>" required placeholder="e.g. Mary Moyo">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Contact Phone (+263) <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control track-field" name="emergency_contact_phone" value="<?= htmlspecialchars($onboarding->emergency_contact_phone ?? ''); ?>" required placeholder="+263 77 123 4567">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Relationship <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control track-field" name="emergency_contact_relationship" value="<?= htmlspecialchars($onboarding->emergency_contact_relationship ?? ''); ?>" required placeholder="e.g. Spouse, Parent, Sibling">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Formal Agreements & Police Clearance -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-file-contract text-primary me-2"></i> 4. Agreements & Clearances</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Upload ZRP Police Clearance (if completed)</label>
                                        <input type="file" class="form-control form-control-sm" name="police_clearance_doc">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Police Clearance Date (if available)</label>
                                        <input type="date" class="form-control form-control-sm track-field" name="police_clearance_date" value="<?= htmlspecialchars($onboarding->police_clearance_date ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Signed Non-Disclosure Agreement (NDA)</label>
                                        <input type="file" class="form-control form-control-sm" name="signed_nda_doc">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Signed Roster Member Agreement</label>
                                        <input type="file" class="form-control form-control-sm" name="signed_contract_doc">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-light text-end py-3">
                                <button type="submit" class="btn btn-success px-4 fw-bold"><i class="fa fa-check-circle me-1"></i> Submit Statutory Onboarding</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('onboarding_form');
    const alertBox = document.getElementById('onboarding_alert');
    const progressBar = document.getElementById('onboarding_progress_bar');
    const percentLabel = document.getElementById('onboarding_percent_label');
    const countsLabel = document.getElementById('onboarding_counts_label');
    const autosaveBadge = document.getElementById('onboarding_autosave_badge');
    const headerStatus = document.getElementById('onboarding_autosave_header');

    let autoSaveTimer = null;
    let isSaving = false;

    // Progress calculation
    function updateProgress() {
        const fields = form.querySelectorAll('.track-field');
        let filled = 0;
        let total = fields.length;

        fields.forEach(f => {
            if (f.value.trim() !== '') filled++;
        });

        const percent = total > 0 ? Math.round((filled / total) * 100) : 0;
        progressBar.style.width = percent + '%';
        progressBar.setAttribute('aria-valuenow', percent);
        percentLabel.textContent = percent + '%';
        countsLabel.textContent = `(${filled} of ${total} fields completed)`;
    }

    // Auto-save function
    function autoSaveOnboarding() {
        if (isSaving) return;
        isSaving = true;

        autosaveBadge.className = 'badge bg-warning text-dark px-3 py-2';
        autosaveBadge.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving changes...';

        const formData = new FormData(form);

        fetch('<?= $siteConfig->siteUrl; ?>/dashboard/application/onboarding', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            isSaving = false;
            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            
            autosaveBadge.className = 'badge bg-success-subtle text-success border border-success-subtle px-3 py-2';
            autosaveBadge.innerHTML = `<i class="fa fa-check-circle me-1"></i> Saved at ${timeStr}`;
            if (headerStatus) {
                headerStatus.innerHTML = `<i class="fa fa-check-circle me-1"></i> Saved at ${timeStr}`;
            }
        })
        .catch(err => {
            isSaving = false;
            autosaveBadge.className = 'badge bg-danger text-white px-3 py-2';
            autosaveBadge.innerHTML = '<i class="fa fa-wifi me-1"></i> Offline / Save pending';
        });
    }

    function queueSave() {
        updateProgress();
        autosaveBadge.className = 'badge bg-info text-dark px-3 py-2';
        autosaveBadge.innerHTML = '<i class="fa fa-pencil-alt me-1"></i> Unsaved changes...';
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(autoSaveOnboarding, 1200);
    }

    form.addEventListener('input', queueSave);
    form.addEventListener('change', queueSave);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        alertBox.style.display = 'block';
        alertBox.className = 'alert alert-info';
        alertBox.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Submitting statutory onboarding data...';

        fetch('<?= $siteConfig->siteUrl; ?>/dashboard/application/onboarding', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 1) {
                alertBox.className = 'alert alert-success';
                alertBox.innerHTML = '<i class="fa fa-check-circle me-2"></i> ' + data.msg;
                setTimeout(() => {
                    window.location.href = '<?= $siteConfig->siteUrl; ?>/dashboard/application?id=<?= $app->iD; ?>';
                }, 1200);
            } else {
                alertBox.className = 'alert alert-danger';
                alertBox.innerHTML = '<i class="fa fa-exclamation-triangle me-2"></i> ' + data.msg;
            }
        })
        .catch(err => {
            alertBox.className = 'alert alert-danger';
            alertBox.innerHTML = '<i class="fa fa-exclamation-triangle me-2"></i> Server error. Please try again.';
        });
    });

    updateProgress();
});
</script>
