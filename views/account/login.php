@extends('layouts.account')

<?php 
$showQuickLogin = !empty($data['show_quick_login']); 
?>

<style>
/* Modern Auth Screen Styles */
.az-signin-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 1.25rem;
}

.az-card-signin-modern {
    background: #ffffff;
    border-radius: 24px;
    padding: 42px 38px;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.28), 0 0 0 1px rgba(255, 255, 255, 0.08);
    width: 100%;
    border: 1px solid rgba(226, 232, 240, 0.85);
    position: relative;
    overflow: hidden;
}

.account-logo-badge {
    height: 56px;
    width: 56px;
    object-fit: contain;
    display: block;
    margin: 0 auto 16px;
    filter: drop-shadow(0 6px 14px rgba(0, 0, 0, 0.15));
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.account-logo-badge:hover {
    transform: scale(1.05);
}

.auth-heading {
    font-size: 1.55rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.025em;
    margin-bottom: 4px;
}

.auth-subheading {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 24px;
}

/* Modern Input Groups */
.auth-field {
    margin-bottom: 1.15rem;
}

.auth-label {
    display: block;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 6px;
}

.auth-input-container {
    display: flex;
    align-items: center;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 0 14px;
    transition: all 0.2s ease;
}

.auth-input-container:focus-within {
    background: #ffffff;
    border-color: #2A114B;
    box-shadow: 0 0 0 3.5px rgba(42, 17, 75, 0.08);
}

.auth-input-container .form-control {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    color: #0f172a !important;
    padding: 12px 10px 12px 2px !important;
    font-size: 0.9375rem !important;
    line-height: 1.5 !important;
    height: auto !important;
}

.auth-input-container .form-control::placeholder {
    color: #94a3b8 !important;
    font-size: 0.875rem;
}

.auth-input-container .auth-icon-btn {
    color: #94a3b8;
    background: none;
    border: none;
    padding: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: color 0.15s ease;
}

.auth-input-container:focus-within .auth-icon-btn {
    color: #475569;
}

.auth-input-container .auth-icon-btn:hover {
    color: #2A114B;
}

/* Forgot Password Link */
.auth-link-forgot {
    font-size: 0.8125rem;
    color: #64748b;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.15s ease;
}
.auth-link-forgot:hover {
    color: #2A114B;
    text-decoration: underline;
}

/* Primary Button */
.btn-auth-submit {
    width: 100%;
    padding: 12px 20px;
    background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3D1B6B 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9375rem;
    letter-spacing: 0.01em;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(42, 17, 75, 0.28);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
    margin-top: 8px;
}
.btn-auth-submit:hover:not(:disabled) {
    background: linear-gradient(135deg, #241140 0%, #35165f 50%, #4a2182 100%);
    box-shadow: 0 6px 20px rgba(42, 17, 75, 0.38);
    transform: translateY(-1px);
}
.btn-auth-submit:active:not(:disabled) {
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(42, 17, 75, 0.25);
}
.btn-auth-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Clean Divider */
.auth-divider {
    display: flex;
    align-items: center;
    margin: 22px 0;
}
.auth-divider-line {
    flex: 1;
    height: 1px;
    background: #e2e8f0;
}
.auth-divider-text {
    padding: 0 14px;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #94a3b8;
    letter-spacing: 0.06em;
}

/* Secondary Button */
.btn-auth-register {
    width: 100%;
    padding: 11px 20px;
    background: #ffffff;
    color: #1e293b !important;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
}
.btn-auth-register:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #2A114B !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}

/* Bottom Links */
.auth-bottom-links {
    margin-top: 22px;
    text-align: center;
}
.auth-bottom-links a {
    font-size: 0.8125rem;
    color: #64748b;
    text-decoration: none;
    transition: color 0.15s ease;
}
.auth-bottom-links a:hover {
    color: #2A114B;
    text-decoration: underline;
}

/* Status Alert */
#msg:not(:empty) {
    padding: 10px 14px;
    border-radius: 10px;
    background: #fef2f2;
    border: 1px solid #fee2e2;
    font-size: 0.8125rem;
    margin-top: 10px;
    margin-bottom: 12px;
}
</style>

<div class="az-signin-wrapper">
    <div class="container my-auto py-4" style="max-width: <?= $showQuickLogin ? '1140px' : '430px' ?>;">
        <div class="row g-4 align-items-stretch justify-content-center">
            
            <!-- Standard Sign-In Form Column -->
            <div class="<?= $showQuickLogin ? 'col-lg-5 col-md-10' : 'col-12' ?> d-flex">
                <div class="az-card-signin-modern shadow-lg w-100">
                    
                    <!-- Header -->
                    <div class="text-center mb-3">
                        <a href="<?= $siteConfig->siteUrl ?>/home" class="d-inline-block text-decoration-none">
                            <img class="account-logo-badge"
                                src="<?= $siteConfig->assetsUrl ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>"
                                alt="<?= htmlspecialchars(_SITE) ?>" />
                        </a>
                        <h1 class="auth-heading">Welcome back</h1>
                        <p class="auth-subheading">Sign in to your Tsigiro Portal workspace</p>
                    </div>

                    <!-- Sign In Form -->
                    <form id="_form" class="text-start" onsubmit="event.preventDefault(); login();">
                        
                        <!-- Email Input -->
                        <div class="auth-field">
                            <label class="auth-label" for="email">Email Address</label>
                            <div class="auth-input-container">
                                <input id="email" type="email" name="email" class="form-control"
                                    placeholder="name@company.co.zw" aria-label="Email address" autocomplete="username" required>
                                <span class="auth-icon-btn"><i class="fa fa-envelope"></i></span>
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="auth-field">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="auth-label mb-0" for="login_password">Password</label>
                                <a href="<?= $siteConfig->siteUrl ?>/reset" class="auth-link-forgot">Forgot?</a>
                            </div>
                            <div class="auth-input-container">
                                <input id="login_password" name="password" type="password"
                                    class="form-control" placeholder="••••••••" aria-label="Password"
                                    autocomplete="current-password" required>
                                <span class="auth-icon-btn show-p" data-target="login_password" title="Toggle password visibility">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Status Message -->
                        <div id="msg" class="<?= !empty($data['status']) ? 'text-danger small fw-semibold' : '' ?>"><?= $data['status']; ?></div>

                        <!-- Submit Button -->
                        <button type="button" onclick="login()" id="submit_btn" class="btn-auth-submit">
                            <span>Sign In</span>
                            <i class="fa fa-arrow-right ms-1" style="font-size: 0.8rem;"></i>
                        </button>

                        <!-- Divider -->
                        <div class="auth-divider">
                            <div class="auth-divider-line"></div>
                            <div class="auth-divider-text">New to Tsigiro?</div>
                            <div class="auth-divider-line"></div>
                        </div>

                        <!-- Create Account Secondary Action -->
                        <a href="<?= $siteConfig->siteUrl ?>/register" class="btn-auth-register">
                            <i class="fa fa-user-plus text-primary"></i>
                            <span>Create a New Account</span>
                        </a>

                        <!-- Footer Link -->
                        <div class="auth-bottom-links">
                            <a href="<?= $siteConfig->siteUrl ?>/opportunities">
                                <i class="fa fa-briefcase me-1 opacity-75"></i> Explore open opportunities &rarr;
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($showQuickLogin): ?>
            <!-- Testing & Demo Accounts Column -->
            <div class="col-lg-7 col-md-10 d-flex">
                <div class="card border-0 shadow-lg rounded-4 w-100 h-100 p-4" style="background: #ffffff; border-radius: 24px; border: 1px solid rgba(226, 232, 240, 0.85) !important;">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small fw-semibold">
                                    <i class="fa fa-flask me-1"></i> Testing Sandbox
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small fw-semibold">
                                    <i class="fa fa-key me-1"></i> Pass: <code>Password123!</code>
                                </span>
                            </div>
                            <h3 class="h5 fw-bold text-dark mb-0">Role-Based Demo Accounts</h3>
                        </div>
                        <span class="text-muted small">Click <strong>Sign In</strong> for instant 1-click access</span>
                    </div>

                    <!-- Internal Staff & Governance Section -->
                    <div class="mb-3">
                        <div class="text-uppercase text-secondary fw-bold small mb-2 d-flex align-items-center" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                            <i class="fa fa-shield-alt text-primary me-2"></i> Internal Staff & Operations Management
                        </div>
                        <div class="row g-2">
                            
                            <!-- 1. System Administrator -->
                            <div class="col-sm-6">
                                <div class="p-2 border rounded-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between hover-shadow">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                            <span class="badge bg-dark px-2 py-0" style="font-size: 0.7rem;">Admin (Role 1)</span>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-muted small text-decoration-none" title="Pre-fill login form" onclick="fillDemo('admin@tsigiro.co.zw')">
                                                <i class="fa fa-paste"></i> Pre-fill
                                            </button>
                                        </div>
                                        <div class="fw-bold text-dark small">System Administrator</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">admin@tsigiro.co.zw</div>
                                        <div class="text-secondary small mt-1" style="font-size: 0.7rem; line-height: 1.2;">Full governance, vacancies, user & roster command center.</div>
                                    </div>
                                    <div class="mt-2 pt-1 border-top">
                                        <a href="<?= $siteConfig->siteUrl ?>/quick-login?as=admin" class="btn btn-dark btn-sm w-100 py-1 fw-semibold text-white" style="font-size: 0.75rem;">
                                            <i class="fa fa-sign-in-alt me-1"></i> Sign In as Admin
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Vetting Officer -->
                            <div class="col-sm-6">
                                <div class="p-2 border rounded-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between hover-shadow">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                            <span class="badge bg-warning text-dark px-2 py-0" style="font-size: 0.7rem;">Vetting (Role 8)</span>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-muted small text-decoration-none" title="Pre-fill login form" onclick="fillDemo('vetting@tsigiro.co.zw')">
                                                <i class="fa fa-paste"></i> Pre-fill
                                            </button>
                                        </div>
                                        <div class="fw-bold text-dark small">Ruvimbo Sithole</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">vetting@tsigiro.co.zw &bull; TSG-STF-008</div>
                                        <div class="text-secondary small mt-1" style="font-size: 0.7rem; line-height: 1.2;">Background checks, police clearances & vetting checklists.</div>
                                    </div>
                                    <div class="mt-2 pt-1 border-top">
                                        <a href="<?= $siteConfig->siteUrl ?>/quick-login?as=vetting" class="btn btn-outline-dark btn-sm w-100 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fa fa-sign-in-alt me-1"></i> Sign In as Vetting
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Service Manager -->
                            <div class="col-sm-6">
                                <div class="p-2 border rounded-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between hover-shadow">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                            <span class="badge bg-primary px-2 py-0" style="font-size: 0.7rem;">Service Mgr (Role 6)</span>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-muted small text-decoration-none" title="Pre-fill login form" onclick="fillDemo('manager@tsigiro.co.zw')">
                                                <i class="fa fa-paste"></i> Pre-fill
                                            </button>
                                        </div>
                                        <div class="fw-bold text-dark small">Tafadzwa Mutasa</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">manager@tsigiro.co.zw &bull; TSG-STF-006</div>
                                        <div class="text-secondary small mt-1" style="font-size: 0.7rem; line-height: 1.2;">Client engagements, SLA oversight & roster assignments.</div>
                                    </div>
                                    <div class="mt-2 pt-1 border-top">
                                        <a href="<?= $siteConfig->siteUrl ?>/quick-login?as=manager" class="btn btn-outline-primary btn-sm w-100 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fa fa-sign-in-alt me-1"></i> Sign In as Manager
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Billing Officer -->
                            <div class="col-sm-6">
                                <div class="p-2 border rounded-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between hover-shadow">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                            <span class="badge bg-success px-2 py-0 text-white" style="font-size: 0.7rem;">Finance (Role 7)</span>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-muted small text-decoration-none" title="Pre-fill login form" onclick="fillDemo('finance@tsigiro.co.zw')">
                                                <i class="fa fa-paste"></i> Pre-fill
                                            </button>
                                        </div>
                                        <div class="fw-bold text-dark small">Nyasha Chidziwa</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">finance@tsigiro.co.zw &bull; TSG-STF-007</div>
                                        <div class="text-secondary small mt-1" style="font-size: 0.7rem; line-height: 1.2;">Client billing, ITF263 tax checks & associate disbursements.</div>
                                    </div>
                                    <div class="mt-2 pt-1 border-top">
                                        <a href="<?= $siteConfig->siteUrl ?>/quick-login?as=finance" class="btn btn-outline-success btn-sm w-100 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fa fa-sign-in-alt me-1"></i> Sign In as Finance
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- External Roster, Candidates & Client Section -->
                    <div>
                        <div class="text-uppercase text-secondary fw-bold small mb-2 d-flex align-items-center" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                            <i class="fa fa-users text-info me-2"></i> External Talent, Roster & Client Portals
                        </div>
                        <div class="row g-2">
                            
                            <!-- 5. Work-Related Learning Apprentice -->
                            <div class="col-sm-6">
                                <div class="p-2 border rounded-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between hover-shadow">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                            <span class="badge bg-info text-dark px-2 py-0" style="font-size: 0.7rem;">Apprentice (Role 2)</span>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-muted small text-decoration-none" title="Pre-fill login form" onclick="fillDemo('apprentice@tsigiro.co.zw')">
                                                <i class="fa fa-paste"></i> Pre-fill
                                            </button>
                                        </div>
                                        <div class="fw-bold text-dark small">Kudzai Mapfumo</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">apprentice@tsigiro.co.zw &bull; NUST CompSci</div>
                                        <div class="text-secondary small mt-1" style="font-size: 0.7rem; line-height: 1.2;">Shortlisted WRL candidate, attachment timeline & onboarding.</div>
                                    </div>
                                    <div class="mt-2 pt-1 border-top">
                                        <a href="<?= $siteConfig->siteUrl ?>/quick-login?as=apprentice" class="btn btn-outline-info btn-sm w-100 py-1 fw-semibold text-dark" style="font-size: 0.75rem;">
                                            <i class="fa fa-sign-in-alt me-1"></i> Sign In as Apprentice
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- 6. Associate Specialist -->
                            <div class="col-sm-6">
                                <div class="p-2 border rounded-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between hover-shadow">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                            <span class="badge bg-secondary px-2 py-0 text-white" style="font-size: 0.7rem;">Associate (Role 2)</span>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-muted small text-decoration-none" title="Pre-fill login form" onclick="fillDemo('associate@tsigiro.co.zw')">
                                                <i class="fa fa-paste"></i> Pre-fill
                                            </button>
                                        </div>
                                        <div class="fw-bold text-dark small">Simbarashe Hove</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">associate@tsigiro.co.zw &bull; Cloud Spec.</div>
                                        <div class="text-secondary small mt-1" style="font-size: 0.7rem; line-height: 1.2;">Senior Associate, $180/day billing rate, ITF263 tax cleared.</div>
                                    </div>
                                    <div class="mt-2 pt-1 border-top">
                                        <a href="<?= $siteConfig->siteUrl ?>/quick-login?as=associate" class="btn btn-outline-secondary btn-sm w-100 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fa fa-sign-in-alt me-1"></i> Sign In as Associate
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- 7. Vacancy Candidate -->
                            <div class="col-sm-6">
                                <div class="p-2 border rounded-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between hover-shadow">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                            <span class="badge px-2 py-0 text-white" style="background-color: #e11d48; font-size: 0.7rem;">Candidate (Role 2)</span>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-muted small text-decoration-none" title="Pre-fill login form" onclick="fillDemo('candidate@tsigiro.co.zw')">
                                                <i class="fa fa-paste"></i> Pre-fill
                                            </button>
                                        </div>
                                        <div class="fw-bold text-dark small">Farai Chikwanha</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">candidate@tsigiro.co.zw &bull; Ops Vacancy</div>
                                        <div class="text-secondary small mt-1" style="font-size: 0.7rem; line-height: 1.2;">Interview scheduled for Talent Operations & Vetting Officer.</div>
                                    </div>
                                    <div class="mt-2 pt-1 border-top">
                                        <a href="<?= $siteConfig->siteUrl ?>/quick-login?as=candidate" class="btn btn-outline-danger btn-sm w-100 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="fa fa-sign-in-alt me-1"></i> Sign In as Candidate
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- 8. Client Organisation Lead -->
                            <div class="col-sm-6">
                                <div class="p-2 border rounded-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-between hover-shadow">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                            <span class="badge px-2 py-0 text-white" style="background-color: #6366f1; font-size: 0.7rem;">Client Lead (Role 3)</span>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-muted small text-decoration-none" title="Pre-fill login form" onclick="fillDemo('client@tsigiro.co.zw')">
                                                <i class="fa fa-paste"></i> Pre-fill
                                            </button>
                                        </div>
                                        <div class="fw-bold text-dark small">Tinashe Gumbo</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">client@tsigiro.co.zw &bull; EcoSolutions</div>
                                        <div class="text-secondary small mt-1" style="font-size: 0.7rem; line-height: 1.2;">Client Organisation Owner, dedicated enterprise portal.</div>
                                    </div>
                                    <div class="mt-2 pt-1 border-top">
                                        <a href="<?= $siteConfig->siteUrl ?>/quick-login?as=client" class="btn btn-sm w-100 py-1 fw-semibold text-white" style="background-color: #6366f1; font-size: 0.75rem;">
                                            <i class="fa fa-sign-in-alt me-1"></i> Sign In as Client
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            <?php endif; ?>

        </div><!-- row -->
    </div><!-- container -->
</div><!-- az-signin-wrapper -->

<?php if ($showQuickLogin): ?>
<script>
function fillDemo(email) {
    var emailInput = document.getElementById('email');
    var passwordInput = document.getElementById('login_password');
    if (emailInput && passwordInput) {
        emailInput.value = email;
        passwordInput.value = 'Password123!';
        emailInput.focus();
        var msg = document.getElementById('msg');
        if (msg) {
            msg.className = 'my-2 text-primary small fw-semibold';
            msg.innerText = 'Credentials filled for ' + email + '. Click Sign In to submit.';
        }
    }
}
</script>
<?php endif; ?>

<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/login.js?v=<?php echo _ASSET_VERSION; ?>"></script>
