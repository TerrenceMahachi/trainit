/**
 * Roster Application 5-Step Stepper Controller
 */

let currentStep = 1;
let currentAppId = 0;
let currentTrackId = 1;
let currentTrackCode = 'apprentice';
let currentFunctionId = 0;
let rosterConfig = null;
let selectedFile = null;

window.init = async function(data) {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    // Retrieve state from navigation params
    const navParams = (window.viewDataStore && window.viewDataStore['roster/apply']) || {};
    currentTrackId = parseInt(navParams.track_id, 10) || 1;
    currentTrackCode = navParams.track_code || (currentTrackId === 2 ? 'associate' : 'apprentice');
    currentAppId = parseInt(navParams.application_id, 10) || 0;

    // Direct to dedicated single-page express application forms matching portal
    const target = (currentTrackCode === 'associate' || currentTrackId === 2) 
        ? 'opportunities/apply/associate' 
        : 'opportunities/apply/apprentice';
    window.navigateTo(target, navParams);
    return;
    const btnBack = document.getElementById('btn-wizard-back');
    if (btnBack) {
        btnBack.onclick = (e) => {
            e.preventDefault();
            window.navigateTo('roster/choose-track');
        };
    }

    // Prev step buttons
    document.querySelectorAll('.btn-step-prev').forEach(btn => {
        btn.onclick = () => {
            if (currentStep > 1) {
                goToStep(currentStep - 1);
            }
        };
    });

    // Step indicators clickable if application ID exists
    document.querySelectorAll('.step-pill').forEach(pill => {
        pill.onclick = () => {
            const targetStep = parseInt(pill.getAttribute('data-step'), 10);
            if (targetStep < currentStep || currentAppId > 0) {
                goToStep(targetStep);
            }
        };
    });

    // Update track badge
    const trackBadge = document.getElementById('wizard-track-badge');
    if (trackBadge) {
        trackBadge.textContent = (currentTrackId === 2) ? 'Associate Track (Specialist)' : 'Apprentice Track (Attachment)';
        trackBadge.className = (currentTrackId === 2) ? 'badge badge-green' : 'badge';
        if (currentTrackId !== 2) {
            trackBadge.style.background = 'rgba(59, 130, 246, 0.1)';
            trackBadge.style.color = '#2563eb';
        }
    }

    // Load static dropdown configuration
    await loadRosterConfig();

    // If existing application passed, load draft dossier
    if (currentAppId > 0) {
        await loadExistingDraft(currentAppId);
    } else {
        // Pre-fill user defaults & saved personal details
        if (user) {
            const nameEl = document.getElementById('stage1-legal-name');
            const emailEl = document.getElementById('stage1-email');
            if (nameEl) nameEl.value = user.name || '';
            if (emailEl) emailEl.value = user.email || '';
        }

        try {
            const detailsRes = await window.apiFetch('/api/mobile/user/personal-details');
            if (detailsRes && detailsRes.status === 1 && detailsRes.details) {
                const d = detailsRes.details;
                const nameEl = document.getElementById('stage1-legal-name');
                const prefNameEl = document.getElementById('stage1-preferred-name');
                const emailEl = document.getElementById('stage1-email');
                const mobileEl = document.getElementById('stage1-mobile');
                const waEl = document.getElementById('stage1-whatsapp');
                const cityEl = document.getElementById('stage1-city');
                const suburbEl = document.getElementById('stage1-suburb');
                const provEl = document.getElementById('stage1-province');
                const dobEl = document.getElementById('stage1-dob');
                const genderEl = document.getElementById('stage1-gender');

                if (nameEl && !nameEl.value && d.legal_name) nameEl.value = d.legal_name;
                if (prefNameEl && !prefNameEl.value && d.preferred_name) prefNameEl.value = d.preferred_name;
                if (mobileEl && !mobileEl.value && d.mobile_number) mobileEl.value = d.mobile_number;
                if (waEl && !waEl.value && d.whatsapp_number) waEl.value = d.whatsapp_number;
                if (cityEl && !cityEl.value && d.city) cityEl.value = d.city;
                if (suburbEl && !suburbEl.value && d.suburb) suburbEl.value = d.suburb;
                if (provEl && !provEl.value && d.zimprovince) provEl.value = d.zimprovince;
                if (dobEl && !dobEl.value && d.date_of_birth) dobEl.value = d.date_of_birth.split(' ')[0];
                if (genderEl && genderEl.value === 'Unspecified' && d.gender) genderEl.value = d.gender;
            }
        } catch (e) {
            console.warn('Could not auto-fetch user personal details for intake prefill:', e);
        }
    }

    // Setup Event Handlers for each stage
    setupStage1Handlers();
    setupStage2Handlers();
    setupStage3Handlers();
    setupStage4Handlers();
    setupStage5Handlers();
}

function goToStep(step) {
    currentStep = step;
    
    // Update step panes visibility
    for (let i = 1; i <= 5; i++) {
        const pane = document.getElementById(`step-pane-${i}`);
        if (pane) {
            pane.style.display = (i === step) ? 'block' : 'none';
        }
    }

    // Update progress bar & counters
    const progressEl = document.getElementById('wizard-progress-bar');
    const counterEl = document.getElementById('wizard-step-counter');
    const titleEl = document.getElementById('wizard-step-title');

    const titles = {
        1: '1. Candidate Intake',
        2: '2. Credentials & Documents',
        3: '3. Competency Matrix',
        4: '4. Experience & Referees',
        5: '5. Review & Submit'
    };

    if (progressEl) progressEl.style.width = `${(step / 5) * 100}%`;
    if (counterEl) counterEl.textContent = `Step ${step} of 5`;
    if (titleEl) titleEl.textContent = titles[step] || '';

    // Update stepper pill styles
    document.querySelectorAll('.step-pill').forEach(pill => {
        const pStep = parseInt(pill.getAttribute('data-step'), 10);
        if (pStep === step) {
            pill.style.color = 'var(--primary)';
            pill.style.fontWeight = '800';
        } else if (pStep < step) {
            pill.style.color = 'var(--text-main)';
            pill.style.fontWeight = '600';
        } else {
            pill.style.color = '#94a3b8';
            pill.style.fontWeight = '500';
        }
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Step-specific triggers
    if (step === 2) loadUploadedDocuments();
    if (step === 3) loadSkillsCatalog();
    if (step === 5) populateReviewSummary();
}

// --------------------------------------------------------------------------
// STATIC CONFIGURATION (Tracks, Functions, Document Types, Provinces)
// --------------------------------------------------------------------------
async function loadRosterConfig() {
    try {
        const res = await fetch(`${window.API_BASE}/api/mobile/roster/config`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.status === 1) {
            rosterConfig = data;

            // Populate Primary Function dropdown
            const funcSelect = document.getElementById('stage1-primary-function');
            if (funcSelect && data.service_functions) {
                funcSelect.innerHTML = '<option value="">-- Select Specialty --</option>' +
                    data.service_functions.map(f => `<option value="${f.id}">${f.name}</option>`).join('');
            }

            // Populate Province dropdown
            const provSelect = document.getElementById('stage1-province');
            if (provSelect && data.provinces) {
                provSelect.innerHTML = '<option value="">-- Select Province --</option>' +
                    data.provinces.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
            }

            // Populate Document Type dropdown
            const docTypeSelect = document.getElementById('stage2-doc-type');
            if (docTypeSelect && data.document_types) {
                docTypeSelect.innerHTML = '<option value="">-- Select Document Type --</option>' +
                    data.document_types.map(dt => `<option value="${dt.id}" data-code="${dt.code}">${dt.name} ${dt.code === 'CV_RESUME' ? '(Mandatory)' : ''}</option>`).join('');
            }
        }
    } catch (e) {
        console.error('Failed to load roster config:', e);
    }
}

// --------------------------------------------------------------------------
// LOAD EXISTING DRAFT
// --------------------------------------------------------------------------
async function loadExistingDraft(appId) {
    try {
        const user = window.getUser();
        const userId = user ? (user.id || user.iD) : 0;
        const res = await fetch(`${window.API_BASE}/api/mobile/roster/status/${appId}?user_id=${userId}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.status === 1 && data.application) {
            const app = data.application;
            currentAppId = parseInt(app.id || app.iD, 10);
            currentTrackId = parseInt(app.applicationtrack, 10);
            currentFunctionId = parseInt(app.primaryfunction, 10);

            // If already submitted, redirect to status tracker
            if (parseInt(app.applicationstatus, 10) >= 2) {
                window.showToast('This application has already been submitted.', 'info');
                window.navigateTo('roster/status', { id: currentAppId });
                return;
            }

            // Stage 1 Fields
            document.getElementById('stage1-app-id').value = currentAppId;
            document.getElementById('stage1-track-id').value = currentTrackId;
            if (document.getElementById('stage1-legal-name')) document.getElementById('stage1-legal-name').value = app.legal_name || '';
            if (document.getElementById('stage1-preferred-name')) document.getElementById('stage1-preferred-name').value = app.preferred_name || '';
            if (document.getElementById('stage1-email')) document.getElementById('stage1-email').value = app.email || '';
            if (document.getElementById('stage1-mobile')) document.getElementById('stage1-mobile').value = app.mobile_number || '';
            if (document.getElementById('stage1-whatsapp')) document.getElementById('stage1-whatsapp').value = app.whatsapp_number || '';
            if (document.getElementById('stage1-city')) document.getElementById('stage1-city').value = app.city || '';
            if (document.getElementById('stage1-suburb')) document.getElementById('stage1-suburb').value = app.suburb || '';
            if (document.getElementById('stage1-primary-function')) document.getElementById('stage1-primary-function').value = app.primaryfunction || '';
            if (document.getElementById('stage1-province')) document.getElementById('stage1-province').value = app.zimprovince || '';
            if (document.getElementById('stage1-work-right')) document.getElementById('stage1-work-right').value = app.workrightstatus || 'Citizen';
            if (document.getElementById('stage1-dob') && app.date_of_birth) document.getElementById('stage1-dob').value = app.date_of_birth.split(' ')[0];
            if (document.getElementById('stage1-gender')) document.getElementById('stage1-gender').value = app.gender || 'Unspecified';

            // Populate Stage 4 items if present
            if (data.work_history && data.work_history.length > 0) {
                const workContainer = document.getElementById('stage4-work-container');
                if (workContainer) {
                    workContainer.innerHTML = '';
                    data.work_history.forEach(item => addWorkHistoryItem(item));
                }
            }
            if (data.referees && data.referees.length > 0) {
                const refContainer = document.getElementById('stage4-referee-container');
                if (refContainer) {
                    refContainer.innerHTML = '';
                    data.referees.forEach(item => addRefereeItem(item));
                }
            }
        }
    } catch (e) {
        console.warn('Failed to load draft application:', e);
    }
}

// --------------------------------------------------------------------------
// STAGE 1: INTAKE HANDLERS
// --------------------------------------------------------------------------
function setupStage1Handlers() {
    const form = document.getElementById('form-stage1-intake');
    if (!form) return;

    form.onsubmit = async (e) => {
        e.preventDefault();
        const user = window.getUser();
        const userId = user ? (user.id || user.iD) : 0;

        const legalName = document.getElementById('stage1-legal-name').value.trim();
        const preferredName = document.getElementById('stage1-preferred-name').value.trim();
        const email = document.getElementById('stage1-email').value.trim();
        const mobile = document.getElementById('stage1-mobile').value.trim();
        const whatsapp = document.getElementById('stage1-whatsapp').value.trim();
        const primaryFunction = parseInt(document.getElementById('stage1-primary-function').value, 10);
        const city = document.getElementById('stage1-city').value.trim();
        const suburb = document.getElementById('stage1-suburb').value.trim();
        const province = parseInt(document.getElementById('stage1-province').value, 10) || 1;
        const workRight = document.getElementById('stage1-work-right').value;
        const dob = document.getElementById('stage1-dob').value;
        const gender = document.getElementById('stage1-gender').value;

        if (!legalName || !email || !mobile || !primaryFunction) {
            window.showToast('Please fill in all mandatory fields.', 'error');
            return;
        }

        currentFunctionId = primaryFunction;

        const btn = document.getElementById('btn-save-stage1');
        btn.disabled = true;
        btn.textContent = 'Saving Profile...';

        try {
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('application_id', currentAppId);
            formData.append('track_id', currentTrackId);
            formData.append('primary_function', primaryFunction);
            formData.append('legal_name', legalName);
            formData.append('preferred_name', preferredName);
            formData.append('email', email);
            formData.append('mobile_number', mobile);
            formData.append('whatsapp_number', whatsapp);
            formData.append('city', city);
            formData.append('suburb', suburb);
            formData.append('zimprovince', province);
            formData.append('workrightstatus', workRight);
            formData.append('date_of_birth', dob);
            formData.append('gender', gender);

            const res = await fetch(`${window.API_BASE}/api/mobile/roster/apply/stage1-intake`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            btn.disabled = false;
            btn.textContent = 'Save & Proceed to Documents →';

            if (data.status === 1) {
                currentAppId = data.application_id;
                document.getElementById('stage1-app-id').value = currentAppId;
                window.showToast('Personal details saved successfully.', 'success');
                goToStep(2);
            } else {
                window.showToast(data.message || 'Failed to save intake details.', 'error');
            }
        } catch (err) {
            btn.disabled = false;
            btn.textContent = 'Save & Proceed to Documents →';
            window.showToast('Network error while saving details.', 'error');
        }
    };
}

// --------------------------------------------------------------------------
// STAGE 2: DOCUMENT UPLOADS
// --------------------------------------------------------------------------
let uploadedDocsCache = [];

function setupStage2Handlers() {
    const fileInput = document.getElementById('stage2-file-input');
    const btnChoose = document.getElementById('btn-choose-file');
    const labelSelected = document.getElementById('stage2-selected-file-label');
    const btnUpload = document.getElementById('btn-upload-file');
    const btnProceed = document.getElementById('btn-proceed-stage3');

    if (btnChoose && fileInput) {
        btnChoose.onclick = () => fileInput.click();
        fileInput.onchange = () => {
            if (fileInput.files && fileInput.files[0]) {
                selectedFile = fileInput.files[0];
                labelSelected.textContent = `${selectedFile.name} (${Math.round(selectedFile.size / 1024)} KB)`;
                btnUpload.disabled = false;
            } else {
                selectedFile = null;
                labelSelected.textContent = 'No file chosen';
                btnUpload.disabled = true;
            }
        };
    }

    if (btnUpload) {
        btnUpload.onclick = async () => {
            const docTypeId = document.getElementById('stage2-doc-type').value;
            if (!docTypeId) {
                window.showToast('Please select a document classification.', 'error');
                return;
            }
            if (!selectedFile) {
                window.showToast('Please choose a file to upload.', 'error');
                return;
            }

            const user = window.getUser();
            const userId = user ? (user.id || user.iD) : 0;

            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('application_id', currentAppId);
            formData.append('document_type', docTypeId);
            formData.append('file', selectedFile);

            btnUpload.disabled = true;
            btnUpload.textContent = 'Uploading Document...';

            try {
                const res = await fetch(`${window.API_BASE}/api/mobile/roster/apply/stage2-upload`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                btnUpload.disabled = false;
                btnUpload.textContent = 'Upload Document to Dossier';

                if (data.status === 1) {
                    window.showToast('Document uploaded successfully!', 'success');
                    selectedFile = null;
                    fileInput.value = '';
                    labelSelected.textContent = 'No file chosen';
                    loadUploadedDocuments();
                } else {
                    window.showToast(data.message || 'Upload failed.', 'error');
                }
            } catch (err) {
                btnUpload.disabled = false;
                btnUpload.textContent = 'Upload Document to Dossier';
                window.showToast('Error uploading file.', 'error');
            }
        };
    }

    if (btnProceed) {
        btnProceed.onclick = () => {
            const hasCv = uploadedDocsCache.some(d => d.doc_type_code === 'CV_RESUME');
            if (!hasCv) {
                window.showToast('A Curriculum Vitae (CV) is mandatory before proceeding.', 'error');
                return;
            }
            goToStep(3);
        };
    }
}

async function loadUploadedDocuments() {
    const container = document.getElementById('stage2-doc-list-container');
    const badge = document.getElementById('stage2-doc-count-badge');
    if (!container || currentAppId <= 0) return;

    try {
        const user = window.getUser();
        const userId = user ? (user.id || user.iD) : 0;
        const res = await fetch(`${window.API_BASE}/api/mobile/roster/apply/documents/${currentAppId}?user_id=${userId}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.status === 1 && data.documents) {
            uploadedDocsCache = data.documents;
            if (badge) badge.textContent = `${uploadedDocsCache.length} Uploaded`;

            if (uploadedDocsCache.length === 0) {
                container.innerHTML = `<div class="empty-state" style="padding: 16px;"><p style="font-size: 0.78rem; color: #94a3b8;">No documents uploaded yet. CV is mandatory.</p></div>`;
                return;
            }

            container.innerHTML = uploadedDocsCache.map(doc => `
                <div class="card" style="padding: 10px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="color: var(--primary); font-size: 1.1rem;">📄</span>
                        <div>
                            <div style="font-size: 0.82rem; font-weight: 700; color: var(--text-main);">${doc.doc_type_name}</div>
                            <div style="font-size: 0.7rem; color: #64748b;">${doc.original_name} (${doc.file_size_kb} KB)</div>
                        </div>
                    </div>
                    <button type="button" class="btn-delete-doc" data-doc-id="${doc.id}" style="border: none; background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 5px 8px; border-radius: 6px; font-size: 0.72rem; cursor: pointer;">
                        Remove
                    </button>
                </div>
            `).join('');

            container.querySelectorAll('.btn-delete-doc').forEach(btn => {
                btn.onclick = async () => {
                    const docId = btn.getAttribute('data-doc-id');
                    await deleteDocument(docId);
                };
            });
        }
    } catch (e) {
        console.warn('Failed to load documents:', e);
    }
}

async function deleteDocument(docId) {
    if (!confirm('Are you sure you want to remove this document?')) return;
    try {
        const user = window.getUser();
        const userId = user ? (user.id || user.iD) : 0;
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('document_id', docId);

        const res = await fetch(`${window.API_BASE}/api/mobile/roster/apply/document-delete`, {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.status === 1) {
            window.showToast('Document removed.', 'info');
            loadUploadedDocuments();
        } else {
            window.showToast(data.message || 'Failed to remove document.', 'error');
        }
    } catch (e) {
        window.showToast('Error removing document.', 'error');
    }
}

// --------------------------------------------------------------------------
// STAGE 3: COMPETENCY & SKILLS MATRIX
// --------------------------------------------------------------------------
let skillsCatalogCache = [];

function setupStage3Handlers() {
    const btnSave = document.getElementById('btn-save-stage3');
    if (!btnSave) return;

    btnSave.onclick = async () => {
        const ratings = [];
        document.querySelectorAll('.skill-rating-select').forEach(sel => {
            const level = parseInt(sel.value, 10);
            if (level > 0) {
                ratings.push({
                    skill_id: parseInt(sel.getAttribute('data-skill-id'), 10),
                    level_number: level
                });
            }
        });

        if (ratings.length === 0) {
            window.showToast('Please rate at least 1 technical competency.', 'error');
            return;
        }

        const user = window.getUser();
        const userId = user ? (user.id || user.iD) : 0;

        btnSave.disabled = true;
        btnSave.textContent = 'Saving Competencies...';

        try {
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('application_id', currentAppId);
            formData.append('skills', JSON.stringify(ratings));

            const res = await fetch(`${window.API_BASE}/api/mobile/roster/apply/stage3-skills`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            btnSave.disabled = false;
            btnSave.textContent = 'Save Skills Matrix →';

            if (data.status === 1) {
                window.showToast('Competency ratings saved.', 'success');
                goToStep(4);
            } else {
                window.showToast(data.message || 'Failed to save skills matrix.', 'error');
            }
        } catch (e) {
            btnSave.disabled = false;
            btnSave.textContent = 'Save Skills Matrix →';
            window.showToast('Network error saving competencies.', 'error');
        }
    };
}

async function loadSkillsCatalog() {
    const container = document.getElementById('stage3-skills-container');
    if (!container) return;

    try {
        const funcId = currentFunctionId || 9;
        const res = await fetch(`${window.API_BASE}/api/mobile/roster/skills-catalog?function_id=${funcId}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.status === 1 && data.skills && data.skills.length > 0) {
            skillsCatalogCache = data.skills;

            // Fetch existing draft ratings if available
            let existingRatings = {};
            try {
                const user = window.getUser();
                const userId = user ? (user.id || user.iD) : 0;
                const statusRes = await fetch(`${window.API_BASE}/api/mobile/roster/status/${currentAppId}?user_id=${userId}`);
                const statusData = await statusRes.json();
                if (statusData.status === 1 && statusData.skills) {
                    statusData.skills.forEach(s => {
                        existingRatings[s.skill_name] = s.level_number;
                    });
                }
            } catch (ignore) {}

            container.innerHTML = data.skills.map(item => {
                const preselected = existingRatings[item.name] || 0;
                return `
                    <div class="card" style="padding: 10px; margin-bottom: 8px; border: 1px solid #e2e8f0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-main);">${item.name}</span>
                            <select class="form-input skill-rating-select" data-skill-id="${item.id}" style="width: auto; padding: 4px 8px; font-size: 0.76rem; background: #ffffff;">
                                <option value="0" ${preselected == 0 ? 'selected' : ''}>Not Rated</option>
                                <option value="1" ${preselected == 1 ? 'selected' : ''}>1 - Novice</option>
                                <option value="2" ${preselected == 2 ? 'selected' : ''}>2 - Adv. Beginner</option>
                                <option value="3" ${preselected == 3 ? 'selected' : ''}>3 - Competent</option>
                                <option value="4" ${preselected == 4 ? 'selected' : ''}>4 - Proficient</option>
                                <option value="5" ${preselected == 5 ? 'selected' : ''}>5 - Expert</option>
                            </select>
                        </div>
                        ${item.description ? `<p style="font-size: 0.72rem; color: #64748b; margin: 0;">${item.description}</p>` : ''}
                    </div>
                `;
            }).join('');
        } else {
            container.innerHTML = `<div class="empty-state" style="padding: 16px;"><p style="font-size: 0.78rem; color: #94a3b8;">No specific skill items configured for this specialty.</p></div>`;
        }
    } catch (e) {
        console.warn('Failed to load skills catalog:', e);
    }
}

// --------------------------------------------------------------------------
// STAGE 4: WORK HISTORY & REFEREES
// --------------------------------------------------------------------------
function setupStage4Handlers() {
    const btnAddWork = document.getElementById('btn-add-work-item');
    const btnAddRef = document.getElementById('btn-add-referee-item');
    const btnSave = document.getElementById('btn-save-stage4');

    if (btnAddWork) {
        btnAddWork.onclick = () => addWorkHistoryItem();
    }
    if (btnAddRef) {
        btnAddRef.onclick = () => addRefereeItem();
    }

    // Default with 1 referee template if empty
    const refContainer = document.getElementById('stage4-referee-container');
    if (refContainer && refContainer.children.length === 0) {
        addRefereeItem();
    }

    if (btnSave) {
        btnSave.onclick = async () => {
            const workHistory = [];
            document.querySelectorAll('.work-history-card').forEach(card => {
                const org = card.querySelector('.wh-org').value.trim();
                const title = card.querySelector('.wh-title').value.trim();
                const start = card.querySelector('.wh-start').value;
                const end = card.querySelector('.wh-end').value;
                const isCurrent = card.querySelector('.wh-current').checked ? 1 : 0;
                const deliverables = card.querySelector('.wh-deliverables').value.trim();

                if (org && title) {
                    workHistory.push({
                        organization_name: org,
                        position_title: title,
                        start_date: start,
                        end_date: end,
                        is_current: isCurrent,
                        key_deliverables: deliverables
                    });
                }
            });

            const referees = [];
            document.querySelectorAll('.referee-card').forEach(card => {
                const name = card.querySelector('.ref-name').value.trim();
                const org = card.querySelector('.ref-org').value.trim();
                const pos = card.querySelector('.ref-pos').value.trim();
                const email = card.querySelector('.ref-email').value.trim();
                const phone = card.querySelector('.ref-phone').value.trim();

                if (name && (email || phone)) {
                    referees.push({
                        referee_name: name,
                        organization: org,
                        position: pos,
                        email: email,
                        phone: phone
                    });
                }
            });

            if (referees.length === 0) {
                window.showToast('Please provide at least 1 professional referee contact.', 'error');
                return;
            }

            const user = window.getUser();
            const userId = user ? (user.id || user.iD) : 0;

            btnSave.disabled = true;
            btnSave.textContent = 'Saving Experience...';

            try {
                const formData = new FormData();
                formData.append('user_id', userId);
                formData.append('application_id', currentAppId);
                formData.append('work_history', JSON.stringify(workHistory));
                formData.append('referees', JSON.stringify(referees));

                const res = await fetch(`${window.API_BASE}/api/mobile/roster/apply/stage4-experience`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                btnSave.disabled = false;
                btnSave.textContent = 'Save & Review →';

                if (data.status === 1) {
                    window.showToast('Experience & referee details saved.', 'success');
                    goToStep(5);
                } else {
                    window.showToast(data.message || 'Failed to save experience details.', 'error');
                }
            } catch (e) {
                btnSave.disabled = false;
                btnSave.textContent = 'Save & Review →';
                window.showToast('Error saving experience details.', 'error');
            }
        };
    }
}

function addWorkHistoryItem(data = {}) {
    const container = document.getElementById('stage4-work-container');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'card work-history-card';
    div.style.cssText = 'padding: 12px; margin-bottom: 10px; border: 1px solid #e2e8f0; background: #f8fafc;';
    div.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="font-size: 0.78rem; font-weight: 700; color: var(--text-main);">Role Experience</span>
            <button type="button" class="btn-remove-work" style="border: none; background: none; color: var(--danger); font-size: 0.72rem; cursor: pointer;">Remove</button>
        </div>
        <div class="form-group" style="margin-bottom: 8px;">
            <input type="text" class="form-input wh-org" placeholder="Organization / Employer" value="${data.organization_name || ''}">
        </div>
        <div class="form-group" style="margin-bottom: 8px;">
            <input type="text" class="form-input wh-title" placeholder="Job Title / Attachment Role" value="${data.position_title || ''}">
        </div>
        <div style="display: flex; gap: 8px; margin-bottom: 8px;">
            <input type="date" class="form-input wh-start" value="${(data.start_date || '').split(' ')[0]}" placeholder="Start Date">
            <input type="date" class="form-input wh-end" value="${(data.end_date || '').split(' ')[0]}" placeholder="End Date">
        </div>
        <label style="display: flex; align-items: center; gap: 6px; font-size: 0.74rem; color: #475569; margin-bottom: 8px;">
            <input type="checkbox" class="wh-current" ${data.is_current ? 'checked' : ''}>
            <span>Currently working here</span>
        </label>
        <textarea class="form-input wh-deliverables" rows="2" placeholder="Key responsibilities & deliverables..." style="resize: none;">${data.key_deliverables || ''}</textarea>
    `;

    div.querySelector('.btn-remove-work').onclick = () => div.remove();
    container.appendChild(div);
}

function addRefereeItem(data = {}) {
    const container = document.getElementById('stage4-referee-container');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'card referee-card';
    div.style.cssText = 'padding: 12px; margin-bottom: 10px; border: 1px solid #e2e8f0; background: #f8fafc;';
    div.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="font-size: 0.78rem; font-weight: 700; color: var(--text-main);">Referee Contact</span>
            <button type="button" class="btn-remove-ref" style="border: none; background: none; color: var(--danger); font-size: 0.72rem; cursor: pointer;">Remove</button>
        </div>
        <div class="form-group" style="margin-bottom: 8px;">
            <input type="text" class="form-input ref-name" placeholder="Full Name *" value="${data.referee_name || ''}" required>
        </div>
        <div style="display: flex; gap: 8px; margin-bottom: 8px;">
            <input type="text" class="form-input ref-org" placeholder="Organization" value="${data.organization || ''}">
            <input type="text" class="form-input ref-pos" placeholder="Designation / Role" value="${data.position || ''}">
        </div>
        <div style="display: flex; gap: 8px;">
            <input type="email" class="form-input ref-email" placeholder="Email Address" value="${data.email || ''}">
            <input type="tel" class="form-input ref-phone" placeholder="Mobile / Phone" value="${data.phone || ''}">
        </div>
    `;

    div.querySelector('.btn-remove-ref').onclick = () => div.remove();
    container.appendChild(div);
}

// --------------------------------------------------------------------------
// STAGE 5: REVIEW, DECLARATIONS & E-SIGNATURE
// --------------------------------------------------------------------------
async function populateReviewSummary() {
    try {
        const user = window.getUser();
        const userId = user ? (user.id || user.iD) : 0;
        const res = await fetch(`${window.API_BASE}/api/mobile/roster/status/${currentAppId}?user_id=${userId}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.status === 1 && data.application) {
            const app = data.application;
            document.getElementById('stage5-summary-name').textContent = app.legal_name || '--';
            document.getElementById('stage5-summary-track').textContent = app.track_name || '--';
            document.getElementById('stage5-summary-function').textContent = app.function_name || '--';
            document.getElementById('stage5-summary-docs').textContent = `${(data.documents || []).length} Verified`;
            document.getElementById('stage5-summary-skills').textContent = `${(data.skills || []).length} Rated`;
            if (document.getElementById('stage5-e-signature')) {
                document.getElementById('stage5-e-signature').value = app.legal_name || '';
            }
        }
    } catch (e) {
        console.warn('Failed to load review summary:', e);
    }
}

function setupStage5Handlers() {
    const form = document.getElementById('form-stage5-submit');
    if (!form) return;

    form.onsubmit = async (e) => {
        e.preventDefault();

        const consentAcc = document.getElementById('stage5-consent-accuracy').checked;
        const consentVet = document.getElementById('stage5-consent-vetting').checked;
        const eSig = document.getElementById('stage5-e-signature').value.trim();

        if (!consentAcc || !consentVet) {
            window.showToast('You must agree to both declarations to submit.', 'error');
            return;
        }
        if (!eSig) {
            window.showToast('Electronic signature name is required.', 'error');
            return;
        }

        const user = window.getUser();
        const userId = user ? (user.id || user.iD) : 0;
        const btn = document.getElementById('btn-final-submit');
        btn.disabled = true;
        btn.textContent = 'Submitting Application...';

        try {
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('application_id', currentAppId);
            formData.append('consent_accuracy', 1);
            formData.append('consent_vetting', 1);
            formData.append('e_signature', eSig);

            const res = await fetch(`${window.API_BASE}/api/mobile/roster/apply/stage5-submit`, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            btn.disabled = false;
            btn.textContent = 'Sign & Submit Application';

            if (data.status === 1) {
                window.showToast(data.message || 'Application Submitted!', 'success');
                // Navigate directly to status tracker
                setTimeout(() => {
                    window.navigateTo('roster/status', { id: currentAppId });
                }, 800);
            } else {
                window.showToast(data.message || 'Submission error.', 'error');
            }
        } catch (err) {
            btn.disabled = false;
            btn.textContent = 'Sign & Submit Application';
            window.showToast('Network error during submission.', 'error');
        }
    };
}

// Auto-run if loaded dynamically

