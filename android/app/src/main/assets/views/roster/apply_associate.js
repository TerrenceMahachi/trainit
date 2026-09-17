/**
 * Associate Talent Single-Page Application Controller
 * Matches https://portal.tsigiro.co.zw/opportunities/apply/associate
 */

window.init = async function(data) {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const form = document.getElementById('express-associate-form');
    const statusContainer = document.getElementById('express-associate-status-view');
    const alertBox = document.getElementById('express-alert-associate');
    const btnBack = document.getElementById('btn-back-associate');
    const btnCancel = document.getElementById('btn-cancel-associate');
    const btnSubmit = document.getElementById('btn-submit-associate');
    const submitText = document.getElementById('btn-submit-assoc-text');
    const cvInput = document.getElementById('field-assoc-cv');
    const cvZone = document.getElementById('cv-assoc-upload-zone');
    const cvBadge = document.getElementById('cv-assoc-file-badge');
    const cvLabel = document.getElementById('cv-assoc-label-text');
    const provinceSelect = document.getElementById('field-assoc-province');
    const functionSelect = document.getElementById('field-assoc-function');
    const empSelect = document.getElementById('field-assoc-employment');

    // Navigation Back
    function goBack() {
        if (window.viewHistory && window.viewHistory.length > 0) {
            window.goBack();
        } else {
            window.navigateTo('dashboard/home');
        }
    }
    if (btnBack) btnBack.onclick = (e) => { e.preventDefault(); goBack(); };
    if (btnCancel) btnCancel.onclick = (e) => { e.preventDefault(); goBack(); };

    // 1. Check if user already has an active / pending Associate application
    let pendingProfile = null;
    if (user && user.profiles) {
        pendingProfile = user.profiles.find(p => p.type_code === 'associate' && (p.status_code === 'pending' || p.status_code === 'approved' || parseInt(p.profilestatus, 10) === 2 || parseInt(p.profilestatus, 10) === 3));
    }

    let existingApp = null;
    try {
        const myAppsRes = await window.apiFetch('/api/mobile/roster/my-applications');
        if (myAppsRes && myAppsRes.status === 1 && myAppsRes.applications) {
            existingApp = myAppsRes.applications.find(a => (a.track_code === 'associate' || parseInt(a.track_id, 10) === 2) && (a.is_submitted || !a.is_draft));
        }
    } catch (_) {}

    if (pendingProfile || existingApp) {
        renderAssociateStatusView(existingApp, pendingProfile, user);
        return;
    }

    // Otherwise, initialize fresh form
    initAssociateForm();

    function renderAssociateStatusView(app, profile, u) {
        if (form) form.style.display = 'none';
        if (!statusContainer) return;

        statusContainer.style.display = 'block';

        const legalName = (app && app.legal_name) || (u && u.name) || 'Consultant';
        const email = (app && app.email) || (u && u.email) || '';
        const phone = (app && app.mobile_number) || '';
        const functionName = (app && app.function_name) || (profile && profile.display_title) || 'Associate Specialist';
        const city = (app && app.city) || 'Zimbabwe';
        const appNumber = (app && app.application_number) || 'TSG-ASC-PENDING';
        const submittedDate = app && app.created_at ? new Date(app.created_at).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) : 'Recently';

        statusContainer.innerHTML = `
            <div class="card" style="margin-bottom: 16px; border: 2px solid rgba(37, 99, 235, 0.3); border-radius: 14px; overflow: hidden; background: #ffffff;">
                <div style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); padding: 18px; color: #ffffff;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                        <span style="background: rgba(255, 255, 255, 0.2); color: #ffffff; font-size: 0.72rem; font-weight: 700; padding: 4px 10px; border-radius: 20px;">
                            ${escapeHtml(appNumber)}
                        </span>
                        <span style="background: #fef3c7; color: #b45309; font-weight: 800; font-size: 0.72rem; padding: 4px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px;">
                            <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #d97706;"></span>
                            Under Review
                        </span>
                    </div>
                    <h2 style="font-size: 1.15rem; font-weight: 800; margin: 0 0 4px 0; color: #ffffff;">
                        Associate Application Submitted
                    </h2>
                    <p style="font-size: 0.76rem; color: rgba(255, 255, 255, 0.85); margin: 0;">
                        Submitted on ${submittedDate} • Panel vetting in progress
                    </p>
                </div>

                <div style="padding: 18px;">
                    <!-- Stepper Progress Tracker -->
                    <div class="role-steps-tracker" style="margin-bottom: 18px;">
                        <div class="step-item step-completed">
                            <div class="step-dot" style="background: #2563eb; color: #ffffff;">✓</div>
                            <div class="step-content">
                                <div class="step-title" style="color: #2563eb;">Specialist Credentials & CV Submitted</div>
                                <div class="step-desc">Your executive profile and capabilities have been queued for panel vetting.</div>
                            </div>
                        </div>
                        <div class="step-item step-current">
                            <div class="step-dot" style="background: #f59e0b; color: #ffffff;">2</div>
                            <div class="step-content">
                                <div class="step-title" style="color: #b45309;">Panel Vetting & Technical Review</div>
                                <div class="step-desc">Tsigiro practice leads are reviewing your advisory domain and rate scope.</div>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-dot">3</div>
                            <div class="step-content">
                                <div class="step-title">Retainer Onboarding & Project Dispatch</div>
                                <div class="step-desc">Upon panel admission, consulting invitations and advisory deliverables will unlock.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Submitted Details Summary -->
                    <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 10px; padding: 14px; margin-bottom: 16px;">
                        <div style="font-weight: 800; font-size: 0.84rem; color: var(--text-main); margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                            <span>📋</span>
                            <span>Submitted Application Details</span>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.76rem;">
                            <div>
                                <div style="color: var(--text-muted); font-size: 0.7rem; font-weight: 600;">Consultant Name</div>
                                <div style="font-weight: 700; color: var(--text-main);">${escapeHtml(legalName)}</div>
                            </div>
                            <div>
                                <div style="color: var(--text-muted); font-size: 0.7rem; font-weight: 600;">Practice Domain</div>
                                <div style="font-weight: 700; color: #2563eb;">${escapeHtml(functionName)}</div>
                            </div>
                            <div>
                                <div style="color: var(--text-muted); font-size: 0.7rem; font-weight: 600;">Email Address</div>
                                <div style="font-weight: 600; color: var(--text-main); word-break: break-all;">${escapeHtml(email)}</div>
                            </div>
                            <div>
                                <div style="color: var(--text-muted); font-size: 0.7rem; font-weight: 600;">Base Location</div>
                                <div style="font-weight: 600; color: var(--text-main);">${escapeHtml(city)}</div>
                            </div>
                            ${phone ? `
                            <div>
                                <div style="color: var(--text-muted); font-size: 0.7rem; font-weight: 600;">Direct Phone</div>
                                <div style="font-weight: 600; color: var(--text-main);">${escapeHtml(phone)}</div>
                            </div>
                            ` : ''}
                            <div>
                                <div style="color: var(--text-muted); font-size: 0.7rem; font-weight: 600;">Executive CV</div>
                                <div style="font-weight: 700; color: #2563eb;">✓ Attached & Uploaded</div>
                            </div>
                        </div>
                    </div>

                    <div style="background: rgba(37, 99, 235, 0.08); border-left: 4px solid #2563eb; border-radius: 8px; padding: 10px 12px; margin-bottom: 18px; font-size: 0.74rem; color: #1e3a8a; line-height: 1.45;">
                        ℹ️ <strong>Status Update:</strong> Associate consultant dossiers are vetted on a weekly cycle. You will receive an invitation email once your profile is approved.
                    </div>

                    <!-- Action Buttons -->
                    <div id="assoc-status-actions" style="display: flex; flex-direction: column; gap: 10px;">
                        <button type="button" id="btn-revoke-assoc-app" style="width: 100%; padding: 13px; font-weight: 700; font-size: 0.86rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1.5px solid #ef4444; background: rgba(239, 68, 68, 0.08); color: #ef4444; transition: all 0.2s ease;">
                            <span style="font-size: 0.95rem;">🗑️</span>
                            <span>Revoke / Withdraw Application</span>
                        </button>

                        <button type="button" id="btn-return-home-assoc" style="width: 100%; padding: 12px; font-weight: 600; font-size: 0.84rem; border-radius: 10px; cursor: pointer; background: var(--card-bg); color: var(--text-main); border: 1px solid var(--border);">
                            Back to Home & Feed
                        </button>
                    </div>
                </div>
            </div>
        `;

        const homeBtn = statusContainer.querySelector('#btn-return-home-assoc');
        if (homeBtn) {
            homeBtn.onclick = () => window.navigateTo('dashboard/home');
        }

        const revokeBtn = statusContainer.querySelector('#btn-revoke-assoc-app');
        const actionsBox = statusContainer.querySelector('#assoc-status-actions');

        if (revokeBtn && actionsBox) {
            revokeBtn.onclick = () => {
                actionsBox.innerHTML = `
                    <div style="background: rgba(239, 68, 68, 0.08); border: 1.5px solid #ef4444; border-radius: 10px; padding: 14px; text-align: center;">
                        <div style="font-weight: 800; font-size: 0.88rem; color: #ef4444; margin-bottom: 6px;">
                            ⚠️ Confirm Application Revocation
                        </div>
                        <div style="font-size: 0.74rem; color: var(--text-main); margin-bottom: 12px; line-height: 1.4;">
                            Are you sure you want to withdraw your <strong>Associate Consultant Application</strong>? You can submit a fresh application at any time.
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" id="btn-cancel-revoke-asc" style="flex: 1; padding: 10px; font-size: 0.78rem; font-weight: 600; border-radius: 8px; cursor: pointer; background: var(--card-bg); color: var(--text-main); border: 1px solid var(--border);">
                                Keep Application
                            </button>
                            <button type="button" id="btn-confirm-revoke-asc" style="flex: 1; padding: 10px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; cursor: pointer; background: #ef4444; color: #ffffff; border: none;">
                                Yes, Revoke
                            </button>
                        </div>
                    </div>
                `;

                const cancelRevoke = actionsBox.querySelector('#btn-cancel-revoke-asc');
                if (cancelRevoke) {
                    cancelRevoke.onclick = () => {
                        renderAssociateStatusView(app, profile, u);
                    };
                }

                const confirmRevoke = actionsBox.querySelector('#btn-confirm-revoke-asc');
                if (confirmRevoke) {
                    confirmRevoke.onclick = async () => {
                        confirmRevoke.disabled = true;
                        confirmRevoke.style.opacity = '0.7';
                        confirmRevoke.innerHTML = `
                            <span style="display: inline-block; width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite;"></span>
                            <span>Withdrawing...</span>
                        `;

                        try {
                            const res = await window.apiFetch('/api/mobile/user/revoke-profile', {
                                method: 'POST',
                                body: {
                                    user_id: u ? u.id : '',
                                    type_code: 'associate',
                                    application_id: app ? app.id : ''
                                }
                            });

                            if (res && res.status === 1) {
                                if (window.showToast) {
                                    window.showToast('✓ Associate application withdrawn successfully.', 'success');
                                }
                                if (res.user) {
                                    window.setUser(res.user);
                                } else if (u.profiles) {
                                    u.profiles = u.profiles.filter(p => p.type_code !== 'associate');
                                    window.setUser(u);
                                }

                                statusContainer.style.display = 'none';
                                statusContainer.innerHTML = '';
                                if (form) form.style.display = 'block';
                                initAssociateForm();
                            } else {
                                confirmRevoke.disabled = false;
                                confirmRevoke.style.opacity = '1';
                                confirmRevoke.textContent = 'Yes, Revoke';
                                if (window.showToast) {
                                    window.showToast(res ? res.message : 'Failed to revoke application', 'error');
                                }
                            }
                        } catch (err) {
                            confirmRevoke.disabled = false;
                            confirmRevoke.style.opacity = '1';
                            confirmRevoke.textContent = 'Yes, Revoke';
                            if (window.showToast) {
                                window.showToast('Network error while revoking application', 'error');
                            }
                        }
                    };
                }
            };
        }
    }

    function initAssociateForm() {
        if (!form) return;
        form.style.display = 'block';

        // Pre-fill User Info
        const nameInput = document.getElementById('field-assoc-legal-name');
        const emailInput = document.getElementById('field-assoc-email');
        if (nameInput && user.name) nameInput.value = user.name;
        if (emailInput && user.email) {
            emailInput.value = user.email;
            emailInput.readOnly = true;
        }

        // Load Dropdown Options (Provinces, Functions, Employment Statuses)
        (async () => {
            try {
                const configRes = await window.apiFetch('/api/mobile/roster/config');
                if (configRes && configRes.status === 1) {
                    if (provinceSelect && configRes.provinces) {
                        provinceSelect.innerHTML = '<option value="">-- Province / Location --</option>' +
                            configRes.provinces.map(p => `<option value="${p.id}">${escapeHtml(p.name)}</option>`).join('');
                    }
                    if (functionSelect && configRes.functions) {
                        functionSelect.innerHTML = '<option value="">-- Select Practice Area --</option>' +
                            configRes.functions.map(f => `<option value="${f.id}">${escapeHtml(f.name)}</option>`).join('');
                    }
                    if (empSelect) {
                        empSelect.innerHTML = `
                            <option value="">-- Select Status --</option>
                            <option value="1">Employed Full-time (Moonlighting / Advisory)</option>
                            <option value="2">Employed Part-time</option>
                            <option value="3">Self-employed / Consulting Firm Principal</option>
                            <option value="4">Between Assignments / Immediately Available</option>
                            <option value="5">Retired / Senior Executive Advisor</option>
                        `;
                    }
                }
            } catch (err) {
                console.warn('Failed to load roster config dropdowns:', err);
            }
        })();

        // Pre-fill Personal Details from Profile
        (async () => {
            try {
                const detailsRes = await window.apiFetch('/api/mobile/user/personal-details');
                if (detailsRes && detailsRes.status === 1 && detailsRes.details) {
                    const d = detailsRes.details;
                    const legalEl = document.getElementById('field-assoc-legal-name');
                    const prefEl = document.getElementById('field-assoc-preferred-name');
                    const mobEl = document.getElementById('field-assoc-mobile');
                    const cityEl = document.getElementById('field-assoc-city');

                    if (legalEl && !legalEl.value && d.legal_name) legalEl.value = d.legal_name;
                    if (prefEl && !prefEl.value && d.preferred_name) prefEl.value = d.preferred_name;
                    if (mobEl && !mobEl.value && d.mobile_number) mobEl.value = d.mobile_number;
                    if (cityEl && !cityEl.value && d.city) cityEl.value = d.city;
                    if (provinceSelect && d.zimprovince) provinceSelect.value = d.zimprovince;
                }
            } catch (err) {
                console.warn('Personal details fetch error:', err);
            }
        })();

        // File Upload Handler
        if (cvZone && cvInput) {
            cvZone.onclick = () => cvInput.click();
            cvInput.onchange = () => {
                if (cvInput.files && cvInput.files[0]) {
                    const file = cvInput.files[0];
                    const sizeKb = Math.round(file.size / 1024);
                    if (file.size > 5 * 1024 * 1024) {
                        alert('File size exceeds 5MB limit. Please choose a smaller PDF or Word document.');
                        cvInput.value = '';
                        cvBadge.style.display = 'none';
                        cvLabel.textContent = 'Tap to Choose CV or Capability Statement';
                        return;
                    }
                    cvLabel.textContent = 'Attached File:';
                    cvBadge.textContent = `✓ ${file.name} (${sizeKb} KB)`;
                    cvBadge.style.display = 'inline-block';
                    cvZone.style.borderColor = '#2563eb';
                    cvZone.style.background = 'rgba(37, 99, 235, 0.05)';
                } else {
                    cvBadge.style.display = 'none';
                    cvLabel.textContent = 'Tap to Choose CV or Capability Statement';
                    cvZone.style.borderColor = '#cbd5e1';
                    cvZone.style.background = '#f8fafc';
                }
            };
        }

        // Form Submit
        form.onsubmit = async (e) => {
            e.preventDefault();

            // Client Validation
            if (!cvInput || !cvInput.files || cvInput.files.length === 0) {
                const cvMessage = 'Please attach your Executive CV or Capability Statement to submit.';
                if (alertBox) {
                    alertBox.style.display = 'block';
                    alertBox.style.background = '#fef2f2';
                    alertBox.style.color = '#991b1b';
                    alertBox.style.border = '1px solid #fecaca';
                    alertBox.textContent = cvMessage;
                }
                if (window.showToast) {
                    window.showToast(cvMessage, 'warning');
                }
                if (cvZone) {
                    cvZone.style.borderColor = '#ef4444';
                    cvZone.style.background = '#fef2f2';
                    cvZone.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            btnSubmit.disabled = true;
            btnSubmit.style.opacity = '0.7';
            if (submitText) submitText.textContent = 'Submitting Specialist Dossier & CV...';
            if (alertBox) alertBox.style.display = 'none';

            try {
                const formData = new FormData(form);
                if (user && user.id) {
                    formData.append('user_id', user.id);
                }

                const res = await fetch(`${window.API_BASE}/api/mobile/opportunities/apply/express`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();

                if (data.status === 1) {
                    if (window.showToast) {
                        window.showToast('Associate application submitted successfully!', 'success');
                    }

                    // Refresh user profiles cache
                    try {
                        const profRes = await window.apiFetch('/api/mobile/user/profiles');
                        if (profRes && profRes.status === 1 && profRes.profiles) {
                            user.profiles = profRes.profiles;
                            window.setUser(user);
                        }
                    } catch (_) {}

                    // Render status view immediately
                    renderAssociateStatusView({
                        legal_name: formData.get('legal_name'),
                        email: formData.get('email'),
                        mobile_number: formData.get('mobile_number'),
                        function_name: functionSelect ? (functionSelect.options[functionSelect.selectedIndex]?.text || 'Associate Consultant') : 'Associate Consultant',
                        city: formData.get('city'),
                        application_number: data.application_number || 'TSG-ASC-NEW',
                        created_at: new Date().toISOString()
                    }, { status_code: 'pending', display_title: 'Associate Consultant' }, user);
                } else {
                    throw new Error(data.message || data.msg || 'Submission failed. Please check your details.');
                }
            } catch (err) {
                console.error('Express associate submit error:', err);
                if (alertBox) {
                    alertBox.style.display = 'block';
                    alertBox.style.background = '#fef2f2';
                    alertBox.style.color = '#991b1b';
                    alertBox.style.border = '1px solid #fecaca';
                    alertBox.textContent = err.message || 'Error submitting application. Please try again.';
                }
                btnSubmit.disabled = false;
                btnSubmit.style.opacity = '1';
                if (submitText) submitText.textContent = 'Submit Consultant Application & CV';
                alertBox.scrollIntoView({ behavior: 'smooth' });
            }
        };
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};
