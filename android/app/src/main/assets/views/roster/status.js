/**
 * Roster Application Status Controller
 */

window.init = async function(data) {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const navParams = (window.viewDataStore && window.viewDataStore['roster/status']) || {};
    const appId = parseInt(navParams.id, 10) || 0;

    const btnBack = document.getElementById('btn-status-back');
    if (btnBack) {
        btnBack.onclick = (e) => {
            e.preventDefault();
            window.navigateTo('roster/choose-track');
        };
    }

    if (appId > 0) {
        await fetchApplicationDossier(appId);
    } else {
        window.showToast('No application ID specified.', 'error');
        window.navigateTo('roster/choose-track');
    }
}

async function fetchApplicationDossier(appId) {
    try {
        const user = window.getUser();
        const userId = user ? (user.id || user.iD) : 0;
        const res = await fetch(`${window.API_BASE}/api/mobile/roster/status/${appId}?user_id=${userId}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.status === 1 && data.application) {
            const app = data.application;

            // Hero details
            const refEl = document.getElementById('status-app-ref');
            const nameEl = document.getElementById('status-applicant-name');
            const trackEl = document.getElementById('status-track-label');
            const funcEl = document.getElementById('status-function-label');
            const badgeEl = document.getElementById('status-current-badge');

            if (refEl) refEl.textContent = app.application_number;
            if (nameEl) nameEl.textContent = app.legal_name || 'Candidate';
            if (trackEl) trackEl.textContent = app.track_name || 'Roster';
            if (funcEl) funcEl.textContent = app.function_name || 'Specialist';
            
            if (badgeEl) {
                badgeEl.textContent = app.status_name || 'In Progress';
                badgeEl.className = 'badge ' + (app.applicationstatus >= 5 ? 'badge-green' : 'badge-yellow');
            }

            // Stats
            const statDocs = document.getElementById('status-stat-docs');
            const statSkills = document.getElementById('status-stat-skills');
            const statRoles = document.getElementById('status-stat-roles');

            if (statDocs) statDocs.textContent = (data.documents || []).length;
            if (statSkills) statSkills.textContent = (data.skills || []).length;
            if (statRoles) statRoles.textContent = (data.work_history || []).length;

            // Milestones Tracker
            renderMilestones(data.milestones || []);

            // Events / Timeline
            renderEvents(data.events || []);

            // Documents Breakdown
            renderDocs(data.documents || []);

        } else {
            window.showToast(data.message || 'Application record not found.', 'error');
        }
    } catch (e) {
        console.error('Failed to load application status:', e);
        window.showToast('Network error loading application status.', 'error');
    }
}

function renderMilestones(milestones) {
    const container = document.getElementById('status-milestones-container');
    if (!container) return;

    container.innerHTML = milestones.map((m, index) => {
        const isReached = m.reached;
        const iconColor = isReached ? 'var(--primary)' : '#cbd5e1';
        const textColor = isReached ? 'var(--text-main)' : '#94a3b8';
        const fontWeight = isReached ? '700' : '500';

        return `
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 28px; height: 28px; border-radius: 50%; background: ${isReached ? 'var(--primary-soft)' : '#f1f5f9'}; border: 2px solid ${iconColor}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: ${iconColor}; font-size: 0.75rem; font-weight: 800;">
                    ${isReached ? '✓' : (index + 1)}
                </div>
                <div style="flex: 1;">
                    <div style="font-size: 0.8rem; font-weight: ${fontWeight}; color: ${textColor};">${m.name}</div>
                </div>
                <div>
                    ${isReached ? `<span class="badge badge-green" style="font-size: 0.65rem;">Completed</span>` : `<span class="badge" style="background:#f1f5f9; color:#94a3b8; font-size: 0.65rem;">Pending</span>`}
                </div>
            </div>
        `;
    }).join('');
}

function renderEvents(events) {
    const container = document.getElementById('status-events-container');
    if (!container) return;

    if (events.length === 0) {
        container.innerHTML = `<div class="empty-state" style="padding: 10px;"><p style="font-size: 0.75rem; color: #94a3b8;">No status events recorded yet.</p></div>`;
        return;
    }

    container.innerHTML = events.map(evt => {
        const dateStr = evt.event_timestamp ? new Date(evt.event_timestamp).toLocaleDateString(undefined, { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';
        return `
            <div style="border-left: 2px solid var(--primary); padding-left: 12px; margin-bottom: 12px; position: relative;">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-main); margin-bottom: 2px;">${evt.status_name}</div>
                <div style="font-size: 0.74rem; color: #64748b; margin-bottom: 2px;">${evt.remarks}</div>
                <div style="font-size: 0.68rem; color: #94a3b8;">${dateStr}</div>
            </div>
        `;
    }).join('');
}

function renderDocs(documents) {
    const container = document.getElementById('status-docs-breakdown');
    if (!container) return;

    if (documents.length === 0) {
        container.innerHTML = `<div class="empty-state" style="padding: 10px;"><p style="font-size: 0.75rem; color: #94a3b8;">No documents attached.</p></div>`;
        return;
    }

    container.innerHTML = documents.map(doc => `
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.76rem;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span>📄</span>
                <div>
                    <strong>${doc.doc_type_name}</strong>
                    <div style="color: #64748b; font-size: 0.7rem;">${doc.original_name} (${doc.file_size_kb} KB)</div>
                </div>
            </div>
            <span class="badge badge-green" style="font-size: 0.65rem;">Verified</span>
        </div>
    `).join('');
}

// Auto-run if loaded dynamically

