/**
 * Roster: Choose Track Controller
 */

window.init = function(data) {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    // Back to dashboard
    const btnBack = document.getElementById('btn-roster-back-home');
    if (btnBack) {
        btnBack.onclick = (e) => {
            e.preventDefault();
            window.navigateTo('dashboard/home');
        };
    }

    // Apply Buttons
    document.querySelectorAll('.btn-apply-track').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const trackId = parseInt(btn.getAttribute('data-track-id'), 10);
            const trackCode = btn.getAttribute('data-track-code');
            const target = (trackCode === 'associate' || trackId === 2) ? 'opportunities/apply/associate' : 'opportunities/apply/apprentice';
            window.navigateTo(target, { track_id: trackId, track_code: trackCode });
        };
    });

    // Card Clicks
    const cardApp = document.getElementById('card-track-apprentice');
    if (cardApp) {
        cardApp.onclick = (e) => {
            if (e.target.tagName.toLowerCase() === 'button' || e.target.closest('button')) return;
            window.navigateTo('opportunities/apply/apprentice', { track_id: 1, track_code: 'apprentice' });
        };
    }

    const cardAsc = document.getElementById('card-track-associate');
    if (cardAsc) {
        cardAsc.onclick = (e) => {
            if (e.target.tagName.toLowerCase() === 'button' || e.target.closest('button')) return;
            window.navigateTo('opportunities/apply/associate', { track_id: 2, track_code: 'associate' });
        };
    }

    // Load existing applications
    loadMyApplications();
};

async function loadMyApplications() {
    const container = document.getElementById('roster-existing-container');
    const section = document.getElementById('roster-existing-section');
    if (!container || !section) return;

    try {
        const user = window.getUser();
        const userId = user ? (user.id || user.iD) : 0;
        const res = await fetch(`${window.API_BASE}/api/mobile/roster/my-applications?user_id=${userId}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.status === 1 && data.applications && data.applications.length > 0) {
            // Update Track Card Buttons if already submitted
            const appApp = data.applications.find(a => (a.track_code === 'apprentice' || parseInt(a.track_id, 10) === 1) && a.is_submitted);
            const ascApp = data.applications.find(a => (a.track_code === 'associate' || parseInt(a.track_id, 10) === 2) && a.is_submitted);

            const btnApp = document.querySelector('.btn-apply-track[data-track-code="apprentice"]');
            if (btnApp && appApp) {
                btnApp.innerHTML = `<span>⏳ Under Review (View / Revoke) →</span>`;
                btnApp.style.background = '#059669';
            }

            const btnAsc = document.querySelector('.btn-apply-track[data-track-code="associate"]');
            if (btnAsc && ascApp) {
                btnAsc.innerHTML = `<span>⏳ Under Review (View / Revoke) →</span>`;
                btnAsc.style.background = '#2563eb';
            }

            section.style.display = 'block';
            container.innerHTML = data.applications.map(app => {
                const isDraft = app.is_draft;
                const statusBadge = `<span class="badge ${app.badge_class}">${app.status_name || 'In Progress'}</span>`;

                return `
                    <div class="card" style="margin-bottom: 12px; border-left: 4px solid ${isDraft ? 'var(--warning)' : 'var(--primary)'}; padding: 14px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                            <div>
                                <span style="font-size: 0.72rem; font-weight: 800; color: #64748b; letter-spacing: 0.5px;">${app.application_number}</span>
                                <h4 style="font-size: 0.92rem; font-weight: 700; color: var(--text-main); margin: 2px 0;">${app.function_name || 'Specialist Track'}</h4>
                            </div>
                            <div style="display: flex; gap: 4px; align-items: center;">
                                ${statusBadge}
                            </div>
                        </div>

                        <div style="display: flex; gap: 12px; font-size: 0.72rem; color: #64748b; margin-bottom: 10px;">
                            <span>${app.city || 'Zimbabwe'}</span>
                            <span>•</span>
                            <span>${app.track_name || 'Track'}</span>
                        </div>

                        <div id="card-actions-${app.id}" style="display: flex; gap: 8px;">
                            <button type="button" class="btn-primary btn-view-status" data-app-id="${app.id}" data-track-code="${app.track_code}" style="padding: 8px 12px; font-size: 0.76rem; flex: 1; background: var(--primary);">
                                View Details & Status →
                            </button>
                            <button type="button" class="btn-revoke-app-item" data-app-id="${app.id}" data-track-code="${app.track_code}" style="padding: 8px 12px; font-size: 0.76rem; font-weight: 700; border-radius: 8px; border: 1px solid #ef4444; background: rgba(239, 68, 68, 0.08); color: #ef4444; cursor: pointer;">
                                Revoke
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            // Attach listeners
            container.querySelectorAll('.btn-view-status').forEach(btn => {
                btn.onclick = () => {
                    const trackCode = btn.getAttribute('data-track-code');
                    const target = (trackCode === 'associate') ? 'opportunities/apply/associate' : 'opportunities/apply/apprentice';
                    window.navigateTo(target, { track_code: trackCode });
                };
            });

            container.querySelectorAll('.btn-revoke-app-item').forEach(btn => {
                btn.onclick = () => {
                    const appId = btn.getAttribute('data-app-id');
                    const trackCode = btn.getAttribute('data-track-code');
                    const actionsBox = container.querySelector(`#card-actions-${appId}`);

                    if (actionsBox) {
                        actionsBox.innerHTML = `
                            <div style="width: 100%; background: rgba(239, 68, 68, 0.08); border: 1px solid #ef4444; border-radius: 8px; padding: 10px; text-align: center;">
                                <div style="font-size: 0.75rem; font-weight: 700; color: #ef4444; margin-bottom: 8px;">
                                    Withdraw this application?
                                </div>
                                <div style="display: flex; gap: 6px;">
                                    <button type="button" class="btn-cancel-card-revoke" style="flex: 1; padding: 6px; font-size: 0.72rem; border-radius: 6px; border: 1px solid var(--border); background: var(--card-bg); color: var(--text-main); cursor: pointer;">
                                        Cancel
                                    </button>
                                    <button type="button" class="btn-confirm-card-revoke" style="flex: 1; padding: 6px; font-size: 0.72rem; font-weight: 700; border-radius: 6px; border: none; background: #ef4444; color: #ffffff; cursor: pointer;">
                                        Yes, Revoke
                                    </button>
                                </div>
                            </div>
                        `;

                        actionsBox.querySelector('.btn-cancel-card-revoke').onclick = () => {
                            loadMyApplications();
                        };

                        const confirmBtn = actionsBox.querySelector('.btn-confirm-card-revoke');
                        confirmBtn.onclick = async () => {
                            confirmBtn.disabled = true;
                            confirmBtn.textContent = '...';

                            try {
                                const revokeRes = await window.apiFetch('/api/mobile/user/revoke-profile', {
                                    method: 'POST',
                                    body: {
                                        user_id: userId,
                                        type_code: trackCode,
                                        application_id: appId
                                    }
                                });

                                if (revokeRes && revokeRes.status === 1) {
                                    if (window.showToast) {
                                        window.showToast('✓ Application revoked successfully', 'success');
                                    }
                                    if (revokeRes.user) {
                                        window.setUser(revokeRes.user);
                                    }
                                    loadMyApplications();
                                } else {
                                    window.showToast(revokeRes ? revokeRes.message : 'Revoke failed', 'error');
                                    loadMyApplications();
                                }
                            } catch (e) {
                                window.showToast('Network error during revoke', 'error');
                                loadMyApplications();
                            }
                        };
                    }
                };
            });
        } else {
            section.style.display = 'none';
        }
    } catch (e) {
        console.warn('Failed to load candidate applications:', e);
    }
}
