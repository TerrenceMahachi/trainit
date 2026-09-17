window.init = async function() {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const titleEl = document.getElementById('requests-list-title');
    const counterEl = document.getElementById('requests-list-counter');
    const containerEl = document.getElementById('requests-ledger-container');
    const searchInput = document.getElementById('requests-search-input');
    const fabBtn = document.getElementById('fab-new-request');
    const filterPills = document.querySelectorAll('.filter-pill');

    const persona = user.persona || '';

    // Show FAB for clients & admins
    if (fabBtn && (persona === 'client' || persona === 'admin')) {
        fabBtn.style.display = 'flex';
        fabBtn.addEventListener('click', () => {
            window.navigateTo('requests/new');
        });
    }

    let allItems = [];
    let currentFilter = 'all';
    let searchQuery = '';

    if (persona === 'candidate') {
        if (titleEl) titleEl.textContent = 'Career Opportunities';
        if (searchInput) searchInput.placeholder = 'Search by position or department...';
        // Vacancies are not filtered by the request-status pills (the
        // opportunities endpoint returns open roles only), and their labels
        // ("Resolved / Closed") are ticket terminology. Hide the row here.
        const pillsBox = document.getElementById('filter-pills-container');
        if (pillsBox) pillsBox.style.display = 'none';
        loadVacancies();
    } else {
        if (titleEl) {
            if (persona === 'associate' || persona === 'apprentice') {
                titleEl.textContent = 'Task Assignments';
            } else {
                titleEl.textContent = 'Service Requests';
            }
        }
        loadRequests();
    }

    async function loadVacancies() {
        const cacheKey = 'vacancies';
        const cached = window.getCache(cacheKey);
        if (cached && Array.isArray(cached) && cached.length > 0) {
            allItems = cached;
            renderItems();
        }

        try {
            const response = await fetch(`${window.API_BASE}/api/mobile/opportunities`);
            const data = await response.json();

            if (data.status === 1 && data.vacancies) {
                allItems = data.vacancies;
                window.setCache(cacheKey, data.vacancies);
                window.updateNetworkBanner(false);
                renderItems();
            }
        } catch (err) {
            console.warn('Error loading vacancies from network:', err);
            window.updateNetworkBanner(true);
            if (allItems.length > 0) {
                window.showToast('Offline — displaying cached opportunities', 'warning');
            } else {
                containerEl.innerHTML = '<div class="empty-state"><div class="empty-icon">📶</div><p>Offline: No cached opportunities available.</p></div>';
            }
        }
    }

    async function loadRequests() {
        const cacheKey = 'requests_' + user.id;
        const cached = window.getCache(cacheKey);
        if (cached && Array.isArray(cached) && cached.length > 0) {
            allItems = cached;
            renderItems();
        }

        try {
            const response = await fetch(`${window.API_BASE}/api/mobile/requests?user_id=${user.id}`, {
                method: 'GET',
                credentials: 'include'
            });
            const data = await response.json();

            if (data.status === 1 && data.requests) {
                allItems = data.requests;
                window.setCache(cacheKey, data.requests);
                window.updateNetworkBanner(false);
                renderItems();
            }
        } catch (err) {
            console.warn('Error loading requests from network:', err);
            window.updateNetworkBanner(true);
            if (allItems.length > 0) {
                window.showToast('Offline — displaying cached requests ledger', 'warning');
            } else {
                containerEl.innerHTML = '<div class="empty-state"><div class="empty-icon">📶</div><p>Offline: No cached service requests available.</p></div>';
            }
        }
    }

    function renderItems() {
        if (!containerEl) return;

        let filtered = allItems;

        // Apply Status Filter
        if (persona !== 'candidate') {
            if (currentFilter === 'active') {
                filtered = filtered.filter(r => parseInt(r.status, 10) < 5);
            } else if (currentFilter === 'closed') {
                filtered = filtered.filter(r => parseInt(r.status, 10) >= 5);
            }
        }

        // Apply Search Query
        if (searchQuery) {
            const q = searchQuery.toLowerCase();
            filtered = filtered.filter(item => {
                const title = (item.title || '').toLowerCase();
                const code = (item.request_number || item.reference_number || '').toLowerCase();
                const client = (item.client_name || item.department_name || '').toLowerCase();
                return title.includes(q) || code.includes(q) || client.includes(q);
            });
        }

        if (counterEl) counterEl.textContent = filtered.length;

        if (filtered.length === 0) {
            containerEl.innerHTML = '<div class="empty-state"><div class="empty-icon">🔍</div><p>No records matching criteria.</p></div>';
            return;
        }

        if (persona === 'candidate') {
            containerEl.innerHTML = filtered.map(v => `
                <div class="item-card" data-vacancy-id="${v.iD}">
                    <div class="item-header">
                        <span class="item-code">${v.reference_number}</span>
                        <span class="badge badge-green">${v.engagement_basis || 'Active'}</span>
                    </div>
                    <div class="item-title">${v.title}</div>
                    <p style="font-size:0.8rem; color:#475569; margin-bottom: 8px;">${v.summary || ''}</p>
                    <div class="item-meta">
                        <span style="font-weight: 600; color: #1e40af;">${v.remuneration_display || 'Competitive'}</span>
                        <span>Closes: ${v.closing_date}</span>
                    </div>
                </div>
            `).join('');

            containerEl.querySelectorAll('.item-card').forEach(card => {
                card.addEventListener('click', () => {
                    const id = card.getAttribute('data-vacancy-id');
                    if (id) {
                        window.navigateTo('vacancies/view', { id });
                    }
                });
            });
        } else {
            containerEl.innerHTML = filtered.map(r => {
                const statusBadge = getStatusBadge(r.status);
                return `
                    <div class="item-card" data-request-id="${r.iD}">
                        <div class="item-header">
                            <span class="item-code">${r.request_number}</span>
                            <span class="badge ${statusBadge.class}">${r.status_name || statusBadge.label}</span>
                        </div>
                        <div class="item-title">${r.title}</div>
                        <div class="item-meta">
                            <span>${r.client_name || r.priority_name || 'Standard'}</span>
                            <span>Due: ${r.desired_due_date || 'TBD'}</span>
                        </div>
                    </div>
                `;
            }).join('');

            containerEl.querySelectorAll('.item-card').forEach(card => {
                card.addEventListener('click', () => {
                    const id = card.getAttribute('data-request-id');
                    if (id) {
                        window.navigateTo('requests/view', { id });
                    }
                });
            });
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.trim();
            renderItems();
        });
    }

    filterPills.forEach(pill => {
        pill.addEventListener('click', () => {
            filterPills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            currentFilter = pill.getAttribute('data-status');
            renderItems();
        });
    });

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
};
