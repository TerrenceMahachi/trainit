<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Rosterapplication;

/**
 * Central Mailer Service for Trainit.
 *
 * Routes notifications to/from designated domain mailboxes:
 * - noreply@trainit.co.zw        : Automated account security & password recovery
 * - hello@trainit.co.zw          : Welcome greetings & general inquiries
 * - apprenticeship@trainit.co.zw : Apprentice applications & status updates
 * - associates@trainit.co.zw     : Associate applications & status updates
 * - jobs@trainit.co.zw           : Talent pipeline & assessment review notifications
 * - admin@trainit.co.zw          : Executive admin alerts & statutory onboarding
 * - billing@trainit.co.zw        : Onboarding banking/tax alerts & finance
 * - sales@trainit.co.zw          : Commercial service inquiries
 * - dev.trainit@trainit.co.zw    : System exception / developer alerts
 */
class Mailer
{
    public const ADMIN          = 'admin@trainit.co.zw';
    public const APPRENTICE     = 'apprenticeship@trainit.co.zw';
    public const ASSOCIATES     = 'associates@trainit.co.zw';
    public const BILLING        = 'billing@trainit.co.zw';
    public const DEV            = 'dev.trainit@trainit.co.zw';
    public const HELLO          = 'hello@trainit.co.zw';
    public const JOBS           = 'jobs@trainit.co.zw';
    public const NOREPLY        = 'noreply@trainit.co.zw';
    public const PROJECTS       = 'projects@trainit.co.zw';
    public const SALES          = 'sales@trainit.co.zw';

    /**
     * Send a Welcome Email upon Account Registration.
     */
    public static function sendWelcome(User $user): bool
    {
        global $siteConfig;
        $siteUrl = $siteConfig->siteUrl ?? 'https://trainit.co.zw';
        $siteName = $siteConfig->siteName ?? 'Trainit';

        $subject = "Welcome to {$siteName} – Your Account is Active";

        $html = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 22px;'>Welcome, " . htmlspecialchars($user->name) . "!</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Your account on the <strong>{$siteName}</strong> digital platform has been successfully created. You now have access to our talent ecosystem, apprentice &amp; associate opportunities, and client service portals.
            </p>
            <div style='background-color: #F8F5FC; border-left: 4px solid #FFCC00; padding: 16px; border-radius: 6px; margin: 20px 0;'>
                <p style='margin: 0 0 8px; font-weight: bold; color: #2A114B;'>Your Account Details:</p>
                <p style='margin: 0 0 4px; color: #4B3E5C;'><strong>Email / Username:</strong> " . htmlspecialchars($user->email) . "</p>
                <p style='margin: 0; color: #4B3E5C;'><strong>Member ID:</strong> #" . (int)$user->iD . "</p>
            </div>
            <p style='margin: 0 0 20px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Ready to take the next step? Explore our current Apprentice and Associate roster intake or visit your dashboard to manage your applications.
            </p>
        ";

        return self::send(
            to: $user->email,
            toName: $user->name,
            subject: $subject,
            bodyHtml: $html,
            fromEmail: self::NOREPLY,
            fromName: "{$siteName} Accounts",
            buttonText: "Open Dashboard",
            buttonUrl: "{$siteUrl}/dashboard"
        );
    }

    /**
     * Send a Password Reset Link.
     */
    public static function sendPasswordReset(User $user, string $resetLink): bool
    {
        global $siteConfig;
        $siteName = $siteConfig->siteName ?? 'Trainit';

        $subject = "Password Reset Request – {$siteName}";

        $html = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 22px;'>Password Reset Request</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Hello <strong>" . htmlspecialchars($user->name) . "</strong>,
            </p>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                We received a request to reset the password for your {$siteName} account (<strong>" . htmlspecialchars($user->email) . "</strong>).
            </p>
            <p style='margin: 0 0 20px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Click the button below to choose a new password. This secure link is valid for <strong>1 hour</strong>.
            </p>
            <div style='background-color: #FFF9E6; border: 1px solid #FFE082; padding: 14px; border-radius: 6px; margin: 20px 0;'>
                <p style='margin: 0; color: #8C6200; font-size: 13px;'>
                    <strong>Security Notice:</strong> If you did not request this password reset, no action is needed and your password remains unchanged.
                </p>
            </div>
        ";

        return self::send(
            to: $user->email,
            toName: $user->name,
            subject: $subject,
            bodyHtml: $html,
            fromEmail: self::NOREPLY,
            fromName: "{$siteName} Security",
            buttonText: "Reset Password",
            buttonUrl: $resetLink
        );
    }

    /**
     * Send Confirmation of Password Reset Success.
     */
    public static function sendPasswordResetSuccess(User $user): bool
    {
        global $siteConfig;
        $siteUrl = $siteConfig->siteUrl ?? 'https://trainit.co.zw';
        $siteName = $siteConfig->siteName ?? 'Trainit';

        $subject = "Your {$siteName} Password Has Been Updated";

        $html = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 22px;'>Password Changed Successfully</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Hello <strong>" . htmlspecialchars($user->name) . "</strong>,
            </p>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                The password for your {$siteName} account has just been successfully updated.
            </p>
            <p style='margin: 0 0 20px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                If you performed this change, you can now log in securely. If you did NOT perform this change, please contact us immediately at <a href='mailto:" . self::ADMIN . "' style='color: #2A114B; font-weight: bold;'>" . self::ADMIN . "</a>.
            </p>
        ";

        return self::send(
            to: $user->email,
            toName: $user->name,
            subject: $subject,
            bodyHtml: $html,
            fromEmail: self::NOREPLY,
            fromName: "{$siteName} Security",
            buttonText: "Sign In to Your Account",
            buttonUrl: "{$siteUrl}/login"
        );
    }

    /**
     * Send Candidate Application Submission Confirmation + Internal Team Alert.
     */
    public static function sendApplicationSubmitted(Rosterapplication $app, User $candidate): bool
    {
        global $siteConfig;
        $siteUrl = $siteConfig->siteUrl ?? 'https://trainit.co.zw';
        $siteName = $siteConfig->siteName ?? 'Trainit';

        $trackCode = strtolower(trim($app->applicationtrack()->code ?? 'apprentice'));
        $trackTitle = $app->applicationtrack()->title ?? ($trackCode === 'associate' ? 'Associate Specialist' : 'Apprentice Graduate');
        $primaryFunc = $app->primaryfunction()->title ?? 'Specialist Role';

        $fromMailbox = ($trackCode === 'associate') ? self::ASSOCIATES : self::APPRENTICE;
        $departmentName = ($trackCode === 'associate') ? "Associate Talent Division" : "Apprenticeship Program";

        // 1. Candidate Confirmation Email
        $candidateSubject = "Application Received – {$trackTitle} (#APP-" . str_pad((string)$app->iD, 5, '0', STR_PAD_LEFT) . ")";
        $candidateHtml = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 22px;'>Application Received</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Dear <strong>" . htmlspecialchars($candidate->name) . "</strong>,
            </p>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Thank you for applying for the <strong>{$trackTitle}</strong> talent roster at {$siteName}. Your submission has been securely recorded and placed into our vetting pipeline.
            </p>
            <div style='background-color: #F8F5FC; border: 1px solid #E5DCF0; border-radius: 8px; padding: 18px; margin: 22px 0;'>
                <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                    <tr>
                        <td style='padding: 6px 0; color: #6C607C; width: 40%;'><strong>Application Ref:</strong></td>
                        <td style='padding: 6px 0; color: #1C0D30; font-weight: bold;'>#APP-" . str_pad((string)$app->iD, 5, '0', STR_PAD_LEFT) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #6C607C;'><strong>Track:</strong></td>
                        <td style='padding: 6px 0; color: #1C0D30;'>" . htmlspecialchars($trackTitle) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #6C607C;'><strong>Primary Function:</strong></td>
                        <td style='padding: 6px 0; color: #1C0D30;'>" . htmlspecialchars($primaryFunc) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #6C607C;'><strong>Current Stage:</strong></td>
                        <td style='padding: 6px 0; color: #2A114B; font-weight: bold;'>Stage 1: Profile &amp; Document Intake</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #6C607C;'><strong>Status:</strong></td>
                        <td style='padding: 6px 0;'><span style='background: #FFF3CD; color: #856404; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;'>Pending Vetting Review</span></td>
                    </tr>
                </table>
            </div>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                <strong>What happens next?</strong><br>
                Our recruitment panel will evaluate your credentials against our 100-point assessment rubric. You can monitor your application status and review feedback in real-time from your portal dashboard.
            </p>
        ";

        self::send(
            to: $candidate->email,
            toName: $candidate->name,
            subject: $candidateSubject,
            bodyHtml: $candidateHtml,
            fromEmail: $fromMailbox,
            fromName: "{$siteName} {$departmentName}",
            buttonText: "Track Application Status",
            buttonUrl: "{$siteUrl}/dashboard/application?id={$app->iD}"
        );

        // 2. Internal Team Alert Email
        $internalSubject = "[New Intake] #APP-" . str_pad((string)$app->iD, 5, '0', STR_PAD_LEFT) . " – " . $candidate->name . " ({$trackTitle})";
        $internalHtml = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 20px;'>New Candidate Submission</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                A new talent application has been submitted and is ready for scoring on the Administrator Review Console.
            </p>
            <div style='background-color: #F8F5FC; border: 1px solid #E5DCF0; border-radius: 8px; padding: 18px; margin: 20px 0;'>
                <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                    <tr>
                        <td style='padding: 6px 0; color: #6C607C; width: 35%;'><strong>Candidate:</strong></td>
                        <td style='padding: 6px 0; color: #1C0D30; font-weight: bold;'>" . htmlspecialchars($candidate->name) . " (" . htmlspecialchars($candidate->email) . ")</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #6C607C;'><strong>Track / Domain:</strong></td>
                        <td style='padding: 6px 0; color: #1C0D30;'>" . htmlspecialchars($trackTitle) . " &bull; " . htmlspecialchars($primaryFunc) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #6C607C;'><strong>Submitted Date:</strong></td>
                        <td style='padding: 6px 0; color: #1C0D30;'>" . date('Y-m-d H:i') . "</td>
                    </tr>
                </table>
            </div>
        ";

        $teamRecipients = array_unique([$fromMailbox, self::JOBS, self::ADMIN]);
        return self::send(
            to: $teamRecipients,
            toName: "{$siteName} Talent Reviewers",
            subject: $internalSubject,
            bodyHtml: $internalHtml,
            fromEmail: self::NOREPLY,
            fromName: "{$siteName} Intake Robot",
            buttonText: "Open Review Console",
            buttonUrl: "{$siteUrl}/admin/roster/review?id={$app->iD}"
        );
    }

    /**
     * Send Candidate Shortlist Invitation Email with Secure Dossier Link.
     */
    public static function sendShortlistInvitation(
        Rosterapplication $app,
        User $candidate,
        string $dossierLink
    ): bool {
        global $siteConfig;
        $siteUrl = $siteConfig->siteUrl ?? 'https://trainit.co.zw';
        $siteName = $siteConfig->siteName ?? 'Trainit';

        $trackCode = strtolower(trim($app->applicationtrack()->code ?? 'apprentice'));
        $trackTitle = $app->applicationtrack()->name ?? ($trackCode === 'associate' ? 'Associate Specialist' : 'Apprentice');
        $primaryFunc = $app->primaryfunction()->name ?? 'Specialist Domain';

        $fromMailbox = ($trackCode === 'associate') ? self::ASSOCIATES : self::APPRENTICE;
        $departmentName = ($trackCode === 'associate') ? "Associate Talent Division" : "Apprenticeship Program";

        $subject = "Congratulations! You are Shortlisted – Complete Your {$trackTitle} Dossier (#APP-" . str_pad((string)$app->iD, 5, '0', STR_PAD_LEFT) . ")";
        $html = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 22px;'>Congratulations, " . htmlspecialchars($candidate->name) . "!</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                We are pleased to inform you that following review of your CV and initial profile, you have been <strong>shortlisted</strong> for the <strong>{$trackTitle}</strong> talent network in <strong>" . htmlspecialchars($primaryFunc) . "</strong>.
            </p>
            <div style='background-color: #ECFDF5; border-left: 4px solid #10B981; padding: 16px; border-radius: 6px; margin: 22px 0;'>
                <p style='margin: 0 0 8px; font-weight: bold; color: #065F46;'>Next Step: Complete Your Verification Dossier</p>
                <p style='margin: 0; color: #047857; font-size: 14px;'>
                    To finalize your admission and prepare you for active client briefs, please complete your credentials (certificates/transcripts), skills matrix rating, and referee contacts.
                </p>
            </div>
            <p style='margin: 0 0 20px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Click the button below to resume and complete your profile. This secure link will automatically open your candidate dossier.
            </p>
        ";

        return self::send(
            to: $candidate->email,
            toName: $candidate->name,
            subject: $subject,
            bodyHtml: $html,
            fromEmail: $fromMailbox,
            fromName: "{$siteName} {$departmentName}",
            buttonText: "Complete Your Dossier Now",
            buttonUrl: $dossierLink
        );
    }

    /**
     * Send Candidate Confirmation upon Completing Full Verification Dossier (Stages 2-5).
     */
    public static function sendDossierSubmitted(
        Rosterapplication $app,
        User $candidate
    ): bool {
        global $siteConfig;
        $siteUrl = $siteConfig->siteUrl ?? 'https://trainit.co.zw';
        $siteName = $siteConfig->siteName ?? 'Trainit';

        $trackCode = strtolower(trim($app->applicationtrack()->code ?? 'apprentice'));
        $trackTitle = $app->applicationtrack()->name ?? ($trackCode === 'associate' ? 'Associate Specialist' : 'Apprentice');
        $fromMailbox = ($trackCode === 'associate') ? self::ASSOCIATES : self::APPRENTICE;
        $departmentName = ($trackCode === 'associate') ? "Associate Talent Division" : "Apprenticeship Program";

        $subject = "Verification Dossier Received – {$trackTitle} (#APP-" . str_pad((string)$app->iD, 5, '0', STR_PAD_LEFT) . ")";
        $html = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 22px;'>Verification Dossier Submitted, " . htmlspecialchars($candidate->name) . "!</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Thank you for completing your verification dossier, including your qualifications, skills competency ratings, and professional references.
            </p>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Our vetting committee is conducting background verification and 100-point scoring. We will contact you regarding the next steps and your onboarding interview.
            </p>
        ";

        return self::send(
            to: $candidate->email,
            toName: $candidate->name,
            subject: $subject,
            bodyHtml: $html,
            fromEmail: $fromMailbox,
            fromName: "{$siteName} {$departmentName}",
            buttonText: "View Application Status",
            buttonUrl: "{$siteUrl}/roster/application/status?id={$app->iD}"
        );
    }

    /**
     * Send Candidate Notification upon Assessment / Vetting Decision Update.
     */
    public static function sendApplicationReviewDecision(
        Rosterapplication $app,
        User $candidate,
        string $recommendationText,
        ?string $comments = null,
        ?float $totalScore = null
    ): bool {
        global $siteConfig;
        $siteUrl = $siteConfig->siteUrl ?? 'https://trainit.co.zw';
        $siteName = $siteConfig->siteName ?? 'Trainit';

        $trackCode = strtolower(trim($app->applicationtrack()->code ?? 'apprentice'));
        $trackTitle = $app->applicationtrack()->title ?? 'Talent Roster';
        $statusCode = (int)$app->applicationstatus;
        $statusName = $app->applicationstatus()->name ?? 'Updated';

        $fromMailbox = ($trackCode === 'associate') ? self::ASSOCIATES : self::APPRENTICE;
        $departmentName = ($trackCode === 'associate') ? "Associate Talent Division" : "Apprenticeship Program";

        $subject = "Application Update: #APP-" . str_pad((string)$app->iD, 5, '0', STR_PAD_LEFT) . " – " . $statusName;

        // Custom status message box
        $badgeColor = '#2A114B';
        $badgeBg = '#F3ECFA';
        $actionButtonText = "View Application Details";
        $actionButtonUrl = "{$siteUrl}/dashboard/application?id={$app->iD}";

        if ($statusCode === 5) { // Admitted on Roster
            $badgeColor = '#0F5132';
            $badgeBg = '#D1E7DD';
            $actionButtonText = "Complete Stage 3 Statutory Onboarding";
            $actionButtonUrl = "{$siteUrl}/dashboard/application/onboarding?id={$app->iD}";
        } elseif ($statusCode === 3) { // Interview / Verification
            $badgeColor = '#055160';
            $badgeBg = '#CFF4FC';
        } elseif ($statusCode === 4) { // Talent Pool Hold
            $badgeColor = '#664D03';
            $badgeBg = '#FFF3CD';
        } elseif ($statusCode === 6) { // Rejected
            $badgeColor = '#842029';
            $badgeBg = '#F8D7DA';
        }

        $html = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 22px;'>Application Status Update</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Dear <strong>" . htmlspecialchars($candidate->name) . "</strong>,
            </p>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                The assessment panel has completed the review of your <strong>{$trackTitle}</strong> application.
            </p>
            <div style='background-color: {$badgeBg}; border: 1px solid {$badgeColor}; border-radius: 8px; padding: 18px; margin: 20px 0;'>
                <p style='margin: 0 0 6px; color: {$badgeColor}; font-size: 13px; text-transform: uppercase; font-weight: bold;'>Decision Status:</p>
                <h3 style='margin: 0 0 10px; color: {$badgeColor}; font-size: 20px;'>" . htmlspecialchars($statusName) . "</h3>
                <p style='margin: 0; color: {$badgeColor}; font-size: 14px;'><strong>Vetting Recommendation:</strong> " . htmlspecialchars($recommendationText) . "</p>
                " . ($totalScore !== null ? "<p style='margin: 6px 0 0; color: {$badgeColor}; font-size: 14px;'><strong>Assessment Score:</strong> " . number_format($totalScore, 1) . " / 100</p>" : "") . "
            </div>
        ";

        if (!empty($comments)) {
            $html .= "
                <div style='background-color: #F8F5FC; border-left: 4px solid #2A114B; padding: 14px; border-radius: 4px; margin: 18px 0;'>
                    <p style='margin: 0 0 4px; font-weight: bold; color: #2A114B; font-size: 13px;'>Assessor Feedback:</p>
                    <p style='margin: 0; color: #4B3E5C; font-size: 14px; line-height: 1.5; font-style: italic;'>&ldquo;" . nl2br(htmlspecialchars($comments)) . "&rdquo;</p>
                </div>
            ";
        }

        if ($statusCode === 5) {
            $html .= "
                <p style='margin: 0 0 16px; color: #198754; font-weight: bold; line-height: 1.6; font-size: 15px;'>
                    Congratulations! You are now admitted onto the Trainit Professional Roster. Please proceed to complete your Stage 3 Statutory Onboarding (National ID, Tax Number, and Banking details) to enable engagement matching and payroll.
                </p>
            ";
        }

        return self::send(
            to: $candidate->email,
            toName: $candidate->name,
            subject: $subject,
            bodyHtml: $html,
            fromEmail: $fromMailbox,
            fromName: "{$siteName} {$departmentName}",
            buttonText: $actionButtonText,
            buttonUrl: $actionButtonUrl
        );
    }

    /**
     * Send Candidate Confirmation & Internal Alert on Stage 3 Statutory Onboarding Submission.
     */
    public static function sendOnboardingSubmitted(Rosterapplication $app, User $candidate, array $onboardingData): bool
    {
        global $siteConfig;
        $siteUrl = $siteConfig->siteUrl ?? 'https://trainit.co.zw';
        $siteName = $siteConfig->siteName ?? 'Trainit';

        // 1. Candidate Confirmation
        $subject = "Stage 3 Statutory Onboarding Verified – #APP-" . str_pad((string)$app->iD, 5, '0', STR_PAD_LEFT);
        $candidateHtml = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 22px;'>Onboarding Submission Received</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Dear <strong>" . htmlspecialchars($candidate->name) . "</strong>,
            </p>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Thank you for completing your <strong>Stage 3 Statutory &amp; Banking Onboarding</strong> for your roster placement (Ref: #APP-" . str_pad((string)$app->iD, 5, '0', STR_PAD_LEFT) . ").
            </p>
            <div style='background-color: #D1E7DD; border: 1px solid #0F5132; border-radius: 8px; padding: 16px; margin: 20px 0;'>
                <p style='margin: 0; color: #0F5132; font-weight: bold;'>
                    &#10003; National ID Verification, Tax Registration, and Disbursal Bank Account details have been encrypted and filed.
                </p>
            </div>
            <p style='margin: 0 0 20px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Our compliance and billing teams will review your statutory documents. You are now placement-ready for upcoming client engagements and project deployments.
            </p>
        ";

        self::send(
            to: $candidate->email,
            toName: $candidate->name,
            subject: $subject,
            bodyHtml: $candidateHtml,
            fromEmail: self::ADMIN,
            fromName: "{$siteName} Compliance &amp; Onboarding",
            buttonText: "View Roster Profile",
            buttonUrl: "{$siteUrl}/dashboard/application?id={$app->iD}"
        );

        // 2. Billing & Admin Alert
        $internalSubject = "[Onboarding Alert] Statutory Details Filed for #APP-" . str_pad((string)$app->iD, 5, '0', STR_PAD_LEFT) . " (" . $candidate->name . ")";
        $internalHtml = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 20px;'>Candidate Statutory Onboarding Filed</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Candidate <strong>" . htmlspecialchars($candidate->name) . "</strong> has completed statutory onboarding and banking verification.
            </p>
            <div style='background-color: #F8F5FC; border: 1px solid #E5DCF0; border-radius: 8px; padding: 18px; margin: 20px 0;'>
                <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                    <tr>
                        <td style='padding: 6px 0; color: #6C607C; width: 35%;'><strong>Candidate:</strong></td>
                        <td style='padding: 6px 0; color: #1C0D30; font-weight: bold;'>" . htmlspecialchars($candidate->name) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #6C607C;'><strong>Bank Name:</strong></td>
                        <td style='padding: 6px 0; color: #1C0D30;'>" . htmlspecialchars($onboardingData['bank_name'] ?? 'N/A') . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #6C607C;'><strong>Branch / Code:</strong></td>
                        <td style='padding: 6px 0; color: #1C0D30;'>" . htmlspecialchars($onboardingData['bank_branch'] ?? 'N/A') . " (" . htmlspecialchars($onboardingData['branch_code'] ?? 'N/A') . ")</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #6C607C;'><strong>Code of Conduct:</strong></td>
                        <td style='padding: 6px 0; color: #198754; font-weight: bold;'>Acknowledged &amp; Signed</td>
                    </tr>
                </table>
            </div>
        ";

        return self::send(
            to: [self::ADMIN, self::BILLING, self::JOBS],
            toName: "{$siteName} Compliance &amp; Billing",
            subject: $internalSubject,
            bodyHtml: $internalHtml,
            fromEmail: self::NOREPLY,
            fromName: "{$siteName} Compliance Bot",
            buttonText: "Review Onboarding Record",
            buttonUrl: "{$siteUrl}/dashboard/application/onboarding?id={$app->iD}"
        );
    }

    /**
     * Send Contact Inquirer Acknowledgment and Team Alert.
     */
    public static function sendContactInquiry(string $name, string $email, string $subjectLine, string $messageBody, ?string $phone = null): bool
    {
        global $siteConfig;
        $siteUrl = $siteConfig->siteUrl ?? 'https://trainit.co.zw';
        $siteName = $siteConfig->siteName ?? 'Trainit';

        // 1. Inquirer Acknowledgment
        $ackSubject = "We Received Your Message – {$siteName}";
        $ackHtml = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 22px;'>Thank You for Contacting Us</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Hello <strong>" . htmlspecialchars($name) . "</strong>,
            </p>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Thank you for reaching out to {$siteName}. We have received your inquiry regarding <strong>&ldquo;" . htmlspecialchars($subjectLine) . "&rdquo;</strong> and our team is reviewing it.
            </p>
            <p style='margin: 0 0 20px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                We typically respond within 1 business day. If your request is urgent, you can also reach our primary desk directly at <a href='mailto:" . self::HELLO . "' style='color: #2A114B; font-weight: bold;'>" . self::HELLO . "</a>.
            </p>
        ";

        self::send(
            to: $email,
            toName: $name,
            subject: $ackSubject,
            bodyHtml: $ackHtml,
            fromEmail: self::HELLO,
            fromName: "{$siteName} Support",
            buttonText: "Visit {$siteName}",
            buttonUrl: $siteUrl
        );

        // 2. Sales & Helpdesk Alert
        $teamSubject = "[Contact Inquiry] " . $subjectLine . " – " . $name;
        $teamHtml = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 20px;'>New Website Contact Inquiry</h2>
            <div style='background-color: #F8F5FC; border: 1px solid #E5DCF0; border-radius: 8px; padding: 18px; margin: 20px 0;'>
                <p style='margin: 0 0 6px;'><strong>From:</strong> " . htmlspecialchars($name) . " &lt;" . htmlspecialchars($email) . "&gt;</p>
                " . ($phone ? "<p style='margin: 0 0 6px;'><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>" : "") . "
                <p style='margin: 0 0 12px;'><strong>Subject:</strong> " . htmlspecialchars($subjectLine) . "</p>
                <hr style='border: none; border-top: 1px solid #E5DCF0; margin: 12px 0;'>
                <p style='margin: 0 0 6px; font-weight: bold; color: #2A114B;'>Message:</p>
                <p style='margin: 0; color: #4B3E5C; line-height: 1.6;'>" . nl2br(htmlspecialchars($messageBody)) . "</p>
            </div>
        ";

        return self::send(
            to: [self::HELLO, self::SALES],
            toName: "{$siteName} Inquiries Desk",
            subject: $teamSubject,
            bodyHtml: $teamHtml,
            fromEmail: self::NOREPLY,
            fromName: "{$siteName} Web Form",
            replyTo: $email
        );
    }

    /**
     * Send an Invitation Email to a new Staff Member to complete statutory onboarding.
     */
    public static function sendStaffInvitation(string $email, string $name, string $roleName, string $jobTitle, string $department, string $inviteUrl): bool
    {
        global $siteConfig;
        $siteName = $siteConfig->siteName ?? 'Trainit';

        $subject = "Welcome to the Team – Staff Onboarding & Account Setup for {$siteName}";

        $html = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 22px;'>Welcome to the Trainit Team, " . htmlspecialchars($name) . "!</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                You have been registered as an internal staff member of <strong>{$siteName} Technologies (t/a Tsigiro)</strong>.
            </p>
            <div style='background-color: #F8F5FC; border-left: 4px solid #FFCC00; padding: 16px; border-radius: 6px; margin: 20px 0;'>
                <p style='margin: 0 0 8px; font-weight: bold; color: #2A114B;'>Appointment Summary:</p>
                <p style='margin: 0 0 4px; color: #4B3E5C;'><strong>Role:</strong> " . htmlspecialchars($roleName) . "</p>
                <p style='margin: 0 0 4px; color: #4B3E5C;'><strong>Job Title:</strong> " . htmlspecialchars($jobTitle) . "</p>
                <p style='margin: 0 0 4px; color: #4B3E5C;'><strong>Department:</strong> " . htmlspecialchars($department) . "</p>
                <p style='margin: 0; color: #4B3E5C;'><strong>Official Work Email:</strong> " . htmlspecialchars($email) . "</p>
            </div>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                To activate your account, please click the button below to set your password and complete your statutory employee onboarding (NSSA Form P4 compliance and banking details).
            </p>
            <p style='margin: 0 0 8px; color: #7C708A; font-size: 13px;'>
                <em>Note: This secure onboarding invitation is valid for 7 days.</em>
            </p>
        ";

        return self::send(
            to: $email,
            toName: $name,
            subject: $subject,
            bodyHtml: $html,
            fromEmail: self::ADMIN,
            fromName: "{$siteName} Human Resources",
            buttonText: "Complete Staff Onboarding",
            buttonUrl: $inviteUrl
        );
    }

    /**
     * Send a Welcome Email for directly provisioned Staff.
     */
    public static function sendStaffWelcome(User $user, string $jobTitle, string $roleName, ?string $temporaryPassword = null): bool
    {
        global $siteConfig;
        $siteUrl = $siteConfig->siteUrl ?? 'https://trainit.co.zw';
        $siteName = $siteConfig->siteName ?? 'Trainit';

        $subject = "Welcome to the Team – Your {$siteName} Staff Account";

        $html = "
            <h2 style='margin: 0 0 16px; color: #1C0D30; font-size: 22px;'>Welcome, " . htmlspecialchars($user->name) . "!</h2>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Your staff portal profile has been created on the <strong>{$siteName}</strong> operations system.
            </p>
            <div style='background-color: #F8F5FC; border-left: 4px solid #FFCC00; padding: 16px; border-radius: 6px; margin: 20px 0;'>
                <p style='margin: 0 0 8px; font-weight: bold; color: #2A114B;'>Your Staff Credentials:</p>
                <p style='margin: 0 0 4px; color: #4B3E5C;'><strong>Username / Email:</strong> " . htmlspecialchars($user->email) . "</p>
                <p style='margin: 0 0 4px; color: #4B3E5C;'><strong>Assigned Role:</strong> " . htmlspecialchars($roleName) . "</p>
                <p style='margin: 0 0 4px; color: #4B3E5C;'><strong>Designation:</strong> " . htmlspecialchars($jobTitle) . "</p>
                " . ($temporaryPassword ? "<p style='margin: 0; color: #2A114B;'><strong>Temporary Password:</strong> <code style='background: #EAE3F5; padding: 2px 6px; border-radius: 4px;'>" . htmlspecialchars($temporaryPassword) . "</code></p>" : "") . "
            </div>
            <p style='margin: 0 0 16px; color: #4B3E5C; line-height: 1.6; font-size: 15px;'>
                Please log in to review your profile and access the operational tools assigned to your role.
            </p>
        ";

        return self::send(
            to: $user->email,
            toName: $user->name,
            subject: $subject,
            bodyHtml: $html,
            fromEmail: self::ADMIN,
            fromName: "{$siteName} Administration",
            buttonText: "Sign In to Staff Portal",
            buttonUrl: "{$siteUrl}/login"
        );
    }

    /**
     * Core Email Dispatcher with Master Tsigiro HTML Brand Template.
     */
    public static function send(
        string|array $to,
        string $subject,
        string $bodyHtml,
        string $fromEmail = self::NOREPLY,
        string $fromName = 'Trainit',
        ?string $toName = null,
        ?string $replyTo = null,
        ?string $buttonText = null,
        ?string $buttonUrl = null
    ): bool {
        global $siteConfig;
        $siteUrl = $siteConfig->siteUrl ?? 'https://trainit.co.zw';
        $siteName = $siteConfig->siteName ?? 'Trainit';
        $assetsUrl = $siteConfig->assetsUrl ?? ($siteUrl . '/assets');

        $toAddresses = is_array($to) ? $to : array_map('trim', explode(',', $to));
        $toAddresses = array_filter($toAddresses, fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL));

        if (empty($toAddresses)) {
            error_log("Mailer error: No valid recipient address provided for subject '{$subject}'");
            return false;
        }

        // Build Responsive HTML Email with Tsigiro Design Language
        $ctaHtml = "";
        if ($buttonText && $buttonUrl) {
            $ctaHtml = "
                <table border='0' cellpadding='0' cellspacing='0' style='margin: 28px 0 16px; width: 100%;'>
                    <tr>
                        <td align='center'>
                            <table border='0' cellpadding='0' cellspacing='0'>
                                <tr>
                                    <td align='center' style='border-radius: 8px; background-color: #2A114B;'>
                                        <a href='" . htmlspecialchars($buttonUrl) . "' target='_blank' style='display: inline-block; padding: 14px 28px; font-family: \"Segoe UI\", Helvetica, Arial, sans-serif; font-size: 15px; font-weight: bold; color: #FFFFFF; text-decoration: none; border-radius: 8px; letter-spacing: 0.3px;'>
                                            " . htmlspecialchars($buttonText) . " &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            ";
        }

        $fullHtml = "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>" . htmlspecialchars($subject) . "</title>
</head>
<body style='margin: 0; padding: 0; background-color: #F8F5FC; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; color: #1C0D30; -webkit-font-smoothing: antialiased;'>
    <table border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color: #F8F5FC; padding: 32px 12px;'>
        <tr>
            <td align='center'>
                <!-- Main Container -->
                <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #FFFFFF; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(42, 17, 75, 0.08); border: 1px solid #E6DCF2;'>
                    
                    <!-- Header Banner -->
                    <tr>
                        <td style='background: linear-gradient(135deg, #1C0D30 0%, #2A114B 55%, #3D196B 100%); padding: 28px 32px; text-align: left; border-bottom: 3px solid #FFCC00;'>
                            <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                                <tr>
                                    <td style='width: 44px; vertical-align: middle;'>
                                        <img src='{$assetsUrl}/img/logo-badge.png' alt='{$siteName}' width='42' height='42' style='display: block; border-radius: 8px; object-fit: contain;'>
                                    </td>
                                    <td style='padding-left: 14px; vertical-align: middle;'>
                                        <span style='color: #FFFFFF; font-size: 20px; font-weight: 800; letter-spacing: -0.3px; display: block;'>{$siteName}</span>
                                        <span style='color: #FFCC00; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;'>Technology &amp; Talent Platform</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td style='padding: 36px 32px 28px;'>
                            {$bodyHtml}
                            {$ctaHtml}
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style='background-color: #140824; padding: 24px 32px; text-align: center; border-top: 1px solid #2A114B;'>
                            <p style='margin: 0 0 10px; color: #C8BFD6; font-size: 13px; line-height: 1.5;'>
                                {$siteName} &bull; Practical technology, managed services, and talent roster solutions.
                            </p>
                            <p style='margin: 0 0 12px; color: #A090B8; font-size: 12px;'>
                                <a href='{$siteUrl}/services' style='color: #FFCC00; text-decoration: none; margin: 0 8px;'>Services</a> &bull;
                                <a href='{$siteUrl}/opportunities' style='color: #FFCC00; text-decoration: none; margin: 0 8px;'>Opportunities</a> &bull;
                                <a href='{$siteUrl}/contact' style='color: #FFCC00; text-decoration: none; margin: 0 8px;'>Contact Us</a>
                            </p>
                            <p style='margin: 0; color: #6B5E80; font-size: 11px;'>
                                &copy; " . date('Y') . " {$siteName}. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
        ";

        // Construct standard RFC 2822 Headers
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/html; charset=UTF-8";
        $headers[] = "From: " . self::formatAddress($fromEmail, $fromName);
        if ($replyTo) {
            $headers[] = "Reply-To: " . $replyTo;
        } else {
            $headers[] = "Reply-To: " . $fromEmail;
        }
        $headers[] = "X-Mailer: Trainit Mail Engine/1.0";

        $headerStr = implode("\r\n", $headers);
        $toStr = implode(', ', $toAddresses);

        // Always log outgoing email trace
        $logFile = _BASE_PATH . '/storage/mail.log';
        $logEntry = "[" . date('Y-m-d H:i:s') . "] TO: {$toStr} | FROM: {$fromEmail} | SUBJECT: {$subject}\n";
        @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

        // Deliver via PHP mail()
        $success = @mail($toStr, $subject, $fullHtml, $headerStr);
        if (!$success) {
            error_log("Mailer notice: mail() delivery attempted for '{$toStr}' ({$subject})");
        }

        return true;
    }

    private static function formatAddress(string $email, ?string $name = null): string
    {
        if (!$name) {
            return $email;
        }
        return sprintf('=?UTF-8?B?%s?= <%s>', base64_encode($name), $email);
    }
}
