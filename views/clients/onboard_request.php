@extends('layouts.main')

<?php
global $siteConfig;
$offerings = $data['offerings'] ?? [];
$sectors   = $data['sectors'] ?? [];
$models    = $data['models'] ?? [];
?>

<main class="trainit-page">
    <!-- Header -->
    <section class="opportunity-hero py-5" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 60%, #3B1B66 100%); color: #ffffff;">
        <div class="container py-4 text-center">
            <p class="text-warning text-uppercase fw-bold letter-spacing-1 mb-2" style="font-size: 0.85rem;">
                <i class="fa fa-handshake me-1"></i> Tsigiro Managed Services
            </p>
            <h1 class="display-5 fw-bold text-white mb-3">Client Organization Self-Onboarding</h1>
            <p class="lead text-white-50 mx-auto mb-0" style="max-width: 720px;">
                Access verified talent rosters, dedicated retainers, and managed delivery teams. Submit your organizational profile to activate your corporate portal.
            </p>
        </div>
    </section>

    <section class="py-5" style="background: #f8fafc;">
        <div class="container" style="max-width: 820px;">
            
            <div id="onboard_alert" class="alert mb-4 d-none"></div>

            <form id="client_onboard_form" class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <?= \App\Helpers\Csrf::field(); ?>

                <!-- Step 1: Corporate Entity -->
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-warning text-dark rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                        <h4 class="fw-bold mb-0 text-dark">Organization Profile</h4>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Registered Company / Organization Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" required class="form-control" placeholder="e.g. Apex Financial Solutions (Pvt) Ltd">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Trading Name (if different)</label>
                            <input type="text" name="trading_name" class="form-control" placeholder="e.g. Apex Finance">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Company Registration Number</label>
                            <input type="text" name="registration_number" class="form-control" placeholder="e.g. 12345/2020">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">ZIMRA BP / Tax Number</label>
                            <input type="text" name="tax_number" class="form-control" placeholder="e.g. 0200123456">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Sector / Industry</label>
                            <select name="sectortype" class="form-select">
                                <option value="">Select Sector...</option>
                                <?php foreach ($sectors as $s): ?>
                                    <option value="<?= $s->iD; ?>"><?= htmlspecialchars($s->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Website URL</label>
                            <input type="url" name="website" class="form-control" placeholder="https://www.example.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Physical Street Address</label>
                            <input type="text" name="street_address" class="form-control" placeholder="e.g. 100 Nelson Mandela Ave">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">City</label>
                            <input type="text" name="city" class="form-control" value="Harare">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Country</label>
                            <input type="text" name="country" class="form-control" value="Zimbabwe">
                        </div>
                    </div>
                </div>

                <hr class="my-4 text-muted">

                <!-- Step 2: Primary Representative Contact -->
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-warning text-dark rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                        <h4 class="fw-bold mb-0 text-dark">Primary Administrator &amp; Contact</h4>
                    </div>
                    <p class="text-muted small mb-3">This contact will be provisioned as the Managing Representative with access to submit service briefs and approve deliverables.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="contact_name" required class="form-control" placeholder="e.g. Tendai Moyo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Job Title / Designation</label>
                            <input type="text" name="contact_title" class="form-control" placeholder="e.g. Managing Director / Head of Operations">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Corporate Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="contact_email" required class="form-control" placeholder="tendai@apexfinance.co.zw">
                            <div class="form-text small">Your login credentials will be delivered to this inbox.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Direct Phone / WhatsApp <span class="text-danger">*</span></label>
                            <input type="tel" name="contact_phone" required class="form-control" placeholder="+263 77 123 4567">
                        </div>
                    </div>
                </div>

                <hr class="my-4 text-muted">

                <!-- Step 3: Service Scope & Retainer Model -->
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-warning text-dark rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                        <h4 class="fw-bold mb-0 text-dark">Desired Service Engagement</h4>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Primary Service Offering</label>
                            <select name="serviceoffering" class="form-select">
                                <option value="">Select Offering...</option>
                                <?php foreach ($offerings as $o): ?>
                                    <option value="<?= $o->iD; ?>">
                                        <?= htmlspecialchars($o->name); ?> (<?= htmlspecialchars($o->code); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Engagement Model</label>
                            <select name="engagementmodel" class="form-select">
                                <option value="">Select Model...</option>
                                <?php foreach ($models as $m): ?>
                                    <option value="<?= $m->iD; ?>"><?= htmlspecialchars($m->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Estimated Monthly Capacity (Hours)</label>
                            <input type="number" name="estimated_monthly_hours" class="form-control" value="40" min="10" step="5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Invoicing Currency Preference</label>
                            <select name="currency_preference" class="form-select">
                                <option value="USD" selected>USD ($)</option>
                                <option value="ZWG">ZWG (Zimbabwe Gold)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Special Operational Requirements / Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Describe the business objectives, key deliverables, or immediate talent needs..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <button type="submit" id="btn_submit_onboard" class="btn btn-warning text-dark fw-bold btn-lg rounded-pill px-5 shadow-sm">
                        <i class="fa fa-paper-plane me-2"></i> Submit Onboarding Application
                    </button>
                </div>
            </form>

        </div>
    </section>
</main>

<script>
document.getElementById('client_onboard_form').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = document.getElementById('btn_submit_onboard');
    const alertBox = document.getElementById('onboard_alert');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Submitting...';

    const formData = new FormData(this);
    fetch('<?= $siteConfig->siteUrl; ?>/clients/onboard', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-paper-plane me-2"></i> Submit Onboarding Application';
        alertBox.classList.remove('d-none', 'alert-danger', 'alert-success');
        if (res.status === 1) {
            alertBox.classList.add('alert-success');
            alertBox.innerHTML = '<i class="fa fa-circle-check me-2"></i>' + res.msg;
            document.getElementById('client_onboard_form').reset();
            window.scrollTo({ top: alertBox.offsetTop - 80, behavior: 'smooth' });
        } else {
            alertBox.classList.add('alert-danger');
            alertBox.innerHTML = '<i class="fa fa-circle-exclamation me-2"></i>' + res.msg;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-paper-plane me-2"></i> Submit Onboarding Application';
        alertBox.classList.remove('d-none');
        alertBox.classList.add('alert-danger');
        alertBox.innerHTML = '<i class="fa fa-triangle-exclamation me-2"></i> Submission failed. Please check connection and try again.';
    });
});
</script>
