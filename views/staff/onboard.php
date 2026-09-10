@extends('layouts.account')

<?php
global $siteConfig;
$invite = $data['invite'];
$roleName = $data['roleName'] ?? 'Staff Member';
$nameParts = explode(' ', $invite->name);
$firstName = $nameParts[0] ?? '';
$surname = $nameParts[1] ?? '';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <!-- Card Container -->
            <div class="card border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <!-- Header Banner -->
                <div class="p-4 p-md-5 text-white" style="background: linear-gradient(135deg, #090b0b 0%, #15221d 50%, #1d332b 100%);">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <img src="<?= $siteConfig->assetsUrl; ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>" alt="Tsigiro Logo" style="height: 44px; border-radius: 8px;">
                        <span class="badge px-3 py-2 fw-bold" style="background: #32c99a; color: #090b0b; font-size: 0.85rem;">Staff Onboarding</span>
                    </div>
                    <h2 class="h3 fw-bold mb-1">Welcome to the Team, <?= htmlspecialchars($invite->name); ?>!</h2>
                    <p class="text-white-50 mb-0">Complete your statutory employee onboarding (NSSA Form P4) and configure your secure account credentials.</p>
                </div>

                <!-- Appointment Summary Strip -->
                <div class="px-4 py-3 bg-light border-bottom d-flex flex-wrap gap-4 text-dark" style="font-size: 0.9rem;">
                    <div><span class="text-muted">Official Email:</span> <strong><?= htmlspecialchars($invite->email); ?></strong></div>
                    <div><span class="text-muted">Role:</span> <span class="badge bg-dark"><?= htmlspecialchars($roleName); ?></span></div>
                    <div><span class="text-muted">Designation:</span> <strong><?= htmlspecialchars($invite->job_title); ?></strong></div>
                    <div><span class="text-muted">Department:</span> <strong><?= htmlspecialchars($invite->department); ?></strong></div>
                </div>

                <!-- Onboarding Form Body -->
                <div class="p-4 p-md-5 bg-white">
                    <form id="staffOnboardingForm" enctype="multipart/form-data">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($invite->token); ?>">

                        <!-- Step 1: Account Security -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">1</span>
                                <h4 class="fw-bold mb-0 text-dark">Step 1: Set Account Password</h4>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Create Password <span class="text-danger">*</span></label>
                                    <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Minimum 6 characters" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control form-control-lg" placeholder="Re-enter password" required>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- Step 2: Statutory & Identification (NSSA P4) -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">2</span>
                                <h4 class="fw-bold mb-0 text-dark">Step 2: Statutory Identification (NSSA Form P4)</h4>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold text-dark">Title</label>
                                    <select name="title" class="form-select">
                                        <option value="MR">MR</option>
                                        <option value="MRS">MRS</option>
                                        <option value="MS">MS</option>
                                        <option value="DR">DR</option>
                                        <option value="ENG">ENG</option>
                                        <option value="PROF">PROF</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold text-dark">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($firstName); ?>" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold text-dark">Surname <span class="text-danger">*</span></label>
                                    <input type="text" name="surname" class="form-control" value="<?= htmlspecialchars($surname); ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">National ID Number <span class="text-danger">*</span></label>
                                    <input type="text" name="national_id_number" class="form-control" placeholder="e.g. 63-1267948M07" required>
                                    <small class="text-muted">Format: XX-XXXXXXXAXX</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">NSSA SSR Number</label>
                                    <input type="text" name="ssr_number" class="form-control" placeholder="e.g. 6738625AA">
                                    <small class="text-muted">Leave blank if this is your first employment.</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_birth" class="form-control" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Gender</label>
                                    <select name="gender" class="form-select">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Marital Status</label>
                                    <select name="marital_status" class="form-select">
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Passport Number</label>
                                    <input type="text" name="passport_number" class="form-control" placeholder="Optional">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold text-dark">Drivers Licence #</label>
                                    <input type="text" name="drivers_licence_number" class="form-control" placeholder="Optional">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- Step 3: Residential & Contacts -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">3</span>
                                <h4 class="fw-bold mb-0 text-dark">Step 3: Residential Address & Contacts</h4>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Street Address</label>
                                    <input type="text" name="street_name" class="form-control" placeholder="e.g. 14 Darling Close">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark">Suburb</label>
                                    <input type="text" name="suburb" class="form-control" placeholder="e.g. Hatfield">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Town / City</label>
                                    <input type="text" name="town" class="form-control" value="HARARE">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Region / Province</label>
                                    <input type="text" name="region" class="form-control" value="HRE">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Primary Telephone / WhatsApp</label>
                                    <input type="text" name="telephone_number" class="form-control" placeholder="e.g. +263 77 123 4567" required>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <!-- Step 4: Banking & Emergency Details -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">4</span>
                                <h4 class="fw-bold mb-0 text-dark">Step 4: Salary Disbursal Banking & Emergency Contact</h4>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control" placeholder="e.g. Stanbic Bank / CABS" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Branch Name</label>
                                    <input type="text" name="bank_branch" class="form-control" placeholder="e.g. Samora Machel" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Account Number</label>
                                    <input type="text" name="account_number" class="form-control" placeholder="e.g. 9140001234567" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Emergency Contact Name</label>
                                    <input type="text" name="emergency_contact_name" class="form-control" placeholder="Full Name" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Emergency Contact Phone</label>
                                    <input type="text" name="emergency_contact_phone" class="form-control" placeholder="e.g. +263 77 987 6543" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark">Relationship</label>
                                    <input type="text" name="emergency_contact_relationship" class="form-control" placeholder="e.g. Spouse, Parent, Sibling" required>
                                </div>
                            </div>
                        </div>

                        <div id="onboardingAlert" class="alert d-none mb-4"></div>

                        <!-- Submit Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" id="btnSubmitOnboarding" class="btn btn-lg fw-bold text-dark py-3" style="background: #FFCC00; border-color: #FFCC00; border-radius: 10px;">
                                <i class="fa fa-check-circle me-1"></i> Complete Onboarding & Enter Portal
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('staffOnboardingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const btn = document.getElementById('btnSubmitOnboarding');
    const alertBox = document.getElementById('onboardingAlert');

    const p1 = document.getElementById('password').value;
    const p2 = document.getElementById('confirm_password').value;

    if (p1.length < 6) {
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Password must be at least 6 characters long.';
        return;
    }

    if (p1 !== p2) {
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Passwords do not match. Please verify.';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Submitting Onboarding Dossier...';
    alertBox.className = 'alert d-none';

    const formData = new FormData(form);

    fetch('<?= $siteConfig->siteUrl; ?>/staff/onboard', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-check-circle me-1"></i> Complete Onboarding & Enter Portal';

        if (data.status === 1) {
            alertBox.className = 'alert alert-success';
            alertBox.innerHTML = '<strong>Success!</strong> ' + data.msg + ' Redirecting...';
            setTimeout(() => {
                window.location.href = '<?= $siteConfig->siteUrl; ?>' + (data.redirect || '/dashboard');
            }, 1200);
        } else {
            alertBox.className = 'alert alert-danger';
            alertBox.innerHTML = '<strong>Error:</strong> ' + (data.msg || 'Unable to submit onboarding.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-check-circle me-1"></i> Complete Onboarding & Enter Portal';
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Network or server error during onboarding submission.';
    });
});
</script>
