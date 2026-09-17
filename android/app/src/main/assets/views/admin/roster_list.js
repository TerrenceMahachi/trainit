window.init = async function() {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const persona = user.persona || '';
    const containerEl = document.getElementById('roster-queue-container');
    const counterEl = document.getElementById('roster-queue-counter');
    const summaryEl = document.getElementById('roster-queue-summary');
    const searchInput = document.getElementById('roster-search-input');
    const filterPills = document.querySelectorAll('.filter-pill');

    // Reviewer-only screen. The API enforces this too; this is just so a
    // candidate who deep-links here gets a clear message instead of a 403.
    const reviewerRoles = [1, 6, 7, 8];
    const reviewerPersonas = ['admin', 'staff', 'manager', 'finance', 'vetting'];
    const isReviewer = reviewerPersonas.includes(persona)
        || (user.role != null && reviewerRoles.includes(parseInt(user.role, 10)));
    if (!isReviewer) {
        containerEl.innerHTML = '<div class="empty-state"><div class="empty-icon">🔒</div>'
            + '<p>This console is only available to reviewers.</p></div>';
        return;
    }

    let currentFilter = 'queue';
    let searchQuery = '';
    let items = [];

    const STATUS_BADGE = {
        2: { label: 'Submitted', cls: 'badge-blue' },
        3: { label: 'Shortlisted', cls: 'badge-yellow' },
        4: { label: 'Interview', cls: 'badge-purple' },
        5: { label: 'On Roster', cls: 'badge-green' },
        6: { label: 'Deployed', cls: 'badge-green' },
        8: { label: 'Declined', cls: 'badge-red' }
    };

    const ACCOUNT_BADGE = {
        pending: { label: 'Account pending', cls: 'badge-yellow' },
        approved: { label: 'Account active', cls: 'badge-green' },
        declined: { label: 'Account declined', cls: 'badge-red' }
    };

    async function load() {
        try {
            const params = new URLSearchParams();
            params.set('status', currentFilter);
            if (searchQuery) params.set('q', searchQuery);

            const data = await window.apiFetch('/api/mobile/admin/roster-applications?' + params.toString());

            if (data.status === 1) {
                items = data.applications || [];
                renderSummary(data.counts || {});
                render();
                window.updateNetworkBanner(false);
            } else {
                containerEl.innerHTML = '<div class="empty-state"><p>'
                    + window.escapeHtml(data.message || 'Could not load the review queue.') + '</p></div>';
            }
        } catch (err) {
            console.error('Failed to load roster queue:', err);
            window.updateNetworkBanner(true);
            containerEl.innerHTML = '<div class="empty-state"><div class="empty-icon">📶</div>'
                + '<p>Could not reach the review queue. Check your connection and try again.</p></div>';
        }
    }

    function renderSummary(counts) {
        if (!summaryEl) return;
        const cells = [
            { label: 'Awaiting', value: counts.queue || 0, cls: 'badge-blue' },
            { label: 'Shortlisted', value: counts.shortlisted || 0, cls: 'badge-yellow' },
            { label: 'On Roster', value: counts.on_roster || 0, cls: 'badge-green' },
            { label: 'Declined', value: counts.rejected || 0, cls: 'badge-red' }
        ];
        summaryEl.innerHTML = cells.map(c =>
            `<span class="badge ${c.cls}" style="font-size:0.68rem;">${c.label}: ${c.value}</span>`
        ).join('');
    }

    function render() {
        if (!containerEl) return;

        if (counterEl) counterEl.textContent = items.length;

        if (items.length === 0) {
            containerEl.innerHTML = '<div class="empty-state"><div class="empty-icon">✅</div>'
                + '<p>Nothing in this queue right now.</p></div>';
            return;
        }

        containerEl.innerHTML = items.map(a => {
            const badge = STATUS_BADGE[parseInt(a.applicationstatus, 10)]
                || { label: a.status_name || 'Pending', cls: 'badge-blue' };
            const acct = ACCOUNT_BADGE[a.account_status];
            const name = a.legal_name || a.account_name || 'Unnamed applicant';
            const score = (a.total_score !== null && a.total_score !== undefined && a.total_score !== '')
                ? `<span style="font-weight:600;">Score: ${Number(a.total_score).toFixed(1)}/100</span>`
                : '<span style="color:#94a3b8;">Not yet scored</span>';

            return `
                <div class="item-card" data-app-id="${a.iD}">
                    <div class="item-header">
                        <span class="item-code">${window.escapeHtml(a.application_number || ('#' + a.iD))}</span>
                        <span class="badge ${badge.cls}">${window.escapeHtml(badge.label)}</span>
                    </div>
                    <div class="item-title">${window.escapeHtml(name)}</div>
                    <p style="font-size:0.8rem; color:#475569; margin-bottom:8px;">
                        ${window.escapeHtml(a.track_name || '')}${a.function_name ? ' · ' + window.escapeHtml(a.function_name) : ''}
                    </p>
                    <div class="item-meta">
                        ${score}
                        <span>${a.document_count || 0} doc${(a.document_count === 1) ? '' : 's'}</span>
                    </div>
                    ${acct ? `<div style="margin-top:8px;"><span class="badge ${acct.cls}" style="font-size:0.66rem;">${acct.label}</span></div>` : ''}
                </div>
            `;
        }).join('');

        containerEl.querySelectorAll('.item-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = card.getAttribute('data-app-id');
                if (id) window.navigateTo('admin/roster-review', { id });
            });
        });
    }

    if (searchInput) {
        let debounce;
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.trim();
            clearTimeout(debounce);
            debounce = setTimeout(load, 300);
        });
    }

    filterPills.forEach(pill => {
        pill.addEventListener('click', () => {
            filterPills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            currentFilter = pill.getAttribute('data-status');
            load();
        });
    });

    load();
};
