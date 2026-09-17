@extends('layouts.main')

<?php
include __DIR__ . '/header.php';
use function App\Helpers\formatDateTime;
use App\Models\Database;

$db = Database::sharedPdo();
$applicant = $item->user();
$applicantId = $item->user;

// Fetch Applicant's Personal Details from rosterapplication
$stmt = $db->prepare("SELECT * FROM rosterapplication WHERE user = ? ORDER BY iD DESC LIMIT 1");
$stmt->execute([$applicantId]);
$personalDetails = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch Applicant's Qualifications from rosterqualification
$stmt = $db->prepare("
    SELECT rq.*, qt.name as qual_type_name 
    FROM rosterqualification rq 
    JOIN rosterapplication ra ON rq.rosterapplication = ra.iD 
    LEFT JOIN qualificationtype qt ON rq.qualificationtype = qt.iD 
    WHERE ra.user = ? AND rq.status = 1 
    ORDER BY rq.date_obtained DESC, rq.iD DESC
");
$stmt->execute([$applicantId]);
$qualifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Company Representation & Client Membership details
$stmt = $db->prepare("
    SELECT cm.*, co.name as company_name, co.company_registration_number, co.city as company_city, 
           cmr.name as role_name, cs.name as vetting_status_name
    FROM clientmembership cm 
    JOIN clientorganization co ON cm.clientorganization = co.iD 
    LEFT JOIN clientmemberrole cmr ON cm.clientmemberrole = cmr.iD 
    LEFT JOIN clientmembershipstatus cs ON cm.status = cs.iD 
    WHERE cm.user = ? 
    ORDER BY cm.iD DESC
");
$stmt->execute([$applicantId]);
$clientMemberships = $stmt->fetchAll(PDO::FETCH_ASSOC);

$profileStatus = $item->profilestatus();
$profileType = $item->profiletype();
?>

<div>
    <!-- Page header -->
    <section class="hero-section">
        <div class="container text-start">
            <h3>Profile Application Review</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-capitalize" href="<?= $siteConfig->siteUrl ?>/<?= $page ?>"><?= $page_name ?>s</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Review #<?= $item->iD ?> (<?= htmlspecialchars($applicant->name ?? 'Applicant') ?>)</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Record details -->
    <section class="py-4">
        <div class="container">
            <?php include __DIR__ . '/nav.php'; ?>

            <div class="row g-4">
                <!-- Left Column: Application Details & Review Actions -->
                <div class="col-lg-7">
                    <!-- Application Overview Card -->
                    <div class="card rounded-4 border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="<?= htmlspecialchars($profileType->icon ?? 'fas fa-id-badge') ?> text-primary me-2"></i>
                                Application Request: <?= htmlspecialchars($item->display_title ?? $profileType->name) ?>
                            </h5>
                            <?php
                            $badgeClass = 'secondary';
                            if ($item->profilestatus == 2) $badgeClass = 'warning text-dark';
                            elseif ($item->profilestatus == 3) $badgeClass = 'success';
                            elseif ($item->profilestatus == 4) $badgeClass = 'danger';
                            ?>
                            <span class="badge bg-<?= $badgeClass ?> px-3 py-2 fs-6">
                                <?= htmlspecialchars($profileStatus->name ?? 'Unknown') ?>
                            </span>
                        </div>
                        <div class="card-body px-4 py-3">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="fw-semibold text-muted" style="width: 35%;">Applicant</th>
                                            <td class="fw-bold text-dark">
                                                <?= htmlspecialchars($applicant->name ?? '') ?> 
                                                <span class="text-muted fw-normal fs-7">(<?= htmlspecialchars($applicant->email ?? '') ?>)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="fw-semibold text-muted">Requested Track</th>
                                            <td class="fw-semibold text-primary"><?= htmlspecialchars($profileType->name ?? '') ?></td>
                                        </tr>
                                        <tr>
                                            <th class="fw-semibold text-muted">Submitted Date</th>
                                            <td class="text-dark"><?= formatDateTime($item->reg_date); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="fw-semibold text-muted">Is Default Profile</th>
                                            <td><?= $item->is_default ? '<span class="badge bg-info text-dark">Yes (Primary)</span>' : '<span class="badge bg-light text-muted">No</span>' ?></td>
                                        </tr>
                                        <tr>
                                            <th class="fw-semibold text-muted align-top">Applicant Motivation / Notes</th>
                                            <td class="text-dark bg-light rounded p-2">
                                                <?= nl2br(htmlspecialchars($item->request_notes ?: 'No additional notes provided with application.')) ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Reviewer Action Card -->
                    <div class="card rounded-4 border-0 shadow-sm mb-4 border-top border-primary border-3">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold mb-1 text-dark"><i class="fas fa-user-check text-primary me-2"></i>Reviewer Decision</h5>
                            <p class="text-muted small mb-0">Review the attached dossier below and record your administrative decision.</p>
                        </div>
                        <div class="card-body px-4 py-3">
                            <?php if (!empty($item->reviewed_at)): ?>
                                <div class="alert alert-info py-2 px-3 small rounded-3 mb-3">
                                    <i class="fas fa-history me-1"></i> Last reviewed by <strong><?= htmlspecialchars($item->reviewed_by()->name ?? 'Administrator') ?></strong> on <strong><?= formatDateTime($item->reviewed_at) ?></strong>
                                </div>
                            <?php endif; ?>

                            <form id="profile-review-form" onsubmit="event.preventDefault();">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark">Reviewer Feedback & Notes</label>
                                    <textarea name="reviewer_notes" id="reviewer_notes" class="form-control rounded-3" rows="3" placeholder="Enter notes or conditions for approval/rejection..."><?= htmlspecialchars($item->reviewer_notes ?? '') ?></textarea>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-success px-4 py-2 rounded-3 shadow-sm" onclick="submitReviewDecision('approve')">
                                        <i class="fas fa-check-circle me-1"></i> Approve Application
                                    </button>
                                    <button type="button" class="btn btn-outline-danger px-4 py-2 rounded-3 shadow-sm" onclick="submitReviewDecision('reject')">
                                        <i class="fas fa-times-circle me-1"></i> Reject Application
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Company Representation Card (For Staff / Corporate Client Profiles) -->
                    <?php if (!empty($clientMemberships) || $item->profiletype == 4 || $item->profiletype == 5): ?>
                    <div class="card rounded-4 border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h5 class="fw-bold mb-1 text-dark">
                                <i class="fas fa-building text-primary me-2"></i>Corporate Client Representation & Vetting
                            </h5>
                            <p class="text-muted small mb-0">Company affiliation and corporate client authorization.</p>
                        </div>
                        <div class="card-body px-4 py-3">
                            <?php if (empty($clientMemberships)): ?>
                                <div class="alert alert-warning py-2 px-3 small rounded-3 mb-0">
                                    <i class="fas fa-info-circle me-1"></i> No corporate client organization linked to this application yet.
                                </div>
                            <?php else: ?>
                                <?php foreach ($clientMemberships as $cm): ?>
                                    <div class="border rounded-3 p-3 mb-2 bg-light">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($cm['company_name']) ?></h6>
                                            <?php 
                                            $vBadge = 'secondary';
                                            if ($cm['status'] == 1) $vBadge = 'success';
                                            elseif ($cm['status'] == 2) $vBadge = 'warning text-dark';
                                            ?>
                                            <span class="badge bg-<?= $vBadge ?> px-2 py-1"><?= htmlspecialchars($cm['vetting_status_name'] ?? 'Pending') ?></span>
                                        </div>
                                        <div class="small text-muted mb-1">
                                            <span><strong>Role:</strong> <?= htmlspecialchars($cm['role_name'] ?? 'Representative') ?></span>
                                            <?php if (!empty($cm['company_registration_number'])): ?>
                                                <span class="ms-3"><strong>Reg #:</strong> <?= htmlspecialchars($cm['company_registration_number']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($cm['company_city'])): ?>
                                                <span class="ms-3"><strong>City:</strong> <?= htmlspecialchars($cm['company_city']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($cm['notes'])): ?>
                                            <div class="small text-secondary bg-white p-2 rounded border mt-2">
                                                <strong>Motivation / Role Details:</strong> <?= nl2br(htmlspecialchars($cm['notes'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Applicant Complete Profile Dossier -->
                <div class="col-lg-5">
                    <!-- Personal Details Dossier Card -->
                    <div class="card rounded-4 border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-id-card text-primary me-2"></i>Personal Details Profile
                            </h5>
                            <?php if ($personalDetails): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                    <i class="fas fa-check-circle me-1"></i>Verified
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Not Set
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body px-4 py-3">
                            <?php if ($personalDetails): ?>
                                <ul class="list-group list-group-flush small">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Legal Name</span>
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($personalDetails['legal_name'] ?? '-') ?></span>
                                    </li>
                                    <?php if (!empty($personalDetails['preferred_name'])): ?>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Preferred Name</span>
                                        <span class="fw-semibold text-dark"><?= htmlspecialchars($personalDetails['preferred_name']) ?></span>
                                    </li>
                                    <?php endif; ?>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Mobile Number</span>
                                        <span class="fw-bold text-primary"><?= htmlspecialchars($personalDetails['mobile_number'] ?? '-') ?></span>
                                    </li>
                                    <?php if (!empty($personalDetails['whatsapp_number'])): ?>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">WhatsApp</span>
                                        <span class="text-dark"><?= htmlspecialchars($personalDetails['whatsapp_number']) ?></span>
                                    </li>
                                    <?php endif; ?>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Location</span>
                                        <span class="text-dark"><?= htmlspecialchars(($personalDetails['suburb'] ? $personalDetails['suburb'] . ', ' : '') . ($personalDetails['city'] ?? 'Harare')) ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Date of Birth</span>
                                        <span class="text-dark"><?= htmlspecialchars($personalDetails['date_of_birth'] ?? '-') ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Gender</span>
                                        <span class="text-dark"><?= htmlspecialchars(ucfirst($personalDetails['gender'] ?? '-')) ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Nationality</span>
                                        <span class="text-dark"><?= htmlspecialchars($personalDetails['nationality'] ?? 'Zimbabwean') ?></span>
                                    </li>
                                    <?php if (!empty($personalDetails['work_permit_number'])): ?>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Work Permit</span>
                                        <span class="text-dark"><?= htmlspecialchars($personalDetails['work_permit_number']) ?></span>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-center py-3 text-muted">
                                    <i class="fas fa-exclamation-circle fa-2x text-warning mb-2"></i>
                                    <p class="mb-0">The applicant has not yet completed their personal details profile.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Qualifications Dossier Card -->
                    <div class="card rounded-4 border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-graduation-cap text-primary me-2"></i>Qualifications Profile
                            </h5>
                            <span class="badge bg-primary rounded-pill px-2 py-1"><?= count($qualifications) ?> on file</span>
                        </div>
                        <div class="card-body px-4 py-3">
                            <?php if (empty($qualifications)): ?>
                                <div class="text-center py-3 text-muted">
                                    <i class="fas fa-certificate fa-2x text-secondary mb-2"></i>
                                    <p class="mb-0">No qualifications recorded on this applicant's profile.</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($qualifications as $q): ?>
                                        <div class="list-group-item px-0 py-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($q['title']) ?></h6>
                                                    <div class="small text-muted">
                                                        <span><?= htmlspecialchars($q['institution_name']) ?></span>
                                                        <?php if (!empty($q['field_of_study'])): ?>
                                                            &bull; <span><?= htmlspecialchars($q['field_of_study']) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <span class="badge bg-light text-primary border border-light-subtle">
                                                    <?= htmlspecialchars($q['qual_type_name'] ?? 'Qualification') ?>
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1 small">
                                                <span class="text-muted"><i class="far fa-calendar-alt me-1"></i><?= htmlspecialchars($q['date_obtained'] ?? 'Completed') ?></span>
                                                <span class="text-success"><i class="fas fa-check-circle me-1"></i>Verified</span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function submitReviewDecision(action) {
    var actionText = action === 'approve' ? 'Approve' : 'Reject';
    if (!confirm('Are you sure you want to ' + actionText + ' this profile application?')) {
        return;
    }

    var notes = document.getElementById('reviewer_notes').value;
    var postData = new URLSearchParams();
    postData.append('action', action);
    postData.append('reviewer_notes', notes);

    fetch('<?= $siteConfig->siteUrl ?>/userprofiles/review/<?= $item->iD ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: postData.toString()
    })
    .then(function(res) {
        return res.json();
    })
    .then(function(data) {
        if (data.status === 'success') {
            alert(data.message || 'Profile updated successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unable to update profile status.'));
        }
    })
    .catch(function(err) {
        alert('Network or server error: ' + err.message);
    });
}
</script>
