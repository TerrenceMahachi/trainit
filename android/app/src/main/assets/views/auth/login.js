window.init = function(data) {
    const loginForm = document.getElementById('login-form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const loginBtnText = document.getElementById('btn-text');
    const loginSpinner = document.getElementById('btn-spinner');
    const loginError = document.getElementById('login-error');

    function setLoading(isLoading) {
        if (isLoading) {
            loginBtnText.style.display = 'none';
            loginSpinner.style.display = 'block';
            loginForm.querySelector('button').disabled = true;
        } else {
            loginBtnText.style.display = 'block';
            loginSpinner.style.display = 'none';
            loginForm.querySelector('button').disabled = false;
        }
    }

    function showError(msg) {
        loginError.textContent = msg;
    }

    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();
            
            if (!email || !password) {
                showError('Please enter both email and password.');
                return;
            }

            setLoading(true);
            showError('');

            try {
                const formData = new URLSearchParams();
                formData.append('email', email);
                formData.append('password', password);

                const response = await fetch(`${window.API_BASE}/api/sign-in`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    credentials: 'include',
                    body: formData.toString()
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.status === 1) {
                    // Success
                    const user = {
                        id: data.iD,
                        name: data.name,
                        email: data.email,
                        role: data.role
                    };
                    localStorage.setItem('user', JSON.stringify(user));
                    
                    const roles = {
                        '1': 'Administrator',
                        '2': 'Manager',
                        '3': 'Management',
                        '5': 'Account Executive'
                    };
                    user.role_name = roles[user.role] || 'User';
                    
                    navigateTo('dashboard/home', user);
                } else {
                    showError(data.message || 'Invalid credentials');
                }
            } catch (err) {
                console.error('Login error:', err);
                const errorDetails = `Error: ${err.name || 'Unknown'} - ${err.message || err}. URL: ${window.API_BASE}/api/sign-in`;
                showError(`Failed to connect to the server. Details: ${errorDetails}`);
                
                // Write detailed connection error to the on-screen debug log
                const debugLog = document.getElementById('debug-log');
                if (debugLog) {
                    debugLog.style.display = 'block';
                    debugLog.innerHTML += `<div style="margin-top: 5px; color: #ffbbbb;"><strong>[API Error]</strong> ${errorDetails}</div>`;
                }
            } finally {
                setLoading(false);
            }
        });
    }
};
