window.init = async function(passedUser) {
    const user = passedUser || window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const nameEl = document.getElementById('dash-user-name');
    const roleBadgeEl = document.getElementById('dash-role-badge');
    const contextBadgeEl = document.getElementById('dash-context-badge');
    const statsGridEl = document.getElementById('dash-stats-grid');
    const itemsContainerEl = document.getElementById('dash-items-container');
    const sectionTitleEl = document.getElementById('dash-section-title');
    const viewAllLink = document.getElementById('dash-view-all-link');
    const fabBtn = document.getElementById('dash-fab-new');

    const persona = user.persona || '';
    // Reviewer = admin/staff/service-manager/billing/vetting, keyed on the real
    // role where available. Only reviewers may see or open the account-request
    // approvals surface; a general user must never be shown another applicant's
    // request or the approve/decline controls.
    const reviewerRoles = [1, 6, 7, 8];
    const reviewerPersonas = ['admin', 'staff', 'manager', 'finance', 'vetting'];
    const isReviewer = reviewerPersonas.includes(persona)
        || (user.role != null && reviewerRoles.includes(parseInt(user.role, 10)));

    if (nameEl) nameEl.textContent = user.name;
    if (roleBadgeEl) roleBadgeEl.textContent = user.role_name;

    // Tsigiro Talent Roster CTA
    const rosterCta = document.getElementById('dash-roster-cta');
    if (rosterCta) {
        rosterCta.addEventListener('click', () => {
            window.navigateTo('roster/choose-track');
        });
    }

    // Show FAB for Client & Admin
    if (fabBtn && (persona === 'client' || persona === 'admin')) {
        fabBtn.style.display = 'flex';
        fabBtn.addEventListener('click', () => {
            window.navigateTo('requests/new');
        });
    }

    if (viewAllLink) {
        viewAllLink.addEventListener('click', (e) => {
            e.preventDefault();
            const isGen = (persona === 'candidate' || (user.active_profile && user.active_profile.type_code === 'general'));
            if (isGen) {
                window.navigateTo('profile/view');
            } else {
                window.navigateTo('requests/list');
            }
        });
    }

    function renderDashboard(data) {
        if (!data || data.status !== 1) return;

        if (data.context) {
            const ctxLabel = data.context.organization || data.context.specialty || '';
            if (ctxLabel && contextBadgeEl) {
                contextBadgeEl.textContent = ctxLabel;
                contextBadgeEl.style.display = 'inline-block';
            }
        }

        // Check if user is in General User / Candidate mode (new user requesting elevated accounts)
        // Reviewers (admin, staff, manager, vetting, finance) should NEVER be treated as general users.
        const isGeneralUser = !isReviewer && (persona === 'candidate' || (user.active_profile && user.active_profile.type_code === 'general'));
        const rolesHeaderEl = document.getElementById('dash-roles-header');

        if (isGeneralUser) {
            if (rolesHeaderEl) rolesHeaderEl.style.display = 'flex';
            const userProfiles = (data.user && data.user.profiles) || user.profiles || [];
            renderRoleActionButtons(statsGridEl, userProfiles);
        } else {
            if (rolesHeaderEl) rolesHeaderEl.style.display = 'none';
            // Render stats for specialized personas (Client, Associate, Apprentice, Admin)
            if (data.stats && statsGridEl) {
                statsGridEl.innerHTML = data.stats.map(s => {
                    const isInvoice = s.label.includes('Invoice');
                    const isRoleReq = s.action === 'role_requests' || s.label.includes('Role Request');
                    const cursorStyle = (isInvoice || isRoleReq) ? 'cursor: pointer;' : '';
                    const dataAttr = isInvoice ? 'data-action="invoices"' : (isRoleReq ? 'data-action="role_requests"' : '');
                    return `
                        <div class="stat-box" style="${cursorStyle}" ${dataAttr}>
                            <div class="stat-value" style="color: ${s.color || '#0f172a'}">${s.value}</div>
                            <div class="stat-label">${s.label}</div>
                        </div>
                    `;
                }).join('');

                // Click on Invoices box navigates to invoices ledger
                statsGridEl.querySelectorAll('[data-action="invoices"]').forEach(box => {
                    box.addEventListener('click', () => {
                        window.navigateTo('invoices/list');
                    });
                });

                // Click on Role Requests box scrolls to approvals queue
                statsGridEl.querySelectorAll('[data-action="role_requests"]').forEach(box => {
                    box.addEventListener('click', () => {
                        const s = document.getElementById('dash-admin-approvals-section');
                        if (s) s.scrollIntoView({ behavior: 'smooth' });
                    });
                });
            }
        }

        // Render Admin Role Approvals Queue Section (for Admin / Staff personas)
        const adminApprovalsSectionEl = document.getElementById('dash-admin-approvals-section');
        const adminApprovalsCountEl = document.getElementById('dash-admin-approvals-count');
        const adminApprovalsContainerEl = document.getElementById('dash-admin-approvals-container');

        if (adminApprovalsSectionEl && adminApprovalsContainerEl) {
            const pendingReqs = data.pending_role_requests || [];
            if (pendingReqs.length > 0 && isReviewer) {
                adminApprovalsSectionEl.style.display = 'block';
                if (adminApprovalsCountEl) {
                    adminApprovalsCountEl.textContent = pendingReqs.length + ' Pending';
                }

                const approvalsHeader = document.getElementById('dash-admin-approvals-header');
                if (approvalsHeader) {
                    approvalsHeader.onclick = () => {
                        window.navigateTo('admin/roster-queue');
                    };
                }

                adminApprovalsContainerEl.innerHTML = pendingReqs.map(req => {
                    let roleIcon = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>`;
                    let roleColor = '#159b75';
                    let roleBg = 'rgba(21, 155, 117, 0.12)';
                    const rCode = (req.type_code || '').toLowerCase();
                    if (rCode === 'associate') {
                        roleIcon = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>`;
                        roleColor = '#4f46e5';
                        roleBg = 'rgba(99, 102, 241, 0.12)';
                    } else if (rCode === 'staff') {
                        roleIcon = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`;
                        roleColor = '#d97706';
                        roleBg = 'rgba(245, 158, 11, 0.14)';
                    }

                    const dateStr = req.reg_date ? req.reg_date.substring(0, 10) : 'Recently';
                    const notesStr = req.request_notes ? `"${escapeHtml(req.request_notes)}"` : '1-Click Profile Application';

                    return `
                        <div class="item-card admin-review-card" data-profile-id="${req.profile_id}" style="cursor: pointer; border-left: 4px solid #f59e0b; margin-bottom: 10px;">
                            <div class="item-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: ${roleBg}; color: ${roleColor}; flex-shrink: 0;">
                                        ${roleIcon}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-main);">${escapeHtml(req.user_name)}</div>
                                        <div style="font-size: 0.7rem; color: var(--text-muted);">${escapeHtml(req.user_email)}</div>
                                    </div>
                                </div>
                                <span class="badge badge-yellow">Pending Review</span>
                            </div>
                            <div style="font-weight: 600; font-size: 0.82rem; color: var(--text-main); margin-bottom: 4px;">
                                Requested: <span style="color: var(--primary);">${escapeHtml(req.display_title || req.type_name)}</span>
                            </div>
                            <div style="font-size: 0.74rem; color: var(--text-muted); font-style: italic; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.35;">
                                ${notesStr}
                            </div>
                            <div class="item-meta" style="border-top: 1px dashed var(--border); padding-top: 8px; margin-top: 4px;">
                                <span>Submitted: ${dateStr}</span>
                                <span style="color: var(--primary); font-weight: 700; display: flex; align-items: center; gap: 4px;">Review & Approve →</span>
                            </div>
                        </div>
                    `;
                }).join('');

                adminApprovalsContainerEl.querySelectorAll('.admin-review-card').forEach(card => {
                    card.addEventListener('click', () => {
                        const pId = parseInt(card.getAttribute('data-profile-id'), 10);
                        const reqData = pendingReqs.find(r => parseInt(r.profile_id, 10) === pId);
                        if (reqData) {
                            openAdminReviewModal(reqData);
                        }
                    });
                });
            } else {
                adminApprovalsSectionEl.style.display = 'none';
            }
        }

        // Adjust section title and view all link by persona
        if (sectionTitleEl) {
            if (isGeneralUser) {
                sectionTitleEl.textContent = 'Application & Vetting Pipeline';
                if (viewAllLink) viewAllLink.textContent = 'Manage Profiles →';
            } else if (persona === 'apprentice') {
                sectionTitleEl.textContent = 'Apprenticeship Tasks & Assignments';
                if (viewAllLink) viewAllLink.textContent = 'View All Tasks →';
            } else if (persona === 'associate') {
                sectionTitleEl.textContent = 'Active Task Assignments';
                if (viewAllLink) viewAllLink.textContent = 'View All Tasks →';
            } else if (persona === 'client') {
                sectionTitleEl.textContent = 'Recent Service Requests';
                if (viewAllLink) viewAllLink.textContent = 'View All Requests →';
            } else {
                sectionTitleEl.textContent = 'Triage & Service Queue';
                if (viewAllLink) viewAllLink.textContent = 'View Ledger →';
            }
        }

        // Render recent items
        if (data.recent_items && data.recent_items.length > 0 && itemsContainerEl) {
            itemsContainerEl.innerHTML = data.recent_items.map(item => {
                if (isGeneralUser) {
                    const isApproved = (parseInt(item.can_access_portal, 10) === 1 || item.status_code === 'approved' || parseInt(item.profilestatus, 10) === 3);
                    const isDeclined = (item.status_code === 'rejected' || parseInt(item.profilestatus, 10) === 4);
                    
                    let statusBadgeClass = 'badge-yellow';
                    let statusBadgeText = '⏳ Under Review';
                    if (isApproved) {
                        statusBadgeClass = 'badge-green';
                        statusBadgeText = '✓ Active';
                    } else if (isDeclined) {
                        statusBadgeClass = 'badge-red';
                        statusBadgeText = 'Declined';
                    }

                    // Role icon & color
                    let iconSvg = '';
                    let iconColor = '#159b75';
                    let iconBg = 'rgba(21, 155, 117, 0.12)';
                    const roleCode = (item.type_code || '').toLowerCase();
                    if (roleCode === 'apprentice') {
                        iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>`;
                        iconColor = '#159b75';
                        iconBg = 'rgba(21, 155, 117, 0.12)';
                    } else if (roleCode === 'associate') {
                        iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>`;
                        iconColor = '#4f46e5';
                        iconBg = 'rgba(99, 102, 241, 0.12)';
                    } else {
                        iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`;
                        iconColor = '#d97706';
                        iconBg = 'rgba(245, 158, 11, 0.14)';
                    }

                    const dateStr = item.reg_date ? item.reg_date.substring(0, 10) : 'Recently';

                    return `
                        <div class="item-card role-app-card" data-role-code="${roleCode}" style="cursor: pointer;">
                            <div class="item-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; background: ${iconBg}; color: ${iconColor}; flex-shrink: 0;">
                                        ${iconSvg}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 0.9rem; color: var(--text-main); line-height: 1.25;">${item.type_name || item.display_title || 'Role Application'}</div>
                                        <div style="font-size: 0.72rem; color: var(--text-muted);">${item.display_title ? item.display_title : (roleCode ? roleCode.toUpperCase() : '')}</div>
                                    </div>
                                </div>
                                <span class="badge ${statusBadgeClass}">${statusBadgeText}</span>
                            </div>
                            ${item.request_notes ? `<div style="font-size: 0.76rem; color: var(--text-muted); margin-bottom: 8px; font-style: italic; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.35;">"${item.request_notes}"</div>` : ''}
                            <div class="item-meta" style="border-top: 1px dashed var(--border); padding-top: 8px; margin-top: 6px;">
                                <span>Applied: ${dateStr}</span>
                                <span style="color: var(--primary); font-weight: 600; display: flex; align-items: center; gap: 4px;">Review Timeline / Manage →</span>
                            </div>
                        </div>
                    `;
                } else {
                    const statusBadge = getStatusBadge(item.status);
                    return `
                        <div class="item-card" data-request-id="${item.iD}">
                            <div class="item-header">
                                <span class="item-code">${item.request_number}</span>
                                <span class="badge ${statusBadge.class}">${item.status_name || statusBadge.label}</span>
                            </div>
                            <div class="item-title">${item.title}</div>
                            <div class="item-meta">
                                <span>${item.client_name || item.priority_name || 'Standard SLA'}</span>
                                <span>Due: ${item.desired_due_date || item.due_date || 'TBD'}</span>
                            </div>
                        </div>
                    `;
                }
            }).join('');

            // Click handlers
            itemsContainerEl.querySelectorAll('[data-request-id]').forEach(card => {
                card.addEventListener('click', () => {
                    const reqId = card.getAttribute('data-request-id');
                    if (reqId) window.navigateTo('requests/view', { id: reqId });
                });
            });

            itemsContainerEl.querySelectorAll('.role-app-card').forEach(card => {
                card.addEventListener('click', () => {
                    const roleCode = card.getAttribute('data-role-code');
                    if (roleCode === 'apprentice') {
                        window.navigateTo('opportunities/apply/apprentice', { track_id: 1, track_code: 'apprentice' });
                    } else if (roleCode === 'associate') {
                        window.navigateTo('opportunities/apply/associate', { track_id: 2, track_code: 'associate' });
                    } else if (roleCode && window.openRoleCategoryModal) {
                        window.openRoleCategoryModal(roleCode);
                    }
                });
            });

        } else if (itemsContainerEl) {
            if (isGeneralUser) {
                const comp = (data.user && data.user.profile_completion) || (user.profile_completion) || {};
                const isPersonalOk = !!comp.personal_complete;
                const isQualsOk = (comp.qualifications_count > 0);

                itemsContainerEl.innerHTML = `
                    <div class="empty-state" style="padding: 18px 16px; text-align: left; background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius-md); box-shadow: var(--shadow-sm);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <div style="font-size: 1.5rem;">🚀</div>
                            <div>
                                <div style="font-weight: 700; font-size: 0.92rem; color: var(--text-main);">Start Your Career Application</div>
                                <div style="font-size: 0.74rem; color: var(--text-muted);">Request an elevated account profile to unlock workspaces & assignments.</div>
                            </div>
                        </div>

                        <div style="background: var(--bg); border: 1px solid var(--border); border-radius: 8px; padding: 10px 12px; margin: 12px 0;">
                            <div style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px;">Profile Readiness for 1-Click Apply:</div>
                            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                                <span style="font-size: 0.74rem; display: inline-flex; align-items: center; gap: 4px; color: ${isPersonalOk ? '#059669' : '#d97706'}; font-weight: 600;">
                                    ${isPersonalOk ? '✓' : '⚠️'} Personal Details ${isPersonalOk ? 'Complete' : 'Pending'}
                                </span>
                                <span style="font-size: 0.74rem; display: inline-flex; align-items: center; gap: 4px; color: ${isQualsOk ? '#059669' : '#d97706'}; font-weight: 600;">
                                    ${isQualsOk ? '✓' : '⚠️'} Qualifications ${isQualsOk ? `(${comp.qualifications_count} Added)` : 'Pending'}
                                </span>
                            </div>
                        </div>

                        <div style="margin-top: 14px; margin-bottom: 8px;">
                            <button type="button" id="dash-onboarding-full-apply-btn" class="btn-primary" style="width: 100%; padding: 10px 14px; font-weight: 700; font-size: 0.82rem; border-radius: 8px; background: linear-gradient(135deg, #159b75 0%, #0e7256 100%); color: #ffffff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                <span>🚀 Start Full Roster Application →</span>
                            </button>
                        </div>

                        <div style="display: flex; gap: 8px; margin-top: 6px;">
                            <button type="button" id="dash-onboarding-quals-btn" style="flex: 1; padding: 8px 10px; font-size: 0.76rem; font-weight: 600; border-radius: 8px; border: 1px solid var(--border); background: var(--card-bg); color: var(--text-main); cursor: pointer;">
                                ${isQualsOk ? 'Manage Qualifications' : '+ Add Qualifications'}
                            </button>
                            <button type="button" id="dash-onboarding-personal-btn" style="flex: 1; padding: 8px 10px; font-size: 0.76rem; font-weight: 600; border-radius: 8px; border: 1px solid var(--border); background: var(--card-bg); color: var(--text-main); cursor: pointer;">
                                ${isPersonalOk ? 'View Personal Info' : 'Complete Personal Info'}
                            </button>
                        </div>
                    </div>
                `;

                const btnApply = document.getElementById('dash-onboarding-full-apply-btn');
                if (btnApply) btnApply.addEventListener('click', () => window.navigateTo('roster/choose-track'));
                const btnQ = document.getElementById('dash-onboarding-quals-btn');
                if (btnQ) btnQ.addEventListener('click', () => window.navigateTo('qualifications/list'));
                const btnP = document.getElementById('dash-onboarding-personal-btn');
                if (btnP) btnP.addEventListener('click', () => window.navigateTo('personal/form'));
            } else {
                let emptyMsg = 'No active records found for this workspace.';
                if (persona === 'apprentice') emptyMsg = 'No apprenticeship assignments assigned yet.';
                else if (persona === 'associate') emptyMsg = 'No active task assignments found.';
                else if (persona === 'client') emptyMsg = 'No active service requests submitted yet.';
                else if (persona === 'admin' || persona === 'staff') emptyMsg = 'No service requests in triage queue.';

                itemsContainerEl.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">📂</div>
                        <p>${emptyMsg}</p>
                    </div>
                `;
            }
        }
    }

    // Immediate hydration from persistent cache
    const cacheKey = 'dashboard_' + user.id;
    const cachedData = window.getCache(cacheKey);
    let hasRenderedFromCache = false;
    if (cachedData) {
        renderDashboard(cachedData);
        hasRenderedFromCache = true;
    }

    // Refresh from live network
    try {
        const response = await fetch(`${window.API_BASE}/api/mobile/dashboard?user_id=${user.id}`, {
            method: 'GET',
            credentials: 'include'
        });

        if (!response.ok) {
            if (response.status === 401) {
                window.clearUser();
                window.navigateTo('auth/login');
                return;
            }
            throw new Error(`Server returned ${response.status}`);
        }

        const data = await response.json();

        if (data.status === 1) {
            window.setCache(cacheKey, data);
            window.updateNetworkBanner(false);
            if (data.user) {
                window.setUser(data.user);
            }
            renderDashboard(data);
        }
    } catch (err) {
        console.warn('Network dashboard fetch failed:', err);
        window.updateNetworkBanner(true);
        if (hasRenderedFromCache) {
            window.showToast('Offline — viewing cached dashboard', 'warning');
        } else if (itemsContainerEl) {
            itemsContainerEl.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">📶</div>
                    <p>Offline: Connect to network to view dashboard data.</p>
                </div>
            `;
        }
    }
};

function getStatusBadge(statusId) {
    const s = parseInt(statusId, 10);
    switch (s) {
        case 1: return { label: 'New', class: 'badge-blue' };
        case 2: return { label: 'Triaged', class: 'badge-yellow' };
        case 3: return { label: 'In Progress', class: 'badge-blue' };
        case 4: return { label: 'Waiting Client', class: 'badge-purple' };
        case 5: return { label: 'Delivered', class: 'badge-green' };
        case 6: return { label: 'Closed', class: 'badge-green' };
        default: return { label: 'Active', class: 'badge-blue' };
    }
}

function renderRoleActionButtons(container, userProfiles) {
    if (!container) return;

    const roles = [
        {
            code: 'apprentice',
            name: 'Apprentice',
            cardClass: 'role-card-apprentice',
            iconClass: 'icon-box-apprentice',
            svg: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>`
        },
        {
            code: 'associate',
            name: 'Associate',
            cardClass: 'role-card-associate',
            iconClass: 'icon-box-associate',
            svg: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>`
        },
        {
            code: 'staff',
            name: 'Staff',
            cardClass: 'role-card-staff',
            iconClass: 'icon-box-staff',
            svg: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`
        }
    ];

    container.innerHTML = roles.map(r => {
        const match = (userProfiles || []).find(p => p.type_code === r.code);
        let statusLabel = '+ Apply';
        let statusClass = 'status-pill-apply';

        if (match) {
            const isApproved = (parseInt(match.can_access_portal, 10) === 1 || match.status_code === 'approved' || parseInt(match.profilestatus, 10) === 3);
            if (isApproved) {
                statusLabel = '✓ Active';
                statusClass = 'status-pill-active';
            } else if (match.status_code === 'rejected' || parseInt(match.profilestatus, 10) === 4) {
                statusLabel = 'Declined';
                statusClass = 'status-pill-pending';
            } else {
                statusLabel = '⏳ Review';
                statusClass = 'status-pill-pending';
            }
        }

        return `
            <div class="role-action-btn ${r.cardClass}" data-role-code="${r.code}" title="Apply or manage ${r.name} account">
                <div class="role-icon-box ${r.iconClass}">
                    ${r.svg}
                </div>
                <div class="role-card-name">${r.name}</div>
                <span class="role-status-pill ${statusClass}">${statusLabel}</span>
            </div>
        `;
    }).join('');

    container.querySelectorAll('.role-action-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const roleCode = btn.getAttribute('data-role-code');
            if (roleCode === 'apprentice') {
                window.navigateTo('opportunities/apply/apprentice', { track_id: 1, track_code: 'apprentice' });
            } else if (roleCode === 'associate') {
                window.navigateTo('opportunities/apply/associate', { track_id: 2, track_code: 'associate' });
            } else if (roleCode && window.openRoleCategoryModal) {
                window.openRoleCategoryModal(roleCode);
            }
        });
    });
}
window.renderRoleActionButtons = renderRoleActionButtons;

function openAdminReviewModal(req) {
    // Hard guard: only a privileged reviewer may ever open the approval sheet,
    // regardless of how this was reached. Non-reviewers get nothing.
    const u = window.getUser && window.getUser();
    const reviewerRoles = [1, 6, 7, 8];
    const reviewerPersonas = ['admin', 'staff', 'manager', 'finance', 'vetting'];
    const allowed = u && (reviewerPersonas.includes(u.persona)
        || (u.role != null && reviewerRoles.includes(parseInt(u.role, 10))));
    if (!allowed) {
        if (typeof window.closeAdminReviewModal === 'function') {
            window.closeAdminReviewModal();
        }
        if (window.showToast) window.showToast('You do not have permission to review account requests.', 'warning');
        return;
    }

    let modal = document.getElementById('modal-admin-review');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'modal-admin-review';
        modal.className = 'modal-backdrop';
        modal.style.display = 'none';
        modal.innerHTML = `
            <div class="modal-sheet" style="max-height: 90vh; overflow-y: auto;">
                <div class="modal-handle"></div>
                <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                    <div>
                        <h3 id="admin-review-title" style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--text-main);">Review Account Request</h3>
                        <div id="admin-review-subtitle" style="font-size: 0.75rem; color: var(--text-muted);">Vetting & Credentials Clearance</div>
                    </div>
                    <button type="button" id="admin-review-close-btn" class="modal-close-btn" style="background: none; border: none; font-size: 1.4rem; color: var(--text-muted); cursor: pointer; padding: 4px 8px;">✕</button>
                </div>
                <div id="admin-review-body"></div>
            </div>
        `;
        document.body.appendChild(modal);
    } else {
        modal.className = 'modal-backdrop';
    }

    // Ensure close and backdrop listeners
    const closeBtn = modal.querySelector('#admin-review-close-btn');
    if (closeBtn) {
        const doClose = (e) => {
            if (e) e.stopPropagation();
            if (typeof window.closeAdminReviewModal === 'function') {
                window.closeAdminReviewModal();
            } else {
                modal.style.display = 'none';
            }
        };
        closeBtn.onclick = doClose;
        closeBtn.ontouchend = doClose;
    }
    modal.onclick = (e) => {
        if (e.target === modal) {
            if (typeof window.closeAdminReviewModal === 'function') {
                window.closeAdminReviewModal();
            } else {
                modal.style.display = 'none';
            }
        }
    };

    const bodyEl = modal.querySelector('#admin-review-body');
    const phone = (req.personal && req.personal.mobile_number) || 'On file';
    const city = (req.personal && req.personal.city) || 'Harare';
    const qualsCount = req.qualifications_count || (req.qualifications ? req.qualifications.length : 0);

    const dossierLinkHtml = req.roster_app_id ? `
        <div style="margin-bottom: 12px;">
            <button type="button" id="admin-btn-view-dossier" style="width: 100%; padding: 10px 12px; font-size: 0.8rem; font-weight: 700; border-radius: 8px; border: 1px solid var(--primary); background: rgba(21, 155, 117, 0.08); color: var(--primary); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                📋 Open Full Candidate Dossier &amp; Rubric Scoring →
            </button>
        </div>
    ` : '';

    bodyEl.innerHTML = `
        <div style="background: var(--bg); border: 1px solid var(--border); border-radius: 12px; padding: 14px; margin-bottom: 14px;">
            <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-main);">${escapeHtml(req.user_name)}</div>
            <div style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 8px;">${escapeHtml(req.user_email)} &bull; ${escapeHtml(phone)}</div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <span class="badge badge-blue">Location: ${escapeHtml(city)}</span>
                <span class="badge badge-green">${qualsCount} Qualifications Verified</span>
            </div>
        </div>

        <div class="detail-summary-card" style="margin-bottom: 12px;">
            <div class="detail-label">Requested Profile / Track</div>
            <div class="detail-value" style="font-weight: 700; color: var(--primary);">
                ${escapeHtml(req.type_name)} — ${escapeHtml(req.display_title || req.type_name)}
            </div>
        </div>

        <div class="detail-summary-card" style="margin-bottom: 14px;">
            <div class="detail-label">Submitted Motivation / Notes</div>
            <div class="detail-value" style="font-size: 0.8rem; line-height: 1.4; color: var(--text-main);">
                ${escapeHtml(req.request_notes || '1-Click Profile Application with verified credentials dossier.')}
            </div>
        </div>

        ${dossierLinkHtml}

        <div style="margin-bottom: 14px;">
            <label class="form-label" style="font-size: 0.76rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px; display: block;">Reviewer Feedback / Approval Notes (Optional)</label>
            <textarea id="admin-reviewer-notes-input" class="form-input" rows="2" placeholder="e.g. Credentials cleared. Approved for active duty." style="width: 100%; border-radius: 8px; font-size: 0.82rem; padding: 8px;"></textarea>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 16px;">
            <button type="button" id="admin-btn-approve-req" style="flex: 1; padding: 12px; font-size: 0.86rem; font-weight: 700; border-radius: 10px; border: none; background: #10b981; color: #ffffff; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                ✓ Approve Account
            </button>
            <button type="button" id="admin-btn-reject-req" style="flex: 1; padding: 12px; font-size: 0.86rem; font-weight: 700; border-radius: 10px; border: 1.5px solid #ef4444; background: #ffffff; color: #ef4444; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                ✕ Decline
            </button>
        </div>
        <button type="button" id="admin-btn-dismiss-req" style="width: 100%; margin-top: 10px; padding: 11px; font-size: 0.82rem; font-weight: 600; border-radius: 8px; border: 1px solid var(--border); background: var(--card-bg); color: var(--text-muted); cursor: pointer;">
            Dismiss / Close
        </button>
    `;

    modal.style.display = 'flex';

    const dismissBtn = modal.querySelector('#admin-btn-dismiss-req');
    if (dismissBtn) {
        dismissBtn.onclick = () => {
            if (typeof window.closeAdminReviewModal === 'function') {
                window.closeAdminReviewModal();
            } else {
                modal.style.display = 'none';
            }
        };
    }

    if (req.roster_app_id) {
        const dossierBtn = modal.querySelector('#admin-btn-view-dossier');
        if (dossierBtn) {
            dossierBtn.onclick = () => {
                if (typeof window.closeAdminReviewModal === 'function') {
                    window.closeAdminReviewModal();
                } else {
                    modal.style.display = 'none';
                }
                window.navigateTo('admin/roster-review', { id: req.roster_app_id });
            };
        }
    }

    const approveBtn = modal.querySelector('#admin-btn-approve-req');
    if (approveBtn) {
        approveBtn.onclick = () => {
            submitReviewAction(req.profile_id, 'approve');
        };
    }

    const rejectBtn = modal.querySelector('#admin-btn-reject-req');
    if (rejectBtn) {
        rejectBtn.onclick = () => {
            submitReviewAction(req.profile_id, 'reject');
        };
    }
}
window.openAdminReviewModal = openAdminReviewModal;

async function submitReviewAction(profileId, action) {
    const user = window.getUser();
    if (!user) return;

    const notesInput = document.getElementById('admin-reviewer-notes-input');
    const notes = notesInput ? notesInput.value.trim() : '';

    const btn = action === 'approve' ? document.getElementById('admin-btn-approve-req') : document.getElementById('admin-btn-reject-req');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Processing...';
    }

    try {
        const formData = new FormData();
        formData.append('user_id', user.id);
        formData.append('profile_id', profileId);
        formData.append('action', action);
        formData.append('reviewer_notes', notes);

        const res = await fetch(`${window.API_BASE}/api/mobile/admin/review-profile`, {
            method: 'POST',
            body: formData,
            credentials: 'include'
        });

        const data = await res.json();
        if (data.status === 1) {
            window.showToast(data.message || (action === 'approve' ? 'Account approved!' : 'Account declined.'), 'success');
            if (typeof window.closeAdminReviewModal === 'function') {
                window.closeAdminReviewModal();
            } else {
                const modal = document.getElementById('modal-admin-review');
                if (modal) modal.style.display = 'none';
            }
            // Reload dashboard
            if (typeof window.init === 'function') {
                window.init(user);
            }
        } else {
            window.showToast(data.message || 'Action failed', 'error');
            if (btn) {
                btn.disabled = false;
                btn.textContent = action === 'approve' ? '✓ Approve Account' : '✕ Decline';
            }
        }
    } catch (err) {
        console.error('Review action failed:', err);
        window.showToast('Network error during review', 'error');
        if (btn) {
            btn.disabled = false;
            btn.textContent = action === 'approve' ? '✓ Approve Account' : '✕ Decline';
        }
    }
}
window.submitReviewAction = submitReviewAction;

