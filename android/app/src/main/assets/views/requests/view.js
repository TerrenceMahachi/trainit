window.init = async function(data) {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const reqId = data ? data.id : null;
    if (!reqId) {
        window.navigateTo('requests/list');
        return;
    }

    const backBtn = document.getElementById('btn-back-requests');
    const ticketNumEl = document.getElementById('ticket-number');
    const statusBadgeEl = document.getElementById('ticket-status-badge');
    const titleEl = document.getElementById('ticket-title');
    const descEl = document.getElementById('ticket-desc');
    const clientEl = document.getElementById('ticket-client');
    const priorityEl = document.getElementById('ticket-priority');
    const dueEl = document.getElementById('ticket-due');
    const requesterEl = document.getElementById('ticket-requester');
    const specialistsSection = document.getElementById('assigned-specialists-section');
    const specialistsList = document.getElementById('assigned-specialists-list');
    const threadContainer = document.getElementById('thread-container');
    const composerForm = document.getElementById('composer-form');
    const composerInput = document.getElementById('composer-input');
    const sendBtn = document.getElementById('btn-send-message');

    // Modal elements
    const openLogTimeBtn = document.getElementById('btn-open-log-time');
    const modalLogTime = document.getElementById('modal-log-time');
    const closeLogTimeBtn = document.getElementById('btn-close-log-time');
    const formLogTime = document.getElementById('form-log-time');
    const timeHoursInput = document.getElementById('time-hours');
    const timeCategorySelect = document.getElementById('time-category');
    const timeDateInput = document.getElementById('time-date');
    const timeSummaryInput = document.getElementById('time-summary');
    const saveTimeBtn = document.getElementById('btn-save-time');

    if (backBtn) {
        backBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.navigateTo('requests/list');
        });
    }

    // Show Log Time button if user is specialist or staff
    const persona = user.persona || '';
    if (openLogTimeBtn && (persona === 'associate' || persona === 'apprentice' || persona === 'manager' || persona === 'admin')) {
        openLogTimeBtn.style.display = 'block';
    }

    if (timeDateInput) {
        timeDateInput.value = new Date().toISOString().split('T')[0];
    }

    if (openLogTimeBtn && modalLogTime) {
        openLogTimeBtn.addEventListener('click', () => {
            if (formLogTime) formLogTime.reset();
            if (timeDateInput) timeDateInput.value = new Date().toISOString().split('T')[0];
            modalLogTime.style.display = 'flex';
        });
    }
    if (closeLogTimeBtn && modalLogTime) {
        closeLogTimeBtn.addEventListener('click', () => {
            modalLogTime.style.display = 'none';
        });
    }

    // Quick Hour Selection Chips
    document.querySelectorAll('.hour-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            const h = chip.getAttribute('data-hour');
            if (timeHoursInput && h) {
                timeHoursInput.value = h;
                document.querySelectorAll('.hour-chip').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
            }
        });
    });

    if (formLogTime) {
        formLogTime.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (navigator.onLine === false || window.isOnline === false) {
                window.showToast('Offline — reconnect to log time', 'warning');
                return;
            }

            const hours = timeHoursInput.value;
            const category = timeCategorySelect.value;
            const workDate = timeDateInput.value;
            const summary = timeSummaryInput.value.trim();

            if (!hours || !summary) return;

            if (saveTimeBtn) saveTimeBtn.disabled = true;

            try {
                const formData = new URLSearchParams();
                formData.append('hours', hours);
                formData.append('category', category);
                formData.append('work_date', workDate);
                formData.append('task_summary', summary);
                formData.append('user_id', user.id);

                const response = await fetch(`${window.API_BASE}/api/mobile/time/submit`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    credentials: 'include',
                    body: formData.toString()
                });

                const res = await response.json();
                if (res.status === 1) {
                    window.showToast(`Logged ${hours} hrs successfully!`, 'success');
                    modalLogTime.style.display = 'none';
                    formLogTime.reset();
                    if (timeDateInput) timeDateInput.value = new Date().toISOString().split('T')[0];
                } else {
                    window.showToast(res.message || 'Failed to log time', 'error');
                }
            } catch (err) {
                console.error('Time log error:', err);
                window.showToast('Network error saving time', 'error');
            } finally {
                if (saveTimeBtn) saveTimeBtn.disabled = false;
            }
        });
    }

    function renderDetails(res) {
        if (!res || res.status !== 1 || !res.request) return;
        const r = res.request;
        if (ticketNumEl) ticketNumEl.textContent = r.request_number;
        if (titleEl) titleEl.textContent = r.title;
        if (descEl) descEl.textContent = r.description;
        if (clientEl) clientEl.textContent = r.client_name || 'Individual';
        if (priorityEl) priorityEl.textContent = r.priority_name || 'Standard';
        if (dueEl) dueEl.textContent = r.desired_due_date || 'None';
        if (requesterEl) requesterEl.textContent = r.requester_name || 'Client';

        const statusBadge = getStatusBadge(r.status);
        if (statusBadgeEl) {
            statusBadgeEl.textContent = r.status_name || statusBadge.label;
            statusBadgeEl.className = `badge ${statusBadge.class}`;
        }

        if (res.assignments && res.assignments.length > 0 && specialistsSection && specialistsList) {
            specialistsSection.style.display = 'block';
            specialistsList.innerHTML = res.assignments.map(a => `
                <div style="margin-bottom: 4px;">• <strong>${a.talent_name}</strong> (${a.role_name || 'Specialist'}) — $${a.hourly_rate_snapshot}/hr</div>
            `).join('');
        }

        renderMessages(res.messages || []);
    }

    async function loadDetails() {
        const cacheKey = 'request_view_' + reqId;
        const cached = window.getCache(cacheKey);
        let hasRenderedFromCache = false;
        if (cached) {
            renderDetails(cached);
            hasRenderedFromCache = true;
        }

        try {
            const response = await fetch(`${window.API_BASE}/api/mobile/requests/view/${reqId}?user_id=${user.id}`, {
                method: 'GET',
                credentials: 'include'
            });
            const res = await response.json();

            if (res.status === 1 && res.request) {
                window.setCache(cacheKey, res);
                window.updateNetworkBanner(false);
                renderDetails(res);
            }
        } catch (err) {
            console.warn('Failed to load request details from network:', err);
            window.updateNetworkBanner(true);
            if (hasRenderedFromCache) {
                window.showToast('Offline — displaying cached ticket details', 'warning');
            } else {
                window.showToast('Offline — cannot load ticket details', 'error');
            }
        }
    }

    function renderMessages(messages) {
        if (!threadContainer) return;
        if (messages.length === 0) {
            threadContainer.innerHTML = '<div class="empty-state" style="padding:16px;"><p>No messages posted yet. Start the conversation below.</p></div>';
            return;
        }

        threadContainer.innerHTML = messages.map(m => {
            const isMe = (m.sender_name === user.name) || String(m.sender) === String(user.id);
            const bubbleClass = isMe ? 'mine' : 'theirs';
            const msgBody = m.body || m.message || '';
            return `
                <div class="chat-bubble ${bubbleClass}">
                    <div class="chat-sender">${isMe ? 'You' : (m.sender_name || 'User')}</div>
                    <div>${msgBody}</div>
                    <div class="chat-time" style="font-size: 0.68rem; opacity: 0.75; text-align: right; margin-top: 4px;">${m.reg_date ? m.reg_date.substring(11, 16) : ''}</div>
                </div>
            `;
        }).join('');
        threadContainer.scrollTop = threadContainer.scrollHeight;
    }

    if (composerForm) {
        composerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (navigator.onLine === false || window.isOnline === false) {
                window.showToast('Offline — reconnect to send message', 'warning');
                return;
            }

            const body = composerInput.value.trim();
            if (!body) return;

            if (sendBtn) sendBtn.disabled = true;

            try {
                const formData = new URLSearchParams();
                formData.append('body', body);
                formData.append('user_id', user.id);

                const response = await fetch(`${window.API_BASE}/api/mobile/requests/view/${reqId}/message`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    credentials: 'include',
                    body: formData.toString()
                });

                const res = await response.json();
                if (res.status === 1) {
                    composerInput.value = '';
                    window.showToast('Message posted', 'success');
                    loadDetails();
                } else {
                    window.showToast(res.message || 'Failed to send message', 'error');
                }
            } catch (err) {
                console.error('Send message error:', err);
                window.showToast('Network error posting message', 'error');
            } finally {
                if (sendBtn) sendBtn.disabled = false;
            }
        });
    }

    loadDetails();

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
