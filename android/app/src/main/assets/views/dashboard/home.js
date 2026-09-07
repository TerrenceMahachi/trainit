window.init = function(data) {
    const logoutBtn = document.getElementById('logout-btn');
    const loadUsersBtn = document.getElementById('load-users-btn');
    const totalUsersEl = document.getElementById('total-users');
    const listContainer = document.getElementById('users-list');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            localStorage.removeItem('user');
            navigateTo('auth/login');
        });
    }

    if (loadUsersBtn) {
        loadUsersBtn.addEventListener('click', loadUsers);
    }

    async function loadUsers() {
        if (loadUsersBtn) {
            loadUsersBtn.textContent = 'Loading...';
            loadUsersBtn.disabled = true;
        }
        
        try {
            const response = await fetch(`${window.API_BASE}/api/get-userlist?limit=10&offset=0`, {
                method: 'GET',
                credentials: 'include'
            });
            
            if (!response.ok) throw new Error('Network error');
            
            const result = await response.json();
            
            if (result && result.data) {
                if (totalUsersEl) {
                    totalUsersEl.textContent = result.total_count || result.data.length;
                }
                
                if (listContainer) {
                    listContainer.innerHTML = '';
                    
                    result.data.forEach(user => {
                        const el = document.createElement('div');
                        el.className = 'list-item d-flex justify-between align-center';
                        el.innerHTML = `
                            <div>
                                <h4 style="margin:0">${user.name}</h4>
                                <p style="margin:0; font-size: 0.8rem; color: #64748b;">${user.email}</p>
                            </div>
                            <span style="font-size: 0.8rem; background: var(--primary); color: white; padding: 2px 8px; border-radius: 10px;">
                                ${user.role}
                            </span>
                        `;
                        listContainer.appendChild(el);
                    });
                }
            } else {
                if (listContainer) {
                    listContainer.innerHTML = `<p style="color: #64748b; text-align: center;">No users found</p>`;
                }
            }
            
        } catch (err) {
            console.error('Error fetching users:', err);
            if (listContainer) {
                listContainer.innerHTML = `<p style="color: #ef4444; text-align: center;">Failed to load users.</p>`;
            }
        } finally {
            if (loadUsersBtn) {
                loadUsersBtn.textContent = 'Refresh';
                loadUsersBtn.disabled = false;
            }
        }
    }

    // Auto load data on init
    loadUsers();
};
