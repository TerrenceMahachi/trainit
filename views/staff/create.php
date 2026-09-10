@extends('layouts.main')

<?php
global $siteConfig;
$roles = $data['roles'] ?? [];
?>

<main class="portal-dashboard">
    <!-- Breadcrumb Header -->
    <section class="portal-dashboard-header" style="background: #2A114B; color: #fff; padding: 2rem 0;">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="text-white-50">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/admin/staff" class="text-white-50">Staff Directory</a></li>
                    <li class="breadcrumb-item active text-warning" aria-current="page">Register Staff Member</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-white mb-1"><i class="fa fa-user-plus text-warning me-2"></i> Register New Staff Member</h1>
            <p class="text-white-50 mb-0">Record internal employee appointments and populate statutory NSSA Form P4 registration data.</p>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <form id="createStaffForm" class="bg-white border rounded-3 p-4 p-md-5 shadow-sm">

                        <!-- Section 1: Account & Role Assignment -->
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                            <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">1</span>
                            <h4 class="fw-bold mb-0 text-dark">Portal Account & Operational Role</h4>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Official Work Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-lg" placeholder="e.g. jmoyo@tsigiro.co.zw" required>
                                <small class="text-muted">Used for authentication and system alerts.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Operational Staff Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select form-select-lg" required>
                                    <option value="">Select Role...</option>
                                    <?php foreach ($roles as $r): ?>
                                        <option value="<?= $r->iD; ?>"><?= htmlspecialchars($r->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Defines permissions across the portal.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Initial Password</label>
                                <input type="text" name="password" class="form-control form-control-lg" placeholder="Leave blank to auto-generate temporary password">
                                <small class="text-muted">Auto-generates if empty and included in the welcome email.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Onboarding Option</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="send_invite_toggle" value="1" id="sendInviteToggle">
                                    <label class="form-check-label text-dark" for="sendInviteToggle">
                                        Send self-onboarding invite link instead (Staff sets own password)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Employment & Organisation -->
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom pt-3">
                            <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">2</span>
                            <h4 class="fw-bold mb-0 text-dark">Employment & Designation</h4>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Job Title / Occupation <span class="text-danger">*</span></label>
                                <input type="text" name="job_title" class="form-control" placeholder="e.g. HEAD OF IT, SERVICE MANAGER" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Department <span class="text-danger">*</span></label>
                                <select name="department" class="form-select">
                                    <option value="Information Technology">Information Technology</option>
                                    <option value="Operations">Operations</option>
                                    <option value="Finance & Billing">Finance & Billing</option>
                                    <option value="Vetting & Talent">Vetting & Talent</option>
                                    <option value="Executive">Executive</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Employee / Works Number</label>
                                <input type="text" name="employee_number" class="form-control" placeholder="e.g. TRN-005 (auto if blank)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Station / Office</label>
                                <input type="text" name="station" class="form-control" value="Harare HQ">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Date of Employment</label>
                                <input type="date" name="date_of_employment" class="form-control" value="<?= date('Y-m-d'); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Nature of Employment</label>
                                <select name="nature_of_employment" class="form-select">
                                    <option value="ORDINARY">ORDINARY (Permanent/Standard)</option>
                                    <option value="CONTRACT">CONTRACT (Fixed Term)</option>
                                    <option value="PROBATION">PROBATION</option>
                                    <option value="PART_TIME">PART TIME</option>
                                </select>
                            </div>
                        </div>

                        <!-- Section 3: Statutory NSSA Form P4 Information -->
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom pt-3">
                            <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">3</span>
                            <h4 class="fw-bold mb-0 text-dark">Statutory Information (NSSA Form P4)</h4>
                        </div>

                        <div class="row g-3 mb-4">
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

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" placeholder="e.g. TERRENCE" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">Other Names</label>
                                <input type="text" name="other_names" class="form-control" placeholder="e.g. SIMBARASHE">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">Surname <span class="text-danger">*</span></label>
                                <input type="text" name="surname" class="form-control" placeholder="e.g. MAHACHI" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">National ID Number <span class="text-danger">*</span></label>
                                <input type="text" name="national_id_number" class="form-control" placeholder="e.g. 63-1267948M07" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">NSSA SSR Number</label>
                                <input type="text" name="ssr_number" class="form-control" placeholder="e.g. 6738625AA">
                                <small class="text-muted">NSSA Social Security Registration #</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control">
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
                                <label class="form-label fw-semibold text-dark">Drivers Licence #</label>
                                <input type="text" name="drivers_licence_number" class="form-control" placeholder="e.g. 12842JC">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">Passport Number</label>
                                <input type="text" name="passport_number" class="form-control" placeholder="e.g. AE671950">
                            </div>
                        </div>

                        <!-- Section 4: Residential Address & Contact -->
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom pt-3">
                            <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">4</span>
                            <h4 class="fw-bold mb-0 text-dark">Residential Address & Contact</h4>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">Street Number / Plot</label>
                                <input type="text" name="street_number" class="form-control" placeholder="e.g. 14">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold text-dark">Street Name</label>
                                <input type="text" name="street_name" class="form-control" placeholder="e.g. Darling Close">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Suburb</label>
                                <input type="text" name="suburb" class="form-control" placeholder="e.g. Hatfield">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Town / City</label>
                                <input type="text" name="town" class="form-control" value="HARARE">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Region / Province Code</label>
                                <input type="text" name="region" class="form-control" value="HRE">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Telephone / Mobile</label>
                                <input type="text" name="telephone_number" class="form-control" placeholder="e.g. +263 77 123 4567">
                            </div>
                        </div>

                        <!-- Section 5: Banking & Emergency Contact -->
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom pt-3">
                            <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">5</span>
                            <h4 class="fw-bold mb-0 text-dark">Banking & Emergency Contact</h4>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control" placeholder="e.g. Stanbic Bank / CABS">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Branch Name</label>
                                <input type="text" name="bank_branch" class="form-control" placeholder="e.g. Samora Machel">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Account Number</label>
                                <input type="text" name="account_number" class="form-control" placeholder="e.g. 9140001234567">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control" placeholder="e.g. Mary Mahachi">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Emergency Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" class="form-control" placeholder="e.g. +263 77 987 6543">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Relationship</label>
                                <input type="text" name="emergency_contact_relationship" class="form-control" placeholder="e.g. Spouse / Sibling">
                            </div>
                        </div>

                        <div id="createStaffAlert" class="alert d-none mb-4"></div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="<?= $siteConfig->siteUrl; ?>/admin/staff" class="btn btn-outline-secondary px-4 py-2">
                                <i class="fa fa-arrow-left me-1"></i> Back to Directory
                            </a>
                            <button type="submit" id="btnSubmitStaff" class="btn btn-primary px-5 py-2 fw-bold text-dark" style="background: #FFCC00; border-color: #FFCC00;">
                                <i class="fa fa-check-circle me-1"></i> Save Staff Member
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.getElementById('createStaffForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const btn = document.getElementById('btnSubmitStaff');
    const alertBox = document.getElementById('createStaffAlert');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';
    alertBox.className = 'alert d-none';

    const formData = new FormData(form);

    fetch('<?= $siteConfig->siteUrl; ?>/admin/staff/store', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-check-circle me-1"></i> Save Staff Member';

        if (data.status === 1) {
            alertBox.className = 'alert alert-success';
            alertBox.innerHTML = '<strong>Success!</strong> ' + data.msg;
            setTimeout(() => {
                window.location.href = '<?= $siteConfig->siteUrl; ?>/admin/staff';
            }, 1500);
        } else {
            alertBox.className = 'alert alert-danger';
            alertBox.innerHTML = '<strong>Error:</strong> ' + (data.msg || 'Unable to register staff member.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-check-circle me-1"></i> Save Staff Member';
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Server connection error. Please try again.';
    });
});
</script>
