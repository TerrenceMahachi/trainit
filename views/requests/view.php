<?php
$refNumber = htmlspecialchars(is_object($request) ? $request->request_number : $request['request_number']);
$title = "{$refNumber} — Service Workspace — " . _SITE;
$navUser = \App\Helpers\Auth::user();
$reqId = is_object($request) ? $request->iD : $request['iD'];
$isClosed = !empty($closure);
?>
<?php include _BASE_PATH . '/views/partials/header.php'; ?>
<?php include _BASE_PATH . '/views/partials/nav.php'; ?>

<main class="py-4" style="background-color: #fcfbfe; min-height: 85vh;">
    <div class="container-xl">

        <!-- Top Header & Breadcrumbs -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted small">
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard" class="text-decoration-none" style="color: #2A114B;">Admin</a></li>
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/admin/requests" class="text-decoration-none" style="color: #2A114B;">Requests</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $refNumber ?></li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h3 fw-bold mb-0" style="color: #1C0D30;"><?= htmlspecialchars(is_object($request) ? $request->title : $request['title']) ?></h2>
                    <span class="badge font-monospace bg-light text-dark border"><?= $refNumber ?></span>
                    <?php if ($isClosed): ?>
                        <span class="badge bg-success"><i class="fa fa-check-circle me-1"></i>CLOSED</span>
                    <?php endif; ?>
                </div>
                <div class="text-muted small mt-1">
                    Client: <strong><?= htmlspecialchars($client ? (is_object($client) ? $client->trading_name : $client['trading_name']) : 'Client') ?></strong> &bull;
                    Plan: <strong><?= htmlspecialchars($plan ? (is_object($plan) ? $plan->plan_name : $plan['plan_name']) : 'Standard') ?></strong> &bull;
                    Priority: <span class="badge bg-danger-subtle text-danger border"><?= htmlspecialchars($priority ? (is_object($priority) ? $priority->name : $priority['name']) : 'Standard') ?></span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= $siteConfig->siteUrl ?>/admin/requests" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fa fa-arrow-left me-1"></i> Delivery Desk
                </a>
                <?php if ($isStaff && !$isClosed): ?>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTriageTicket">
                        <i class="fa fa-clock me-1"></i> Triage & SLA
                    </button>
                    <button type="button" class="btn text-white rounded-pill px-3 shadow-sm" style="background-color: #2A114B;" data-bs-toggle="modal" data-bs-target="#modalAssignTalent">
                        <i class="fa fa-user-plus me-1"></i> Dispatch Talent
                    </button>
                    <button type="button" class="btn btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalCloseRequest">
                        <i class="fa fa-check me-1"></i> Complete & Close
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                <i class="fa fa-check-circle me-2"></i>
                <?php if ($_GET['msg'] === 'triaged'): ?>Ticket triaged and SLA target recorded.<?php endif; ?>
                <?php if ($_GET['msg'] === 'talent_assigned'): ?>Talent assigned with rate snapshot and supervisor mentor.<?php endif; ?>
                <?php if ($_GET['msg'] === 'status_updated'): ?>Request status transition updated in immutable event log.<?php endif; ?>
                <?php if ($_GET['msg'] === 'closed'): ?>Engagement formally resolved and archived.<?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Column: Scope, Assignments & Timeline -->
            <div class="col-lg-5">

                <!-- Scope Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                    <h5 class="fw-bold mb-3" style="color: #2A114B;">
                        <i class="fa fa-file-lines me-2 text-primary"></i>Engagement Scope & Brief
                    </h5>
                    <div class="p-3 bg-light rounded-3 mb-3 small text-secondary" style="white-space: pre-line;">
                        <?= htmlspecialchars(is_object($request) ? $request->description : $request['description']) ?>
                    </div>

                    <div class="row g-2 small border-top pt-3">
                        <div class="col-6">
                            <span class="text-muted d-block">Target Due Date:</span>
                            <span class="fw-bold text-dark"><i class="fa fa-calendar me-1 text-primary"></i><?= htmlspecialchars(is_object($request) ? $request->desired_due_date : $request['desired_due_date']) ?></span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">Requester:</span>
                            <span class="fw-bold text-dark"><i class="fa fa-user me-1 text-secondary"></i><?= htmlspecialchars($requester ? (is_object($requester) ? $requester->name : $requester['name']) : 'Client Staff') ?></span>
                        </div>
                    </div>

                    <?php if ($triage): ?>
                        <div class="mt-3 p-2 rounded-3 bg-warning-subtle border border-warning-subtle small text-dark">
                            <i class="fa fa-bolt me-1 text-danger"></i><strong>Committed SLA Target:</strong>
                            <div><?= htmlspecialchars(is_object($triage) ? $triage->sla_due_date : $triage['sla_due_date']) ?></div>
                            <div class="text-muted" style="font-size: 0.78rem;"><?= htmlspecialchars(is_object($triage) ? $triage->triage_notes : $triage['triage_notes']) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($attachments)): ?>
                        <div class="mt-3 pt-3 border-top">
                            <h6 class="fw-bold small text-muted text-uppercase mb-2">Scope Attachments</h6>
                            <?php foreach ($attachments as $att): ?>
                                <div class="d-flex justify-content-between align-items-center p-2 rounded-3 border bg-white mb-1 small">
                                    <span><i class="fa fa-paperclip me-1 text-primary"></i><?= htmlspecialchars(is_object($att) ? $att->file_name : $att['file_name']) ?></span>
                                    <a href="<?= $siteConfig->siteUrl ?>/<?= htmlspecialchars(is_object($att) ? $att->file_path : $att['file_path']) ?>" target="_blank" class="btn btn-sm btn-light py-0">View</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Talent Work Assignments -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0" style="color: #2A114B;">
                            <i class="fa fa-users me-2 text-warning"></i>Dispatched Talent
                        </h5>
                        <?php if ($isStaff && !$isClosed): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalAssignTalent">
                                <i class="fa fa-plus me-1"></i> Assign
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($assignments)): ?>
                        <div class="text-center py-4 text-muted small">
                            <i class="fa fa-user-slash fa-2x mb-2 text-secondary" style="opacity: 0.3;"></i>
                            <p class="mb-0">No talent associates assigned to this engagement yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($assignments as $item): ?>
                                <?php
                                $assign = $item['assignment'];
                                $talent = $item['talent'];
                                $role = $item['role'];
                                $supervisor = $item['supervisor'];
                                ?>
                                <div class="list-group-item px-0 py-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <span class="fw-bold text-dark"><?= htmlspecialchars($talent ? (is_object($talent) ? $talent->name : $talent['name']) : 'Talent') ?></span>
                                            <span class="badge bg-secondary ms-1"><?= htmlspecialchars($role ? (is_object($role) ? $role->name : $role['name']) : 'Associate') ?></span>
                                        </div>
                                        <span class="fw-bold text-success">$<?= number_format(is_object($assign) ? $assign->hourly_rate_snapshot : $assign['hourly_rate_snapshot'], 2) ?>/hr</span>
                                    </div>
                                    <div class="small text-muted mb-2">
                                        <i class="fa fa-clock me-1"></i>Target Completion: <?= htmlspecialchars(is_object($assign) ? $assign->due_date : $assign['due_date']) ?>
                                    </div>

                                    <?php if ($supervisor): ?>
                                        <div class="p-2 rounded-3 bg-info-subtle small text-dark border border-info-subtle">
                                            <i class="fa fa-shield-alt me-1 text-primary"></i><strong>Supervised By:</strong> <?= htmlspecialchars(is_object($supervisor) ? $supervisor->name : $supervisor['name']) ?>
                                            <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($item['supervision_notes']) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Status Progression Event Timeline -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-3" style="color: #2A114B;">
                        <i class="fa fa-timeline me-2 text-secondary"></i>Status Audit Trail
                    </h5>
                    <div class="position-relative ps-3" style="border-left: 2px solid #e9ecef;">
                        <?php foreach ($statusEvents as $seItem): ?>
                            <?php
                            $se = $seItem['event'];
                            $st = $seItem['status'];
                            $actor = $seItem['actor'];
                            ?>
                            <div class="mb-3 position-relative">
                                <div class="position-absolute rounded-circle bg-primary" style="width: 10px; height: 10px; left: -21px; top: 4px;"></div>
                                <div class="fw-bold small text-dark"><?= htmlspecialchars($st ? (is_object($st) ? $st->name : $st['name']) : 'Status Event') ?></div>
                                <div class="small text-muted"><?= htmlspecialchars(is_object($se) ? $se->notes : $se['notes']) ?></div>
                                <div class="small text-secondary" style="font-size: 0.72rem;">
                                    by <?= htmlspecialchars($actor ? (is_object($actor) ? $actor->name : $actor['name']) : 'System') ?> on <?= htmlspecialchars(is_object($se) ? $se->reg_date : $se['reg_date']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

            <!-- Right Column: Collaboration & Threaded Messaging Stream -->
            <div class="col-lg-7" id="messages-area">
                <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-0" style="color: #2A114B;">
                                <i class="fa fa-comments me-2 text-primary"></i>Collaboration Workspace
                            </h5>
                            <span class="text-muted small">Real-time collaboration stream with client vs internal staff visibility.</span>
                        </div>
                        <?php if ($isStaff && !$isClosed): ?>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalUpdateStatus">
                                <i class="fa fa-arrow-right-arrow-left me-1"></i> Update Status
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Messages Stream Body -->
                    <div class="card-body p-4 overflow-auto flex-grow-1" style="max-height: 600px; background-color: #faf9fd;">
                        <?php if (empty($messages)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fa fa-message fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
                                <p class="mb-0">No messages posted yet. Start the conversation below.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($messages as $msgItem): ?>
                                <?php
                                $m = $msgItem['message'];
                                $author = $msgItem['author'];
                                $vis = $msgItem['visibility'];
                                $isInternal = ($vis && (is_object($vis) ? $vis->code : $vis['code']) === 'INTERNAL_STAFF');
                                ?>
                                <div class="mb-3 p-3 rounded-4 border <?= $isInternal ? 'bg-warning-subtle border-warning-subtle' : 'bg-white shadow-sm' ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle p-1 me-2 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 28px; height: 28px; background-color: #2A114B; font-size: 0.75rem;">
                                                <?= strtoupper(substr($author ? (is_object($author) ? $author->name : $author['name']) : 'U', 0, 1)) ?>
                                            </div>
                                            <span class="fw-bold text-dark small"><?= htmlspecialchars($author ? (is_object($author) ? $author->name : $author['name']) : 'User') ?></span>
                                            <?php if ($isInternal): ?>
                                                <span class="badge bg-warning text-dark ms-2" style="font-size: 0.65rem;">
                                                    <i class="fa fa-lock me-1"></i>INTERNAL STAFF ONLY
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted border ms-2" style="font-size: 0.65rem;">CLIENT VISIBLE</span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-muted small" style="font-size: 0.72rem;"><?= htmlspecialchars(is_object($m) ? $m->reg_date : $m['reg_date']) ?></span>
                                    </div>
                                    <div class="small text-secondary" style="white-space: pre-line;">
                                        <?= htmlspecialchars(is_object($m) ? $m->body : $m['body']) ?>
                                    </div>

                                    <?php if (!empty($msgItem['attachments'])): ?>
                                        <div class="mt-2 pt-2 border-top">
                                            <?php foreach ($msgItem['attachments'] as $mAtt): ?>
                                                <a href="<?= $siteConfig->siteUrl ?>/<?= htmlspecialchars(is_object($mAtt) ? $mAtt->file_path : $mAtt['file_path']) ?>" target="_blank" class="badge bg-light text-primary border text-decoration-none me-1 p-2">
                                                    <i class="fa fa-paperclip me-1"></i><?= htmlspecialchars(is_object($mAtt) ? $mAtt->file_name : $mAtt['file_name']) ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Message Compose Box -->
                    <?php if (!$isClosed): ?>
                        <div class="card-footer bg-white border-0 p-3">
                            <form action="<?= $siteConfig->siteUrl ?>/requests/message" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="request_id" value="<?= $reqId ?>">
                                <div class="mb-2">
                                    <textarea name="body" class="form-control rounded-3" rows="3" placeholder="Post a progress update, clarification, or deliverable note..." required></textarea>
                                </div>
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if ($isStaff): ?>
                                            <select name="visibility" class="form-select form-select-sm rounded-pill" style="max-width: 200px;">
                                                <option value="PUBLIC_CLIENT">Public (Client Visible)</option>
                                                <option value="INTERNAL_STAFF">Internal Staff Only</option>
                                            </select>
                                        <?php else: ?>
                                            <input type="hidden" name="visibility" value="PUBLIC_CLIENT">
                                        <?php endif; ?>
                                        <input type="file" name="message_attachment" class="form-control form-control-sm rounded-pill" style="max-width: 220px;">
                                    </div>
                                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #2A114B;">
                                        <i class="fa fa-paper-plane me-1"></i> Send Note
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="card-footer bg-light border-0 p-3 text-center text-muted small">
                            <i class="fa fa-lock me-1"></i> This engagement is closed. Messaging is disabled.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Modal: Triage & SLA Commitment -->
<div class="modal fade" id="modalTriageTicket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= $siteConfig->siteUrl ?>/admin/requests/triage" method="POST">
                <input type="hidden" name="request_id" value="<?= $reqId ?>">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" style="color: #2A114B;">
                        <i class="fa fa-bolt me-2 text-warning"></i>Triage & Commit SLA
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Committed SLA Due Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="sla_due_date" class="form-control rounded-3" value="<?= date('Y-m-d\TH:i', strtotime('+24 hours')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Triage Notes</label>
                        <textarea name="triage_notes" class="form-control rounded-3" rows="3" placeholder="SLA verified with client priority policy; talent requirements identified..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #2A114B;">Commit SLA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Dispatch Talent & Assign Mentor -->
<div class="modal fade" id="modalAssignTalent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= $siteConfig->siteUrl ?>/admin/requests/assign" method="POST">
                <input type="hidden" name="request_id" value="<?= $reqId ?>">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" style="color: #2A114B;">
                        <i class="fa fa-user-plus me-2 text-warning"></i>Dispatch Talent & Assign Mentor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Select Talent Candidate <span class="text-danger">*</span></label>
                            <select name="talent_user_id" class="form-select rounded-3" required>
                                <?php foreach ($allCandidateTalent as $cand): ?>
                                    <option value="<?= is_object($cand) ? $cand->iD : $cand['iD'] ?>">
                                        <?= htmlspecialchars(is_object($cand) ? $cand->name : $cand['name']) ?> (<?= htmlspecialchars(is_object($cand) ? $cand->email : $cand['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Hourly Rate Snapshot ($ USD)</label>
                            <input type="number" step="0.5" name="hourly_rate_snapshot" class="form-control rounded-3" value="35.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Assignment Due Date</label>
                            <input type="date" name="due_date" class="form-control rounded-3" value="<?= htmlspecialchars(is_object($request) ? $request->desired_due_date : $request['desired_due_date']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Supervisor / Quality Mentor (Optional)</label>
                            <select name="supervisor_user_id" class="form-select rounded-3">
                                <option value="0">-- No Supervisor Required (Direct Associate) --</option>
                                <?php foreach ($allCandidateTalent as $cand): ?>
                                    <option value="<?= is_object($cand) ? $cand->iD : $cand['iD'] ?>">
                                        <?= htmlspecialchars(is_object($cand) ? $cand->name : $cand['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Supervision / Delivery Instructions</label>
                            <textarea name="supervision_notes" class="form-control rounded-3" rows="2" placeholder="Tasks, deliverables, milestones, or quality checkpoints..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #2A114B;">Confirm Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Update Status -->
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= $siteConfig->siteUrl ?>/admin/requests/status" method="POST">
                <input type="hidden" name="request_id" value="<?= $reqId ?>">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" style="color: #2A114B;">
                        <i class="fa fa-arrow-right-arrow-left me-2 text-primary"></i>Update Request Status
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Target Status</label>
                        <select name="status_id" class="form-select rounded-3" required>
                            <?php foreach ($statuses as $st): ?>
                                <option value="<?= is_object($st) ? $st->iD : $st['iD'] ?>">
                                    <?= htmlspecialchars(is_object($st) ? $st->name : $st['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Transition Notes</label>
                        <textarea name="status_notes" class="form-control rounded-3" rows="2" placeholder="e.g. Work submitted to client staging for review..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Record Transition</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Complete & Close Request -->
<div class="modal fade" id="modalCloseRequest" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= $siteConfig->siteUrl ?>/admin/requests/close" method="POST">
                <input type="hidden" name="request_id" value="<?= $reqId ?>">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-success">
                        <i class="fa fa-circle-check me-2"></i>Complete & Close Engagement
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="small text-muted mb-3">Formally signs off deliverables, locks the ticket workspace, and records final client satisfaction.</p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Satisfaction Rating (1 to 5 Stars)</label>
                        <select name="satisfaction_rating" class="form-select rounded-3">
                            <option value="5">⭐⭐⭐⭐⭐ 5 Stars - Exceptional Delivery</option>
                            <option value="4">⭐⭐⭐⭐ 4 Stars - Met Requirements Fully</option>
                            <option value="3">⭐⭐⭐ 3 Stars - Satisfactory</option>
                            <option value="2">⭐⭐ 2 Stars - Partial Deficiencies</option>
                            <option value="1">⭐ 1 Star - Unsatisfactory</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Closure Sign-Off Notes</label>
                        <textarea name="closure_notes" class="form-control rounded-3" rows="3" placeholder="Deliverable accepted by client project lead; verified against SLA criteria." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Confirm Closure</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include _BASE_PATH . '/views/partials/footer.php'; ?>
