window.init = async function(data) {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const vacId = data ? data.id : null;
    if (!vacId) {
        window.navigateTo('requests/list');
        return;
    }

    const backBtn = document.getElementById('btn-back-vacancies');
    const refEl = document.getElementById('vac-ref');
    const basisEl = document.getElementById('vac-basis');
    const titleEl = document.getElementById('vac-title');
    const deptEl = document.getElementById('vac-dept');
    const payEl = document.getElementById('vac-pay');
    const closingEl = document.getElementById('vac-closing');
    const slotsEl = document.getElementById('vac-slots');
    const descEl = document.getElementById('vac-desc');
    const respEl = document.getElementById('vac-resp');
    const reqsEl = document.getElementById('vac-reqs');
    const applyBtn = document.getElementById('btn-express-apply');

    if (backBtn) {
        backBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.navigateTo('requests/list');
        });
    }

    try {
        const response = await fetch(`${window.API_BASE}/api/mobile/opportunities/view/${vacId}`);
        const res = await response.json();

        if (res.status === 1 && res.vacancy) {
            const v = res.vacancy;
            if (refEl) refEl.textContent = v.reference_number;
            if (basisEl) basisEl.textContent = v.engagement_basis || 'Full-time';
            if (titleEl) titleEl.textContent = v.title;
            if (deptEl) deptEl.textContent = v.department_name || 'Operations';
            if (payEl) payEl.textContent = v.remuneration_display || 'Competitive';
            if (closingEl) closingEl.textContent = v.closing_date;
            if (slotsEl) slotsEl.textContent = v.open_slots || 1;
            if (descEl) descEl.textContent = v.description || v.summary || '';
            if (respEl) respEl.textContent = v.responsibilities || 'Standard role responsibilities apply.';
            if (reqsEl) reqsEl.textContent = v.requirements || 'Relevant tertiary qualifications required.';
        }
    } catch (err) {
        console.error('Error fetching vacancy:', err);
        window.showToast('Error loading vacancy details', 'error');
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', async () => {
            applyBtn.disabled = true;
            applyBtn.textContent = 'Submitting Application...';

            try {
                const formData = new URLSearchParams();
                formData.append('vacancy_id', vacId);
                formData.append('user_id', user.id);

                const response = await fetch(`${window.API_BASE}/api/mobile/opportunities/apply`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    credentials: 'include',
                    body: formData.toString()
                });

                const res = await response.json();
                if (res.status === 1) {
                    window.showToast('Application submitted successfully!', 'success');
                    applyBtn.textContent = 'Application Submitted ✓';
                    applyBtn.style.background = '#065f46';
                } else {
                    window.showToast(res.message || 'Application failed', 'error');
                    applyBtn.disabled = false;
                    applyBtn.textContent = '1-Click Express Apply';
                }
            } catch (err) {
                console.error('Apply error:', err);
                window.showToast('Network error submitting application', 'error');
                applyBtn.disabled = false;
                applyBtn.textContent = '1-Click Express Apply';
            }
        });
    }
};
