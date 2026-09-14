window.init = function() {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const backBtn = document.getElementById('btn-back-to-profile');
    if (backBtn) {
        backBtn.addEventListener('click', () => {
            window.navigateTo('profile/view');
        });
    }

    const form = document.getElementById('personal-details-form');
    const alertEl = document.getElementById('personal-alert');
    const statusPill = document.getElementById('personal-status-pill');
    const saveBtn = document.getElementById('btn-save-personal');

    const legalNameInput = document.getElementById('personal-legal-name');
    const preferredNameInput = document.getElementById('personal-preferred-name');
    const mobileInput = document.getElementById('personal-mobile');
    const whatsappInput = document.getElementById('personal-whatsapp');
    const dobInput = document.getElementById('personal-dob');
    const genderSelect = document.getElementById('personal-gender');
    const cityInput = document.getElementById('personal-city');
    const suburbInput = document.getElementById('personal-suburb');
    const provinceInput = document.getElementById('personal-province');
    const countryInput = document.getElementById('personal-country');
    const idNumberInput = document.getElementById('personal-id-number');
    const nationalityInput = document.getElementById('personal-nationality');

    // Immediate initial values from logged-in user state
    if (legalNameInput && user.name) legalNameInput.value = user.name;
    const initialComplete = !!(user.profile_completion && user.profile_completion.personal_complete);
    updateStatusPill(initialComplete);

    function updateStatusPill(isComplete) {
        if (!statusPill) return;
        if (isComplete) {
            statusPill.className = 'status-pill status-pill-active';
            statusPill.innerHTML = '✓ Profile Complete';
        } else {
            statusPill.className = 'status-pill status-pill-pending';
            statusPill.innerHTML = '⚠️ Required for 1-Click';
        }
    }

    function showAlert(msg, isError = false) {
        if (!alertEl) return;
        alertEl.style.display = 'block';
        alertEl.className = 'alert-box ' + (isError ? 'alert-danger' : 'alert-success');
        alertEl.style.background = isError ? '#fee2e2' : '#dcfce7';
        alertEl.style.color = isError ? '#991b1b' : '#166534';
        alertEl.style.border = '1px solid ' + (isError ? '#fca5a5' : '#86efac');
        alertEl.style.padding = '10px 14px';
        alertEl.style.borderRadius = '8px';
        alertEl.style.fontSize = '0.8rem';
        alertEl.textContent = msg;
    }

    // Load current personal details from API
    window.apiFetch('/api/mobile/user/personal-details')
        .then(res => {
            if (res && res.status === 1) {
                const d = res.details || {};
                if (legalNameInput) legalNameInput.value = d.legal_name || user.name || '';
                if (preferredNameInput) preferredNameInput.value = d.preferred_name || '';
                if (mobileInput) mobileInput.value = d.mobile_number || '';
                if (whatsappInput) whatsappInput.value = d.whatsapp_number || '';
                if (dobInput) dobInput.value = d.date_of_birth || '';
                if (genderSelect && d.gender) genderSelect.value = d.gender;
                if (cityInput) cityInput.value = d.city || '';
                if (suburbInput) suburbInput.value = d.suburb || '';
                if (countryInput && d.country) countryInput.value = d.country;
                if (nationalityInput && d.nationality) nationalityInput.value = d.nationality;
                if (idNumberInput) idNumberInput.value = d.work_permit_number || '';

                // Populate open province value and datalist options
                const provVal = d.province_name || (res.provinces && res.provinces.find(p => p.iD == d.zimprovince)?.name) || (d.zimprovince ? String(d.zimprovince) : '');
                if (provinceInput && provVal) {
                    provinceInput.value = provVal;
                }

                if (res.provinces && res.provinces.length > 0) {
                    const datalist = document.getElementById('province-suggestions');
                    if (datalist) {
                        datalist.innerHTML = res.provinces.map(p => `<option value="${p.name}">`).join('');
                    }
                }

                const isComplete = !!(d.legal_name && d.mobile_number && d.city);
                updateStatusPill(isComplete);
            }
        })
        .catch(err => {
            console.error('Failed to load personal details:', err);
            const isComplete = !!(legalNameInput && legalNameInput.value && mobileInput && mobileInput.value && cityInput && cityInput.value);
            updateStatusPill(isComplete);
        });

    function handleSavePersonal(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (alertEl) alertEl.style.display = 'none';

        const payload = {
            legal_name: legalNameInput ? legalNameInput.value.trim() : '',
            preferred_name: preferredNameInput ? preferredNameInput.value.trim() : '',
            mobile_number: mobileInput ? mobileInput.value.trim() : '',
            whatsapp_number: whatsappInput ? whatsappInput.value.trim() : '',
            date_of_birth: dobInput ? dobInput.value : '',
            gender: genderSelect ? genderSelect.value : '',
            city: cityInput ? cityInput.value.trim() : '',
            suburb: suburbInput ? suburbInput.value.trim() : '',
            zimprovince: provinceInput ? provinceInput.value.trim() : '',
            country: countryInput ? countryInput.value.trim() : 'Zimbabwe',
            nationality: nationalityInput ? nationalityInput.value.trim() : 'Zimbabwean',
            work_permit_number: idNumberInput ? idNumberInput.value.trim() : '',
        };

        if (!payload.legal_name || !payload.mobile_number || !payload.city) {
            showAlert('Please fill in required fields: Legal Name, Mobile Number, and City.', true);
            return;
        }

        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving Profile...';
        }

        window.apiFetch('/api/mobile/user/personal-details', {
            method: 'POST',
            body: payload
        })
        .then(res => {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save & Update Personal Details';
            }
            if (res && res.status === 1) {
                showAlert('Personal details saved successfully!');
                if (res.user) {
                    window.setUser(res.user);
                }
                updateStatusPill(true);
                if (typeof window.showToast === 'function') {
                    window.showToast('Personal details updated successfully!', 'success');
                }
            } else {
                showAlert(res?.message || 'Failed to save personal details.', true);
            }
        })
        .catch(err => {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save & Update Personal Details';
            }
            showAlert(err.message || 'Network error while saving details. Please try again.', true);
        });
    }

    if (saveBtn) {
        saveBtn.addEventListener('click', handleSavePersonal);
    }

    if (form) {
        form.addEventListener('submit', handleSavePersonal);
    }
};
