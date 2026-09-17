window.init = async function(data) {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const appId = data ? data.id : null;
    if (!appId) {
        window.navigateTo('admin/roster-queue');
        return;
    }

    const loadingEl = document.getElementById('review-loading');
    const bodyEl = document.getElementById('review-body');
    const sectionsEl = document.getElementById('review-sections');
    const notesEl = document.getElementById('review-notes');
    const totalEl = document.getElementById('review-score-total');
    const scoreInputsEl = document.getElementById('review-score-inputs');

    document.getElementById('btn-back-queue').addEventListener('click', () => {
        window.navigateTo('admin/roster-queue');
    });

    const STATUS_BADGE = {
        1: { label: 'Draft', cls: 'badge-blue' },
        2: { label: 'Submitted', cls: 'badge-blue' },
        3: { label: 'Shortlisted', cls: 'badge-yellow' },
        4: { label: 'Interview', cls: 'badge-purple' },
        5: { label: 'On Roster', cls: 'badge-green' },
        6: { label: 'Deployed', cls: 'badge-green' },
        8: { label: 'Declined', cls: 'badge-red' }
    };

    // Each band is worth 20 points; together they make the 100-point rubric.
    const SCORE_FIELDS = [
        { key: 'technical_fit_score', label: 'Technical fit' },
        { key: 'evidence_score', label: 'Evidence quality' },
        { key: 'judgement_score', label: 'Judgement' },
        { key: 'availability_score', label: 'Availability' },
        { key: 'motivation_score', label: 'Motivation' }
    ];

    function esc(v) {
        return window.escapeHtml(v == null ? '' : String(v));
    }

    function recalcTotal() {
        let total = 0;
        SCORE_FIELDS.forEach(f => {
            const el = document.getElementById('score-' + f.key);
            const v = el ? parseFloat(el.value) : 0;
            if (!isNaN(v)) total += v;
        });
        if (totalEl) totalEl.textContent = total.toFixed(1);
        return total;
    }

    function renderScoreInputs(assessment) {
        scoreInputsEl.innerHTML = SCORE_FIELDS.map(f => {
            const val = assessment && assessment[f.key] != null ? assessment[f.key] : '';
            return `
                <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                    <label for="score-${f.key}" style="font-size:0.82rem; color:#334155;">${f.label}</label>
                    <input type="number" id="score-${f.key}" class="form-input"
                           min="0" max="20" step="0.5" value="${esc(val)}"
                           style="width: 92px; text-align:center; padding:8px;">
                </div>
            `;
        }).join('');

        SCORE_FIELDS.forEach(f => {
            const el = document.getElementById('score-' + f.key);
            if (el) el.addEventListener('input', recalcTotal);
        });
        recalcTotal();
    }

    function sectionCard(title, innerHtml, emptyText) {
        const content = innerHtml && innerHtml.trim()
            ? innerHtml
            : `<p style="font-size:0.8rem; color:#94a3b8; margin:6px 0 0;">${esc(emptyText || 'Nothing recorded.')}</p>`;
        return `
            <div class="card" style="margin-bottom: 14px;">
                <div class="section-title" style="font-size:0.9rem;"><span>${esc(title)}</span></div>
                ${content}
            </div>
        `;
    }

    function row(label, value) {
        if (value == null || value === '') return '';
        return `
            <div style="display:flex; justify-content:space-between; gap:12px; padding:5px 0; border-bottom:1px solid #f1f5f9;">
                <span style="font-size:0.78rem; color:#64748b;">${esc(label)}</span>
                <span style="font-size:0.82rem; color:#0f172a; font-weight:600; text-align:right;">${esc(value)}</span>
            </div>
        `;
    }

    let dossier = null;

    async function load() {
        try {
            const res = await window.apiFetch('/api/mobile/admin/roster-application/' + encodeURIComponent(appId));
            if (res.status !== 1) {
                loadingEl.innerHTML = `<p>${esc(res.message || 'Could not load this application.')}</p>`;
                return;
            }
            dossier = res;
            render(res);
        } catch (err) {
            console.error('Failed to load dossier:', err);
            loadingEl.innerHTML = '<p>Could not load this application. Check your connection and try again.</p>';
        }
    }

    function render(d) {
        const app = d.application || {};
        const badge = STATUS_BADGE[parseInt(app.applicationstatus, 10)]
            || { label: app.status_name || 'Pending', cls: 'badge-blue' };

        document.getElementById('review-app-number').textContent = app.application_number || ('#' + app.iD);
        const badgeEl = document.getElementById('review-status-badge');
        badgeEl.textContent = badge.label;
        badgeEl.className = 'badge ' + badge.cls;

        document.getElementById('review-name').textContent =
            app.legal_name || app.account_name || 'Unnamed applicant';
        document.getElementById('review-track').textContent =
            [app.track_name, app.function_name].filter(Boolean).join(' · ');

        // Account request state — the thing the decision actually resolves.
        const acctEl = document.getElementById('review-account-state');
        if (d.account) {
            const map = {
                pending: { label: 'Account request pending approval', cls: 'badge-yellow' },
                approved: { label: 'Account active', cls: 'badge-green' },
                declined: { label: 'Account request declined', cls: 'badge-red' }
            };
            const a = map[d.account.status_code] || { label: d.account.status_name || '', cls: 'badge-blue' };
            acctEl.innerHTML = `<span class="badge ${a.cls}" style="font-size:0.68rem;">${esc(a.label)}</span>`;
        } else {
            acctEl.innerHTML = '<span class="badge badge-blue" style="font-size:0.68rem;">No account request yet</span>';
        }

        // Red flags
        const flags = (d.assessment && d.assessment.red_flags) || [];
        if (flags.length) {
            document.getElementById('review-flags-card').style.display = 'block';
            document.getElementById('review-flags').innerHTML =
                flags.map(f => `<li style="margin-bottom:4px;">${esc(f)}</li>`).join('');
        }

        // Dossier sections
        let html = '';

        html += sectionCard('Applicant',
            row('Preferred name', app.preferred_name) +
            row('Email', app.email || app.account_email) +
            row('Mobile', app.mobile_number) +
            row('Location', [app.suburb, app.city, app.province_name].filter(Boolean).join(', ')) +
            row('Nationality', app.nationality) +
            row('Date of birth', app.date_of_birth) +
            row('Submitted', app.reg_date)
        );

        html += sectionCard('Documents',
            (d.documents || []).map(doc => row(
                doc.doc_type_name,
                (doc.original_name || '') + (doc.file_size_kb ? ` (${doc.file_size_kb} KB)` : '')
            )).join(''),
            'No documents uploaded.'
        );

        html += sectionCard('Qualifications',
            (d.qualifications || []).map(q => row(
                q.title || q.type_name,
                [q.institution_name, q.field_of_study, q.date_obtained].filter(Boolean).join(' · ')
            )).join(''),
            'No qualifications recorded.'
        );

        html += sectionCard('Skills matrix',
            (d.skills || []).map(s => row(
                s.skill_name,
                `L${s.level_number} — ${s.proficiency_name || ''}`
            )).join(''),
            'No skills declared.'
        );

        html += sectionCard('Work history',
            (d.work_history || []).map(w => row(
                w.position_title || 'Role',
                [w.organization_name, w.start_date, (parseInt(w.is_current, 10) === 1 ? 'present' : w.end_date)]
                    .filter(Boolean).join(' · ')
            )).join(''),
            'No work history recorded.'
        );

        html += sectionCard('Referees',
            (d.referees || []).map(r => row(
                r.referee_name,
                [r.position, r.organization, r.email].filter(Boolean).join(' · ')
            )).join(''),
            'No referees supplied.'
        );

        html += sectionCard('Timeline',
            (d.timeline || []).map(t => row(
                t.status_name || ('Status ' + t.applicationstatus),
                [t.reg_date, t.actor_name].filter(Boolean).join(' · ')
            )).join(''),
            'No status history.'
        );

        sectionsEl.innerHTML = html;

        renderScoreInputs(d.assessment);
        if (d.assessment && d.assessment.interview_notes && notesEl) {
            notesEl.value = d.assessment.interview_notes;
        }

        loadingEl.style.display = 'none';
        bodyEl.style.display = 'block';
    }

    const CONFIRM = {
        approve: 'Approve this application?\n\nThe candidate is admitted to the roster and their account is activated immediately.',
        reject: 'Decline this application?\n\nThe application is closed and the account request is declined.'
    };

    async function submitDecision(action, buttons) {
        if (CONFIRM[action] && !window.confirm(CONFIRM[action])) {
            return;
        }

        buttons.forEach(b => { b.disabled = true; });

        const payload = {
            application_id: appId,
            action: action,
            notes: notesEl ? notesEl.value.trim() : ''
        };
        SCORE_FIELDS.forEach(f => {
            const el = document.getElementById('score-' + f.key);
            if (el && el.value !== '') payload[f.key] = el.value;
        });

        try {
            const res = await window.apiFetch('/api/mobile/admin/roster-review', {
                method: 'POST',
                body: payload
            });

            if (res.status === 1) {
                window.showToast(
                    res.account_changed
                        ? res.message + ' Account updated.'
                        : res.message,
                    'success'
                );
                window.navigateTo('admin/roster-queue');
            } else {
                window.showToast(res.message || 'Could not record the decision.', 'warning');
                buttons.forEach(b => { b.disabled = false; });
            }
        } catch (err) {
            console.error('Decision failed:', err);
            window.showToast(err.message || 'Could not record the decision.', 'warning');
            buttons.forEach(b => { b.disabled = false; });
        }
    }

    const actionButtons = Array.from(document.querySelectorAll('.review-action'));
    actionButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            submitDecision(btn.getAttribute('data-action'), actionButtons);
        });
    });

    load();
};
