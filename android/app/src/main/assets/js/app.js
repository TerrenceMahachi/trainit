/**
 * Tsigiro Mobile Application Core
 */

// Determine intelligent API base
function getApiBase() {
    const saved = localStorage.getItem('api_base');
    if (saved) return saved.replace(/\/+$/, '');
    
    // Check if running on Android WebView file asset
    if (window.location.protocol === 'file:') {
        return 'https://portal.tsigiro.co.zw';
    }
    // Fallback to local host environment
    const pathSegments = window.location.pathname.split('/').filter(Boolean);
    const basePath = pathSegments.length > 0 && pathSegments[0] === 'trainit' ? '/trainit' : '';
    return window.location.origin + basePath;
}

window.API_BASE = getApiBase();

const viewCache = {};
const viewDataStore = {};
const viewHistory = [];

window.routeMap = {
    'auth/login': 'auth/login',
    'dashboard/home': 'dashboard/home',
    'requests/list': 'requests/list',
    'requests/new': 'requests/new',
    'requests/view': 'requests/view',
    'invoices/list': 'invoices/list',
    'invoices/view': 'invoices/view',
    'vacancies/view': 'vacancies/view',
    'profile/view': 'profile/view',
    'profile/personal': 'profile/personal',
    'profile/qualifications': 'profile/qualifications',
    'opportunities/apply/apprentice': 'roster/apply_apprentice',
    'opportunities/apply/associate': 'roster/apply_associate',
    'roster/apply/apprentice': 'roster/apply_apprentice',
    'roster/apply/associate': 'roster/apply_associate',
    'roster/choose-track': 'roster/choose_track',
    'roster/apply': 'roster/apply_apprentice',
    'roster/status': 'roster/status',
    // Reviewer console
    'admin/roster-queue': 'admin/roster_list',
    'admin/roster-review': 'admin/roster_review'
};

// Persistent Cache Helpers
window.setCache = function(key, data) {
    try {
        localStorage.setItem('tsigiro_cache_' + key, JSON.stringify({
            timestamp: Date.now(),
            data: data
        }));
    } catch (e) {
        console.warn('Cache write failed:', e);
    }
};

window.getCache = function(key) {
    try {
        const raw = localStorage.getItem('tsigiro_cache_' + key);
        if (!raw) return null;
        const parsed = JSON.parse(raw);
        return parsed.data || null;
    } catch (e) {
        return null;
    }
};

// Network Connectivity State
window.isOnline = navigator.onLine !== false;

window.updateNetworkBanner = function(isOffline) {
    const banner = document.getElementById('offline-banner');
    if (banner) {
        banner.style.display = isOffline ? 'flex' : 'none';
    }
    window.isOnline = !isOffline;
};

// Toast notification helper
window.showToast = function(message, type = 'info') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.className = `toast show ${type}`;
    setTimeout(() => {
        toast.className = 'toast';
    }, 2800);
};

// Auth State Helpers
window.getUser = function() {
    try {
        const u = localStorage.getItem('tsigiro_user');
        return u ? JSON.parse(u) : null;
    } catch (e) {
        return null;
    }
};

window.setUser = function(user) {
    localStorage.setItem('tsigiro_user', JSON.stringify(user));
    updateAppHeader(user);
};

window.clearUser = function() {
    localStorage.removeItem('tsigiro_user');
    updateAppHeader(null);
};

// Universal Mobile API Fetch Helper
window.apiFetch = async function(endpoint, options = {}) {
    const user = window.getUser();
    let url = endpoint.startsWith('http') ? endpoint : `${window.API_BASE}${endpoint.startsWith('/') ? '' : '/'}${endpoint}`;

    // Attach user_id query parameter if user is logged in and not already in URL
    if (user && user.id && !url.includes('user_id=')) {
        url += (url.includes('?') ? '&' : '?') + `user_id=${encodeURIComponent(user.id)}`;
    }

    const headers = {
        'Accept': 'application/json',
        ...(options.headers || {})
    };

    let body = options.body;
    // If body is a plain object, encode as URLSearchParams for universal PHP compatibility
    if (body && typeof body === 'object' && !(body instanceof FormData) && !(body instanceof URLSearchParams)) {
        if (user && user.id && !body.user_id) {
            body.user_id = user.id;
        }
        const params = new URLSearchParams();
        for (const [key, val] of Object.entries(body)) {
            if (val !== undefined && val !== null) {
                params.append(key, String(val));
            }
        }
        body = params.toString();
        headers['Content-Type'] = 'application/x-www-form-urlencoded';
    }

    const fetchOptions = {
        method: options.method || 'GET',
        credentials: 'include',
        headers: headers,
        ...(body ? { body } : {})
    };

    try {
        const res = await fetch(url, fetchOptions);
        if (!res.ok) {
            if (res.status === 401) {
                window.clearUser();
                window.navigateTo('auth/login');
                throw new Error('Session expired. Please log in again.');
            }
            let errMsg = `Server returned status ${res.status}`;
            try {
                const errData = await res.json();
                if (errData && errData.message) errMsg = errData.message;
            } catch (_) {}
            throw new Error(errMsg);
        }
        return await res.json();
    } catch (err) {
        console.error(`apiFetch failed for ${endpoint}:`, err);
        throw err;
    }
};

// HTML & SVG Helpers
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
window.escapeHtml = escapeHtml;

function getProfileIconSvg(typeCode) {
    switch (typeCode) {
        case 'apprentice':
            return `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>`;
        case 'associate':
            return `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><polyline points="16 11 18 13 22 9"></polyline></svg>`;
        case 'staff':
            return `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>`;
        case 'client':
            return `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M8 10h.01"></path><path d="M16 10h.01"></path><path d="M8 14h.01"></path><path d="M16 14h.01"></path></svg>`;
        default:
            return `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>`;
    }
}

function getNavIcon(name) {
    switch (name) {
        case 'home':
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>`;
        case 'briefcase':
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>`;
        case 'inbox':
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>`;
        case 'plus-circle':
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>`;
        case 'file-text':
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>`;
        case 'check-circle':
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`;
        case 'clock':
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>`;
        case 'user':
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>`;
        default:
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg>`;
    }
}

function updateAppHeader(user) {
    const rolePill = document.getElementById('role-pill');
    const bottomNav = document.getElementById('bottom-nav');
    const navRequestsLabel = document.getElementById('nav-requests-label');
    const headerSubtitle = document.getElementById('header-subtitle');
    const notifBell = document.getElementById('btn-notifications-bell');
    const notifBadge = document.getElementById('notifications-badge');
    const sidebarToggle = document.getElementById('btn-sidebar-toggle');

    if (user) {
        if (sidebarToggle) sidebarToggle.style.display = 'flex';
        if (rolePill) rolePill.textContent = user.role_name || 'Active User';
        if (bottomNav) bottomNav.style.display = 'flex';
        if (notifBell) notifBell.style.display = 'flex';
        
        // Contextual bottom navigation label
        if (navRequestsLabel) {
            if (user.persona === 'candidate') {
                navRequestsLabel.textContent = 'Vacancies';
            } else if (user.persona === 'associate' || user.persona === 'apprentice') {
                navRequestsLabel.textContent = 'Tasks';
            } else {
                navRequestsLabel.textContent = 'Requests';
            }
        }
        if (headerSubtitle) {
            headerSubtitle.textContent = user.name || 'Workspace';
        }
        if (typeof window.refreshNotifications === 'function') {
            window.refreshNotifications();
        }
        renderSidebar(user);
    } else {
        if (sidebarToggle) sidebarToggle.style.display = 'none';
        if (rolePill) rolePill.textContent = 'Guest';
        if (bottomNav) bottomNav.style.display = 'none';
        if (notifBell) notifBell.style.display = 'none';
        if (notifBadge) notifBadge.style.display = 'none';
        if (headerSubtitle) headerSubtitle.textContent = 'Workforce & Service Portal';
        window.closeSidebar();
    }
}

// Sidebar Drawer Control
window.openSidebar = function() {
    const drawer = document.getElementById('sidebar-drawer');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (!drawer || !backdrop) return;
    const user = window.getUser();
    if (user) {
        renderSidebar(user);
        // Silently sync latest profiles from backend
        fetch(`${window.API_BASE}/api/mobile/user/profiles?user_id=${user.id}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 1 && data.profiles) {
                    user.profiles = data.profiles;
                    if (data.active_profile) user.active_profile = data.active_profile;
                    window.setUser(user);
                    renderSidebar(user);
                }
            })
            .catch(() => {});
    }
    drawer.classList.add('open');
    backdrop.style.display = 'block';
    drawer.setAttribute('aria-hidden', 'false');
};

window.closeSidebar = function() {
    const drawer = document.getElementById('sidebar-drawer');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (!drawer) return;
    drawer.classList.remove('open');
    if (backdrop) backdrop.style.display = 'none';
    drawer.setAttribute('aria-hidden', 'true');
};

// Render Sidebar with Persona Switcher & Role Navigation
function renderSidebar(user) {
    if (!user) return;

    // Header elements
    const avatar = document.getElementById('sidebar-avatar');
    const nameEl = document.getElementById('sidebar-user-name');
    const emailEl = document.getElementById('sidebar-user-email');
    const badgeEl = document.getElementById('sidebar-role-badge');

    if (avatar) avatar.textContent = (user.name || 'U').charAt(0).toUpperCase();
    if (nameEl) nameEl.textContent = user.name || 'User';
    if (emailEl) emailEl.textContent = user.email || '';
    if (badgeEl) badgeEl.textContent = user.role_name || 'General User';

    // Persona Switcher List
    const profilesList = document.getElementById('sidebar-profiles-list');
    if (profilesList) {
        const profiles = user.profiles || [];
        if (profiles.length === 0) {
            profilesList.innerHTML = `<div style="font-size:0.75rem; color:var(--text-muted); padding:4px;">No profile extensions</div>`;
        } else {
            profilesList.innerHTML = profiles.map(p => {
                const isActive = !!p.is_default;
                const isPending = p.status_code === 'pending';
                const isApproved = p.status_code === 'approved' || p.can_access_portal == 1;
                const title = p.display_title || p.type_name;
                const meta = isPending ? 'Pending Approval' : (isActive ? 'Active Account' : 'Approved');
                const badge = isPending 
                    ? `<span class="profile-status-pill warning">Pending</span>` 
                    : (isActive ? `<span class="profile-status-pill success" style="background:#dcfce7;color:#15803d;">Active</span>` : '');

                return `
                    <div class="profile-switch-card ${isActive ? 'active' : ''} ${isPending ? 'pending' : ''}" data-profile-id="${p.profile_id}" data-type-code="${p.type_code}" data-approved="${isApproved ? '1' : '0'}">
                        <div class="profile-switch-info">
                            <div class="profile-type-icon">
                                ${getProfileIconSvg(p.type_code)}
                            </div>
                            <div class="profile-switch-text">
                                <span class="profile-switch-title">${escapeHtml(title)}</span>
                                <span class="profile-switch-meta">${meta}</span>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:6px;">
                            ${badge}
                            <div class="profile-switch-radio"></div>
                        </div>
                    </div>
                `;
            }).join('');

            // Add click listeners to switch profiles
            profilesList.querySelectorAll('.profile-switch-card').forEach(card => {
                card.addEventListener('click', () => {
                    const isApproved = card.getAttribute('data-approved') === '1';
                    const profileId = card.getAttribute('data-profile-id');
                    const isActive = card.classList.contains('active');

                    if (isActive) return;

                    if (!isApproved) {
                        window.showToast('This profile is pending approval and cannot be activated yet.', 'warning');
                        return;
                    }

                    window.switchUserProfile(profileId);
                });
            });
        }
    }

    // Role-Specific Navigation Menu
    const roleNav = document.getElementById('sidebar-role-nav');
    const roleTitle = document.getElementById('sidebar-nav-role-title');
    if (roleNav) {
        const persona = user.persona || 'candidate';
        let navItems = [];

        if (persona === 'candidate' || persona === 'general') {
            if (roleTitle) roleTitle.textContent = 'GENERAL & OPPORTUNITIES';
            navItems = [
                { title: 'Home & Feed', route: 'dashboard/home', icon: getNavIcon('home') },
                { title: 'Join Talent Roster', route: 'roster/choose-track', icon: getNavIcon('plus-circle') },
                { title: 'Job Vacancies & Gigs', route: 'requests/list', icon: getNavIcon('briefcase') },
                { title: 'My Roster Dossiers', route: 'roster/choose-track', icon: getNavIcon('file-text') }
            ];
        } else if (persona === 'apprentice') {
            if (roleTitle) roleTitle.textContent = 'APPRENTICE WORKSPACE';
            navItems = [
                { title: 'Apprentice Dashboard', route: 'dashboard/home', icon: getNavIcon('home') },
                { title: 'Tasks & Work Orders', route: 'requests/list', icon: getNavIcon('check-circle') },
                { title: 'Supervisor & Placements', route: 'profile/view', icon: getNavIcon('user') }
            ];
        } else if (persona === 'associate') {
            if (roleTitle) roleTitle.textContent = 'CONSULTANT OPERATIONS';
            navItems = [
                { title: 'Consultant Dashboard', route: 'dashboard/home', icon: getNavIcon('home') },
                { title: 'Service Tasks & Deliverables', route: 'requests/list', icon: getNavIcon('briefcase') },
                { title: 'Invoices & Remittance', route: 'invoices/list', icon: getNavIcon('file-text') }
            ];
        } else if (persona === 'staff') {
            if (roleTitle) roleTitle.textContent = 'STAFF OPERATIONS';
            navItems = [
                { title: 'Operations Dashboard', route: 'dashboard/home', icon: getNavIcon('home') },
                { title: 'Roster Review Queue', route: 'admin/roster-queue', icon: getNavIcon('check-circle') },
                { title: 'Triage Queue & Requests', route: 'requests/list', icon: getNavIcon('inbox') },
                { title: 'Create Service Ticket', route: 'requests/new', icon: getNavIcon('plus-circle') },
                { title: 'Billing & Invoices', route: 'invoices/list', icon: getNavIcon('file-text') }
            ];
        } else if (persona === 'client') {
            if (roleTitle) roleTitle.textContent = 'CLIENT WORKSPACE';
            navItems = [
                { title: 'Client Dashboard', route: 'dashboard/home', icon: getNavIcon('home') },
                { title: 'My Service Requests', route: 'requests/list', icon: getNavIcon('briefcase') },
                { title: 'Request New Service', route: 'requests/new', icon: getNavIcon('plus-circle') },
                { title: 'Invoices & Statements', route: 'invoices/list', icon: getNavIcon('file-text') }
            ];
        } else if (persona === 'admin') {
            if (roleTitle) roleTitle.textContent = 'ADMIN COMMAND';
            navItems = [
                { title: 'Executive Overview', route: 'dashboard/home', icon: getNavIcon('home') },
                { title: 'Roster Review Queue', route: 'admin/roster-queue', icon: getNavIcon('check-circle') },
                { title: 'Account & Role Requests', route: 'dashboard/home', icon: getNavIcon('user-check') },
                { title: 'All Service Requests', route: 'requests/list', icon: getNavIcon('inbox') },
                { title: 'Dispatch Service Request', route: 'requests/new', icon: getNavIcon('plus-circle') },
                { title: 'Invoices Ledger', route: 'invoices/list', icon: getNavIcon('file-text') }
            ];
        }

        roleNav.innerHTML = navItems.map(item => `
            <a href="#" class="sidebar-nav-item" ${item.route ? `data-sidebar-nav="${item.route}"` : ''} ${item.action ? `data-sidebar-action="${item.action}"` : ''}>
                ${item.icon}
                <span>${escapeHtml(item.title)}</span>
            </a>
        `).join('');

        // Wire click handlers for dynamic nav items
        roleNav.querySelectorAll('.sidebar-nav-item').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                window.closeSidebar();
                const navRoute = link.getAttribute('data-sidebar-nav');
                if (navRoute) {
                    window.navigateTo(navRoute);
                }
            });
        });
    }
}

// Switch Active User Profile
window.switchUserProfile = async function(profileId) {
    const user = window.getUser();
    if (!user) return;

    window.showToast('Switching profile...', 'info');

    try {
        const formData = new FormData();
        formData.append('user_id', user.id);
        formData.append('profile_id', profileId);

        const res = await fetch(`${window.API_BASE}/api/mobile/user/switch-profile`, {
            method: 'POST',
            body: formData
        });

        const data = await res.json();
        if (data.status === 1 && data.user) {
            window.setUser(data.user);
            window.closeSidebar();
            window.showToast(`Switched to ${data.user.role_name}`, 'success');
            // Navigate to home view for new persona
            window.navigateTo('dashboard/home');
        } else {
            window.showToast(data.message || 'Failed to switch profile', 'error');
        }
    } catch (err) {
        console.error('Profile switch failed:', err);
        window.showToast('Network error while switching profile', 'error');
    }
};

// Open Request Profile Modal
window.openRequestProfileModal = async function() {
    const modal = document.getElementById('modal-request-profile');
    const container = document.getElementById('request-profile-types-container');
    const user = window.getUser();

    if (!modal || !user) return;

    modal.style.display = 'flex';
    window.closeSidebar();

    if (container) {
        container.innerHTML = '<div class="empty-state" style="padding: 12px;"><p>Loading options...</p></div>';
        try {
            const res = await fetch(`${window.API_BASE}/api/mobile/user/profiles?user_id=${user.id}`);
            const data = await res.json();

            if (data.status === 1 && data.available_types && data.available_types.length > 0) {
                container.innerHTML = data.available_types.map((t, idx) => {
                    const isRoster = (t.code === 'apprentice' || t.code === 'associate');
                    const badgeHtml = isRoster 
                        ? `<span style="font-size: 0.65rem; font-weight: 700; color: #15803d; background: #dcfce7; padding: 2px 8px; border-radius: 999px; white-space: nowrap;">Full Application →</span>` 
                        : '';
                    return `
                    <div class="type-select-card ${idx === 0 ? 'selected' : ''}" data-code="${t.code}">
                        <div class="type-select-icon">${getProfileIconSvg(t.code)}</div>
                        <div class="type-select-details">
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 6px;">
                                <span class="type-select-name">${escapeHtml(t.name)}</span>
                                ${badgeHtml}
                            </div>
                            <span class="type-select-desc">${escapeHtml(t.description || 'Specialized role')}</span>
                        </div>
                    </div>
                `}).join('');

                container.querySelectorAll('.type-select-card').forEach(card => {
                    card.addEventListener('click', () => {
                        const code = card.getAttribute('data-code');
                        if (code === 'apprentice' || code === 'associate') {
                            window.closeRequestProfileModal();
                            const trackId = (code === 'associate') ? 2 : 1;
                            window.navigateTo('roster/apply', { track_id: trackId, track_code: code });
                            return;
                        }

                        container.querySelectorAll('.type-select-card').forEach(c => c.classList.remove('selected'));
                        card.classList.add('selected');
                    });
                });
            } else {
                container.innerHTML = `<div class="empty-state" style="padding: 12px;"><p>You already have or have requested all available profile types!</p></div>`;
            }
        } catch (e) {
            container.innerHTML = `<div class="empty-state" style="padding: 12px;"><p>Failed to load profile options.</p></div>`;
        }
    }
};

window.closeRequestProfileModal = function() {
    const modal = document.getElementById('modal-request-profile');
    if (modal) modal.style.display = 'none';
};

// ==========================================================================
// Role Category Modal & Profile Management (Apprentice, Associate, Staff)
// ==========================================================================

const ROLE_CONFIGS = {
    apprentice: {
        code: 'apprentice',
        name: 'Apprentice',
        categoryLabel: 'Skills & Engineering Training',
        description: 'Guided hands-on industry experience, mentorship with senior engineers, and monthly training stipends.',
        titlePlaceholder: 'e.g. ICT & Systems Apprentice, Software Apprentice',
        notesLabel: 'PROGRAM, BACKGROUND & MOTIVATION',
        notesPlaceholder: 'Describe your university program, key technical interests, and why you are applying for this apprenticeship...',
        iconClass: 'icon-box-apprentice',
        bannerClass: 'banner-apprentice',
        svg: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>`,
        perks: [
            'Access to apprentice training tasks & work orders',
            'Daily/weekly project hours logging and timesheet submission',
            'Direct mentor feedback and performance tracking'
        ]
    },
    associate: {
        code: 'associate',
        name: 'Associate Consultant',
        categoryLabel: 'Professional Consultant & Subject Matter Expert',
        description: 'Deliver expert advisory, tackle client service requests, and earn competitive hourly rates.',
        titlePlaceholder: 'e.g. Senior DevOps Specialist, Financial Grants Consultant',
        notesLabel: 'CORE COMPETENCIES & CONSULTING EXPERIENCE',
        notesPlaceholder: 'Summarize your industry expertise, years of experience, relevant certifications, and consulting availability...',
        iconClass: 'icon-box-associate',
        bannerClass: 'banner-associate',
        svg: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>`,
        perks: [
            'Client service request triage and deliverables handling',
            'Work order execution and client milestone sign-off',
            'Direct consulting invoice submission and tracking'
        ]
    },
    staff: {
        code: 'staff',
        name: 'Company Representative',
        categoryLabel: 'Corporate Client Representative',
        description: 'Apply as an authorized corporate representative to manage organization accounts, review retainers, and dispatch service requests.',
        titlePlaceholder: 'e.g. Acme Logistics — Operations Manager',
        notesLabel: 'AUTHORIZATION MOTIVATION & SCOPE',
        notesPlaceholder: 'Describe your role within the company and why you are requesting corporate access...',
        iconClass: 'icon-box-staff',
        bannerClass: 'banner-staff',
        svg: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`,
        perks: [
            'Direct access to corporate service request workspace',
            'Monthly VAT billing and retainer balance statements',
            'Authorized liaison with Tsigiro consulting teams'
        ]
    }
};

window.openRoleCategoryModal = async function(roleCode) {
    const config = ROLE_CONFIGS[roleCode] || ROLE_CONFIGS.apprentice;
    const modal = document.getElementById('modal-role-category');
    const titleEl = document.getElementById('role-modal-title');
    const subtitleEl = document.getElementById('role-modal-subtitle');
    const bodyEl = document.getElementById('role-modal-body');
    const user = window.getUser();

    if (!modal || !user) return;

    modal.style.display = 'flex';
    window.closeSidebar();

    if (titleEl) titleEl.textContent = `${config.name} Account`;
    if (subtitleEl) subtitleEl.textContent = 'Account Status & Application';
    if (bodyEl) {
        bodyEl.innerHTML = `
            <div class="empty-state" style="padding: 24px;">
                <p>Loading account details...</p>
            </div>
        `;
    }

    // Fetch fresh profile data & completion status
    let profiles = user.profiles || [];
    try {
        const res = await fetch(`${window.API_BASE}/api/mobile/user/profiles?user_id=${user.id}`);
        const data = await res.json();
        if (data.status === 1) {
            if (data.profiles) {
                profiles = data.profiles;
                user.profiles = profiles;
            }
            if (data.user && data.user.profile_completion) {
                user.profile_completion = data.user.profile_completion;
            }
            window.setUser(user);
        }
    } catch (e) {
        console.warn('Using cached profiles for modal:', e);
    }

    // Determine category state
    const existing = profiles.find(p => p.type_code === roleCode);
    const isApproved = existing && (parseInt(existing.can_access_portal, 10) === 1 || existing.status_code === 'approved' || parseInt(existing.profilestatus, 10) === 3);
    const isPending = existing && !isApproved && (existing.status_code === 'pending' || parseInt(existing.profilestatus, 10) === 2);

    if (!bodyEl) return;

    if (isApproved) {
        // STATE 3: Active Account & Switch Action
        renderActiveRoleState(bodyEl, config, existing, user);
    } else if (isPending) {
        // STATE 2: Current Request Status
        renderPendingRoleState(bodyEl, config, existing);
    } else {
        // Not yet applied: For apprentice and associate, direct immediately to the single-page application form matching web
        if (roleCode === 'apprentice' || roleCode === 'associate') {
            window.closeRoleCategoryModal();
            const target = (roleCode === 'associate') ? 'opportunities/apply/associate' : 'opportunities/apply/apprentice';
            window.navigateTo(target, { track_code: roleCode });
            return;
        }

        if (roleCode === 'staff') {
            renderCompanyRepFormState(bodyEl, config, user);
        } else {
            const comp = user.profile_completion || {};
            if (comp.can_one_click_apply) {
                renderOneClickApplyState(bodyEl, config, user);
            } else {
                renderProfileRequiredState(bodyEl, config, user);
            }
        }
    }
};

window.closeRoleCategoryModal = function() {
    const modal = document.getElementById('modal-role-category');
    if (modal) modal.style.display = 'none';
};

function renderProfileRequiredState(container, config, user) {
    const comp = user.profile_completion || {};
    const personalOk = !!comp.personal_complete;
    const qualsCount = comp.qualifications_count || 0;
    const qualsOk = qualsCount > 0;
    const isRosterTrack = (config.code === 'apprentice' || config.code === 'associate');

    container.innerHTML = `
        <div style="display: flex; flex-direction: column; gap: 14px;">
            <div class="role-hero-banner ${config.bannerClass}" style="border-left: 4px solid #159b75;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                    <div style="font-size: 1.5rem;">🚀</div>
                    <div>
                        <strong style="font-size: 0.95rem; color: var(--text-main);">${config.name} Application</strong>
                        <div style="font-size: 0.72rem; color: var(--text-muted);">Full Candidate Intake Process</div>
                    </div>
                </div>
                <p class="role-banner-desc">
                    ${isRosterTrack 
                        ? 'You can complete your full 5-stage application directly with your CV, specialty, skills matrix, and referee details.' 
                        : 'Vetting partners require verified personal details and credentials before processing your 1-Click application.'}
                </p>
            </div>

            ${isRosterTrack ? `
            <div style="margin-top: 2px;">
                <button type="button" id="btn-modal-direct-roster-apply" class="btn-primary" style="width: 100%; padding: 13px 16px; font-weight: 800; font-size: 0.88rem; border-radius: 10px; background: linear-gradient(135deg, #159b75 0%, #0e7256 100%); display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(21, 155, 117, 0.25); cursor: pointer; color: #ffffff; border: none;">
                    <span>Launch Full ${config.name} Application →</span>
                </button>
            </div>
            ` : ''}

            <div style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                1-Click Apply Prerequisites
            </div>

            <div class="card" style="padding: 12px; display: flex; justify-content: space-between; align-items: center; border: 1px solid ${personalOk ? '#86efac' : 'var(--border)'}; background: ${personalOk ? '#f0fdf4' : 'var(--card-bg)'};">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="font-size: 1.3rem;">👤</div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);">Personal Details Profile</div>
                        <div style="font-size: 0.72rem; color: var(--text-muted);">Legal name, phone, city &amp; ID</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="status-pill ${personalOk ? 'status-pill-active' : 'status-pill-pending'}" style="font-size: 0.68rem;">
                        ${personalOk ? '✓ Completed' : '⚠️ Incomplete'}
                    </span>
                    <button type="button" id="btn-modal-goto-personal" class="btn-primary" style="width: auto; padding: 6px 12px; font-size: 0.74rem; font-weight: 700; margin: 0; background: ${personalOk ? '#64748b' : 'var(--primary)'};">
                        ${personalOk ? 'Edit' : 'Complete →'}
                    </button>
                </div>
            </div>

            <div class="card" style="padding: 12px; display: flex; justify-content: space-between; align-items: center; border: 1px solid ${qualsOk ? '#86efac' : 'var(--border)'}; background: ${qualsOk ? '#f0fdf4' : 'var(--card-bg)'};">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="font-size: 1.3rem;">🎓</div>
                    <div>
                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-main);">Qualifications Profile</div>
                        <div style="font-size: 0.72rem; color: var(--text-muted);">Degrees, diplomas &amp; certifications</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="status-pill ${qualsOk ? 'status-pill-active' : 'status-pill-pending'}" style="font-size: 0.68rem;">
                        ${qualsOk ? '✓ ' + qualsCount + ' Added' : '⚠️ None Added'}
                    </span>
                    <button type="button" id="btn-modal-goto-quals" class="btn-primary" style="width: auto; padding: 6px 12px; font-size: 0.74rem; font-weight: 700; margin: 0; background: ${qualsOk ? '#64748b' : 'var(--primary)'};">
                        ${qualsOk ? 'View' : 'Add →'}
                    </button>
                </div>
            </div>

            <div style="background: rgba(59, 130, 246, 0.08); border-radius: 8px; padding: 10px 12px; display: flex; gap: 8px; align-items: flex-start;">
                <span style="font-size: 0.9rem;">⚡</span>
                <span style="font-size: 0.72rem; color: #1e40af; line-height: 1.4;">
                    Once both profile sections are saved, <strong>1-Click Apply</strong> will automatically unlock so you can apply instantly.
                </span>
            </div>
        </div>
    `;

    const directApplyBtn = container.querySelector('#btn-modal-direct-roster-apply');
    if (directApplyBtn) {
        directApplyBtn.addEventListener('click', () => {
            window.closeRoleCategoryModal();
            const target = (config.code === 'associate') ? 'opportunities/apply/associate' : 'opportunities/apply/apprentice';
            window.navigateTo(target, { track_code: config.code });
        });
    }

    container.querySelector('#btn-modal-goto-personal')?.addEventListener('click', () => {
        window.closeRoleCategoryModal();
        window.navigateTo('profile/personal');
    });

    container.querySelector('#btn-modal-goto-quals')?.addEventListener('click', () => {
        window.closeRoleCategoryModal();
        window.navigateTo('profile/qualifications');
    });
}

function renderOneClickApplyState(container, config, user) {
    const comp = user.profile_completion || {};
    const summary = comp.summary || {};

    container.innerHTML = `
        <form id="form-one-click-apply" style="display: flex; flex-direction: column; gap: 14px;">
            <div class="role-hero-banner ${config.bannerClass}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="role-icon-box ${config.iconClass}" style="margin: 0; width: 34px; height: 34px;">
                            ${config.svg}
                        </div>
                        <div>
                            <strong style="font-size: 0.95rem; color: var(--text-main);">${config.name} Application</strong>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">${config.categoryLabel}</div>
                        </div>
                    </div>
                    <span class="status-pill status-pill-active" style="font-size: 0.68rem;">⚡ 1-Click Ready</span>
                </div>
                <p class="role-banner-desc">${config.description}</p>
            </div>

            <!-- Verified Candidate Profile Dossier Snapshot -->
            <div class="card" style="padding: 14px; background: #f8fafc; border: 1px solid var(--border);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.4px;">
                        Verified Profile Snapshot
                    </div>
                    <span style="font-size: 0.7rem; color: #166534; font-weight: 600;">✓ Auto-Attached to Dossier</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.78rem;">
                    <div>
                        <span style="color: var(--text-muted);">Applicant:</span>
                        <div style="font-weight: 700; color: var(--text-main);">${escapeHtml(user.name)}</div>
                    </div>
                    <div>
                        <span style="color: var(--text-muted);">Phone:</span>
                        <div style="font-weight: 600; color: var(--text-main);">${escapeHtml(summary.phone || 'On file')}</div>
                    </div>
                    <div>
                        <span style="color: var(--text-muted);">Location:</span>
                        <div style="font-weight: 600; color: var(--text-main);">${escapeHtml(summary.city || 'Harare')}</div>
                    </div>
                    <div>
                        <span style="color: var(--text-muted);">Qualification:</span>
                        <div style="font-weight: 600; color: var(--text-main); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(summary.latest_qualification || 'Degree on file')}</div>
                    </div>
                </div>
            </div>

            <div>
                <label for="role-app-title" style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.4px;">PROPOSED SPECIALTY / TITLE</label>
                <input type="text" id="role-app-title" class="form-input" value="${config.titlePlaceholder.split(',')[0].replace('e.g. ', '').trim()}" required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border); font-size: 0.85rem; background: var(--card-bg); color: var(--text-main);">
            </div>

            <button type="submit" id="btn-submit-one-click" class="btn-primary" style="width: 100%; padding: 14px; font-weight: 800; font-size: 0.95rem; border-radius: 10px; cursor: pointer; margin-top: 4px; background: linear-gradient(135deg, #159b75 0%, #0e7256 100%); box-shadow: 0 4px 12px rgba(21, 155, 117, 0.3);">
                ⚡ 1-Click Apply for ${config.name}
            </button>
        </form>
    `;

    const form = container.querySelector('#form-one-click-apply');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const titleVal = document.getElementById('role-app-title').value.trim();
        const submitBtn = document.getElementById('btn-submit-one-click');

        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting 1-Click Application...';

        try {
            const formData = new FormData();
            formData.append('user_id', user.id);
            formData.append('type_code', config.code);
            formData.append('display_title', titleVal);
            formData.append('notes', `1-Click application submitted with verified credentials (${summary.latest_qualification || 'Verified profile'}).`);

            const res = await fetch(`${window.API_BASE}/api/mobile/user/request-profile`, {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            if (data.status === 1) {
                window.showToast(`✓ 1-Click Application submitted for ${config.name}!`, 'success');

                const newProfile = {
                    type_code: config.code,
                    type_name: config.name,
                    display_title: titleVal,
                    request_notes: `1-Click application submitted with verified credentials.`,
                    reg_date: new Date().toISOString(),
                    status_name: 'Pending Review',
                    status_code: 'pending',
                    profilestatus: 2,
                    can_access_portal: 0
                };

                if (data.user) {
                    window.setUser(data.user);
                } else if (data.profiles) {
                    user.profiles = data.profiles;
                    window.setUser(user);
                }

                const statsGridEl = document.getElementById('dash-stats-grid');
                if (statsGridEl && window.renderRoleActionButtons) {
                    window.renderRoleActionButtons(statsGridEl, user.profiles);
                }

                renderPendingRoleState(container, config, newProfile);
            } else {
                submitBtn.disabled = false;
                submitBtn.textContent = `⚡ 1-Click Apply for ${config.name}`;
                window.showToast(data.message || 'Failed to submit application', 'error');
            }
        } catch (err) {
            submitBtn.disabled = false;
            submitBtn.textContent = `⚡ 1-Click Apply for ${config.name}`;
            window.showToast('Network error while submitting application', 'error');
        }
    });
}

async function renderCompanyRepFormState(container, config, user) {
    container.innerHTML = `
        <form id="form-company-rep-app" style="display: flex; flex-direction: column; gap: 14px;">
            <div class="role-hero-banner banner-staff">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <div class="role-icon-box icon-box-staff" style="margin: 0; width: 34px; height: 34px;">
                        ${config.svg}
                    </div>
                    <div>
                        <strong style="font-size: 0.95rem; color: var(--text-main);">Company Representative Application</strong>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">Corporate Client Affiliation &amp; Access</div>
                    </div>
                </div>
                <p class="role-banner-desc">Apply as a representative for an authorized corporate client to access projects, billing, and services.</p>
            </div>

            <div>
                <label for="staff-company-select" style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.4px;">SELECT CLIENT ORGANIZATION *</label>
                <select id="staff-company-select" class="form-input" required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border); font-size: 0.85rem; background: var(--card-bg); color: var(--text-main);">
                    <option value="">Loading client organizations...</option>
                </select>
            </div>

            <div>
                <label for="staff-role-select" style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.4px;">ORGANIZATION ROLE *</label>
                <select id="staff-role-select" class="form-input" required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border); font-size: 0.85rem; background: var(--card-bg); color: var(--text-main);">
                    <option value="3">Authorized Requester / Project Lead</option>
                    <option value="2">Billing &amp; Financial Officer</option>
                    <option value="1">Organization Signatory / Owner</option>
                    <option value="4">General Corporate Team Member</option>
                </select>
            </div>

            <div>
                <label for="staff-motivation" style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.4px;">AUTHORIZATION MOTIVATION &amp; NOTES *</label>
                <textarea id="staff-motivation" rows="3" class="form-input" placeholder="Explain your position in the company and your need for corporate portal authorization..." required style="width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border); font-size: 0.85rem; background: var(--card-bg); color: var(--text-main); resize: vertical;"></textarea>
            </div>

            <div style="background: rgba(245, 158, 11, 0.1); border-left: 3px solid #f59e0b; border-radius: 6px; padding: 10px 12px; font-size: 0.72rem; color: #92400e; line-height: 1.4;">
                <strong>Client Vetting Required:</strong> Applications are forwarded to the registered corporate signatory of the selected organization for confirmation alongside Tsigiro vetting.
            </div>

            <button type="submit" id="btn-submit-staff-rep" class="btn-primary" style="width: 100%; padding: 13px; font-weight: 700; border-radius: 10px; cursor: pointer; margin-top: 4px;">
                Submit Representative Application →
            </button>
        </form>
    `;

    // Fetch companies
    const compSelect = container.querySelector('#staff-company-select');
    const roleSelect = container.querySelector('#staff-role-select');
    try {
        const res = await fetch(`${window.API_BASE}/api/mobile/companies?user_id=${user.id}`);
        const data = await res.json();
        if (data && data.companies && compSelect) {
            compSelect.innerHTML = '<option value="">-- Choose Corporate Client --</option>' + 
                data.companies.map(c => `<option value="${c.iD}">${escapeHtml(c.trading_name || c.legal_name)}${c.city ? ' (' + escapeHtml(c.city) + ')' : ''}</option>`).join('');
        }
        if (data && data.roles && roleSelect) {
            roleSelect.innerHTML = data.roles.map(r => `<option value="${r.iD}">${escapeHtml(r.name)}</option>`).join('');
        }
    } catch (e) {
        console.warn('Failed to load companies dynamically:', e);
        if (compSelect) {
            compSelect.innerHTML = `
                <option value="">-- Choose Corporate Client --</option>
                <option value="1">Acme Global Logistics</option>
                <option value="2">Delta Beverages Holdings</option>
                <option value="10">Acme Tech Solutions</option>
                <option value="12">EcoSolutions Zimbabwe</option>
                <option value="13">ZimFin Bank</option>
            `;
        }
    }

    const form = container.querySelector('#form-company-rep-app');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const compId = compSelect ? compSelect.value : '';
        const roleId = roleSelect ? roleSelect.value : '';
        const motivation = document.getElementById('staff-motivation').value.trim();
        const submitBtn = document.getElementById('btn-submit-staff-rep');

        if (!compId || !roleId || !motivation) {
            window.showToast('Please select a company, role, and provide motivation.', 'warning');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting Representative Request...';

        try {
            const formData = new FormData();
            formData.append('user_id', user.id);
            formData.append('type_code', 'staff');
            formData.append('company_id', compId);
            formData.append('role_id', roleId);
            formData.append('notes', motivation);

            const res = await fetch(`${window.API_BASE}/api/mobile/user/request-profile`, {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            if (data.status === 1) {
                window.showToast('✓ Representative application submitted for client vetting!', 'success');

                const newProfile = {
                    type_code: 'staff',
                    type_name: 'Company Representative',
                    display_title: data.user?.profiles?.find(p => p.type_code === 'staff')?.display_title || 'Company Representative',
                    request_notes: motivation,
                    reg_date: new Date().toISOString(),
                    status_name: 'Pending Review',
                    status_code: 'pending',
                    profilestatus: 2,
                    can_access_portal: 0
                };

                if (data.user) {
                    window.setUser(data.user);
                } else if (data.profiles) {
                    user.profiles = data.profiles;
                    window.setUser(user);
                }

                const statsGridEl = document.getElementById('dash-stats-grid');
                if (statsGridEl && window.renderRoleActionButtons) {
                    window.renderRoleActionButtons(statsGridEl, user.profiles);
                }

                renderPendingRoleState(container, config, newProfile);
            } else {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Representative Application →';
                window.showToast(data.message || 'Failed to submit application', 'error');
            }
        } catch (err) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Representative Application →';
            window.showToast('Network error while submitting application', 'error');
        }
    });
}

function renderPendingRoleState(container, config, profile) {
    let regDateDisplay = 'Just now';
    if (profile.reg_date) {
        try {
            const d = new Date(profile.reg_date.replace(' ', 'T'));
            regDateDisplay = d.toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        } catch (e) {
            regDateDisplay = profile.reg_date;
        }
    }

    container.innerHTML = `
        <div class="status-view-container">
            <div class="role-hero-banner ${config.bannerClass}" style="border-left: 4px solid #f59e0b;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="role-icon-box ${config.iconClass}" style="margin: 0; width: 32px; height: 32px;">
                            ${config.svg}
                        </div>
                        <strong style="font-size: 0.95rem; color: var(--text-main);">${config.name} Application</strong>
                    </div>
                    <span class="role-status-pill status-pill-pending">⏳ Under Review</span>
                </div>
                <p class="role-banner-desc">Your request has been received and is in the administrative vetting pipeline.</p>
            </div>

            <!-- Review Stepper -->
            <div style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px;">Application Timeline</div>
            <div class="review-stepper">
                <div class="step-item step-done">
                    <div class="step-dot">✓</div>
                    <div class="step-content">
                        <div class="step-title">Application Submitted</div>
                        <div class="step-desc">Received on ${regDateDisplay}</div>
                    </div>
                </div>
                <div class="step-item step-active">
                    <div class="step-dot">⚡</div>
                    <div class="step-content">
                        <div class="step-title" style="color: #b45309;">Vetting & Credentials Review</div>
                        <div class="step-desc">Tsigiro talent operations are evaluating your proposed specialty and background.</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-dot">3</div>
                    <div class="step-content">
                        <div class="step-title">Account Activation & Permissions</div>
                        <div class="step-desc">Upon approval, your specialized account permissions and active workspace will be unlocked.</div>
                    </div>
                </div>
            </div>

            <!-- Detail Summary Card -->
            <div class="detail-summary-card">
                <div class="detail-label">Requested Title / Specialty</div>
                <div class="detail-value" style="font-weight: 600;">${escapeHtml(safeDecode(profile.display_title || config.name))}</div>

                <div class="detail-label" style="margin-top: 10px;">Submitted Motivation & Notes</div>
                <div class="detail-value" style="color: var(--text-muted); font-style: italic; white-space: pre-wrap;">"${escapeHtml(safeDecode(profile.request_notes || 'No extra notes provided.'))}"</div>

                ${profile.reviewer_notes ? `
                    <div class="detail-label" style="margin-top: 10px; color: #b45309;">Reviewer Comments</div>
                    <div class="detail-value" style="background: rgba(245, 158, 11, 0.1); padding: 8px; border-radius: 6px; color: #92400e;">${escapeHtml(safeDecode(profile.reviewer_notes))}</div>
                ` : ''}
            </div>

            <div style="background: rgba(59, 130, 246, 0.08); border-radius: 8px; padding: 10px 12px; margin-bottom: 14px; display: flex; gap: 8px; align-items: flex-start;">
                <span style="font-size: 0.9rem;">ℹ️</span>
                <span style="font-size: 0.72rem; color: #1e40af; line-height: 1.4;">
                    Applications are typically processed within 24–48 hours. You will receive an immediate notification as soon as your account is approved.
                </span>
            </div>

            <div id="role-modal-action-buttons" style="display: flex; flex-direction: column; gap: 10px;">
                ${(config.code === 'apprentice' || config.code === 'associate') ? `
                <button type="button" id="btn-view-full-dossier" class="btn-primary" style="width: 100%; padding: 13px; font-weight: 800; font-size: 0.88rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; background: linear-gradient(135deg, #159b75 0%, #0e7256 100%); color: #ffffff; border: none; box-shadow: 0 4px 12px rgba(21, 155, 117, 0.25);">
                    <span>📋 Open Full Roster Application →</span>
                </button>
                ` : ''}

                <button type="button" id="btn-revoke-application" style="width: 100%; padding: 13px; font-weight: 700; font-size: 0.86rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1.5px solid #ef4444; background: rgba(239, 68, 68, 0.08); color: #ef4444; transition: all 0.2s ease;">
                    <span style="font-size: 0.95rem;">🗑️</span>
                    <span>Revoke / Remove Application</span>
                </button>

                <button type="button" id="btn-close-role-status" class="btn-primary" style="width: 100%; padding: 12px; font-weight: 600; border-radius: 10px; cursor: pointer; background: var(--card-bg); color: var(--text-main); border: 1px solid var(--border);">
                    Close
                </button>
            </div>
        </div>
    `;

    function safeDecode(val) {
        if (!val) return '';
        try {
            return decodeURIComponent(String(val).replace(/\+/g, ' '));
        } catch (e) {
            return String(val).replace(/%26/g, '&').replace(/%20/g, ' ');
        }
    }

    function wireActionButtons() {
        const closeBtn = container.querySelector('#btn-close-role-status');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                window.closeRoleCategoryModal();
            });
        }

        const viewDossierBtn = container.querySelector('#btn-view-full-dossier');
        if (viewDossierBtn) {
            viewDossierBtn.addEventListener('click', () => {
                window.closeRoleCategoryModal();
                const trackId = (config.code === 'associate') ? 2 : 1;
                window.navigateTo('roster/apply', { track_id: trackId, track_code: config.code });
            });
        }

        const revokeBtn = container.querySelector('#btn-revoke-application');
        const actionContainer = container.querySelector('#role-modal-action-buttons');

        if (revokeBtn && actionContainer) {
            revokeBtn.addEventListener('click', () => {
                // Render inline confirmation box
                actionContainer.innerHTML = `
                    <div style="background: rgba(239, 68, 68, 0.08); border: 1.5px solid #ef4444; border-radius: 10px; padding: 14px; text-align: center;">
                        <div style="font-weight: 800; font-size: 0.88rem; color: #ef4444; margin-bottom: 6px;">
                            ⚠️ Confirm Application Revocation
                        </div>
                        <div style="font-size: 0.74rem; color: var(--text-main); margin-bottom: 12px; line-height: 1.4;">
                            Are you sure you want to withdraw your <strong>${escapeHtml(config.name)}</strong> application? You can submit a new application at any time.
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" id="btn-cancel-revoke" style="flex: 1; padding: 10px; font-size: 0.78rem; font-weight: 600; border-radius: 8px; cursor: pointer; background: var(--card-bg); color: var(--text-main); border: 1px solid var(--border);">
                                Keep Application
                            </button>
                            <button type="button" id="btn-confirm-revoke" style="flex: 1; padding: 10px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; cursor: pointer; background: #ef4444; color: #ffffff; border: none;">
                                Yes, Revoke
                            </button>
                        </div>
                    </div>
                `;

                const cancelBtn = actionContainer.querySelector('#btn-cancel-revoke');
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', () => {
                        // Restore default action buttons
                        actionContainer.innerHTML = `
                            <button type="button" id="btn-revoke-application" style="width: 100%; padding: 13px; font-weight: 700; font-size: 0.86rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1.5px solid #ef4444; background: rgba(239, 68, 68, 0.08); color: #ef4444; transition: all 0.2s ease;">
                                <span style="font-size: 0.95rem;">🗑️</span>
                                <span>Revoke / Remove Application</span>
                            </button>
                            <button type="button" id="btn-close-role-status" class="btn-primary" style="width: 100%; padding: 12px; font-weight: 600; border-radius: 10px; cursor: pointer; background: var(--card-bg); color: var(--text-main); border: 1px solid var(--border);">
                                Close
                            </button>
                        `;
                        wireActionButtons();
                    });
                }

                const confirmBtn = actionContainer.querySelector('#btn-confirm-revoke');
                if (confirmBtn) {
                    confirmBtn.addEventListener('click', async () => {
                        confirmBtn.disabled = true;
                        confirmBtn.style.opacity = '0.7';
                        confirmBtn.innerHTML = `
                            <span style="display: inline-block; width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite;"></span>
                            <span>Withdrawing...</span>
                        `;

                        try {
                            const user = window.getUser();
                            const res = await window.apiFetch('/api/mobile/user/revoke-profile', {
                                method: 'POST',
                                body: {
                                    user_id: user ? user.id : '',
                                    profile_id: profile.profile_id || profile.iD || profile.id || '',
                                    type_code: profile.type_code || config.code || ''
                                }
                            });

                            if (res && res.status === 1) {
                                window.showToast(`✓ Application for ${config.name} withdrawn successfully.`, 'success');
                                if (res.user) {
                                    window.setUser(res.user);
                                }

                                // Refresh dashboard role action buttons if on home dashboard
                                const statsGridEl = document.getElementById('dash-stats-grid');
                                if (statsGridEl && window.renderRoleActionButtons) {
                                    window.renderRoleActionButtons(statsGridEl, res.user ? res.user.profiles : []);
                                }

                                // Smoothly refresh modal to fresh application state
                                if (window.openRoleCategoryModal) {
                                    window.openRoleCategoryModal(config.code);
                                } else {
                                    window.closeRoleCategoryModal();
                                }
                            } else {
                                confirmBtn.disabled = false;
                                confirmBtn.style.opacity = '1';
                                confirmBtn.textContent = 'Yes, Revoke';
                                window.showToast(res ? res.message : 'Failed to revoke application', 'error');
                            }
                        } catch (err) {
                            confirmBtn.disabled = false;
                            confirmBtn.style.opacity = '1';
                            confirmBtn.textContent = 'Yes, Revoke';
                            window.showToast('Network error while revoking application', 'error');
                        }
                    });
                }
            });
        }
    }

    wireActionButtons();
}

function renderActiveRoleState(container, config, profile, user) {
    const isActiveWorkspace = (user.active_profile && (user.active_profile.profile_id === profile.profile_id || user.active_profile.type_code === profile.type_code));

    let regDateDisplay = 'Active Member';
    if (profile.reg_date) {
        try {
            const d = new Date(profile.reg_date.replace(' ', 'T'));
            regDateDisplay = `Active since ${d.toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' })}`;
        } catch (e) {
            regDateDisplay = `Active since ${profile.reg_date}`;
        }
    }

    container.innerHTML = `
        <div class="active-view-container">
            <div class="role-hero-banner ${config.bannerClass}" style="border-left: 4px solid #10b981;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="role-icon-box ${config.iconClass}" style="margin: 0; width: 32px; height: 32px;">
                            ${config.svg}
                        </div>
                        <strong style="font-size: 0.95rem; color: var(--text-main);">${escapeHtml(profile.display_title || config.name)}</strong>
                    </div>
                    <span class="role-status-pill status-pill-active">✓ Active Account</span>
                </div>
                <p class="role-banner-desc">${regDateDisplay} — Full portal access enabled.</p>
            </div>

            <!-- Active Workspace Status or Switch Button -->
            ${isActiveWorkspace ? `
                <div class="detail-summary-card" style="border-color: rgba(16, 185, 129, 0.35); background: rgba(16, 185, 129, 0.05); display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                    <span style="font-size: 1.2rem;">🟢</span>
                    <div>
                        <strong style="font-size: 0.85rem; color: #065f46;">Currently Active Workspace</strong>
                        <div style="font-size: 0.72rem; color: var(--text-muted);">You are actively working inside this account.</div>
                    </div>
                </div>
            ` : `
                <button type="button" id="btn-modal-switch-workspace" class="btn-primary" style="width: 100%; padding: 13px; font-weight: 700; border-radius: 10px; cursor: pointer; margin-bottom: 14px; background: linear-gradient(135deg, #159b75, #0e7256); box-shadow: 0 4px 12px rgba(21, 155, 117, 0.25);">
                    🚀 Switch to this Profile & Launch Workspace
                </button>
            `}

            <!-- Category Functions -->
            <div style="font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Enabled Account Features</div>
            <div class="active-perks-list">
                ${config.perks.map(p => `
                    <div class="perk-item">
                        <span style="color: #10b981; font-weight: 700;">✓</span>
                        <span>${p}</span>
                    </div>
                `).join('')}
            </div>

            <button type="button" id="btn-close-active-state" class="btn-primary" style="width: 100%; padding: 12px; font-weight: 600; border-radius: 10px; cursor: pointer; background: var(--card-bg); color: var(--text-main); border: 1px solid var(--border); margin-top: 6px;">
                Close
            </button>
        </div>
    `;

    const switchBtn = container.querySelector('#btn-modal-switch-workspace');
    if (switchBtn) {
        switchBtn.addEventListener('click', async () => {
            window.closeRoleCategoryModal();
            await window.switchUserProfile(profile.profile_id);
        });
    }

    const closeBtn = container.querySelector('#btn-close-active-state');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            window.closeRoleCategoryModal();
        });
    }
}

// Global Logout Helper
window.logoutUser = async function() {
    const user = window.getUser();
    try {
        if (user) {
            const fd = new URLSearchParams();
            fd.append('user_id', user.id);
            await fetch(`${window.API_BASE}/api/mobile/logout`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                credentials: 'include',
                body: fd.toString()
            });
        }
    } catch (e) {
        console.warn('Logout failed:', e);
    } finally {
        window.closeSidebar();
        window.clearUser();
        window.navigateTo('auth/login');
        window.showToast('You have been signed out', 'info');
    }
};

// Router Navigation
window.navigateTo = function(routeKey, data = null) {
    const viewPath = window.routeMap[routeKey];
    if (!viewPath) {
        console.error(`Route not found: ${routeKey}`);
        return;
    }

    const user = window.getUser();
    if (!user && viewPath.indexOf('auth/') === -1) {
        window.navigateTo('auth/login');
        return;
    }

    if (user && typeof window.refreshNotifications === 'function') {
        window.refreshNotifications();
    }

    // Update bottom nav active state
    document.querySelectorAll('.nav-item').forEach(item => {
        if (item.getAttribute('data-nav') === routeKey) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });

    // Global FAB (+ New Request) visibility
    const globalFab = document.getElementById('global-fab-btn');
    if (globalFab) {
        const persona = user ? user.persona : '';
        if ((persona === 'client' || persona === 'admin') && (routeKey === 'dashboard/home' || routeKey === 'requests/list')) {
            globalFab.style.display = 'flex';
        } else {
            globalFab.style.display = 'none';
        }
    }

    loadView(viewPath, data);
};

function loadLocalFile(url) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 0 || (xhr.status >= 200 && xhr.status < 300)) {
                    resolve(xhr.responseText);
                } else {
                    reject(new Error(`Failed to load ${url} with status ${xhr.status}`));
                }
            }
        };
        xhr.onerror = function() {
            reject(new Error(`Network error loading ${url}`));
        };
        xhr.send();
    });
}

window.loadView = async function(viewName, data = null) {
    const loadId = Date.now() + "_" + Math.random().toString(36).slice(2, 8);
    window.__activeViewLoadId = loadId;

    if (data !== null) viewDataStore[viewName] = data;
    const passedData = viewDataStore[viewName] || null;

    const scriptUrl = `views/${viewName}.js`;
    const htmlUrl = `views/${viewName}.html`;

    const mainContent = document.getElementById("mainContent");

    try {
        let htmlText = viewCache[viewName] ? viewCache[viewName].html : null;
        if (!htmlText) {
            htmlText = await loadLocalFile(htmlUrl);
            viewCache[viewName] = { html: htmlText };
        }

        if (window.__activeViewLoadId !== loadId) return;

        // Render HTML content
        mainContent.innerHTML = htmlText;
        mainContent.setAttribute('data-active-view', viewName);

        // Load and execute script
        loadScript(scriptUrl, passedData, loadId, viewName);

    } catch (err) {
        console.error(`Failed to load view: ${viewName}`, err);
        mainContent.innerHTML = `<div class="empty-state"><div class="empty-icon">⚠️</div><p>Failed to load view (${viewName})</p></div>`;
    }
};

async function loadScript(scriptUrl, passedData, loadId, viewName) {
    window.init = undefined;

    try {
        const scriptText = await loadLocalFile(scriptUrl);
        if (window.__activeViewLoadId !== loadId) return;

        // Remove previous dynamically inserted scripts
        const oldScript = document.getElementById('view-script');
        if (oldScript) oldScript.remove();

        const script = document.createElement('script');
        script.id = 'view-script';
        script.textContent = scriptText;
        document.body.appendChild(script);

        if (typeof window.init === 'function') {
            window.init(passedData);
        }
    } catch (err) {
        console.warn(`No script or script error for view: ${viewName}`, err);
    }
}

// --------------------------------------------------------------------------
// Notification Center Controller
// --------------------------------------------------------------------------
let currentNotifFilter = 'all';
let cachedNotifications = [];

window.refreshNotifications = async function() {
    const user = window.getUser();
    const badgeEl = document.getElementById('notifications-badge');
    const subtitleEl = document.getElementById('notif-unread-subtitle');
    if (!user) {
        if (badgeEl) badgeEl.style.display = 'none';
        return;
    }

    // Immediately show cached notifications if available
    const cached = window.getCache('notifications_' + user.id);
    if (cached && Array.isArray(cached.notifications)) {
        cachedNotifications = cached.notifications;
        const unreadCount = cached.unread_count || 0;
        if (badgeEl) {
            badgeEl.style.display = unreadCount > 0 ? 'block' : 'none';
        }
        if (subtitleEl) {
            subtitleEl.textContent = `${unreadCount} unread alert${unreadCount === 1 ? '' : 's'}`;
        }
        renderNotificationsList();
    }

    try {
        const res = await fetch(`${window.API_BASE}/api/mobile/notifications?user_id=${user.id}`, {
            credentials: 'include'
        });
        const data = await res.json();
        if (data.status === 1) {
            cachedNotifications = data.notifications || [];
            window.setCache('notifications_' + user.id, data);
            const unreadCount = data.unread_count || 0;
            if (badgeEl) {
                badgeEl.style.display = unreadCount > 0 ? 'block' : 'none';
            }
            if (subtitleEl) {
                subtitleEl.textContent = `${unreadCount} unread alert${unreadCount === 1 ? '' : 's'}`;
            }
            renderNotificationsList();
        }
    } catch (e) {
        console.warn('Failed to refresh notifications from network, using cache:', e);
    }
};

function renderNotificationsList() {
    const container = document.getElementById('notifications-list-container');
    if (!container) return;

    let items = cachedNotifications;
    if (currentNotifFilter === 'unread') {
        items = items.filter(n => n.is_unread);
    }

    if (items.length === 0) {
        container.innerHTML = `
            <div class="empty-state" style="padding: 28px 12px; text-align: center;">
                <div style="font-size: 2.2rem; margin-bottom: 8px;">🔔</div>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">No ${currentNotifFilter === 'unread' ? 'unread ' : ''}notifications at this time.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = items.map(n => `
        <div class="notif-card ${n.is_unread ? 'unread' : ''}" data-id="${n.iD}" data-link="${n.link || ''}">
            <div class="notif-card-header">
                <span class="notif-card-title">${n.title}</span>
                <span class="notif-card-time">${n.time_ago}</span>
            </div>
            <p class="notif-card-msg">${n.message}</p>
        </div>
    `).join('');

    container.querySelectorAll('.notif-card').forEach(card => {
        card.addEventListener('click', async () => {
            const notifId = card.getAttribute('data-id');
            const link = card.getAttribute('data-link');
            const user = window.getUser();
            if (notifId && user) {
                try {
                    const fd = new URLSearchParams();
                    fd.append('id', notifId);
                    fd.append('user_id', user.id);
                    await fetch(`${window.API_BASE}/api/mobile/notifications/read`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        credentials: 'include',
                        body: fd.toString()
                    });
                    const item = cachedNotifications.find(n => String(n.iD) === String(notifId));
                    if (item) {
                        item.is_unread = false;
                        item.is_read = 1;
                    }
                    card.classList.remove('unread');
                    window.refreshNotifications();
                } catch (err) {
                    console.error('Failed to mark read:', err);
                }
            }
            if (link) {
                const modal = document.getElementById('modal-notifications');
                if (modal) modal.style.display = 'none';
                if (link.startsWith('requests/view')) {
                    window.navigateTo('requests/list');
                } else if (link.startsWith('invoices/list')) {
                    window.navigateTo('invoices/list');
                } else if (link.startsWith('vacancies/list')) {
                    window.navigateTo('requests/list');
                }
            }
        });
    });
}

// --------------------------------------------------------------------------
// Over-The-Air (OTA) Hot-Update System
// --------------------------------------------------------------------------
window.checkAppUpdates = function() {
    if (window.TsigiroNative && typeof window.TsigiroNative.checkForUpdates === 'function') {
        window.showToast('Checking for updates...', 'info');
        window.TsigiroNative.checkForUpdates(window.API_BASE);
    } else {
        const bundleVer = (window.TsigiroNative && typeof window.TsigiroNative.getBundleVersion === 'function')
            ? window.TsigiroNative.getBundleVersion()
            : '1.0.0';
        fetch(`${window.API_BASE}/api/mobile/ota/check?bundle_version=${bundleVer}`)
            .then(res => res.json())
            .then(data => {
                if (data.update_available) {
                    window.showToast(`Update v${data.latest_bundle_version} available!`, 'success');
                } else {
                    window.showToast('Application is up to date!', 'info');
                }
            })
            .catch(() => {
                window.showToast('Could not reach update server.', 'error');
            });
    }
};

window.onOtaUpdateReady = function(version, notes) {
    console.log(`[OTA] Update ready: v${version} - ${notes}`);
    const banner = document.getElementById('ota-update-banner');
    const titleEl = document.getElementById('ota-banner-title');
    const subEl = document.getElementById('ota-banner-subtitle');
    const reloadBtn = document.getElementById('btn-ota-reload');

    if (titleEl) titleEl.textContent = `Update v${version} Ready!`;
    if (subEl) subEl.textContent = notes || 'New features installed. Tap to apply.';

    if (banner) {
        banner.style.display = 'flex';
    }

    if (reloadBtn) {
        reloadBtn.onclick = function() {
            if (window.TsigiroNative && typeof window.TsigiroNative.reloadApp === 'function') {
                window.TsigiroNative.reloadApp();
            } else {
                window.location.reload();
            }
        };
    }
};

// Initial Boot
document.addEventListener('DOMContentLoaded', () => {
    // Enforce crisp light mode across app
    localStorage.removeItem('tsigiro_theme');
    document.body.classList.remove('dark-mode');

    // Bottom Nav Click Handlers
    document.querySelectorAll('.bottom-nav .nav-item').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetRoute = btn.getAttribute('data-nav');
            if (targetRoute) {
                window.navigateTo(targetRoute);
            }
        });
    });

    // Global FAB (+ New Request) Handler
    const globalFab = document.getElementById('global-fab-btn');
    if (globalFab) {
        globalFab.addEventListener('click', () => {
            window.navigateTo('requests/new');
        });
    }

    // Notifications Modal Handlers
    const notifBell = document.getElementById('btn-notifications-bell');
    const modalNotif = document.getElementById('modal-notifications');
    const closeNotif = document.getElementById('btn-close-notifications');
    const markAllReadBtn = document.getElementById('btn-mark-all-read');
    const filterAllBtn = document.getElementById('notif-filter-all');
    const filterUnreadBtn = document.getElementById('notif-filter-unread');

    if (notifBell && modalNotif) {
        notifBell.addEventListener('click', () => {
            modalNotif.style.display = 'flex';
            window.refreshNotifications();
        });
    }

    if (closeNotif && modalNotif) {
        closeNotif.addEventListener('click', () => {
            modalNotif.style.display = 'none';
        });
        modalNotif.addEventListener('click', (e) => {
            if (e.target === modalNotif) {
                modalNotif.style.display = 'none';
            }
        });
    }

    if (filterAllBtn && filterUnreadBtn) {
        filterAllBtn.addEventListener('click', () => {
            filterAllBtn.classList.add('active');
            filterUnreadBtn.classList.remove('active');
            currentNotifFilter = 'all';
            renderNotificationsList();
        });
        filterUnreadBtn.addEventListener('click', () => {
            filterUnreadBtn.classList.add('active');
            filterAllBtn.classList.remove('active');
            currentNotifFilter = 'unread';
            renderNotificationsList();
        });
    }

    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', async () => {
            const user = window.getUser();
            if (!user) return;
            try {
                const fd = new URLSearchParams();
                fd.append('user_id', user.id);
                await fetch(`${window.API_BASE}/api/mobile/notifications/read-all`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    credentials: 'include',
                    body: fd.toString()
                });
                cachedNotifications.forEach(n => {
                    n.is_unread = false;
                    n.is_read = 1;
                });
                window.refreshNotifications();
                window.showToast('All notifications marked as read', 'success');
            } catch (err) {
                console.error('Failed to mark all read:', err);
            }
        });
    }

    // Offline Reconnect Button Handler
    const reconnectBtn = document.getElementById('btn-reconnect');
    if (reconnectBtn) {
        reconnectBtn.addEventListener('click', async () => {
            reconnectBtn.disabled = true;
            const originalHtml = reconnectBtn.innerHTML;
            reconnectBtn.innerHTML = '<span>Checking...</span>';

            try {
                const user = window.getUser();
                const pingUrl = user 
                    ? `${window.API_BASE}/api/mobile/notifications?user_id=${user.id}&_t=${Date.now()}`
                    : `${window.API_BASE}/api/mobile/opportunities?_t=${Date.now()}`;

                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 4000);

                const response = await fetch(pingUrl, {
                    method: 'GET',
                    signal: controller.signal
                });
                clearTimeout(timeoutId);

                if (response.ok) {
                    window.updateNetworkBanner(false);
                    window.showToast('Network connection verified!', 'success');
                    // Refresh current view
                    const mainContent = document.getElementById('mainContent');
                    const activeView = mainContent ? mainContent.getAttribute('data-active-view') : null;
                    if (activeView) {
                        window.loadView(activeView);
                    }
                    if (typeof window.refreshNotifications === 'function') {
                        window.refreshNotifications();
                    }
                } else {
                    window.showToast('Server returned an error. Try again shortly.', 'warning');
                }
            } catch (e) {
                window.updateNetworkBanner(true);
                window.showToast('Server not reachable. Still offline.', 'error');
            } finally {
                reconnectBtn.disabled = false;
                reconnectBtn.innerHTML = originalHtml;
            }
        });
    }

    // Window Network Online / Offline Listeners
    window.addEventListener('online', () => {
        window.updateNetworkBanner(false);
        window.showToast('Connection restored — back online', 'success');
        const mainContent = document.getElementById('mainContent');
        const activeView = mainContent ? mainContent.getAttribute('data-active-view') : null;
        if (activeView) {
            window.loadView(activeView);
        }
        if (typeof window.refreshNotifications === 'function') {
            window.refreshNotifications();
        }
    });

    window.addEventListener('offline', () => {
        window.updateNetworkBanner(true);
        window.showToast('Device offline — switching to local cache', 'warning');
    });

    // Sidebar Drawer Toggle Listeners
    const sidebarToggleBtn = document.getElementById('btn-sidebar-toggle');
    const sidebarCloseBtn = document.getElementById('btn-sidebar-close');
    const sidebarBackdrop = document.getElementById('sidebar-backdrop');
    const openReqProfileBtn = document.getElementById('btn-open-request-profile');
    const closeReqProfileBtn = document.getElementById('btn-close-request-profile');
    const modalReqProfile = document.getElementById('modal-request-profile');
    const formReqProfile = document.getElementById('form-request-profile');
    const sidebarLogoutBtn = document.getElementById('btn-sidebar-logout');
    const sidebarProfileNav = document.getElementById('sidebar-nav-profile');
    const sidebarNotifNav = document.getElementById('sidebar-nav-notifications');

    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', () => {
            window.openSidebar();
        });
    }

    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', () => {
            window.closeSidebar();
        });
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', () => {
            window.closeSidebar();
        });
    }

    if (openReqProfileBtn) {
        openReqProfileBtn.addEventListener('click', () => {
            window.openRequestProfileModal();
        });
    }

    if (closeReqProfileBtn && modalReqProfile) {
        closeReqProfileBtn.addEventListener('click', () => {
            window.closeRequestProfileModal();
        });
        modalReqProfile.addEventListener('click', (e) => {
            if (e.target === modalReqProfile) {
                window.closeRequestProfileModal();
            }
        });
    }

    const closeRoleModalBtn = document.getElementById('btn-close-role-modal');
    const modalRoleCategory = document.getElementById('modal-role-category');
    if (closeRoleModalBtn && modalRoleCategory) {
        closeRoleModalBtn.addEventListener('click', () => {
            window.closeRoleCategoryModal();
        });
        modalRoleCategory.addEventListener('click', (e) => {
            if (e.target === modalRoleCategory) {
                window.closeRoleCategoryModal();
            }
        });
    }

    const closeAdminReviewBtn = document.getElementById('admin-review-close-btn');
    const modalAdminReview = document.getElementById('modal-admin-review');
    if (closeAdminReviewBtn && modalAdminReview) {
        closeAdminReviewBtn.addEventListener('click', () => {
            modalAdminReview.style.display = 'none';
        });
        modalAdminReview.addEventListener('click', (e) => {
            if (e.target === modalAdminReview) {
                modalAdminReview.style.display = 'none';
            }
        });
    }

    const sidebarPersonalNav = document.getElementById('sidebar-nav-personal');
    const sidebarQualsNav = document.getElementById('sidebar-nav-quals');

    if (sidebarPersonalNav) {
        sidebarPersonalNav.addEventListener('click', (e) => {
            e.preventDefault();
            window.closeSidebar();
            window.navigateTo('profile/personal');
        });
    }

    if (sidebarQualsNav) {
        sidebarQualsNav.addEventListener('click', (e) => {
            e.preventDefault();
            window.closeSidebar();
            window.navigateTo('profile/qualifications');
        });
    }

    if (sidebarProfileNav) {
        sidebarProfileNav.addEventListener('click', (e) => {
            e.preventDefault();
            window.closeSidebar();
            window.navigateTo('profile/view');
        });
    }

    if (sidebarNotifNav && modalNotif) {
        sidebarNotifNav.addEventListener('click', (e) => {
            e.preventDefault();
            window.closeSidebar();
            modalNotif.style.display = 'flex';
            window.refreshNotifications();
        });
    }

    const sidebarUpdateBtn = document.getElementById('btn-sidebar-update');
    if (sidebarUpdateBtn) {
        sidebarUpdateBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.closeSidebar();
            window.checkAppUpdates();
        });
    }

    // Display active bundle version
    const versionLabelEl = document.getElementById('sidebar-version-label');
    if (versionLabelEl && window.TsigiroNative && typeof window.TsigiroNative.getBundleVersion === 'function') {
        versionLabelEl.textContent = `Tsigiro Mobile v${window.TsigiroNative.getBundleVersion()}`;
    }

    if (sidebarLogoutBtn) {
        sidebarLogoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.logoutUser();
        });
    }

    // Handle Profile Request Form Submission
    if (formReqProfile) {
        formReqProfile.addEventListener('submit', async (e) => {
            e.preventDefault();
            const user = window.getUser();
            if (!user) return;

            const selectedTypeEl = document.querySelector('#request-profile-types-container .type-select-card.selected');
            if (!selectedTypeEl) {
                window.showToast('Please select a profile type', 'warning');
                return;
            }

            const typeCode = selectedTypeEl.getAttribute('data-code');
            const displayTitle = document.getElementById('req-display-title')?.value || '';
            const notes = document.getElementById('req-notes')?.value || '';

            if (!notes.trim()) {
                window.showToast('Please provide your qualifications or motivation notes', 'warning');
                return;
            }

            const submitBtn = document.getElementById('btn-submit-profile-request');
            const origText = submitBtn ? submitBtn.textContent : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting Request...';
            }

            try {
                const fd = new FormData();
                fd.append('user_id', user.id);
                fd.append('type_code', typeCode);
                fd.append('display_title', displayTitle);
                fd.append('notes', notes);

                const res = await fetch(`${window.API_BASE}/api/mobile/user/request-profile`, {
                    method: 'POST',
                    body: fd
                });

                const data = await res.json();
                if (data.status === 1) {
                    window.showToast(data.message || 'Profile request submitted for review!', 'success');
                    window.closeRequestProfileModal();
                    formReqProfile.reset();

                    // Update user object with updated profile list
                    if (data.profiles) {
                        user.profiles = data.profiles;
                        window.setUser(user);
                    }
                    if (typeof window.refreshNotifications === 'function') {
                        window.refreshNotifications();
                    }
                } else {
                    window.showToast(data.message || 'Failed to submit profile request', 'error');
                }
            } catch (err) {
                console.error('Submit profile request failed:', err);
                window.showToast('Network error submitting profile request', 'error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = origText;
                }
            }
        });
    }

    const user = window.getUser();
    updateAppHeader(user);

    if (user) {
        window.navigateTo('dashboard/home', user);
    } else {
        window.navigateTo('auth/login');
    }
});

