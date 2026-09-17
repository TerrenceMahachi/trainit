window.init = function() {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const cancelBtn = document.getElementById('btn-cancel-new-req');
    const form = document.getElementById('new-request-form');
    const submitBtn = document.getElementById('btn-submit-work-req');
    const titleInput = document.getElementById('req-title');
    const prioritySelect = document.getElementById('req-priority');
    const dueInput = document.getElementById('req-due');
    const descInput = document.getElementById('req-desc');

    if (cancelBtn) {
        cancelBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.navigateTo('requests/list');
        });
    }

    // Default due date: 14 days ahead
    if (dueInput) {
        const d = new Date();
        d.setDate(d.getDate() + 14);
        dueInput.value = d.toISOString().split('T')[0];
    }

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const title = titleInput.value.trim();
            const description = descInput.value.trim();
            const priority = prioritySelect.value;
            const dueDate = dueInput.value;

            if (!title || !description) return;

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting & Dispatching...';
            }

            try {
                const formData = new URLSearchParams();
                formData.append('title', title);
                formData.append('description', description);
                formData.append('priority', priority);
                formData.append('due_date', dueDate);
                formData.append('user_id', user.id);

                const response = await fetch(`${window.API_BASE}/api/mobile/requests/submit`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    credentials: 'include',
                    body: formData.toString()
                });

                const res = await response.json();

                if (res.status === 1) {
                    window.showToast(`Request ${res.request_number} created!`, 'success');
                    window.navigateTo('requests/view', { id: res.request_id });
                } else {
                    window.showToast(res.message || 'Failed to submit request', 'error');
                }
            } catch (err) {
                console.error('Error submitting request:', err);
                window.showToast('Network error submitting request', 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Request & Dispatch';
                }
            }
        });
    }
};
