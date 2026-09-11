<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Applicationtrack;
use App\Models\Applicationstatus;
use App\Models\Servicefunction;
use App\Models\Proficiencylevel;
use App\Models\Skillitem;
use App\Models\Zimprovince;
use App\Models\Workrightstatus;
use App\Models\Apprenticestatus;
use App\Models\Employmentstatus;
use App\Models\Qualificationtype;
use App\Models\Qualificationstatus;
use App\Models\Professionalbody;
use App\Models\Sectortype;
use App\Models\Engagementbasis;
use App\Models\Engagementmodel;
use App\Models\Worklocationpreference;
use App\Models\Invoiceentitytype;
use App\Models\Refereecontacttiming;
use App\Models\Refereeverificationstatus;
use App\Models\Vettingrecommendation;
use App\Models\Gender;
use App\Models\Rosterapplication;
use App\Models\Apprenticeprofile;
use App\Models\Associateprofile;
use App\Models\Rosterskill;
use App\Models\Rosterqualification;
use App\Models\Rosterworkhistory;
use App\Models\Rosterreferee;
use App\Models\Rosterjudgementresponse;
use App\Models\Rosterassessment;
use App\Models\Rosteronboarding;
use App\Models\Documenttype;
use App\Models\Rosterdocument;
use App\Models\Rosterstatusevent;
use App\Models\Login;
use App\Helpers\Auth;
use App\Helpers\Mailer;
use Exception;

class RosterApplicationController
{
    /**
     * Get all active and submitted applications/profiles for a given user.
     */
    public function getUserProfiles(int $userId): array
    {
        $applications = Rosterapplication::findByQuery(
            "SELECT * FROM rosterapplication WHERE user = ? ORDER BY iD DESC",
            [$userId]
        );

        $apprenticeApps = [];
        $associateApps = [];

        foreach ($applications as $app) {
            $trackCode = $app->applicationtrack()->code ?? '';
            if ($trackCode === 'apprentice') {
                $apprenticeApps[] = $app;
            } elseif ($trackCode === 'associate') {
                $associateApps[] = $app;
            }
        }

        return [
            'total' => count($applications),
            'apprentice' => $apprenticeApps,
            'associate' => $associateApps,
            'has_profiles' => count($applications) > 0,
        ];
    }

    /**
     * Helper to retrieve application owned by current user or accessible by admin/vetting officer.
     */
    public function getAuthorizedApplication(int $appId): ?Rosterapplication
    {
        $userId = Auth::id();
        if ($appId <= 0) {
            return null;
        }
        $apps = Rosterapplication::findByQuery("SELECT * FROM rosterapplication WHERE iD = ?", [$appId]);
        if (empty($apps)) {
            return null;
        }
        $app = $apps[0];
        if ($userId && (int)$app->user === (int)$userId) {
            return $app;
        }
        if (Auth::isAdmin() || Auth::isVettingOfficer()) {
            return $app;
        }
        return null;
    }

    /**
     * Generate secure HMAC token for candidate shortlist magic link.
     */
    public function generateShortlistToken(Rosterapplication $app): string
    {
        return hash_hmac('sha256', $app->iD . '|' . $app->reg_date . '|' . $app->user, _APP_SECRET);
    }

    /**
     * Verify HMAC token for candidate shortlist magic link.
     */
    public function verifyShortlistToken(Rosterapplication $app, string $token): bool
    {
        $expected = $this->generateShortlistToken($app);
        return hash_equals($expected, $token);
    }

    /**
     * Helper to retrieve application and verify candidate has been shortlisted (or is admin/vetting officer)
     * before accessing the deep verification dossier (stages 2-5).
     */
    public function ensureShortlistedDossierAccess(int $appId): ?Rosterapplication
    {
        global $siteConfig;
        $app = $this->getAuthorizedApplication($appId);
        if (!$app) {
            if (php_sapi_name() === 'cli') {
                return null;
            }
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }

        // If user is Admin or Vetting Officer, allow access unconditionally
        if (Auth::isAdmin() || Auth::isVettingOfficer()) {
            return $app;
        }

        // If candidate application status is not yet shortlisted (status < 3, e.g. status 1 Draft or 2 Submitted)
        if ((int)$app->applicationstatus < 3) {
            $_SESSION['flash_warning'] = 'Your application has been received and is currently under initial screening. Once shortlisted by our vetting committee, you will receive an invitation email with a link to complete your credentials and verification dossier.';
            if (php_sapi_name() === 'cli') {
                return null;
            }
            header("Location: " . $siteConfig->siteUrl . "/roster/application/status?id=" . $appId);
            exit;
        }

        return $app;
    }

    /**
     * Handle Shortlist Magic Link Token Login.
     * Route: GET /roster/shortlist/complete?id=X&token=Y
     */
    public function handleShortlistTokenLogin()
    {
        global $siteConfig;
        $appId = (int)($_GET['id'] ?? 0);
        $token = trim($_GET['token'] ?? '');

        if ($appId <= 0 || empty($token)) {
            $_SESSION['flash_error'] = 'Invalid shortlist verification link.';
            if (php_sapi_name() === 'cli') return ['status' => 0, 'msg' => 'Invalid parameters'];
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }

        $app = (new Rosterapplication())->find($appId);
        if (!$app || !$this->verifyShortlistToken($app, $token)) {
            $_SESSION['flash_error'] = 'Shortlist link is invalid or expired.';
            if (php_sapi_name() === 'cli') return ['status' => 0, 'msg' => 'Invalid token'];
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }

        // Candidate must be at least shortlisted (status >= 3)
        if ((int)$app->applicationstatus < 3) {
            $_SESSION['flash_warning'] = 'Your application is not yet shortlisted.';
            if (php_sapi_name() === 'cli') return ['status' => 0, 'msg' => 'Not shortlisted'];
            header("Location: " . $siteConfig->siteUrl . "/roster/application/status?id=" . $appId);
            exit;
        }

        // Log the candidate user in
        Auth::login($app->user);

        if (php_sapi_name() === 'cli') {
            return ['status' => 1, 'msg' => 'Authenticated successfully', 'redirect' => $siteConfig->siteUrl . "/roster/apply/credentials?id=" . $appId];
        }

        // Redirect directly into Stage 2: Credentials
        header("Location: " . $siteConfig->siteUrl . "/roster/apply/credentials?id=" . $appId);
        exit;
    }

    /**
     * Handle Admin Action: Shortlist Candidate & Email Dossier Link.
     * Route: POST /admin/roster/shortlist
     */
    public function handleShortlistCandidate(): array
    {
        global $siteConfig;
        if (!Auth::isAdmin() && !Auth::isVettingOfficer() && !Auth::isServiceManager()) {
            return ['status' => 0, 'msg' => 'Unauthorized'];
        }

        $appId = (int)($_POST['rosterapplication'] ?? $_POST['id'] ?? 0);
        $app = (new Rosterapplication())->find($appId);
        if (!$app) {
            return ['status' => 0, 'msg' => 'Application not found'];
        }

        $app->applicationstatus = 3; // Screened / Shortlisted
        $app->update();

        $token = $this->generateShortlistToken($app);
        $dossierLink = $siteConfig->siteUrl . '/roster/shortlist/complete?id=' . $app->iD . '&token=' . $token;

        $this->logStatusEvent($appId, 3, 'Candidate shortlisted by review committee. Verification dossier link dispatched via email.');

        $candidate = (new User())->find($app->user);
        if ($candidate) {
            Mailer::sendShortlistInvitation($app, $candidate, $dossierLink);
        }

        return [
            'status' => 1,
            'msg' => 'Candidate shortlisted and dossier completion invitation emailed successfully!',
            'dossier_link' => $dossierLink
        ];
    }

    /**
     * Helper to save a document file into Rosterdocument model.
     */
    public function saveRosterDocument(int $appId, string $docTypeCode, array $file, string $prefix): ?Rosterdocument
    {
        $filename = $this->uploadFile($file, $prefix);
        if (!$filename) {
            return null;
        }

        $docTypes = Documenttype::findByQuery("SELECT * FROM documenttype WHERE code = ?", [$docTypeCode]);
        $docTypeId = !empty($docTypes) ? (int)$docTypes[0]->iD : 1;

        $doc = new Rosterdocument();
        $doc->rosterapplication = $appId;
        $doc->documenttype = $docTypeId;
        $doc->file_path = $filename;
        $doc->original_name = $file['name'] ?? $filename;
        $doc->file_size_kb = (int) round(($file['size'] ?? 0) / 1024);
        $doc->reg_by = Auth::id() ?: 1;
        $doc->save();

        return $doc;
    }

    /**
     * Helper to log status audit event.
     */
    public function logStatusEvent(int $appId, int $statusId, string $remarks): void
    {
        $event = new Rosterstatusevent();
        $event->rosterapplication = $appId;
        $event->applicationstatus = $statusId;
        $event->remarks = $remarks;
        $event->reg_by = Auth::id() ?: 1;
        $event->save();
    }

    /**
     * Render the Stage 1 Express Intake form initiated from the Opportunities page.
     */
    public function showExpressForm(string $track, ?int $appId = null)
    {
        global $siteConfig;
        $trackCode = strtolower(trim($track));
        $tracks = Applicationtrack::findByQuery("SELECT * FROM applicationtrack WHERE code = ?", [$trackCode]);
        if (empty($tracks)) {
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }
        $trackObj = $tracks[0];

        $application = null;
        if ($appId) {
            $application = $this->getAuthorizedApplication($appId);
        }

        $provinces = Zimprovince::findByQuery("SELECT * FROM zimprovince ORDER BY sort_order ASC");
        $serviceFunctions = Servicefunction::findByQuery("SELECT * FROM servicefunction ORDER BY sort_order ASC");
        $employmentStatuses = Employmentstatus::all();
        $professionalBodies = Professionalbody::all();

        $data = [
            'title' => 'Express Apply: ' . $trackObj->name . ' Roster',
            'track' => $trackObj,
            'application' => $application,
            'user' => Auth::check() ? (new AccountController())->getUser(Auth::id()) : null,
            'provinces' => $provinces,
            'serviceFunctions' => $serviceFunctions,
            'employmentStatuses' => $employmentStatuses,
            'professionalBodies' => $professionalBodies,
        ];

        $viewName = $trackCode === 'associate' ? 'roster.apply_express_associate' : 'roster.apply_express_apprentice';
        return view($viewName, compact('data'));
    }

    /**
     * Handle Stage 1 Express Intake Submission (Auto-provisions user if guest, creates draft application).
     */
    public function handleExpressSubmit()
    {
        global $siteConfig;

        try {
            $trackCode = strtolower(trim($_POST['track_code'] ?? 'apprentice'));
            $tracks = Applicationtrack::findByQuery("SELECT * FROM applicationtrack WHERE code = ?", [$trackCode]);
            $trackObj = !empty($tracks) ? $tracks[0] : null;
            if (!$trackObj) {
                throw new Exception('Invalid application track specified.');
            }

            $email = strtolower(trim($_POST['email'] ?? ''));
            $legalName = trim($_POST['legal_name'] ?? '');
            if (empty($email) || empty($legalName)) {
                throw new Exception('Full legal name and email address are required.');
            }

            // 1. Authenticate or Provision User
            $userId = Auth::id();
            $tempPass = null;
            if (!$userId) {
                $existingUsers = User::findByQuery("SELECT * FROM user WHERE email = ? LIMIT 1", [$email]);
                if (!empty($existingUsers)) {
                    $user = $existingUsers[0];
                    $userId = (int)$user->iD;
                } else {
                    $user = new User();
                    $user->name = $legalName;
                    $user->email = $email;
                    $user->role = 2; // candidate
                    $user->status = 1;
                    $user->save();
                    $userId = (int)$user->iD;

                    $tempPass = bin2hex(random_bytes(6));
                    $login = new Login();
                    $login->user = $userId;
                    $login->password = password_hash($tempPass, PASSWORD_BCRYPT);
                    $login->status = 1;
                    $login->save();
                }
                Auth::login($userId);
            }

            // 2. Create or Update Core Application
            $appId = (int)($_POST['application_id'] ?? 0);
            $app = null;
            if ($appId > 0) {
                $app = $this->getAuthorizedApplication($appId);
            }
            if (!$app) {
                $app = new Rosterapplication();
                $app->user = $userId;
                $app->applicationtrack = $trackObj->iD;
                $app->applicationstatus = 2; // Submitted (Initial one-page intake)
                $app->reg_by = $userId;
            } else {
                $app->applicationstatus = 2; // Submitted
            }

            $app->legal_name = $legalName;
            $app->preferred_name = trim($_POST['preferred_name'] ?? '');
            $app->email = $email;
            $app->mobile_number = trim($_POST['mobile_number'] ?? '');
            $app->city = trim($_POST['city'] ?? '');
            $app->zimprovince = !empty($_POST['zimprovince']) ? (int)$_POST['zimprovince'] : null;
            $app->primaryfunction = !empty($_POST['primaryfunction']) ? (int)$_POST['primaryfunction'] : 1;

            if ($app->iD) {
                $app->update();
            } else {
                $app->save();
            }
            $appId = (int)$app->iD;

            // 3. Track-Specific Profile
            if ($trackCode === 'apprentice') {
                $appProfiles = Apprenticeprofile::findByQuery("SELECT * FROM apprenticeprofile WHERE rosterapplication = ?", [$appId]);
                $appProfile = !empty($appProfiles) ? $appProfiles[0] : new Apprenticeprofile();
                $appProfile->rosterapplication = $appId;
                $appProfile->institution_name = trim($_POST['institution_name'] ?? '');
                $appProfile->degree_programme = trim($_POST['degree_programme'] ?? '');
                $appProfile->study_level = trim($_POST['study_level'] ?? '');
                $appProfile->wrl_start_date = !empty($_POST['wrl_start_date']) ? $_POST['wrl_start_date'] : null;
                $appProfile->wrl_duration_months = !empty($_POST['wrl_duration_months']) ? (int)$_POST['wrl_duration_months'] : 12;
                $appProfile->is_wrl_attachment = 1;
                $appProfile->reg_by = $userId;

                if ($appProfile->iD) {
                    $appProfile->update();
                } else {
                    $appProfile->save();
                }
            } elseif ($trackCode === 'associate') {
                $assocProfiles = Associateprofile::findByQuery("SELECT * FROM associateprofile WHERE rosterapplication = ?", [$appId]);
                $assocProfile = !empty($assocProfiles) ? $assocProfiles[0] : new Associateprofile();
                $assocProfile->rosterapplication = $appId;
                $assocProfile->years_experience = trim($_POST['years_experience'] ?? '');
                $assocProfile->employmentstatus = !empty($_POST['employmentstatus']) ? (int)$_POST['employmentstatus'] : null;
                $assocProfile->day_rate_expectation = !empty($_POST['day_rate_expectation']) ? (float)$_POST['day_rate_expectation'] : null;
                $assocProfile->capacity_days_per_month = trim($_POST['capacity_days_per_month'] ?? '');
                $assocProfile->reg_by = $userId;

                if ($assocProfile->iD) {
                    $assocProfile->update();
                } else {
                    $assocProfile->save();
                }
            }

            // 4. File Upload (CV / Resume)
            if (!empty($_FILES['cv_doc']['name'])) {
                $this->saveRosterDocument($appId, 'CV_RESUME', $_FILES['cv_doc'], 'cv_' . $appId);
            }

            // 5. Log Status Event
            $this->logStatusEvent($appId, 2, 'Initial one-page application and CV submitted via Opportunities page. Awaiting review.');

            // 6. Send Submission Receipt Email
            $candidate = (new User())->find($userId);
            if ($candidate) {
                Mailer::sendApplicationSubmitted($app, $candidate, $tempPass);
            }

            $redirectUrl = $siteConfig->siteUrl . '/roster/application/status?id=' . $appId;

            // Handle AJAX vs standard POST
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                return ['status' => 1, 'redirect' => $redirectUrl, 'msg' => 'Application submitted successfully!'];
            }

            header("Location: " . $redirectUrl);
            exit;
        } catch (Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                return ['status' => 0, 'msg' => $e->getMessage()];
            }
            $_SESSION['flash_error'] = $e->getMessage();
            header("Location: " . ($siteConfig->siteUrl . "/opportunities/apply/" . ($trackCode ?? 'apprentice')));
            exit;
        }
    }

    /**
     * Render Stage 2: Credentials & Docs.
     */
    public function showCredentialsForm(int $appId)
    {
        global $siteConfig;
        $app = $this->ensureShortlistedDossierAccess($appId);
        if (!$app) {
            if (php_sapi_name() === 'cli') {
                return 'ACCESS_DENIED_NOT_SHORTLISTED';
            }
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }

        $qualificationTypes = Qualificationtype::findByQuery("SELECT * FROM qualificationtype ORDER BY sort_order ASC");
        $professionalBodies = Professionalbody::all();

        $data = [
            'title' => 'Step 2: Qualifications & Credentials',
            'application' => $app,
            'qualificationTypes' => $qualificationTypes,
            'professionalBodies' => $professionalBodies,
        ];

        return view('roster.apply_credentials', compact('data'));
    }

    /**
     * Handle Stage 2: Credentials Submission.
     */
    public function handleCredentialsSubmit()
    {
        global $siteConfig;
        $appId = (int)($_POST['application_id'] ?? 0);
        $app = $this->ensureShortlistedDossierAccess($appId);
        if (!$app) {
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }

        $trackCode = $app->applicationtrack()->code ?? 'apprentice';

        // 1. Primary Qualification Record
        if (!empty($_POST['qualification_title'])) {
            $quals = Rosterqualification::findByQuery("SELECT * FROM rosterqualification WHERE rosterapplication = ?", [$appId]);
            $qual = !empty($quals) ? $quals[0] : new Rosterqualification();
            $qual->rosterapplication = $appId;
            $qual->qualificationtype = !empty($_POST['qualificationtype']) ? (int)$_POST['qualificationtype'] : 1;
            $qual->title = trim($_POST['qualification_title']);
            $qual->institution_name = trim($_POST['qualification_institution'] ?? '');
            $qual->field_of_study = trim($_POST['field_of_study'] ?? '');
            $qual->qualificationstatus = 1;
            $qual->reg_by = Auth::id() ?: 1;

            if ($qual->iD) {
                $qual->update();
            } else {
                $qual->save();
            }
        }

        // 2. Track Specific Fields
        if ($trackCode === 'apprentice') {
            $appProfile = $app->apprenticeProfile();
            if ($appProfile) {
                $appProfile->student_reg_number = trim($_POST['student_reg_number'] ?? '');
                if (!empty($_POST['expected_completion_year'])) {
                    $appProfile->expected_completion_date = trim($_POST['expected_completion_year']) . '-12-31';
                }
                $appProfile->update();
            }
            if (!empty($_FILES['wrl_letter_doc']['name'])) {
                $this->saveRosterDocument($appId, 'WRL_LETTER', $_FILES['wrl_letter_doc'], 'wrl_let_' . $appId);
            }
            if (!empty($_FILES['transcript_doc']['name'])) {
                $this->saveRosterDocument($appId, 'TRANSCRIPT', $_FILES['transcript_doc'], 'transcript_' . $appId);
            }
        } else {
            $assocProfile = $app->associateProfile();
            if ($assocProfile) {
                if (!empty($_POST['professionalbody'])) {
                    $assocProfile->professionalbody = (int)$_POST['professionalbody'];
                }
                $assocProfile->update();
            }
            if (!empty($_FILES['pro_cert_doc']['name'])) {
                $this->saveRosterDocument($appId, 'PRO_CERT', $_FILES['pro_cert_doc'], 'pro_cert_' . $appId);
            }
            if (!empty($_FILES['tax_clearance_doc']['name'])) {
                $this->saveRosterDocument($appId, 'RES_PRF', $_FILES['tax_clearance_doc'], 'tax_' . $appId);
            }
        }

        // 3. National Identity
        if (!empty($_POST['national_id_number'])) {
            $onboardings = Rosteronboarding::findByQuery("SELECT * FROM rosteronboarding WHERE rosterapplication = ?", [$appId]);
            $onboarding = !empty($onboardings) ? $onboardings[0] : new Rosteronboarding();
            $onboarding->rosterapplication = $appId;
            $onboarding->national_id_number = trim($_POST['national_id_number']);
            $onboarding->reg_by = Auth::id() ?: 1;
            if ($onboarding->iD) {
                $onboarding->update();
            } else {
                $onboarding->save();
            }
        }
        if (!empty($_FILES['national_id_doc']['name'])) {
            $this->saveRosterDocument($appId, 'NAT_ID', $_FILES['national_id_doc'], 'nat_id_' . $appId);
        }

        $this->logStatusEvent($appId, 1, 'Stage 2: Credentials and identification uploaded');

        header("Location: " . $siteConfig->siteUrl . "/roster/apply/skills?id=" . $appId);
        exit;
    }

    /**
     * Render Stage 3: Skills & Competency Matrix.
     */
    public function showSkillsForm(int $appId)
    {
        global $siteConfig;
        $app = $this->ensureShortlistedDossierAccess($appId);
        if (!$app) {
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }

        $funcId = (int)$app->primaryfunction;
        $skillItems = Skillitem::findByQuery("SELECT * FROM skillitem WHERE servicefunction = ? ORDER BY sort_order ASC", [$funcId]);
        if (empty($skillItems)) {
            $skillItems = Skillitem::findByQuery("SELECT * FROM skillitem ORDER BY sort_order ASC LIMIT 12");
        }
        $proficiencyLevels = Proficiencylevel::findByQuery("SELECT * FROM proficiencylevel ORDER BY level_number ASC");

        $existingSkills = Rosterskill::findByQuery("SELECT * FROM rosterskill WHERE rosterapplication = ?", [$appId]);
        $existingSkillsMap = [];
        foreach ($existingSkills as $sk) {
            $existingSkillsMap[$sk->skillitem] = $sk->proficiencylevel;
        }

        $data = [
            'title' => 'Step 3: Skills & Competency Matrix',
            'application' => $app,
            'skillItems' => $skillItems,
            'proficiencyLevels' => $proficiencyLevels,
            'existingSkillsMap' => $existingSkillsMap,
        ];

        return view('roster.apply_skills', compact('data'));
    }

    /**
     * Handle Stage 3: Skills Submission.
     */
    public function handleSkillsSubmit()
    {
        global $siteConfig;
        $appId = (int)($_POST['application_id'] ?? 0);
        $app = $this->ensureShortlistedDossierAccess($appId);
        if (!$app) {
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }

        $userId = Auth::id() ?: 1;
        $funcId = (int)$app->primaryfunction;

        foreach ($_POST as $key => $val) {
            if (str_starts_with($key, 'skill_')) {
                $skillItemId = (int)substr($key, 6);
                $levelId = (int)$val;
                if ($skillItemId > 0) {
                    $existing = Rosterskill::findByQuery(
                        "SELECT * FROM rosterskill WHERE rosterapplication = ? AND skillitem = ?",
                        [$appId, $skillItemId]
                    );
                    if ($levelId > 0) {
                        $sk = !empty($existing) ? $existing[0] : new Rosterskill();
                        $sk->rosterapplication = $appId;
                        $sk->servicefunction = $funcId;
                        $sk->skillitem = $skillItemId;
                        $sk->proficiencylevel = $levelId;
                        $sk->reg_by = $userId;
                        if ($sk->iD) {
                            $sk->update();
                        } else {
                            $sk->save();
                        }
                    } elseif (!empty($existing)) {
                        $existing[0]->delete();
                    }
                }
            }
        }

        $this->logStatusEvent($appId, 1, 'Stage 3: Skills matrix competency ratings saved');

        header("Location: " . $siteConfig->siteUrl . "/roster/apply/experience?id=" . $appId);
        exit;
    }

    /**
     * Render Stage 4: Practical Experience & Referees.
     */
    public function showExperienceForm(int $appId)
    {
        global $siteConfig;
        $app = $this->ensureShortlistedDossierAccess($appId);
        if (!$app) {
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }

        $refereeTimings = Refereecontacttiming::all();

        $data = [
            'title' => 'Step 4: Experience & Referees',
            'application' => $app,
            'refereeTimings' => $refereeTimings,
        ];

        return view('roster.apply_experience', compact('data'));
    }

    /**
     * Handle Stage 4: Experience Submission.
     */
    public function handleExperienceSubmit()
    {
        global $siteConfig;
        $appId = (int)($_POST['application_id'] ?? 0);
        $app = $this->ensureShortlistedDossierAccess($appId);
        if (!$app) {
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }

        $userId = Auth::id() ?: 1;

        // 1. Work History
        if (!empty($_POST['organization_name']) && !empty($_POST['position_title'])) {
            $histories = Rosterworkhistory::findByQuery("SELECT * FROM rosterworkhistory WHERE rosterapplication = ?", [$appId]);
            $wh = !empty($histories) ? $histories[0] : new Rosterworkhistory();
            $wh->rosterapplication = $appId;
            $wh->organization_name = trim($_POST['organization_name']);
            $wh->position_title = trim($_POST['position_title']);
            $wh->start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d');
            $wh->end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            $wh->key_deliverables = trim($_POST['key_deliverables'] ?? '');
            $wh->sectortype = 1;
            $wh->engagementbasis = 1;
            $wh->reg_by = $userId;

            if ($wh->iD) {
                $wh->update();
            } else {
                $wh->save();
            }
        }

        // 2. Referee
        if (!empty($_POST['referee_name']) && !empty($_POST['referee_email'])) {
            $refs = Rosterreferee::findByQuery("SELECT * FROM rosterreferee WHERE rosterapplication = ?", [$appId]);
            $ref = !empty($refs) ? $refs[0] : new Rosterreferee();
            $ref->rosterapplication = $appId;
            $ref->referee_name = trim($_POST['referee_name']);
            $ref->organization = trim($_POST['referee_org'] ?? '');
            $ref->position = trim($_POST['referee_pos'] ?? '');
            $ref->relationship = trim($_POST['referee_relationship'] ?? '');
            $ref->email = trim($_POST['referee_email']);
            $ref->phone = trim($_POST['referee_phone'] ?? '');
            $ref->refereecontacttiming = !empty($_POST['refereecontacttiming']) ? (int)$_POST['refereecontacttiming'] : 1;
            $ref->refereeverificationstatus = 1;
            $ref->reg_by = $userId;

            if ($ref->iD) {
                $ref->update();
            } else {
                $ref->save();
            }
        }

        $this->logStatusEvent($appId, 1, 'Stage 4: Work deliverables and referee contact details saved');

        header("Location: " . $siteConfig->siteUrl . "/roster/apply/review?id=" . $appId);
        exit;
    }

    /**
     * Render Stage 5: Review & Digital Declaration.
     */
    public function showReviewForm(int $appId)
    {
        global $siteConfig;
        $app = $this->ensureShortlistedDossierAccess($appId);
        if (!$app) {
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }

        $data = [
            'title' => 'Step 5: Review & Digital Declaration',
            'application' => $app,
        ];

        return view('roster.apply_review', compact('data'));
    }

    /**
     * Handle Stage 5: Final Submission & Digital Signature.
     */
    public function handleFinalSubmit()
    {
        global $siteConfig;
        $appId = (int)($_POST['application_id'] ?? 0);
        $app = $this->ensureShortlistedDossierAccess($appId);
        if (!$app) {
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }

        $app->e_signature = trim($_POST['e_signature'] ?? $app->legal_name);
        $app->consent_timestamp = date('Y-m-d H:i:s');
        $app->consent_ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $app->applicationstatus = 4; // Interview / Verification (Dossier Completed)
        $app->update();

        $this->logStatusEvent($appId, 4, 'Stage 5: Verification dossier completed and signed with digital signature. Ready for final assessment & interview.');

        // Send confirmation email
        $candidate = (new User())->find($app->user);
        if ($candidate) {
            Mailer::sendDossierSubmitted($app, $candidate);
        }

        $_SESSION['flash_success'] = 'Your verification dossier has been submitted successfully! Our committee will review your credentials and schedule an interview.';

        if (php_sapi_name() === 'cli') {
            return ['status' => 1, 'msg' => 'Dossier completed and moved to status 4', 'app_status' => 4];
        }

        header("Location: " . $siteConfig->siteUrl . "/roster/application/status?id=" . $appId);
        exit;
    }

    /**
     * Render Stage 6: Real-time Application Status & Milestones.
     */
    public function showStatusView(int $appId)
    {
        global $siteConfig;
        $app = $this->getAuthorizedApplication($appId);
        if (!$app) {
            header("Location: " . $siteConfig->siteUrl . "/opportunities");
            exit;
        }

        $data = [
            'title' => 'Application Status: #' . $appId,
            'application' => $app,
        ];

        return view('roster.apply_status', compact('data'));
    }

    /**
     * Render the multi-step application intake form for a specific track.
     */
    public function showApplyForm(string $track, ?int $appId = null)
    {
        $userId = Auth::id();
        $trackCode = strtolower(trim($track));

        $tracks = Applicationtrack::findByQuery("SELECT * FROM applicationtrack WHERE code = ?", [$trackCode]);
        if (empty($tracks)) {
            header("Location: " . _BASEURL . "/dashboard");
            exit;
        }
        $trackObj = $tracks[0];

        // Load existing draft if editing/resuming
        $application = null;
        if ($appId) {
            $existing = Rosterapplication::findByQuery(
                "SELECT * FROM rosterapplication WHERE iD = ? AND user = ?",
                [$appId, $userId]
            );
            if (!empty($existing)) {
                $application = $existing[0];
            }
        }

        // Reference Lookups
        $serviceFunctions = Servicefunction::findByQuery("SELECT * FROM servicefunction ORDER BY sort_order ASC");
        $proficiencyLevels = Proficiencylevel::findByQuery("SELECT * FROM proficiencylevel ORDER BY level_number ASC");
        $provinces = Zimprovince::findByQuery("SELECT * FROM zimprovince ORDER BY sort_order ASC");
        $workRights = Workrightstatus::all();
        $genders = Gender::all();
        $qualificationTypes = Qualificationtype::findByQuery("SELECT * FROM qualificationtype ORDER BY sort_order ASC");
        $qualificationStatuses = Qualificationstatus::all();
        $professionalBodies = Professionalbody::all();
        $sectorTypes = Sectortype::all();
        $engagementBases = Engagementbasis::all();
        $refereeTimings = Refereecontacttiming::all();

        // Track-specific Lookups
        $apprenticeStatuses = Apprenticestatus::all();
        $engagementModels = Engagementmodel::all();
        $locationPreferences = Worklocationpreference::all();
        $employmentStatuses = Employmentstatus::all();
        $invoiceEntityTypes = Invoiceentitytype::all();

        $data = [
            'title' => 'Apply: ' . $trackObj->name . ' Roster',
            'track' => $trackObj,
            'application' => $application,
            'user' => (new AccountController())->getUser($userId),
            'serviceFunctions' => $serviceFunctions,
            'proficiencyLevels' => $proficiencyLevels,
            'provinces' => $provinces,
            'workRights' => $workRights,
            'genders' => $genders,
            'qualificationTypes' => $qualificationTypes,
            'qualificationStatuses' => $qualificationStatuses,
            'professionalBodies' => $professionalBodies,
            'sectorTypes' => $sectorTypes,
            'engagementBases' => $engagementBases,
            'refereeTimings' => $refereeTimings,
            'apprenticeStatuses' => $apprenticeStatuses,
            'engagementModels' => $engagementModels,
            'locationPreferences' => $locationPreferences,
            'employmentStatuses' => $employmentStatuses,
            'invoiceEntityTypes' => $invoiceEntityTypes,
        ];

        return view('roster.apply_wizard', compact('data'));
    }

    /**
     * Submit or Save Draft of Application.
     */
    public function handleSubmission(): array
    {
        $userId = Auth::id();
        if (!$userId) {
            return ['status' => 0, 'msg' => 'Authentication required. Please sign in.'];
        }

        try {
            $appId = (int) ($_POST['application_id'] ?? 0);
            $trackId = (int) ($_POST['applicationtrack'] ?? 1);
            $isFinalSubmit = (isset($_POST['is_submit']) && $_POST['is_submit'] == '1');

            $trackObj = (new Applicationtrack())->find($trackId);
            $trackCode = $trackObj ? $trackObj->code : 'apprentice';

            // 1. Create or Update Core Application
            $app = $appId > 0 ? (new Rosterapplication())->find($appId) : new Rosterapplication();
            if ($app && $app->iD && (int)$app->user !== (int)$userId) {
                return ['status' => 0, 'msg' => 'Unauthorized access to application.'];
            }

        $app->user = $userId;
        $app->applicationtrack = $trackId;
        $app->applicationstatus = $isFinalSubmit ? 2 : 1; // 2: Submitted, 1: Draft
        $app->primaryfunction = (int) ($_POST['primaryfunction'] ?? 1);
        $app->secondary_functions = json_encode($_POST['secondary_functions'] ?? []);
        $app->legal_name = trim($_POST['legal_name'] ?? '');
        $app->preferred_name = trim($_POST['preferred_name'] ?? '');
        $app->email = trim($_POST['email'] ?? '');
        $app->mobile_number = trim($_POST['mobile_number'] ?? '');
        $app->whatsapp_number = trim($_POST['whatsapp_number'] ?? '');
        $app->date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
        $app->gender = !empty($_POST['gender']) ? (int)$_POST['gender'] : null;
        $app->city = trim($_POST['city'] ?? '');
        $app->suburb = trim($_POST['suburb'] ?? '');
        $app->zimprovince = !empty($_POST['zimprovince']) ? (int)$_POST['zimprovince'] : null;
        $app->country = trim($_POST['country'] ?? 'Zimbabwe');
        $app->nationality = trim($_POST['nationality'] ?? 'Zimbabwean');
        $app->workrightstatus = !empty($_POST['workrightstatus']) ? (int)$_POST['workrightstatus'] : 1;
        $app->work_permit_number = trim($_POST['work_permit_number'] ?? '');
        $app->work_permit_expiry = !empty($_POST['work_permit_expiry']) ? $_POST['work_permit_expiry'] : null;
        $app->has_disability_adjustment = isset($_POST['has_disability_adjustment']) ? 1 : 0;
        $app->adjustment_details = trim($_POST['adjustment_details'] ?? '');
        $app->how_heard = trim($_POST['how_heard'] ?? '');
        $app->referred_by = trim($_POST['referred_by'] ?? '');
        $app->consent_version = '2026.1';
        $app->consent_timestamp = date('Y-m-d H:i:s');
        $app->consent_ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $app->e_signature = trim($_POST['e_signature'] ?? '');

        if ($app->iD) {
            $app->update();
        } else {
            $app->save();
        }

        $appId = (int) $app->iD;

        // 2. Track Specific Branch
        if ($trackCode === 'apprentice') {
            $appProfileList = Apprenticeprofile::findByQuery("SELECT * FROM apprenticeprofile WHERE rosterapplication = ?", [$appId]);
            $appProfile = !empty($appProfileList) ? $appProfileList[0] : new Apprenticeprofile();

            $appProfile->rosterapplication = $appId;
            $appProfile->apprenticestatus = !empty($_POST['apprenticestatus']) ? (int)$_POST['apprenticestatus'] : null;
            $appProfile->institution_name = trim($_POST['institution_name'] ?? '');
            $appProfile->degree_programme = trim($_POST['degree_programme'] ?? '');
            $appProfile->study_level = trim($_POST['study_level'] ?? '');
            $appProfile->student_reg_number = trim($_POST['student_reg_number'] ?? '');
            $appProfile->expected_completion_date = !empty($_POST['expected_completion_date']) ? $_POST['expected_completion_date'] : null;
            $appProfile->is_wrl_attachment = isset($_POST['is_wrl_attachment']) ? 1 : 0;
            $appProfile->wrl_start_date = !empty($_POST['wrl_start_date']) ? $_POST['wrl_start_date'] : null;
            $appProfile->wrl_end_date = !empty($_POST['wrl_end_date']) ? $_POST['wrl_end_date'] : null;
            $appProfile->wrl_duration_months = !empty($_POST['wrl_duration_months']) ? (int)$_POST['wrl_duration_months'] : null;
            $appProfile->wrl_coordinator_name = trim($_POST['wrl_coordinator_name'] ?? '');
            $appProfile->wrl_coordinator_email = trim($_POST['wrl_coordinator_email'] ?? '');
            $appProfile->wrl_coordinator_phone = trim($_POST['wrl_coordinator_phone'] ?? '');
            $appProfile->requires_placement_letter = isset($_POST['requires_placement_letter']) ? 1 : 0;
            $appProfile->requires_host_mou = isset($_POST['requires_host_mou']) ? 1 : 0;
            $appProfile->requires_logbook_visits = isset($_POST['requires_logbook_visits']) ? 1 : 0;
            $appProfile->requires_host_insurance = isset($_POST['requires_host_insurance']) ? 1 : 0;
            $appProfile->min_stipend_required = !empty($_POST['min_stipend_required']) ? (float)$_POST['min_stipend_required'] : null;
            $appProfile->engagementmodel = !empty($_POST['engagementmodel']) ? (int)$_POST['engagementmodel'] : null;
            $appProfile->worklocationpreference = !empty($_POST['worklocationpreference']) ? (int)$_POST['worklocationpreference'] : null;
            $appProfile->current_average_grade = trim($_POST['current_average_grade'] ?? '');

            // File uploads
            if (!empty($_FILES['proof_of_registration_doc']['name'])) {
                $docKey = $this->uploadFile($_FILES['proof_of_registration_doc'], 'wrl_reg_' . $appId);
                if ($docKey) $appProfile->proof_of_registration_doc = $docKey;
            }
            if (!empty($_FILES['transcript_doc']['name'])) {
                $docKey = $this->uploadFile($_FILES['transcript_doc'], 'transcript_' . $appId);
                if ($docKey) $appProfile->transcript_doc = $docKey;
            }

            if ($appProfile->iD) {
                $appProfile->update();
            } else {
                $appProfile->save();
            }
        } elseif ($trackCode === 'associate') {
            $assocProfileList = Associateprofile::findByQuery("SELECT * FROM associateprofile WHERE rosterapplication = ?", [$appId]);
            $assocProfile = !empty($assocProfileList) ? $assocProfileList[0] : new Associateprofile();

            $assocProfile->rosterapplication = $appId;
            $assocProfile->employmentstatus = !empty($_POST['employmentstatus']) ? (int)$_POST['employmentstatus'] : null;
            $assocProfile->years_experience = trim($_POST['years_experience'] ?? '');
            $assocProfile->donor_experience_years = trim($_POST['donor_experience_years'] ?? '');
            $assocProfile->donors_worked_with = json_encode($_POST['donors_worked_with'] ?? []);
            $assocProfile->largest_budget_handled = trim($_POST['largest_budget_handled'] ?? '');
            $assocProfile->largest_team_supervised = !empty($_POST['largest_team_supervised']) ? (int)$_POST['largest_team_supervised'] : null;
            $assocProfile->largest_endpoints_supported = !empty($_POST['largest_endpoints_supported']) ? (int)$_POST['largest_endpoints_supported'] : null;
            $assocProfile->largest_dataset_managed = trim($_POST['largest_dataset_managed'] ?? '');
            $assocProfile->supervised_juniors_before = trim($_POST['supervised_juniors_before'] ?? '');
            $assocProfile->led_audits_or_evaluations = trim($_POST['led_audits_or_evaluations'] ?? '');
            $assocProfile->rejected_work_experience = trim($_POST['rejected_work_experience'] ?? '');
            $assocProfile->day_rate_expectation = !empty($_POST['day_rate_expectation']) ? (float)$_POST['day_rate_expectation'] : null;
            $assocProfile->capacity_days_per_month = trim($_POST['capacity_days_per_month'] ?? '');
            $assocProfile->notice_period = trim($_POST['notice_period'] ?? '');
            $assocProfile->invoiceentitytype = !empty($_POST['invoiceentitytype']) ? (int)$_POST['invoiceentitytype'] : null;
            $assocProfile->has_tax_clearance_itf263 = isset($_POST['has_tax_clearance_itf263']) ? 1 : 0;
            $assocProfile->zimra_bp_number = trim($_POST['zimra_bp_number'] ?? '');
            $assocProfile->is_vat_registered = isset($_POST['is_vat_registered']) ? 1 : 0;
            $assocProfile->vat_number = trim($_POST['vat_number'] ?? '');
            $assocProfile->has_indemnity_insurance = isset($_POST['has_indemnity_insurance']) ? 1 : 0;
            $assocProfile->insurance_cover_amount = !empty($_POST['insurance_cover_amount']) ? (float)$_POST['insurance_cover_amount'] : null;
            $assocProfile->conflict_of_interest = trim($_POST['conflict_of_interest'] ?? '');
            $assocProfile->moonlighting_restrictions = trim($_POST['moonlighting_restrictions'] ?? '');
            $assocProfile->cv_bid_consent = trim($_POST['cv_bid_consent'] ?? 'yes');
            $assocProfile->restricted_sectors_or_donors = trim($_POST['restricted_sectors_or_donors'] ?? '');
            $assocProfile->public_website_listing_consent = isset($_POST['public_website_listing_consent']) ? 1 : 0;

            if (!empty($_FILES['tax_clearance_doc']['name'])) {
                $docKey = $this->uploadFile($_FILES['tax_clearance_doc'], 'itf263_' . $appId);
                if ($docKey) $assocProfile->tax_clearance_doc = $docKey;
            }

            if ($assocProfile->iD) {
                $assocProfile->update();
            } else {
                $assocProfile->save();
            }
        }

        // 3. Save Skills Matrix (1-5 Anchored Ratings)
        if (!empty($_POST['skills']) && is_array($_POST['skills'])) {
            $pdo = \App\Models\Database::sharedPdo();
            $pdo->prepare("DELETE FROM rosterskill WHERE rosterapplication = ?")->execute([$appId]);

            $stmtSkill = $pdo->prepare(
                "INSERT INTO rosterskill (rosterapplication, servicefunction, skillitem, proficiencylevel, reg_by, reg_date, status)
                 VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1)"
            );

            foreach ($_POST['skills'] as $skillItemId => $proficiencyId) {
                if ((int)$proficiencyId > 0) {
                    $skillItem = (new Skillitem())->find((int)$skillItemId);
                    if ($skillItem) {
                        $stmtSkill->execute([
                            $appId,
                            $skillItem->servicefunction,
                            $skillItem->iD,
                            (int)$proficiencyId,
                            $userId
                        ]);
                    }
                }
            }
        }

        // 4. Save Qualifications (Repeatable)
        if (!empty($_POST['qual_title']) && is_array($_POST['qual_title'])) {
            $pdo = \App\Models\Database::sharedPdo();
            $pdo->prepare("DELETE FROM rosterqualification WHERE rosterapplication = ?")->execute([$appId]);

            $stmtQual = $pdo->prepare(
                "INSERT INTO rosterqualification (rosterapplication, qualificationtype, professionalbody, qualificationstatus, title, institution_name, field_of_study, date_obtained, reg_by, reg_date, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1)"
            );

            foreach ($_POST['qual_title'] as $idx => $title) {
                if (trim($title) !== '') {
                    $stmtQual->execute([
                        $appId,
                        !empty($_POST['qual_type'][$idx]) ? (int)$_POST['qual_type'][$idx] : null,
                        !empty($_POST['qual_body'][$idx]) ? (int)$_POST['qual_body'][$idx] : null,
                        !empty($_POST['qual_status'][$idx]) ? (int)$_POST['qual_status'][$idx] : null,
                        trim($title),
                        trim($_POST['qual_inst'][$idx] ?? ''),
                        trim($_POST['qual_field'][$idx] ?? ''),
                        !empty($_POST['qual_date'][$idx]) ? $_POST['qual_date'][$idx] : null,
                        $userId
                    ]);
                }
            }
        }

        // 5. Save Work Experience (Repeatable)
        if (!empty($_POST['work_org']) && is_array($_POST['work_org'])) {
            $pdo = \App\Models\Database::sharedPdo();
            $pdo->prepare("DELETE FROM rosterworkhistory WHERE rosterapplication = ?")->execute([$appId]);

            $stmtWork = $pdo->prepare(
                "INSERT INTO rosterworkhistory (rosterapplication, sectortype, engagementbasis, organization_name, position_title, start_date, end_date, is_current, key_deliverables, reason_for_leaving, reg_by, reg_date, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1)"
            );

            foreach ($_POST['work_org'] as $idx => $org) {
                if (trim($org) !== '') {
                    $stmtWork->execute([
                        $appId,
                        !empty($_POST['work_sector'][$idx]) ? (int)$_POST['work_sector'][$idx] : null,
                        !empty($_POST['work_basis'][$idx]) ? (int)$_POST['work_basis'][$idx] : null,
                        trim($org),
                        trim($_POST['work_title'][$idx] ?? ''),
                        !empty($_POST['work_start'][$idx]) ? $_POST['work_start'][$idx] : null,
                        !empty($_POST['work_end'][$idx]) ? $_POST['work_end'][$idx] : null,
                        isset($_POST['work_current'][$idx]) ? 1 : 0,
                        trim($_POST['work_deliverables'][$idx] ?? ''),
                        trim($_POST['work_reason'][$idx] ?? ''),
                        $userId
                    ]);
                }
            }
        }

        // 6. Save Referees
        if (!empty($_POST['referee_name']) && is_array($_POST['referee_name'])) {
            $pdo = \App\Models\Database::sharedPdo();
            $pdo->prepare("DELETE FROM rosterreferee WHERE rosterapplication = ?")->execute([$appId]);

            $stmtRef = $pdo->prepare(
                "INSERT INTO rosterreferee (rosterapplication, refereecontacttiming, refereeverificationstatus, referee_name, organization, position, relationship, email, phone, reg_by, reg_date, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1)"
            );

            foreach ($_POST['referee_name'] as $idx => $refName) {
                if (trim($refName) !== '') {
                    $stmtRef->execute([
                        $appId,
                        !empty($_POST['ref_timing'][$idx]) ? (int)$_POST['ref_timing'][$idx] : 2,
                        1, // 1: Pending
                        trim($refName),
                        trim($_POST['ref_org'][$idx] ?? ''),
                        trim($_POST['ref_pos'][$idx] ?? ''),
                        trim($_POST['ref_rel'][$idx] ?? ''),
                        trim($_POST['ref_email'][$idx] ?? ''),
                        trim($_POST['ref_phone'][$idx] ?? ''),
                        $userId
                    ]);
                }
            }
        }

        // 7. Save Scored Judgement Responses
        $judgementList = Rosterjudgementresponse::findByQuery("SELECT * FROM rosterjudgementresponse WHERE rosterapplication = ?", [$appId]);
        $judgement = !empty($judgementList) ? $judgementList[0] : new Rosterjudgementresponse();

        $judgement->rosterapplication = $appId;
        $judgement->motivation_narrative = trim($_POST['motivation_narrative'] ?? '');
        $judgement->primary_function_evidence = trim($_POST['primary_function_evidence'] ?? '');
        $judgement->shared_client_management_plan = trim($_POST['shared_client_management_plan'] ?? '');
        $judgement->error_discovery_resolution = trim($_POST['error_discovery_resolution'] ?? '');
        $judgement->urgent_friday_deadline_dilemma = trim($_POST['urgent_friday_deadline_dilemma'] ?? '');
        $judgement->associate_apprentice_qa_methodology = trim($_POST['associate_apprentice_qa_methodology'] ?? '');
        $judgement->associate_unethical_client_solution = trim($_POST['associate_unethical_client_solution'] ?? '');
        $judgement->apprentice_twelve_month_goal = trim($_POST['apprentice_twelve_month_goal'] ?? '');
        $judgement->additional_notes = trim($_POST['additional_notes'] ?? '');

        if ($judgement->iD) {
            $judgement->update();
        } else {
            $judgement->save();
        }

        // 8. If final submit, compute automated red flags and send email notifications
        if ($isFinalSubmit) {
            $this->evaluateAutomatedRedFlags($appId);
            $candidate = (new User())->find($userId);
            if ($candidate && $app) {
                Mailer::sendApplicationSubmitted($app, $candidate);
            }
        }

            return [
                'status' => 1,
                'application_id' => $appId,
                'is_submit' => $isFinalSubmit,
                'msg' => $isFinalSubmit
                    ? 'Your application has been successfully submitted for review!'
                    : 'Application draft saved successfully.'
            ];
        } catch (\Throwable $e) {
            error_log("handleSubmission error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            return [
                'status' => 0,
                'msg' => 'An error occurred while saving: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Compute automated red flags based on vetting rules.
     */
    private function evaluateAutomatedRedFlags(int $appId): array
    {
        $app = (new Rosterapplication())->find($appId);
        if (!$app) return [];

        $flags = [];
        $appProfile = $app->apprenticeProfile();
        $assocProfile = $app->associateProfile();
        $judgement = $app->judgementResponse();
        $skills = $app->skills();
        $referees = $app->referees();

        // 1. Inflated self-rating with thin narrative
        $maxSkillRating = 0;
        foreach ($skills as $s) {
            $pl = $s->proficiencylevel();
            $lvl = $pl ? (int)$pl->level_number : 0;
            if ($lvl > $maxSkillRating) $maxSkillRating = $lvl;
        }

        $evidenceWordCount = str_word_count($judgement->primary_function_evidence ?? '');
        if ($maxSkillRating >= 4 && $evidenceWordCount < 50) {
            $flags[] = "High self-rating (Level {$maxSkillRating}) with very brief deliverable narrative ({$evidenceWordCount} words). Requires technical probe during verification.";
        }

        // 2. Timeline mismatch for attachment
        if ($appProfile && $appProfile->is_wrl_attachment && $appProfile->expected_completion_date) {
            $compTimestamp = strtotime($appProfile->expected_completion_date);
            $monthsRemaining = round(($compTimestamp - time()) / (30 * 86400));
            $reqMonths = (int)($appProfile->wrl_duration_months ?: 12);
            if ($monthsRemaining < $reqMonths) {
                $flags[] = "Timeline conflict: Expected graduation is in {$monthsRemaining} months, but applying for {$reqMonths}-month attachment.";
            }
        }

        // 3. Referee email surname matching on public free email domain
        $applicantSurname = strtolower(trim(explode(' ', $app->legal_name)[count(explode(' ', $app->legal_name)) - 1] ?? ''));
        $freeDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'live.com', 'icloud.com'];
        foreach ($referees as $r) {
            $email = strtolower(trim($r->email));
            $domain = substr(strrchr($email, "@"), 1);
            if (in_array($domain, $freeDomains) && strlen($applicantSurname) > 3 && strpos($email, $applicantSurname) !== false) {
                $flags[] = "Referee '{$r->referee_name}' uses a free email ({$r->email}) matching applicant surname. Verify non-relative referee status.";
            }
        }

        // 4. Rate card unviability without requisite credentials
        if ($assocProfile && (float)$assocProfile->day_rate_expectation > 350) {
            $quals = $app->qualifications();
            if (empty($quals)) {
                $flags[] = "High day rate expectation ($" . number_format($assocProfile->day_rate_expectation, 2) . ") with no formal professional qualification entries recorded.";
            }
        }

        // 5. Declared Conflict of Interest
        if ($assocProfile && !empty($assocProfile->conflict_of_interest)) {
            $flags[] = "Declared commercial or organizational conflict of interest: " . substr($assocProfile->conflict_of_interest, 0, 100) . "...";
        }

        // Save or update assessment draft
        $assessments = Rosterassessment::findByQuery("SELECT * FROM rosterassessment WHERE rosterapplication = ?", [$appId]);
        $assessment = !empty($assessments) ? $assessments[0] : new Rosterassessment();
        $assessment->rosterapplication = $appId;
        $assessment->reviewer = null; // System / unassigned reviewer
        $assessment->automated_red_flags = json_encode($flags);
        if ($assessment->iD) {
            $assessment->update();
        } else {
            $assessment->save();
        }

        return $flags;
    }

    /**
     * Upload and secure applicant file.
     */
    private function uploadFile(array $file, string $prefix): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
            return null;
        }

        // Max 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['pdf', 'jpg', 'jpeg', 'png', 'docx', 'doc'];
        if (!in_array($ext, $allowedExts, true)) {
            return null;
        }

        $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $prefix);
        $filename = $cleanName . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $destDir = _BASE_PATH . '/storage/roster_uploads';

        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $destPath = $destDir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            return $filename;
        }

        // Fallback for CLI testing where is_uploaded_file() returns false
        if (php_sapi_name() === 'cli' && copy($file['tmp_name'], $destPath)) {
            return $filename;
        }

        return null;
    }

    /**
     * Show applicant status & timeline detail.
     */
    public function showApplicationDetail(int $appId)
    {
        $userId = Auth::id();
        $app = (new Rosterapplication())->find($appId);

        if (!$app || ((int)$app->user !== (int)$userId && !Auth::isAdmin())) {
            header("Location: " . _BASEURL . "/dashboard");
            exit;
        }

        $data = [
            'title' => 'Application #' . $app->iD . ' - ' . ($app->primaryfunction()->name ?? 'Details'),
            'app' => $app,
            'user' => (new AccountController())->getUser($userId),
        ];

        return view('roster.application_detail', compact('data'));
    }

    /**
     * Stage 3 Statutory Onboarding Form.
     */
    public function showOnboardingForm(int $appId)
    {
        $userId = Auth::id();
        $app = (new Rosterapplication())->find($appId);

        if (!$app || (int)$app->user !== (int)$userId) {
            header("Location: " . _BASEURL . "/dashboard");
            exit;
        }

        // Allow onboarding if on roster or screened
        $statusId = (int)$app->applicationstatus;
        $onboarding = $app->onboarding();

        $data = [
            'title' => 'Stage 3: Onboarding & Statutory Details',
            'app' => $app,
            'onboarding' => $onboarding,
            'user' => (new AccountController())->getUser($userId),
        ];

        return view('roster.onboarding', compact('data'));
    }

    /**
     * Handle Stage 3 Statutory Onboarding Submission.
     */
    public function handleOnboardingSubmit(): array
    {
        $userId = Auth::id();
        $appId = (int)($_POST['rosterapplication'] ?? 0);
        $app = (new Rosterapplication())->find($appId);

        if (!$app || (int)$app->user !== (int)$userId) {
            return ['status' => 0, 'msg' => 'Unauthorized or invalid application.'];
        }

        $onboarding = $app->onboarding() ?: new Rosteronboarding();
        $onboarding->rosterapplication = $appId;
        $onboarding->national_id_number = trim($_POST['national_id_number'] ?? '');
        $onboarding->passport_number = trim($_POST['passport_number'] ?? '');
        $onboarding->passport_expiry = !empty($_POST['passport_expiry']) ? $_POST['passport_expiry'] : null;
        $onboarding->street_address = trim($_POST['street_address'] ?? '');
        $onboarding->city = trim($_POST['city'] ?? '');
        $onboarding->country = trim($_POST['country'] ?? 'Zimbabwe');
        $onboarding->bank_name = trim($_POST['bank_name'] ?? '');
        $onboarding->bank_branch = trim($_POST['bank_branch'] ?? '');
        $onboarding->account_name = trim($_POST['account_name'] ?? '');
        $onboarding->account_number = trim($_POST['account_number'] ?? '');
        $onboarding->bank_currency = trim($_POST['bank_currency'] ?? 'USD');
        $onboarding->emergency_contact_name = trim($_POST['emergency_contact_name'] ?? '');
        $onboarding->emergency_contact_phone = trim($_POST['emergency_contact_phone'] ?? '');
        $onboarding->emergency_contact_relationship = trim($_POST['emergency_contact_relationship'] ?? '');
        $onboarding->nssa_number = trim($_POST['nssa_number'] ?? '');
        $onboarding->police_clearance_date = !empty($_POST['police_clearance_date']) ? $_POST['police_clearance_date'] : null;

        // Files
        if (!empty($_FILES['national_id_doc']['name'])) {
            $f = $this->uploadFile($_FILES['national_id_doc'], 'nat_id_' . $appId);
            if ($f) $onboarding->national_id_doc = $f;
        }
        if (!empty($_FILES['police_clearance_doc']['name'])) {
            $f = $this->uploadFile($_FILES['police_clearance_doc'], 'police_clr_' . $appId);
            if ($f) $onboarding->police_clearance_doc = $f;
        }
        if (!empty($_FILES['signed_nda_doc']['name'])) {
            $f = $this->uploadFile($_FILES['signed_nda_doc'], 'nda_' . $appId);
            if ($f) $onboarding->signed_nda_doc = $f;
        }
        if (!empty($_FILES['signed_contract_doc']['name'])) {
            $f = $this->uploadFile($_FILES['signed_contract_doc'], 'contract_' . $appId);
            if ($f) $onboarding->signed_contract_doc = $f;
        }

        if ($onboarding->iD) {
            $onboarding->update();
        } else {
            $onboarding->save();
        }

        // Send confirmation to candidate & alert to admin/billing
        $candidate = (new User())->find($userId);
        if ($candidate && $app) {
            Mailer::sendOnboardingSubmitted($app, $candidate, $_POST);
        }

        return [
            'status' => 1,
            'msg' => 'Stage 3 statutory details and onboarding documents submitted successfully!'
        ];
    }

    /**
     * Admin / Vetting Officer Pipeline Overview with Server-Side Pagination.
     */
    public function adminPipeline()
    {
        $statusFilter = isset($_GET['status']) ? (int)$_GET['status'] : 0;
        $trackFilter = isset($_GET['track']) ? (int)$_GET['track'] : 0;
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = max(5, min(100, (int)($_GET['per_page'] ?? 10)));

        $where = "1=1";
        $params = [];
        if ($statusFilter > 0) {
            $where .= " AND applicationstatus = ?";
            $params[] = $statusFilter;
        }
        if ($trackFilter > 0) {
            $where .= " AND applicationtrack = ?";
            $params[] = $trackFilter;
        }
        if ($search !== '') {
            $where .= " AND (legal_name LIKE ? OR email LIKE ? OR city LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $pdo = \App\Models\Database::sharedPdo();
        $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM rosterapplication WHERE {$where}");
        $countStmt->execute($params);
        $totalCount = (int)$countStmt->fetchColumn();

        $totalPages = $totalCount > 0 ? (int)ceil($totalCount / $perPage) : 1;
        if ($page > $totalPages) $page = $totalPages;
        $offset = ($page - 1) * $perPage;

        $queryParams = $params;
        $queryParams[] = $perPage;
        $queryParams[] = $offset;

        $applications = Rosterapplication::findByQuery(
            "SELECT * FROM rosterapplication WHERE {$where} ORDER BY iD DESC LIMIT ? OFFSET ?",
            $queryParams
        );

        $statuses = Applicationstatus::all();
        $tracks = Applicationtrack::all();

        $startRecord = $totalCount > 0 ? $offset + 1 : 0;
        $endRecord = min($offset + count($applications), $totalCount);

        $data = [
            'title' => 'Talent Roster & Vetting Pipeline',
            'applications' => $applications,
            'statuses' => $statuses,
            'tracks' => $tracks,
            'statusFilter' => $statusFilter,
            'trackFilter' => $trackFilter,
            'search' => $search,
            'page' => $page,
            'perPage' => $perPage,
            'totalCount' => $totalCount,
            'totalPages' => $totalPages,
            'startRecord' => $startRecord,
            'endRecord' => $endRecord,
        ];

        return view('roster.admin_pipeline', compact('data'));
    }

    /**
     * AJAX endpoint for Talent Pipeline Records with Standard Pagination.
     */
    public function getAdminRosterRecords(): array
    {
        $search = trim($_POST['search'] ?? '');
        $page = max(1, (int)($_POST['page'] ?? 1));
        $perPage = max(5, min(250, (int)($_POST['page_size'] ?? 10)));
        $orderBy = trim($_POST['order_by'] ?? 'reg_date DESC');
        $trackFilter = (int)($_POST['track'] ?? 0);
        $statusFilter = (int)($_POST['status'] ?? 0);

        $allowedSorts = [
            'reg_date DESC', 'reg_date ASC', 'legal_name ASC', 'legal_name DESC', 'iD DESC', 'iD ASC'
        ];
        if (!in_array($orderBy, $allowedSorts)) {
            $orderBy = 'iD DESC';
        }

        $where = "1=1";
        $params = [];
        if ($trackFilter > 0) {
            $where .= " AND applicationtrack = ?";
            $params[] = $trackFilter;
        }
        if ($statusFilter > 0) {
            $where .= " AND applicationstatus = ?";
            $params[] = $statusFilter;
        }
        if ($search !== '') {
            $where .= " AND (legal_name LIKE ? OR email LIKE ? OR city LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $pdo = \App\Models\Database::sharedPdo();
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM rosterapplication WHERE {$where}");
        $countStmt->execute($params);
        $totalCount = (int)$countStmt->fetchColumn();

        $totalPages = $totalCount > 0 ? (int)ceil($totalCount / $perPage) : 1;
        if ($page > $totalPages) $page = $totalPages;
        $offset = ($page - 1) * $perPage;

        $queryParams = $params;
        $queryParams[] = $perPage;
        $queryParams[] = $offset;

        $applications = Rosterapplication::findByQuery(
            "SELECT * FROM rosterapplication WHERE {$where} ORDER BY {$orderBy} LIMIT ? OFFSET ?",
            $queryParams
        );

        $records = [];
        foreach ($applications as $app) {
            $tr = $app->applicationtrack();
            $st = $app->applicationstatus();
            $fn = $app->primaryfunction();
            $zp = $app->zimprovince();
            $assessment = $app->assessment();
            $docs = $app->documents();
            $hasCv = false;
            $cvPath = '';
            foreach ($docs as $doc) {
                $dt = $doc->documenttype();
                if ($dt && $dt->code === 'CV_RESUME') {
                    $hasCv = true;
                    $cvPath = $doc->file_path;
                    break;
                }
            }

            $records[] = [
                'iD' => $app->iD,
                'legal_name' => $app->legal_name,
                'email' => $app->email,
                'mobile_number' => $app->mobile_number,
                'track_name' => $tr ? $tr->name : 'N/A',
                'track_code' => $tr ? $tr->code : 'general',
                'primary_function' => $fn ? $fn->name : 'General',
                'city' => $app->city ?: 'Harare',
                'province' => $zp ? $zp->name : '',
                'status_id' => (int)$app->applicationstatus,
                'status_name' => $st ? $st->name : 'Draft',
                'status_code' => $st ? $st->code : 'draft',
                'total_score' => $assessment ? (float)$assessment->total_score : null,
                'gate_passed' => $assessment ? (bool)$assessment->eligibility_gate_passed : false,
                'has_cv' => $hasCv,
                'cv_path' => $cvPath,
                'doc_count' => count($docs),
                'reg_date' => date('d M Y', strtotime($app->reg_date)),
            ];
        }

        return [
            'status' => 1,
            'records' => $records,
            'pagination' => [
                'total_records' => $totalCount,
                'total_pages' => $totalPages,
                'current_page' => $page,
                'page_size' => $perPage,
            ]
        ];
    }

    /**
     * Admin / Vetting Officer Review & 100-Point Scoring Console.
     */
    public function adminReviewConsole(int $appId)
    {
        $app = (new Rosterapplication())->find($appId);
        if (!$app) {
            header("Location: " . _BASEURL . "/admin/roster");
            exit;
        }

        $assessment = $app->assessment() ?: new Rosterassessment();
        $recommendations = Vettingrecommendation::all();
        $statuses = Applicationstatus::all();

        $data = [
            'title' => 'Vetting Review: Application #' . $app->iD . ' (' . $app->legal_name . ')',
            'app' => $app,
            'assessment' => $assessment,
            'recommendations' => $recommendations,
            'statuses' => $statuses,
            'redFlags' => !empty($assessment->automated_red_flags) ? json_decode($assessment->automated_red_flags, true) : [],
        ];

        return view('roster.admin_review', compact('data'));
    }

    /**
     * Save Vetting Assessment & 100-point Score.
     */
    public function handleAssessmentSubmit(): array
    {
        $appId = (int)($_POST['rosterapplication'] ?? 0);
        $app = (new Rosterapplication())->find($appId);
        if (!$app) {
            return ['status' => 0, 'msg' => 'Application not found.'];
        }

        $assessment = $app->assessment() ?: new Rosterassessment();
        $assessment->rosterapplication = $appId;
        $assessment->reviewer = (int)(Auth::id() ?: 1);
        $assessment->vettingrecommendation = !empty($_POST['vettingrecommendation']) ? (int)$_POST['vettingrecommendation'] : null;
        $assessment->eligibility_gate_passed = isset($_POST['eligibility_gate_passed']) ? 1 : 0;
        $assessment->technical_fit_score = (float)($_POST['technical_fit_score'] ?? 0);
        $assessment->evidence_score = (float)($_POST['evidence_score'] ?? 0);
        $assessment->judgement_score = (float)($_POST['judgement_score'] ?? 0);
        $assessment->availability_score = (float)($_POST['availability_score'] ?? 0);
        $assessment->motivation_score = (float)($_POST['motivation_score'] ?? 0);

        $assessment->total_score = $assessment->technical_fit_score +
                                   $assessment->evidence_score +
                                   $assessment->judgement_score +
                                   $assessment->availability_score +
                                   $assessment->motivation_score;

        $assessment->interview_notes = trim($_POST['interview_notes'] ?? '');
        $assessment->technical_test_result = trim($_POST['technical_test_result'] ?? '');
        $assessment->vetted_at = date('Y-m-d H:i:s');

        if ($assessment->iD) {
            $assessment->update();
        } else {
            $assessment->save();
        }

        global $siteConfig;

        // Update application status
        $statusChanged = false;
        $oldStatusId = (int)$app->applicationstatus;
        $newStatusId = !empty($_POST['new_applicationstatus']) ? (int)$_POST['new_applicationstatus'] : $oldStatusId;

        if ($newStatusId && $newStatusId !== $oldStatusId) {
            $app->applicationstatus = $newStatusId;
            $app->update();
            $statusChanged = true;

            $statusObj = $app->applicationstatus();
            $statusName = $statusObj ? $statusObj->name : "Status #$newStatusId";
            $this->logStatusEvent($appId, $newStatusId, "Status updated to {$statusName} by reviewer");
        }

        // Send Review Decision or Shortlist notification to Candidate
        $candidate = $app->creator() ?: (new User())->find($app->user);
        if ($candidate) {
            if ($newStatusId === 3 && $statusChanged) {
                $token = $this->generateShortlistToken($app);
                $dossierLink = $siteConfig->siteUrl . '/roster/shortlist/complete?id=' . $app->iD . '&token=' . $token;
                Mailer::sendShortlistInvitation($app, $candidate, $dossierLink);
            } else {
                $recTitle = "Under Assessment";
                if ($assessment->vettingrecommendation) {
                    $recObj = $assessment->vettingRecommendation();
                    $recTitle = $recObj ? $recObj->title : "Recommendation #" . $assessment->vettingrecommendation;
                } elseif (!empty($_POST['new_applicationstatus'])) {
                    $statusObj = $app->applicationstatus();
                    $recTitle = $statusObj ? $statusObj->name : "Status Updated";
                }

                Mailer::sendApplicationReviewDecision(
                    $app,
                    $candidate,
                    $recTitle,
                    $assessment->interview_notes,
                    $assessment->total_score
                );
            }
        }

        return [
            'status' => 1,
            'total_score' => $assessment->total_score,
            'msg' => 'Assessment and scoring recorded successfully!'
        ];
    }
}
