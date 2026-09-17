window.init = function() {
    const loginForm = document.getElementById('mobile-login-form');
    const registerForm = document.getElementById('mobile-register-form');
    const submitBtn = document.getElementById('btn-submit-login');
    const registerBtn = document.getElementById('btn-submit-register');
    const emailInput = document.getElementById('login-email');
    const passwordInput = document.getElementById('login-password');
    const tabLogin = document.getElementById('tab-login');
    const tabRegister = document.getElementById('tab-register');
    const regRoleSelect = document.getElementById('reg-role');
    const regNotesGroup = document.getElementById('reg-notes-group');

    // Tab Switching
    function setAuthTab(tab) {
        if (tab === 'register') {
            if (tabRegister) tabRegister.classList.add('active');
            if (tabLogin) tabLogin.classList.remove('active');
            if (loginForm) loginForm.style.display = 'none';
            if (registerForm) registerForm.style.display = 'block';
        } else {
            if (tabLogin) tabLogin.classList.add('active');
            if (tabRegister) tabRegister.classList.remove('active');
            if (loginForm) loginForm.style.display = 'block';
            if (registerForm) registerForm.style.display = 'none';
        }
    }

    if (tabLogin) {
        tabLogin.addEventListener('click', () => setAuthTab('login'));
    }
    if (tabRegister) {
        tabRegister.addEventListener('click', () => setAuthTab('register'));
    }

    const btnGotoRegister = document.getElementById('btn-goto-register');
    if (btnGotoRegister) {
        btnGotoRegister.addEventListener('click', () => setAuthTab('register'));
    }

    const linkGotoRegister = document.getElementById('link-goto-register');
    if (linkGotoRegister) {
        linkGotoRegister.addEventListener('click', (e) => {
            e.preventDefault();
            setAuthTab('register');
        });
    }

    const btnGotoLogin = document.getElementById('btn-goto-login');
    if (btnGotoLogin) {
        btnGotoLogin.addEventListener('click', () => setAuthTab('login'));
    }

    // Show/Hide Password Toggle
    document.querySelectorAll('.btn-toggle-password').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;
            const eyeShow = btn.querySelector('.eye-show');
            const eyeHide = btn.querySelector('.eye-hide');
            if (input.type === 'password') {
                input.type = 'text';
                if (eyeShow) eyeShow.style.display = 'none';
                if (eyeHide) eyeHide.style.display = 'block';
            } else {
                input.type = 'password';
                if (eyeShow) eyeShow.style.display = 'block';
                if (eyeHide) eyeHide.style.display = 'none';
            }
        });
    });

    // Robot Verification Math Question
    let robotExpectedAnswer = 0;
    function generateRobotQuestion() {
        const num1 = Math.floor(Math.random() * 8) + 2; // 2 to 9
        const num2 = Math.floor(Math.random() * 8) + 1; // 1 to 8
        robotExpectedAnswer = num1 + num2;
        const qEl = document.getElementById('robot-math-question');
        if (qEl) qEl.textContent = `${num1} + ${num2} =`;
        const ansInput = document.getElementById('reg-robot-answer');
        if (ansInput) ansInput.value = '';
    }
    generateRobotQuestion();

    const refreshRobotBtn = document.getElementById('btn-refresh-robot');
    if (refreshRobotBtn) {
        refreshRobotBtn.addEventListener('click', () => {
            generateRobotQuestion();
        });
    }

    async function doLogin(params) {
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Authenticating...';
        }

        try {
            const formData = new URLSearchParams();
            for (const [key, value] of Object.entries(params)) {
                formData.append(key, value);
            }

            const response = await fetch(`${window.API_BASE}/api/mobile/login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                credentials: 'include',
                body: formData.toString()
            });

            const data = await response.json();

            if (data.status === 1 && data.user) {
                window.setUser(data.user);
                window.showToast(`Welcome, ${data.user.name}`, 'success');
                window.navigateTo('dashboard/home', data.user);
            } else {
                window.showToast(data.message || 'Login failed. Please check credentials.', 'error');
            }
        } catch (err) {
            console.error('Login error:', err);
            window.showToast('Unable to connect to Tsigiro server (' + window.API_BASE + ')', 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Sign In';
            }
        }
    }

    async function doRegister(params) {
        if (registerBtn) {
            registerBtn.disabled = true;
            registerBtn.textContent = 'Creating Account...';
        }

        try {
            const formData = new URLSearchParams();
            for (const [key, value] of Object.entries(params)) {
                formData.append(key, value);
            }

            const response = await fetch(`${window.API_BASE}/api/mobile/register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                credentials: 'include',
                body: formData.toString()
            });

            const data = await response.json();

            if (data.status === 1 && data.user) {
                window.setUser(data.user);
                window.showToast(data.message || 'Account created successfully! Welcome to Tsigiro.', 'success');
                window.navigateTo('dashboard/home', data.user);
            } else {
                window.showToast(data.message || 'Registration failed. Please check your details.', 'error');
                generateRobotQuestion();
            }
        } catch (err) {
            console.error('Register error:', err);
            window.showToast('Unable to connect to Tsigiro server (' + window.API_BASE + ')', 'error');
            generateRobotQuestion();
        } finally {
            if (registerBtn) {
                registerBtn.disabled = false;
                registerBtn.textContent = 'Create Account & Continue →';
            }
        }
    }

    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();
            if (!email || !password) return;
            doLogin({ email, password });
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('reg-name')?.value.trim();
            const email = document.getElementById('reg-email')?.value.trim();
            const password = document.getElementById('reg-password')?.value.trim();
            const robotAnswer = parseInt(document.getElementById('reg-robot-answer')?.value, 10);

            if (!name || !email || !password) {
                window.showToast('Please fill in all required fields.', 'error');
                return;
            }
            if (password.length < 6) {
                window.showToast('Password must be at least 6 characters.', 'error');
                return;
            }
            if (isNaN(robotAnswer) || robotAnswer !== robotExpectedAnswer) {
                window.showToast('Incorrect verification answer. Please confirm you are not a robot.', 'warning');
                generateRobotQuestion();
                document.getElementById('reg-robot-answer')?.focus();
                return;
            }

            doRegister({ name, email, password, requested_profile: 'general' });
        });
    }

    // Quick Login Demo Chips
    document.querySelectorAll('.chip-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const role = btn.getAttribute('data-quick');
            if (role) {
                doLogin({ quick_as: role });
            }
        });
    });
};
