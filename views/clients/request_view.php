@extends('layouts.main')

<?php
global $siteConfig;
$client = $data['client'] ?? null;
$request = $data['request'] ?? null;
$plan = $data['plan'] ?? null;
$priority = $data['priority'] ?? null;
$requester = $data['requester'] ?? null;
$triage = $data['triage'] ?? null;
$statusEvents = $data['statusEvents'] ?? [];
$latestStatusCode = $data['latestStatusCode'] ?? 'NEW';
$latestStatusName = $data['latestStatusName'] ?? 'New';
$milestoneStep = $data['milestoneStep'] ?? 1;
$attachments = $data['attachments'] ?? [];
$closure = $data['closure'] ?? null;
$assignments = $data['assignments'] ?? [];
$messages = $data['messages'] ?? [];
$activeTab = 'requests';

$rId = $request ? (is_object($request) ? $request->iD : $request['iD']) : 0;
$reqNumber = htmlspecialchars($request ? (is_object($request) ? $request->request_number : $request['request_number']) : '');
$title = htmlspecialchars($request ? (is_object($request) ? $request->title : $request['title']) : '');
$description = nl2br(htmlspecialchars($request ? (is_object($request) ? $request->description : $request['description']) : ''));
$dueDate = $request ? (is_object($request) ? $request->desired_due_date : $request['desired_due_date']) : null;
$regDate = $request ? (is_object($request) ? $request->reg_date : $request['reg_date']) : null;

$isClosed = !empty($closure) || $latestStatusCode === 'CLOSED';
$isReviewReady = ($latestStatusCode === 'RESOLVED' || $latestStatusCode === 'WAITING_CLIENT') && !$isClosed;
?>

<div class="container py-4 my-2">
    <?php include _BASE_PATH . '/views/clients/nav.php'; ?>

    <!-- Breadcrumb & Back Link -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/client/portal" class="text-decoration-none">Portal</a></li>
                <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/client/requests" class="text-decoration-none">Work Requests</a></li>
                <li class="breadcrumb-item active text-truncate" style="max-width: 260px;" aria-current="page"><?= $reqNumber ?></li>
            </ol>
        </nav>
        <a href="<?= $siteConfig->siteUrl ?>/client/requests" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fa fa-arrow-left me-1"></i> Back to All Requests
        </a>
    </div>

    <!-- Alert Notifications -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i>
            <?php if ($_GET['msg'] === 'request_submitted'): ?>
                <strong>Work brief received!</strong> Your request reference is <code><?= $reqNumber ?></code>. Our delivery operations team has been notified.
            <?php elseif ($_GET['msg'] === 'message_sent'): ?>
                Your message has been posted to the talent collaboration stream.
            <?php elseif ($_GET['msg'] === 'signed_off'): ?>
                <strong>Engagement Approved!</strong> Thank you for signing off on this deliverable and providing your feedback.
            <?php else: ?>
                <?= htmlspecialchars($_GET['msg']) ?>
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fa fa-exclamation-triangle me-2"></i>
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Request Master Header Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center justify-content-between">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-light text-dark font-monospace border px-3 py-1"><?= $reqNumber ?></span>
                        
                        <?php 
                        $prioCode = $priority ? (is_object($priority) ? $priority->code : $priority['code']) : 'MEDIUM';
                        $prioName = $priority ? htmlspecialchars(is_object($priority) ? $priority->name : $priority['name']) : 'Normal';
                        ?>
                        <?php if ($prioCode === 'URGENT' || $prioCode === 'HIGH'): ?>
                            <span class="badge bg-danger rounded-pill px-3 py-1"><i class="fa fa-bolt me-1"></i> <?= $prioName ?> Priority</span>
                        <?php else: ?>
                            <span class="badge bg-info text-dark rounded-pill px-3 py-1"><?= $prioName ?> Priority</span>
                        <?php endif; ?>

                        <?php if ($plan): ?>
                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">
                                <i class="fa fa-cubes me-1"></i> <?= htmlspecialchars(is_object($plan) ? $plan->plan_name : $plan['plan_name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <h3 class="fw-bold mb-2 text-dark"><?= $title ?></h3>
                    <div class="d-flex align-items-center gap-3 text-muted small flex-wrap">
                        <span><i class="fa fa-calendar-plus me-1"></i> Submitted: <?= $regDate ? date('M d, Y H:i', strtotime($regDate)) : 'Recent' ?></span>
                        <?php if ($dueDate): ?>
                            <span><i class="fa fa-flag-checkered me-1 text-primary"></i> Target Due Date: <strong><?= date('M d, Y', strtotime($dueDate)) ?></strong></span>
                        <?php endif; ?>
                        <?php if ($requester): ?>
                            <span><i class="fa fa-user me-1"></i> Requester: <?= htmlspecialchars(is_object($requester) ? $requester->name : $requester['name']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="d-inline-block text-start p-3 bg-light rounded-4 border">
                        <div class="small text-muted mb-1 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Current Engagement State</div>
                        <?php if ($isClosed): ?>
                            <span class="badge bg-success rounded-pill px-3 py-2 fs-6">
                                <i class="fa fa-check-double me-1"></i> Deliverable Signed Off
                            </span>
                        <?php elseif ($isReviewReady): ?>
                            <span class="badge bg-warning text-dark fw-bold rounded-pill px-3 py-2 fs-6">
                                <i class="fa fa-clipboard-check me-1"></i> Ready for Client Review
                            </span>
                        <?php elseif ($latestStatusCode === 'IN_PROGRESS'): ?>
                            <span class="badge rounded-pill px-3 py-2 fs-6 text-white" style="background-color: #2A114B;">
                                <i class="fa fa-cog fa-spin me-1 text-warning"></i> In Production
                            </span>
                        <?php elseif ($latestStatusCode === 'TRIAGED'): ?>
                            <span class="badge bg-info text-dark rounded-pill px-3 py-2 fs-6">
                                <i class="fa fa-calendar-check me-1"></i> Scoped &amp; Scheduled
                            </span>
                        <?php else: ?>
                            <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">
                                <i class="fa fa-inbox me-1"></i> Under Initial Triage
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5-Stage Milestone Progress Stepper -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3 text-muted text-uppercase small" style="letter-spacing: 0.5px;">
                <i class="fa fa-tasks me-1 text-warning"></i> Delivery Milestone Lifecycle
            </h6>
            
            <div class="row g-2 text-center position-relative">
                <!-- Step 1 -->
                <div class="col">
                    <div class="p-3 rounded-4 <?= $milestoneStep >= 1 ? 'bg-success text-white' : 'bg-light text-muted' ?> shadow-sm">
                        <div class="fs-4 mb-1">
                            <i class="fa <?= $milestoneStep > 1 ? 'fa-check-circle' : 'fa-inbox' ?>"></i>
                        </div>
                        <div class="fw-bold small">1. Submitted</div>
                        <div style="font-size: 0.72rem;" class="<?= $milestoneStep >= 1 ? 'text-white-50' : 'text-muted' ?>">Brief logged</div>
                    </div>
                </div>
                <!-- Step 2 -->
                <div class="col">
                    <div class="p-3 rounded-4 <?= $milestoneStep >= 2 ? ($milestoneStep == 2 ? 'bg-primary text-white' : 'bg-success text-white') : 'bg-light text-muted' ?> shadow-sm">
                        <div class="fs-4 mb-1">
                            <i class="fa <?= $milestoneStep > 2 ? 'fa-check-circle' : 'fa-clipboard-list' ?>"></i>
                        </div>
                        <div class="fw-bold small">2. Triaged &amp; Scoped</div>
                        <div style="font-size: 0.72rem;" class="<?= $milestoneStep >= 2 ? 'text-white-50' : 'text-muted' ?>">SLA &amp; pairing committed</div>
                    </div>
                </div>
                <!-- Step 3 -->
                <div class="col">
                    <div class="p-3 rounded-4 <?= $milestoneStep >= 3 ? ($milestoneStep == 3 ? 'text-white' : 'bg-success text-white') : 'bg-light text-muted' ?> shadow-sm"
                         style="<?= $milestoneStep == 3 ? 'background-color: #2A114B;' : '' ?>">
                        <div class="fs-4 mb-1">
                            <i class="fa <?= $milestoneStep > 3 ? 'fa-check-circle' : ($milestoneStep == 3 ? 'fa-cog fa-spin text-warning' : 'fa-users-cog') ?>"></i>
                        </div>
                        <div class="fw-bold small">3. In Production</div>
                        <div style="font-size: 0.72rem;" class="<?= $milestoneStep >= 3 ? 'text-white-50' : 'text-muted' ?>">Talent executing</div>
                    </div>
                </div>
                <!-- Step 4 -->
                <div class="col">
                    <div class="p-3 rounded-4 <?= $milestoneStep >= 4 ? ($milestoneStep == 4 ? 'bg-warning text-dark fw-bold' : 'bg-success text-white') : 'bg-light text-muted' ?> shadow-sm">
                        <div class="fs-4 mb-1">
                            <i class="fa <?= $milestoneStep > 4 ? 'fa-check-circle' : 'fa-clipboard-check' ?>"></i>
                        </div>
                        <div class="fw-bold small">4. Client Review</div>
                        <div style="font-size: 0.72rem;" class="<?= $milestoneStep >= 4 ? ($milestoneStep == 4 ? 'text-dark text-opacity-75' : 'text-white-50') : 'text-muted' ?>">Deliverables submitted</div>
                    </div>
                </div>
                <!-- Step 5 -->
                <div class="col">
                    <div class="p-3 rounded-4 <?= $milestoneStep >= 5 ? 'bg-success text-white' : 'bg-light text-muted' ?> shadow-sm">
                        <div class="fs-4 mb-1">
                            <i class="fa fa-award"></i>
                        </div>
                        <div class="fw-bold small">5. Signed Off</div>
                        <div style="font-size: 0.72rem;" class="<?= $milestoneStep >= 5 ? 'text-white-50' : 'text-muted' ?>">Deliverables approved</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Workspace Two-Column Grid -->
    <div class="row g-4">
        <!-- Left Column: Scope, Attachments, Collaboration Stream, Sign-off -->
        <div class="col-lg-8">
            <!-- 1. Work Brief & Specifications -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white p-4 border-0 pb-2">
                    <h5 class="fw-bold mb-1" style="color: #2A114B;">
                        <i class="fa fa-file-alt me-2 text-warning"></i> Brief &amp; Scope Requirements
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="p-3 bg-light rounded-4 mb-3 border">
                        <div class="lh-base text-dark"><?= $description ?></div>
                    </div>

                    <?php if (!empty($attachments)): ?>
                        <div class="mt-3">
                            <h6 class="fw-bold small text-muted text-uppercase mb-2"><i class="fa fa-paperclip me-1"></i> Attached Specifications &amp; Assets</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($attachments as $att): 
                                    $attName = htmlspecialchars(is_object($att) ? $att->file_name : $att['file_name']);
                                    $attPath = htmlspecialchars(is_object($att) ? $att->file_path : $att['file_path']);
                                ?>
                                    <a href="<?= $siteConfig->siteUrl . '/' . $attPath ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2">
                                        <i class="fa fa-download me-1 text-primary"></i> <?= $attName ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 2. Client Review & Formal Sign-off Section -->
            <?php if ($isClosed && $closure): 
                $rating = (int)(is_object($closure) ? $closure->satisfaction_rating : $closure['satisfaction_rating']);
                $closureNotes = htmlspecialchars(is_object($closure) ? $closure->closure_notes : $closure['closure_notes']);
                $closedAt = is_object($closure) ? $closure->reg_date : $closure['reg_date'];
            ?>
                <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-success border-4" style="background: #F4FAF5;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <h5 class="fw-bold text-success mb-0">
                                <i class="fa fa-check-circle me-2"></i> Engagement Completed &amp; Signed Off
                            </h5>
                            <span class="text-muted small">Signed off on <?= date('M d, Y H:i', strtotime($closedAt)) ?></span>
                        </div>
                        <div class="mb-2">
                            <span class="fw-semibold text-dark me-2">Client Satisfaction Rating:</span>
                            <span class="text-warning fs-5">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa <?= $i <= $rating ? 'fa-star' : 'fa-star-o' ?>"></i>
                                <?php endfor; ?>
                            </span>
                            <strong class="ms-1 text-dark">(<?= $rating ?> / 5)</strong>
                        </div>
                        <?php if ($closureNotes): ?>
                            <p class="mb-0 text-muted fst-italic">"<?= $closureNotes ?>"</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($isReviewReady || $latestStatusCode === 'IN_PROGRESS'): ?>
                <!-- Sign-Off Action Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-warning border-4" style="background: #FFFBF0;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill">Deliverable Acceptance</span>
                            <h5 class="fw-bold mb-0 text-dark">Ready to Approve &amp; Sign Off</h5>
                        </div>
                        <p class="text-muted small mb-3">Review the deliverables and communication updates below. When you are satisfied with the completed work, sign off to close this engagement ticket and provide talent performance feedback.</p>

                        <form method="POST" action="<?= $siteConfig->siteUrl ?>/client/requests/signoff">
                            <input type="hidden" name="request_id" value="<?= $rId ?>">
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold small text-dark">Satisfaction Rating (1 to 5 Stars) <span class="text-danger">*</span></label>
                                    <select name="satisfaction_rating" class="form-select rounded-pill" required>
                                        <option value="5" selected>★★★★★ - Exceptional (5 Stars)</option>
                                        <option value="4">★★★★☆ - Very Good (4 Stars)</option>
                                        <option value="3">★★★☆☆ - Satisfactory (3 Stars)</option>
                                        <option value="2">★★☆☆☆ - Needs Improvement (2 Stars)</option>
                                        <option value="1">★☆☆☆☆ - Unsatisfactory (1 Star)</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold small text-dark">Sign-off Notes / Testimonial</label>
                                    <input type="text" name="closure_notes" class="form-control rounded-pill" 
                                           placeholder="e.g. Deliverables verified, clean handover and great communication." 
                                           value="Deliverables accepted and approved by client organization.">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm" onclick="return confirm('Confirm deliverable sign-off and closure of this engagement ticket?');">
                                <i class="fa fa-check-double me-1"></i> Accept Deliverables &amp; Sign Off
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 3. Public Collaboration Stream -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white p-4 border-0 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: #2A114B;">
                            <i class="fa fa-comments me-2 text-warning"></i> Talent Collaboration Stream
                        </h5>
                        <p class="text-muted small mb-0">Live dialogue between your organization, assigned talent, and the service manager.</p>
                    </div>
                    <span class="badge bg-light text-muted border rounded-pill px-3 py-1 small">
                        <?= count($messages) ?> message(s)
                    </span>
                </div>

                <div class="card-body p-4">
                    <!-- Messages List -->
                    <div class="collaboration-stream mb-4" style="max-height: 520px; overflow-y: auto;">
                        <?php if (empty($messages)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fa fa-comment-dots fa-2x mb-2 text-secondary"></i>
                                <p class="small mb-0">No messages yet. Post a comment or question below to start collaborating with the assigned delivery team.</p>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($messages as $item): 
                                    $msg = $item['message'];
                                    $author = $item['author'];
                                    $authorName = htmlspecialchars($author ? (is_object($author) ? $author->name : $author['name']) : 'User');
                                    $authorRole = $author ? ((int)(is_object($author) ? $author->role : $author['role'])) : 0;
                                    $isClientAuthor = in_array($authorRole, [2, 3], true);
                                    $msgBody = nl2br(htmlspecialchars(is_object($msg) ? ($msg->body ?? $msg->message_body ?? '') : ($msg['body'] ?? $msg['message_body'] ?? '')));
                                    $msgDate = is_object($msg) ? $msg->reg_date : $msg['reg_date'];
                                    $msgAtts = $item['attachments'] ?? [];
                                ?>
                                    <div class="d-flex gap-3 <?= $isClientAuthor ? 'flex-row-reverse text-end' : '' ?>">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                                             style="width: 42px; height: 42px; flex-shrink: 0; background-color: <?= $isClientAuthor ? '#431E76' : '#2A114B' ?>;">
                                            <?= strtoupper(substr($authorName, 0, 1)) ?>
                                        </div>
                                        <div style="max-width: 80%;">
                                            <div class="d-flex align-items-center gap-2 mb-1 <?= $isClientAuthor ? 'justify-content-end' : '' ?>">
                                                <span class="fw-bold small text-dark"><?= $authorName ?></span>
                                                <?php if ($isClientAuthor): ?>
                                                    <span class="badge bg-light text-dark border rounded-pill small" style="font-size: 0.68rem;">Client Team</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark fw-bold rounded-pill small" style="font-size: 0.68rem;">Tsigiro Talent</span>
                                                <?php endif; ?>
                                                <span class="text-muted" style="font-size: 0.72rem;"><?= date('M d, H:i', strtotime($msgDate)) ?></span>
                                            </div>
                                            <div class="p-3 rounded-4 shadow-sm text-start <?= $isClientAuthor ? 'bg-white border' : 'text-white' ?>" 
                                                 style="<?= !$isClientAuthor ? 'background-color: #2A114B;' : '' ?>">
                                                <div class="small lh-base"><?= $msgBody ?></div>
                                                <?php if (!empty($msgAtts)): ?>
                                                    <div class="mt-2 pt-2 border-top border-white border-opacity-25">
                                                        <?php foreach ($msgAtts as $matt): 
                                                            $fname = htmlspecialchars(is_object($matt) ? $matt->file_name : $matt['file_name']);
                                                            $fpath = htmlspecialchars(is_object($matt) ? $matt->file_path : $matt['file_path']);
                                                        ?>
                                                            <a href="<?= $siteConfig->siteUrl . '/' . $fpath ?>" target="_blank" class="btn btn-sm btn-light rounded-pill px-2 py-1 text-dark" style="font-size: 0.75rem;">
                                                                <i class="fa fa-download me-1 text-primary"></i> <?= $fname ?>
                                                            </a>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Post Reply Box -->
                    <?php if (!$isClosed): ?>
                        <div class="pt-3 border-top">
                            <form method="POST" action="<?= $siteConfig->siteUrl ?>/requests/message" enctype="multipart/form-data">
                                <input type="hidden" name="request_id" value="<?= $rId ?>">
                                <input type="hidden" name="visibility" value="PUBLIC_CLIENT">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold small text-muted">Post a Message / Share Deliverables Feedback</label>
                                    <textarea name="message" class="form-control rounded-4" rows="3" placeholder="Type your message, query, or revision feedback here..." required></textarea>
                                </div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <label for="msg_file" class="btn btn-outline-secondary btn-sm rounded-pill px-3 mb-0" style="cursor: pointer;">
                                            <i class="fa fa-paperclip me-1"></i> Attach File
                                        </label>
                                        <input type="file" id="msg_file" name="attachment" class="d-none" onchange="document.getElementById('msg_file_name').innerText = this.files[0] ? this.files[0].name : '';">
                                        <span id="msg_file_name" class="small text-muted text-truncate" style="max-width: 180px;"></span>
                                    </div>
                                    <button type="submit" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                                        <i class="fa fa-paper-plane me-1"></i> Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="p-3 bg-light rounded-4 text-center text-muted small border">
                            <i class="fa fa-lock me-1"></i> This engagement is signed off and closed. To request additional work, please submit a new work request.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Assigned Talent & Retainer Summary & Status Events -->
        <div class="col-lg-4">
            <!-- 1. Assigned Talent Pairing -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white p-4 border-0 pb-2">
                    <h6 class="fw-bold mb-0 text-uppercase small" style="color: #2A114B; letter-spacing: 0.5px;">
                        <i class="fa fa-user-shield me-2 text-warning"></i> Assigned Delivery Team
                    </h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <?php if (empty($assignments)): ?>
                        <div class="p-3 bg-light rounded-4 text-center text-muted">
                            <i class="fa fa-hourglass-start fa-2x mb-2 text-secondary"></i>
                            <p class="small mb-0">Delivery desk is currently triaging this brief to assign the optimal <strong>Associate Mentor &amp; Apprentice</strong> team.</p>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($assignments as $asg): 
                                $talent = $asg['talent'];
                                $tRole = $asg['role'];
                                $supervisor = $asg['supervisor'];
                                $tName = htmlspecialchars($talent ? (is_object($talent) ? $talent->name : $talent['name']) : 'Assigned Talent');
                                $tRoleName = htmlspecialchars($tRole ? (is_object($tRole) ? $tRole->name : $tRole['name']) : 'Specialist');
                                $supName = $supervisor ? htmlspecialchars(is_object($supervisor) ? $supervisor->name : $supervisor['name']) : null;
                            ?>
                                <div class="p-3 rounded-4 bg-light border">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                             style="width: 42px; height: 42px; background-color: #2A114B;">
                                            <?= strtoupper(substr($tName, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-0"><?= $tName ?></div>
                                            <span class="badge bg-warning text-dark rounded-pill px-2 py-0 small"><?= $tRoleName ?></span>
                                        </div>
                                    </div>
                                    <?php if ($supName): ?>
                                        <div class="small text-muted pt-2 border-top">
                                            <i class="fa fa-shield-alt me-1 text-primary"></i> Supervised by: <strong><?= $supName ?></strong>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 2. Retainer Plan & SLA Specs -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white p-4 border-0 pb-2">
                    <h6 class="fw-bold mb-0 text-uppercase small" style="color: #2A114B; letter-spacing: 0.5px;">
                        <i class="fa fa-shield-alt me-2 text-warning"></i> Retainer &amp; SLA Commitment
                    </h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="list-group list-group-flush small">
                        <div class="list-group-item d-flex justify-content-between px-0 py-2 border-0">
                            <span class="text-muted">Retainer Plan</span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($plan ? (is_object($plan) ? $plan->plan_name : $plan['plan_name']) : 'Custom') ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0 py-2 border-0">
                            <span class="text-muted">Target Completion</span>
                            <span class="fw-semibold text-primary"><?= $dueDate ? date('M d, Y', strtotime($dueDate)) : 'Within SLA Window' ?></span>
                        </div>
                        <?php if ($triage): ?>
                            <div class="list-group-item d-flex justify-content-between px-0 py-2 border-0">
                                <span class="text-muted">Estimated Hours</span>
                                <span class="fw-bold text-dark"><?= is_object($triage) ? $triage->estimated_hours : $triage['estimated_hours'] ?> hrs</span>
                            </div>
                        <?php endif; ?>
                        <div class="list-group-item d-flex justify-content-between px-0 py-2 border-0">
                            <span class="text-muted">Quality Supervision</span>
                            <span class="badge bg-success bg-opacity-25 text-success rounded-pill px-2">Peer Reviewed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Audit Trail / Status Events -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white p-4 border-0 pb-2">
                    <h6 class="fw-bold mb-0 text-uppercase small" style="color: #2A114B; letter-spacing: 0.5px;">
                        <i class="fa fa-history me-2 text-warning"></i> Lifecycle Audit Trail
                    </h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <?php if (empty($statusEvents)): ?>
                        <p class="text-muted small mb-0">No status transitions recorded yet.</p>
                    <?php else: ?>
                        <div class="timeline small">
                            <?php foreach ($statusEvents as $se): 
                                $ev = $se['event'];
                                $st = $se['status'];
                                $actor = $se['actor'];
                                $stName = $st ? htmlspecialchars(is_object($st) ? $st->name : $st['name']) : 'Updated';
                                $evNotes = htmlspecialchars(is_object($ev) ? $ev->notes : $ev['notes']);
                                $evDate = is_object($ev) ? $ev->reg_date : $ev['reg_date'];
                            ?>
                                <div class="mb-3 pb-2 border-bottom border-light">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-dark"><?= $stName ?></strong>
                                        <span class="text-muted" style="font-size: 0.72rem;"><?= date('M d, H:i', strtotime($evDate)) ?></span>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.8rem;"><?= $evNotes ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
