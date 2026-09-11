@extends('layouts.account')

<div class="az-signin-wrapper">
    <div class="container my-auto py-4" style="max-width: 1160px;">
        <div class="row g-4 align-items-stretch justify-content-center">
            
            <!-- Standard Sign-In Form Column -->
            <div class="col-lg-5 col-md-10 d-flex">
                <div class="az-card-signin w-100 h-100 shadow-sm">
                    <div class="az-signin-header text-center">
                        <a href="<?php echo $siteConfig->siteUrl; ?>/home">
                            <img class="account-logo mx-auto mb-4"
                                src="<?php echo $siteConfig->assetsUrl; ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>"
                                alt="<?= htmlspecialchars(_SITE) ?>" />
                        </a>
                        <h2 class="h4 mb-2 fw-bold text-dark">Welcome back</h2>
                        <p class="text-muted small mb-4">Sign in to your Tsigiro Portal workspace</p>

                        <form id="_form" class="text-start">
                            <div class="input-group mb-3 rounded-3 border p-1 px-3 bg-white">
                                <input id="email" type="text" name="email" class="form-control bg-transparent border-0 text-dark"
                                    placeholder="Email address" aria-label="Username" autocomplete="username">
                                <span class="input-group-text border-0 bg-transparent text-muted"><i
                                        class="fa fa-envelope"></i></span>
                            </div>

                            <div class="input-group border rounded-3 p-1 mt-2 px-3 bg-white">
                                <input id="login_password" name="password" type="password"
                                    class="form-control bg-transparent border-0 text-dark" placeholder="Password" aria-label="Password"
                                    autocomplete="current-password">
                                <span class="input-group-text border-0 bg-transparent text-muted"><i
                                        class="fa fa-eye show-p" data-target="login_password" style="cursor: pointer;"></i></span>
                            </div>

                            <p id='msg' class="my-2 text-danger small fw-semibold"><?= $data['status']; ?></p>

                            <button type="button" onclick="login()" id="submit_btn" class="btn btn-lg button1 rounded-pill w-100 mx-auto fw-semibold mt-2 shadow-sm">Sign In</button>

                            <div class="mt-4 pt-2 border-top text-center">
                                <p class="mb-1">
                                    <a class="auth-link small text-decoration-none fw-semibold" href="<?php echo $siteConfig->siteUrl; ?>/reset">Forgot your password?</a>
                                </p>
                                <p class="mb-0">
                                    <a class="auth-link small text-decoration-none" href="<?php echo $siteConfig->siteUrl; ?>/opportunities">Explore open opportunities &rarr;</a>
                                </p>
                            </div>
                        </form>
                    </div><!-- az-signin-header -->
                </div><!-- az-card-signin -->
            </div>

            <!-- Testing & Demo Accounts Column -->
            <div class="col-lg-7 col-md-10 d-flex">
                <div class="card border-0 shadow-sm rounded-4 w-100 h-100 p-4" style="background: #ffffff; border-top: 4px solid #2563eb !important;">
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

        </div><!-- row -->
    </div><!-- container -->
</div><!-- az-signin-wrapper -->

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

<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/login.js?v=<?php echo _ASSET_VERSION; ?>"></script>
