window.init = function() {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const backBtn = document.getElementById('btn-back-to-profile-from-quals');
    if (backBtn) {
        backBtn.addEventListener('click', () => {
            window.navigateTo('profile/view');
        });
    }

    const showAddBtn = document.getElementById('btn-show-add-qual');
    const cancelFormBtn = document.getElementById('btn-cancel-qual-form');
    const formCard = document.getElementById('qual-form-card');
    const formTitle = document.getElementById('qual-form-title');
    const form = document.getElementById('qual-form');
    const alertEl = document.getElementById('qual-alert');
    const countBadge = document.getElementById('quals-count-badge');
    const listContainer = document.getElementById('quals-list-container');
    const saveBtn = document.getElementById('btn-save-qual');

    const qualIdInput = document.getElementById('qual-id');
    const titleInput = document.getElementById('qual-title');
    const institutionInput = document.getElementById('qual-institution');
    const fieldInput = document.getElementById('qual-field');
    const typeSelect = document.getElementById('qual-type');
    const dateInput = document.getElementById('qual-date');

    let currentQuals = [];
    let qualificationTypes = [];

    function showAlert(msg, isError = false) {
        if (!alertEl) return;
        alertEl.style.display = 'block';
        alertEl.style.background = isError ? '#fee2e2' : '#dcfce7';
        alertEl.style.color = isError ? '#991b1b' : '#166534';
        alertEl.style.border = '1px solid ' + (isError ? '#fca5a5' : '#86efac');
        alertEl.style.padding = '10px 14px';
        alertEl.style.borderRadius = '8px';
        alertEl.style.fontSize = '0.8rem';
        alertEl.textContent = msg;
    }

    function resetForm() {
        if (qualIdInput) qualIdInput.value = '';
        if (titleInput) titleInput.value = '';
        if (institutionInput) institutionInput.value = '';
        if (fieldInput) fieldInput.value = '';
        if (dateInput) dateInput.value = '';
        if (typeSelect && typeSelect.options.length > 0) typeSelect.selectedIndex = 0;
        if (formTitle) formTitle.textContent = 'Add New Qualification';
        if (alertEl) alertEl.style.display = 'none';
        if (formCard) formCard.style.display = 'none';
    }

    if (showAddBtn) {
        showAddBtn.addEventListener('click', () => {
            resetForm();
            if (formCard) formCard.style.display = 'block';
            if (titleInput) titleInput.focus();
        });
    }

    if (cancelFormBtn) {
        cancelFormBtn.addEventListener('click', () => {
            resetForm();
        });
    }

    function renderList() {
        if (!listContainer) return;

        if (countBadge) {
            const count = currentQuals.length;
            countBadge.textContent = count === 1 ? '1 Qualification' : `${count} Qualifications`;
            countBadge.className = count > 0 ? 'status-pill status-pill-active' : 'status-pill status-pill-pending';
        }

        if (currentQuals.length === 0) {
            listContainer.innerHTML = `
                <div class="card" style="text-align: center; padding: 28px 16px; border: 1.5px dashed var(--border);">
                    <div style="font-size: 2rem; margin-bottom: 8px;">🎓</div>
                    <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-main); margin-bottom: 4px;">No Qualifications Added</div>
                    <div style="font-size: 0.78rem; color: var(--text-muted); max-width: 280px; margin: 0 auto 14px;">
                        Add at least one degree, diploma or certification to unlock 1-Click Applications.
                    </div>
                    <button type="button" id="btn-empty-add-qual" class="btn-primary" style="width: auto; margin: 0 auto; padding: 8px 18px; font-size: 0.8rem;">
                        + Add Your First Qualification
                    </button>
                </div>
            `;
            const emptyAddBtn = document.getElementById('btn-empty-add-qual');
            if (emptyAddBtn) {
                emptyAddBtn.addEventListener('click', () => {
                    resetForm();
                    if (formCard) formCard.style.display = 'block';
                    if (titleInput) titleInput.focus();
                });
            }
            return;
        }

        listContainer.innerHTML = currentQuals.map(q => {
            const dateDisplay = q.date_obtained ? new Date(q.date_obtained).getFullYear() : '';
            return `
                <div class="card" style="padding: 16px 14px; border: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                        <div style="font-weight: 700; font-size: 0.92rem; color: var(--text-main); flex: 1; padding-right: 8px;">
                            ${q.title}
                        </div>
                        <span class="status-pill status-pill-active" style="font-size: 0.68rem; padding: 2px 8px;">
                            ${q.type_name || 'Degree / Cert'}
                        </span>
                    </div>
                    <div style="font-size: 0.82rem; font-weight: 600; color: var(--primary); margin-bottom: 4px;">
                        🏛️ ${q.institution_name}
                    </div>
                    ${q.field_of_study ? `<div style="font-size: 0.76rem; color: var(--text-muted); margin-bottom: 4px;">Field: <strong>${q.field_of_study}</strong></div>` : ''}
                    ${dateDisplay ? `<div style="font-size: 0.74rem; color: var(--text-muted); margin-bottom: 10px;">Obtained: ${dateDisplay}</div>` : ''}
                    
                    <div style="display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid var(--border); padding-top: 10px; margin-top: 6px;">
                        <button type="button" class="btn-edit-qual btn-link-action" data-id="${q.iD}" style="font-size: 0.76rem; font-weight: 700; color: var(--primary);">
                            ✏️ Edit
                        </button>
                        <button type="button" class="btn-delete-qual btn-link-action" data-id="${q.iD}" style="font-size: 0.76rem; font-weight: 700; color: #dc2626;">
                            🗑️ Delete
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        // Wire edit buttons
        listContainer.querySelectorAll('.btn-edit-qual').forEach(btn => {
            btn.addEventListener('click', () => {
                const qId = parseInt(btn.getAttribute('data-id'), 10);
                const q = currentQuals.find(item => item.iD == qId);
                if (!q) return;

                if (qualIdInput) qualIdInput.value = q.iD;
                if (titleInput) titleInput.value = q.title || '';
                if (institutionInput) institutionInput.value = q.institution_name || '';
                if (fieldInput) fieldInput.value = q.field_of_study || '';
                if (dateInput) dateInput.value = q.date_obtained || '';
                if (typeSelect) typeSelect.value = q.qualificationtype || '6';
                if (formTitle) formTitle.textContent = 'Edit Qualification';
                if (formCard) formCard.style.display = 'block';
                formCard.scrollIntoView({ behavior: 'smooth' });
            });
        });

        // Wire delete buttons
        listContainer.querySelectorAll('.btn-delete-qual').forEach(btn => {
            btn.addEventListener('click', () => {
                const qId = parseInt(btn.getAttribute('data-id'), 10);
                if (!confirm('Are you sure you want to remove this qualification?')) return;

                btn.disabled = true;
                btn.textContent = 'Deleting...';

                window.apiFetch('/api/mobile/user/qualifications/delete', {
                    method: 'POST',
                    body: { id: qId }
                })
                .then(res => {
                    if (res && res.status === 1) {
                        currentQuals = currentQuals.filter(item => item.iD != qId);
                        renderList();
                        if (res.user) window.setUser(res.user);
                    } else {
                        alert(res?.message || 'Failed to delete qualification.');
                        btn.disabled = false;
                        btn.textContent = '🗑️ Delete';
                    }
                })
                .catch(err => {
                    alert('Network error while deleting qualification.');
                    btn.disabled = false;
                    btn.textContent = '🗑️ Delete';
                });
            });
        });
    }

    const defaultTypes = [
        { iD: 1, name: "Bachelor's Degree" },
        { iD: 2, name: "Master's Degree" },
        { iD: 3, name: "Doctorate (PhD)" },
        { iD: 4, name: "Diploma" },
        { iD: 5, name: "Certificate" },
        { iD: 6, name: "Professional Certification" },
        { iD: 7, name: "High School / O-Level / A-Level" },
        { iD: 8, name: "Other" }
    ];

    if (typeSelect && typeSelect.options.length === 0) {
        typeSelect.innerHTML = defaultTypes.map(t => `<option value="${t.iD}">${t.name}</option>`).join('');
    }

    function loadQualifications() {
        if (listContainer) {
            listContainer.innerHTML = `
                <div style="text-align: center; padding: 24px 10px; color: var(--text-muted); font-size: 0.82rem;">
                    Loading qualifications...
                </div>
            `;
        }

        window.apiFetch('/api/mobile/user/qualifications')
            .then(res => {
                if (res && res.status === 1) {
                    currentQuals = res.qualifications || [];
                    qualificationTypes = (res.qualification_types && res.qualification_types.length > 0) ? res.qualification_types : defaultTypes;

                    if (typeSelect) {
                        const curVal = typeSelect.value;
                        typeSelect.innerHTML = qualificationTypes.map(t => `<option value="${t.iD}">${t.name}</option>`).join('');
                        if (curVal) typeSelect.value = curVal;
                    }

                    renderList();
                } else {
                    throw new Error(res?.message || 'Unable to retrieve qualifications.');
                }
            })
            .catch(err => {
                console.error('Failed to load qualifications:', err);
                if (listContainer) {
                    listContainer.innerHTML = `
                        <div class="card" style="text-align: center; padding: 20px 14px; border: 1px solid rgba(239, 68, 68, 0.3);">
                            <div style="font-size: 1.8rem; margin-bottom: 6px;">⚠️</div>
                            <div style="font-weight: 700; font-size: 0.88rem; color: #ef4444; margin-bottom: 6px;">Failed to Load Records</div>
                            <div style="font-size: 0.76rem; color: var(--text-muted); margin-bottom: 12px;">${err.message || 'Please check your connection and retry.'}</div>
                            <button type="button" id="btn-retry-quals" class="btn-primary" style="width: auto; margin: 0 auto; padding: 6px 16px; font-size: 0.78rem;">
                                Retry Loading
                            </button>
                        </div>
                    `;
                    const retryBtn = document.getElementById('btn-retry-quals');
                    if (retryBtn) {
                        retryBtn.addEventListener('click', loadQualifications);
                    }
                }
            });
    }

    loadQualifications();

    async function handleSaveQualification(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (alertEl) alertEl.style.display = 'none';

        const payload = {
            id: qualIdInput ? qualIdInput.value : '',
            title: titleInput ? titleInput.value.trim() : '',
            institution_name: institutionInput ? institutionInput.value.trim() : '',
            field_of_study: fieldInput ? fieldInput.value.trim() : '',
            qualificationtype: typeSelect ? typeSelect.value : '6',
            date_obtained: dateInput ? dateInput.value : '',
        };

        if (!payload.title || !payload.institution_name) {
            showAlert('Please fill in required fields: Qualification Title and Institution.', true);
            return;
        }

        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving Record...';
        }

        try {
            const res = await window.apiFetch('/api/mobile/user/qualifications/save', {
                method: 'POST',
                body: payload
            });

            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Qualification Record';
            }

            if (res && res.status === 1) {
                showAlert('Qualification record saved successfully!');
                resetForm();
                loadQualifications();
                if (res.user) window.setUser(res.user);
                if (typeof window.showToast === 'function') {
                    window.showToast('Qualification saved successfully!', 'success');
                }
            } else {
                showAlert(res?.message || 'Failed to save qualification.', true);
            }
        } catch (err) {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Qualification Record';
            }
            showAlert(err.message || 'Network error while saving qualification.', true);
        }
    }

    if (saveBtn) {
        saveBtn.addEventListener('click', handleSaveQualification);
    }

    if (form) {
        form.addEventListener('submit', handleSaveQualification);
    }
};
