window.init = function() {
    const user = window.getUser();
    if (!user) {
        window.navigateTo('auth/login');
        return;
    }

    const nameEl = document.getElementById('profile-name');
    const emailEl = document.getElementById('profile-email');
    const roleBadgeEl = document.getElementById('profile-role-badge');
    const avatarEl = document.getElementById('profile-avatar');
    const logoutBtn = document.getElementById('btn-logout');
    const shortcutsCard = document.getElementById('profile-shortcuts-card');
    const profileInvoicesBtn = document.getElementById('btn-profile-invoices');
    const darkToggleBtn = document.getElementById('btn-toggle-darkmode');
    const darkLabel = document.getElementById('darkmode-label');

    const persona = user.persona || '';

    function updateProfileHeader(u) {
        if (nameEl) nameEl.textContent = u.name || '';
        if (emailEl) emailEl.textContent = u.email || '';
        if (roleBadgeEl) roleBadgeEl.textContent = u.role_name || '';
        if (avatarEl && u.name) {
            avatarEl.textContent = u.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();
        }
    }
    updateProfileHeader(user);

    // Application Credentials Profile Buttons & Badges
    const gotoPersonalBtn = document.getElementById('btn-goto-personal');
    const gotoQualsBtn = document.getElementById('btn-goto-quals');
    const personalBadge = document.getElementById('badge-personal-status');
    const qualsBadge = document.getElementById('badge-quals-status');

    if (gotoPersonalBtn) {
        gotoPersonalBtn.addEventListener('click', () => {
            window.navigateTo('profile/personal');
        });
    }

    if (gotoQualsBtn) {
        gotoQualsBtn.addEventListener('click', () => {
            window.navigateTo('profile/qualifications');
        });
    }

    function updateCredentialsBadges() {
        const comp = user.profile_completion || {};
        if (personalBadge) {
            if (comp.personal_complete) {
                personalBadge.className = 'status-pill status-pill-active';
                personalBadge.textContent = '✓ Complete';
            } else {
                personalBadge.className = 'status-pill status-pill-pending';
                personalBadge.textContent = '⚠️ Incomplete';
            }
        }
        if (qualsBadge) {
            const count = comp.qualifications_count || 0;
            if (count > 0) {
                qualsBadge.className = 'status-pill status-pill-active';
                qualsBadge.textContent = count === 1 ? '✓ 1 Record' : `✓ ${count} Records`;
            } else {
                qualsBadge.className = 'status-pill status-pill-pending';
                qualsBadge.textContent = '⚠️ None Added';
            }
        }
    }
    updateCredentialsBadges();

    // Render Multi-Role Accounts List
    const rolesListEl = document.getElementById('profile-roles-list');
    const reqNewRoleBtn = document.getElementById('btn-profile-request-new');

    if (reqNewRoleBtn) {
        reqNewRoleBtn.addEventListener('click', () => {
            if (typeof window.openRequestProfileModal === 'function') {
                window.openRequestProfileModal();
            }
        });
    }

    if (rolesListEl) {
        const profiles = user.profiles || [];
        if (profiles.length === 0) {
            rolesListEl.innerHTML = '<div style="font-size:0.75rem; color:var(--text-muted);">Standard General Profile</div>';
        } else {
            rolesListEl.innerHTML = profiles.map(p => {
                const isActive = !!p.is_default;
                const isPending = p.status_code === 'pending';
                const isApproved = p.status_code === 'approved' || p.can_access_portal == 1;
                let title = p.display_title || p.type_name;
                try { title = decodeURIComponent(title.replace(/\+/g, ' ')); } catch (_) {}

                const badge = isPending 
                    ? `<span class="profile-status-pill warning">Pending Approval</span>` 
                    : (isActive ? `<span class="profile-status-pill success" style="background:#dcfce7;color:#15803d;">Active Profile</span>` : `<span class="profile-status-pill secondary">Approved</span>`);

                return `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; border-radius: 8px; border: 1.5px solid ${isActive ? 'var(--primary)' : 'var(--border)'}; background: ${isActive ? 'rgba(50, 201, 154, 0.12)' : 'var(--card-bg, #111a17)'};">
                        <div>
                            <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);">${escapeHtml(title)}</div>
                            <div style="font-size: 0.72rem; color: var(--text-muted);">${p.type_description || 'Specialized role'}</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            ${badge}
                            ${isPending ? `<button type="button" class="btn-manage-pending-item" data-code="${p.type_code}" style="padding: 4px 10px; font-size: 0.72rem; font-weight: 600; border-radius: 6px; background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.4); cursor: pointer;">Status / Revoke</button>` : ''}
                            ${(!isActive && isApproved) ? `<button type="button" class="btn-switch-prof-item" data-id="${p.profile_id}" style="padding: 4px 10px; font-size: 0.72rem; font-weight: 600; border-radius: 6px; background: var(--primary); color: #fff; border: none; cursor: pointer;">Activate</button>` : ''}
                        </div>
                    </div>
                `;
            }).join('');

            rolesListEl.querySelectorAll('.btn-switch-prof-item').forEach(btn => {
                btn.addEventListener('click', () => {
                    const profId = btn.getAttribute('data-id');
                    if (typeof window.switchUserProfile === 'function') {
                        window.switchUserProfile(profId);
                    }
                });
            });

            rolesListEl.querySelectorAll('.btn-manage-pending-item').forEach(btn => {
                btn.addEventListener('click', () => {
                    const code = btn.getAttribute('data-code');
                    if (code && typeof window.openRoleCategoryModal === 'function') {
                        window.openRoleCategoryModal(code);
                    }
                });
            });
        }
    }

    // Shortcuts for Client & Admin
    if (shortcutsCard && (persona === 'client' || persona === 'admin')) {
        shortcutsCard.style.display = 'block';
        if (profileInvoicesBtn) {
            profileInvoicesBtn.addEventListener('click', () => {
                window.navigateTo('invoices/list');
            });
        }
    }

    // Dark Mode Toggle
    function updateDarkLabel() {
        const isDark = document.body.classList.contains('dark-mode');
        if (darkLabel) {
            darkLabel.textContent = isDark ? '☀️ Light Mode' : '🌙 Dark Mode';
        }
    }
    updateDarkLabel();

    if (darkToggleBtn) {
        darkToggleBtn.addEventListener('click', () => {
            const isDark = document.body.classList.toggle('dark-mode');
            localStorage.setItem('tsigiro_theme', isDark ? 'dark' : 'light');
            updateDarkLabel();
            window.showToast(isDark ? 'Dark theme enabled' : 'Light theme enabled', 'info');
        });
    }

    // Password Visibility Toggles
    document.querySelectorAll('.btn-toggle-password').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🙈';
            } else {
                input.type = 'password';
                btn.textContent = '👁️';
            }
        });
    });

    // --------------------------------------------------------------------------
    // Edit Account Profile Logic
    // --------------------------------------------------------------------------
    const editNameInput = document.getElementById('edit-profile-name');
    const editEmailInput = document.getElementById('edit-profile-email');
    const editCurPwdInput = document.getElementById('edit-profile-cur-pwd');
    const editProfileAlert = document.getElementById('edit-profile-alert');
    const saveProfileBtn = document.getElementById('btn-save-profile');

    if (editNameInput) editNameInput.value = user.name || '';
    if (editEmailInput) editEmailInput.value = user.email || '';

    function showProfileAlert(msg, isError = false) {
        if (!editProfileAlert) return;
        editProfileAlert.style.display = 'block';
        editProfileAlert.className = 'alert-box ' + (isError ? 'alert-danger' : 'alert-success');
        editProfileAlert.style.background = isError ? '#fee2e2' : '#dcfce7';
        editProfileAlert.style.color = isError ? '#991b1b' : '#166534';
        editProfileAlert.style.border = '1px solid ' + (isError ? '#fca5a5' : '#86efac');
        editProfileAlert.style.padding = '10px 14px';
        editProfileAlert.style.borderRadius = '8px';
        editProfileAlert.style.fontSize = '0.8rem';
        editProfileAlert.textContent = msg;
    }

    if (saveProfileBtn) {
        saveProfileBtn.addEventListener('click', async () => {
            const name = (editNameInput ? editNameInput.value : '').trim();
            const email = (editEmailInput ? editEmailInput.value : '').trim();
            const curPwd = editCurPwdInput ? editCurPwdInput.value : '';

            if (!name) {
                showProfileAlert('Please enter your full name.', true);
                if (editNameInput) editNameInput.focus();
                return;
            }

            if (!email || !email.includes('@')) {
                showProfileAlert('Please enter a valid email address.', true);
                if (editEmailInput) editEmailInput.focus();
                return;
            }

            saveProfileBtn.disabled = true;
            const originalBtnText = saveProfileBtn.textContent;
            saveProfileBtn.textContent = 'Saving Changes...';

            try {
                const res = await window.apiFetch('/api/mobile/user/update-profile', {
                    method: 'POST',
                    body: {
                        name: name,
                        email: email,
                        current_password: curPwd
                    }
                });

                if (res && res.status === 1) {
                    showProfileAlert(res.message || 'Profile updated successfully!', false);
                    if (res.user) {
                        window.setUser(res.user);
                        updateProfileHeader(res.user);
                    }
                    if (editCurPwdInput) editCurPwdInput.value = '';
                    if (typeof window.showToast === 'function') {
                        window.showToast('Profile details updated successfully!', 'success');
                    }
                } else {
                    showProfileAlert(res?.message || 'Failed to update profile.', true);
                }
            } catch (err) {
                showProfileAlert(err.message || 'Error updating profile. Please try again.', true);
            } finally {
                saveProfileBtn.disabled = false;
                saveProfileBtn.textContent = originalBtnText;
            }
        });
    }

    // --------------------------------------------------------------------------
    // Change Password Logic
    // --------------------------------------------------------------------------
    const pwdCurrentInput = document.getElementById('pwd-current');
    const pwdNewInput = document.getElementById('pwd-new');
    const pwdConfirmInput = document.getElementById('pwd-confirm');
    const changePwdAlert = document.getElementById('change-pwd-alert');
    const savePasswordBtn = document.getElementById('btn-save-password');

    function showPasswordAlert(msg, isError = false) {
        if (!changePwdAlert) return;
        changePwdAlert.style.display = 'block';
        changePwdAlert.className = 'alert-box ' + (isError ? 'alert-danger' : 'alert-success');
        changePwdAlert.style.background = isError ? '#fee2e2' : '#dcfce7';
        changePwdAlert.style.color = isError ? '#991b1b' : '#166534';
        changePwdAlert.style.border = '1px solid ' + (isError ? '#fca5a5' : '#86efac');
        changePwdAlert.style.padding = '10px 14px';
        changePwdAlert.style.borderRadius = '8px';
        changePwdAlert.style.fontSize = '0.8rem';
        changePwdAlert.textContent = msg;
    }

    if (savePasswordBtn) {
        savePasswordBtn.addEventListener('click', async () => {
            const curPwd = pwdCurrentInput ? pwdCurrentInput.value : '';
            const newPwd = pwdNewInput ? pwdNewInput.value : '';
            const confirmPwd = pwdConfirmInput ? pwdConfirmInput.value : '';

            if (!curPwd) {
                showPasswordAlert('Please enter your current password.', true);
                if (pwdCurrentInput) pwdCurrentInput.focus();
                return;
            }

            if (!newPwd || newPwd.length < 6) {
                showPasswordAlert('New password must be at least 6 characters long.', true);
                if (pwdNewInput) pwdNewInput.focus();
                return;
            }

            if (newPwd !== confirmPwd) {
                showPasswordAlert('New password and confirmation do not match.', true);
                if (pwdConfirmInput) pwdConfirmInput.focus();
                return;
            }

            savePasswordBtn.disabled = true;
            const originalBtnText = savePasswordBtn.textContent;
            savePasswordBtn.textContent = 'Updating Password...';

            try {
                const res = await window.apiFetch('/api/mobile/user/change-password', {
                    method: 'POST',
                    body: {
                        current_password: curPwd,
                        new_password: newPwd,
                        confirm_password: confirmPwd
                    }
                });

                if (res && res.status === 1) {
                    showPasswordAlert(res.message || 'Password changed successfully!', false);
                    if (pwdCurrentInput) pwdCurrentInput.value = '';
                    if (pwdNewInput) pwdNewInput.value = '';
                    if (pwdConfirmInput) pwdConfirmInput.value = '';
                    if (typeof window.showToast === 'function') {
                        window.showToast('Password changed successfully!', 'success');
                    }
                } else {
                    showPasswordAlert(res?.message || 'Failed to change password.', true);
                }
            } catch (err) {
                showPasswordAlert(err.message || 'Error updating password. Please try again.', true);
            } finally {
                savePasswordBtn.disabled = false;
                savePasswordBtn.textContent = originalBtnText;
            }
        });
    }

    // --------------------------------------------------------------------------
    // Logout Action
    // --------------------------------------------------------------------------
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            try {
                await fetch(`${window.API_BASE}/api/mobile/logout`, {
                    method: 'POST',
                    credentials: 'include'
                });
            } catch (e) {
                // ignore network error
            }
            window.clearUser();
            window.showToast('Signed out successfully', 'info');
            window.navigateTo('auth/login');
        });
    }
};
