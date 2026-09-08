@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'];
$profile = $data['profile'];
$roles = $data['roles'] ?? [];
?>

<main class="portal-dashboard">
    <section class="portal-dashboard-header" style="background: #2A114B; color: #fff; padding: 2rem 0;">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="text-white-50">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/admin/staff" class="text-white-50">Staff Directory</a></li>
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/admin/staff/view/<?= $user->iD; ?>" class="text-white-50"><?= htmlspecialchars($user->name); ?></a></li>
                    <li class="breadcrumb-item active text-warning" aria-current="page">Edit Profile</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-white mb-1"><i class="fa fa-user-edit text-warning me-2"></i> Edit Staff Profile</h1>
            <p class="text-white-50 mb-0">Update employee designation, operational role, or statutory NSSA Form P4 record.</p>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <form id="editStaffForm" class="bg-white border rounded-3 p-4 p-md-5 shadow-sm">
                        <input type="hidden" name="user_id" value="<?= (int)$user->iD; ?>">

                        <!-- Section 1: Role & Core Details -->
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                            <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">1</span>
                            <h4 class="fw-bold mb-0 text-dark">Portal Role & Assignment</h4>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Official Work Email</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($user->email); ?>" disabled>
                                <small class="text-muted">Email address cannot be changed directly.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Operational Staff Role</label>
                                <select name="role" class="form-select" <?= !App\Helpers\Auth::isAdmin() ? 'disabled' : ''; ?>>
                                    <?php foreach ($roles as $r): ?>
                                        <option value="<?= $r->iD; ?>" <?= $user->role == $r->iD ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($r->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Job Title / Designation</label>
                                <input type="text" name="job_title" class="form-control" value="<?= htmlspecialchars($profile ? $profile->job_title : ''); ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Department</label>
                                <select name="department" class="form-select">
                                    <?php
                                    $depts = ['Information Technology', 'Operations', 'Finance & Billing', 'Vetting & Talent', 'Executive'];
                                    $currDept = $profile ? $profile->department : 'Operations';
                                    foreach ($depts as $d): ?>
                                        <option value="<?= $d; ?>" <?= $currDept === $d ? 'selected' : ''; ?>><?= $d; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Employee / Works #</label>
                                <input type="text" name="employee_number" class="form-control" value="<?= htmlspecialchars($profile ? $profile->employee_number : ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Station / Office</label>
                                <input type="text" name="station" class="form-control" value="<?= htmlspecialchars($profile ? $profile->station : 'Harare HQ'); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Date of Employment</label>
                                <input type="date" name="date_of_employment" class="form-control" value="<?= htmlspecialchars($profile ? $profile->date_of_employment : ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Nature of Employment</label>
                                <select name="nature_of_employment" class="form-select">
                                    <?php
                                    $natures = ['ORDINARY', 'CONTRACT', 'PROBATION', 'PART_TIME'];
                                    $currNature = $profile ? $profile->nature_of_employment : 'ORDINARY';
                                    foreach ($natures as $n): ?>
                                        <option value="<?= $n; ?>" <?= $currNature === $n ? 'selected' : ''; ?>><?= $n; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Section 2: Statutory NSSA Form P4 Details -->
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom pt-3">
                            <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">2</span>
                            <h4 class="fw-bold mb-0 text-dark">Statutory Details (NSSA Form P4)</h4>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-2">
                                <label class="form-label fw-semibold text-dark">Title</label>
                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($profile ? $profile->title : 'MR'); ?>">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold text-dark">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($profile ? $profile->first_name : ''); ?>" required>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold text-dark">Surname <span class="text-danger">*</span></label>
                                <input type="text" name="surname" class="form-control" value="<?= htmlspecialchars($profile ? $profile->surname : ''); ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">National ID Number</label>
                                <input type="text" name="national_id_number" class="form-control" value="<?= htmlspecialchars($profile ? $profile->national_id_number : ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">NSSA SSR Number</label>
                                <input type="text" name="ssr_number" class="form-control" value="<?= htmlspecialchars($profile ? $profile->ssr_number : ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="<?= htmlspecialchars($profile ? $profile->date_of_birth : ''); ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="Male" <?= ($profile && $profile->gender === 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?= ($profile && $profile->gender === 'Female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">Marital Status</label>
                                <select name="marital_status" class="form-select">
                                    <?php
                                    $statuses = ['Single', 'Married', 'Divorced', 'Widowed'];
                                    $currStatus = $profile ? $profile->marital_status : 'Single';
                                    foreach ($statuses as $st): ?>
                                        <option value="<?= $st; ?>" <?= $currStatus === $st ? 'selected' : ''; ?>><?= $st; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">Drivers Licence #</label>
                                <input type="text" name="drivers_licence_number" class="form-control" value="<?= htmlspecialchars($profile ? $profile->drivers_licence_number : ''); ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">Passport Number</label>
                                <input type="text" name="passport_number" class="form-control" value="<?= htmlspecialchars($profile ? $profile->passport_number : ''); ?>">
                            </div>
                        </div>

                        <!-- Section 3: Residential Address & Contact -->
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom pt-3">
                            <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">3</span>
                            <h4 class="fw-bold mb-0 text-dark">Residential Location & Contact</h4>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark">Street Number</label>
                                <input type="text" name="street_number" class="form-control" value="<?= htmlspecialchars($profile ? $profile->street_number : ''); ?>">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold text-dark">Street Name</label>
                                <input type="text" name="street_name" class="form-control" value="<?= htmlspecialchars($profile ? $profile->street_name : ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Suburb</label>
                                <input type="text" name="suburb" class="form-control" value="<?= htmlspecialchars($profile ? $profile->suburb : ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Town / City</label>
                                <input type="text" name="town" class="form-control" value="<?= htmlspecialchars($profile ? $profile->town : 'HARARE'); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Region / Province</label>
                                <input type="text" name="region" class="form-control" value="<?= htmlspecialchars($profile ? $profile->region : 'HRE'); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Primary Telephone</label>
                                <input type="text" name="telephone_number" class="form-control" value="<?= htmlspecialchars($profile ? $profile->telephone_number : ''); ?>">
                            </div>
                        </div>

                        <!-- Section 4: Banking & Emergency Contact -->
                        <div class="d-flex align-items-center mb-4 pb-2 border-bottom pt-3">
                            <span class="badge rounded-pill me-2 px-3 py-2 fw-bold" style="background-color: #2A114B; color: #FFCC00;">4</span>
                            <h4 class="fw-bold mb-0 text-dark">Banking & Emergency Contact</h4>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control" value="<?= htmlspecialchars($profile ? $profile->bank_name : ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Branch Name</label>
                                <input type="text" name="bank_branch" class="form-control" value="<?= htmlspecialchars($profile ? $profile->bank_branch : ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Account Number</label>
                                <input type="text" name="account_number" class="form-control" value="<?= htmlspecialchars($profile ? $profile->account_number : ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control" value="<?= htmlspecialchars($profile ? $profile->emergency_contact_name : ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Emergency Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" class="form-control" value="<?= htmlspecialchars($profile ? $profile->emergency_contact_phone : ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Relationship</label>
                                <input type="text" name="emergency_contact_relationship" class="form-control" value="<?= htmlspecialchars($profile ? $profile->emergency_contact_relationship : ''); ?>">
                            </div>
                        </div>

                        <div id="editStaffAlert" class="alert d-none mb-4"></div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="<?= $siteConfig->siteUrl; ?>/admin/staff/view/<?= $user->iD; ?>" class="btn btn-outline-secondary px-4 py-2">
                                <i class="fa fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" id="btnUpdateStaff" class="btn btn-primary px-5 py-2 fw-bold text-dark" style="background: #FFCC00; border-color: #FFCC00;">
                                <i class="fa fa-save me-1"></i> Update Staff Record
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.getElementById('editStaffForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const btn = document.getElementById('btnUpdateStaff');
    const alertBox = document.getElementById('editStaffAlert');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';
    alertBox.className = 'alert d-none';

    const formData = new FormData(form);

    fetch('<?= $siteConfig->siteUrl; ?>/admin/staff/update', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-save me-1"></i> Update Staff Record';

        if (data.status === 1) {
            alertBox.className = 'alert alert-success';
            alertBox.innerHTML = '<strong>Success!</strong> ' + data.msg;
            setTimeout(() => {
                window.location.href = '<?= $siteConfig->siteUrl; ?>/admin/staff/view/<?= $user->iD; ?>';
            }, 1200);
        } else {
            alertBox.className = 'alert alert-danger';
            alertBox.innerHTML = '<strong>Error:</strong> ' + (data.msg || 'Failed to update profile.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-save me-1"></i> Update Staff Record';
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = 'Network or server error during update.';
    });
});
</script>
