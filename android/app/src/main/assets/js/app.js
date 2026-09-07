const API_BASE = 'http://10.0.2.2/master/public';
window.API_BASE = API_BASE;

const viewCache = {};
const viewDataStore = {};
const viewHistory = [];

window.routeMap = {
    'auth/login': 'auth/login',
    'dashboard/home': 'dashboard/home'
};

function getRoleName(roleId) {
    const roles = {
        '1': 'Administrator',
        '2': 'Manager',
        '3': 'Management',
        '5': 'Account Executive'
    };
    return roles[roleId] || 'User';
}

document.addEventListener('DOMContentLoaded', () => {
    // Check if user is already logged in
    const savedUser = localStorage.getItem('user');
    if (savedUser) {
        try {
            const userObj = JSON.parse(savedUser);
            userObj.role_name = getRoleName(userObj.role);
            navigateTo('dashboard/home', userObj);
        } catch(e) {
            localStorage.removeItem('user');
            navigateTo('auth/login');
        }
    } else {
        navigateTo('auth/login');
    }
});

window.navigateTo = function(routeKey, data = null, transition = 'fade') {
    const viewPath = window.routeMap[routeKey];

    if (!viewPath) {
        console.error(`No route found for: ${routeKey}`);
        return;
    }

    if (!localStorage.getItem('user')) {
        if (viewPath.indexOf('auth') === -1) {
            console.warn(`Route not found: ${routeKey}, redirecting to auth/login.`);
            navigateTo('auth/login', null, 'fade');
            return;
        }
    }

    console.log('[navigateTo]', { routeKey, viewPath, data });
    loadView(viewPath, transition, data);
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

window.loadView = async function(viewName, transitionType = 'fade', data = null) {
    const loadId = Date.now() + "_" + Math.random().toString(36).slice(2, 8);
    window.__activeViewLoadId = loadId;
    
    const normalizedViewName = String(viewName || "").trim();
    const isAuthView = normalizedViewName.indexOf("auth/") === 0;
    const shouldTrackInHistory = normalizedViewName !== "" && !isAuthView;

    // Push view to history (if not duplicate)
    if (shouldTrackInHistory && (viewHistory.length === 0 || viewHistory[viewHistory.length - 1] !== viewName)) {
        viewHistory.push(viewName);
    }
    
    if (!viewName) return;

    const scriptUrl = `views/${viewName}.js`;
    const htmlUrl = `views/${viewName}.html`;
    const viewKey = viewName;

    if (data !== null) viewDataStore[viewName] = data;

    const mainContent = document.getElementById("mainContent");
    const passedData = viewDataStore[viewName] || null;

    // Transition out
    mainContent.className = '';
    switch (transitionType) {
        case 'slide-left': mainContent.classList.add('slide-left-out'); break;
        case 'slide-right': mainContent.classList.add('slide-right-out'); break;
        default: mainContent.classList.add('fade-out');
    }

    setTimeout(async () => {
        if (window.__activeViewLoadId !== loadId) return;

        const cachedView = viewCache[viewKey];
        const hasCachedHtml = !!(cachedView && typeof cachedView.html === 'string' && cachedView.html.trim() !== '');

        if (hasCachedHtml) {
            renderViewContent(cachedView.html, passedData, mainContent, viewName, loadId, transitionType);
            loadScript(scriptUrl, passedData, loadId, viewKey, viewName);
        } else {
            try {
                const htmlText = await loadLocalFile(htmlUrl);
                if (window.__activeViewLoadId !== loadId) return;
                
                viewCache[viewKey] = { html: htmlText, scriptLoaded: false };
                renderViewContent(htmlText, passedData, mainContent, viewName, loadId, transitionType);
                loadScript(scriptUrl, passedData, loadId, viewKey, viewName);
                
            } catch (err) {
                if (window.__activeViewLoadId !== loadId) return;
                console.error(`Failed to load view: ${viewName}`, err);
            }
        }
    }, 200);
};

async function loadScript(scriptUrl, passedData, loadId, viewKey, viewName) {
    window.init = undefined;

    try {
        const scriptText = await loadLocalFile(scriptUrl);
        if (window.__activeViewLoadId !== loadId) return;

        const script = document.createElement('script');
        script.textContent = scriptText;
        document.body.appendChild(script);

        if (typeof window.init === 'function') {
            window.init(passedData);
        }
        if (viewCache[viewKey]) {
            viewCache[viewKey].scriptLoaded = true;
        }
    } catch (err) {
        if (window.__activeViewLoadId !== loadId) return;
        console.warn(`No script found for view: ${viewName}`, err);
    }
}

function renderViewContent(rawHtml, data, target, viewName, loadId, transitionType) {
    if (window.__activeViewLoadId !== loadId) return;

    let viewHtml = rawHtml;

    // Named loops: {{loop:name}}...{{endloop}}
    if (data && typeof data === 'object') {
        const loopRegex = /{{loop:([\w]+)}}([\s\S]*?){{endloop}}/g;
        viewHtml = viewHtml.replace(loopRegex, (_, loopName, loopTemplate) => {
            const dataArray = data[loopName];
            if (!Array.isArray(dataArray)) return '';

            return dataArray.map(item => {
                let block = loopTemplate;
                for (const [key, value] of Object.entries(item)) {
                    block = block.replaceAll(`{{${key}}}`, value);
                }
                return block;
            }).join('');
        });
    }

    // If-Else Conditionals
    if (data && typeof data === 'object') {
        viewHtml = viewHtml.replace(/{{if (\w+)\s*==\s*["']?([^"'}]+)["']?}}([\s\S]*?)({{else}}([\s\S]*?))?{{endif}}/g, function (_, key, expectedValue, trueContent, __, falseContent) {
            return data[key] == expectedValue ? trueContent : (falseContent || '');
        });
    }

    // Basic replacements
    if (data && typeof data === 'object') {
        for (const [key, value] of Object.entries(data)) {
            if (typeof value !== 'object') {
                viewHtml = viewHtml.replaceAll(`{{${key}}}`, value);
            }
        }
    }

    target.innerHTML = viewHtml;
    target.setAttribute('data-active-view', viewName);

    // Animate In
    target.className = '';
    switch (transitionType) {
        case 'slide-left': target.classList.add('slide-left-in'); break;
        case 'slide-right': target.classList.add('slide-right-in'); break;
        default: target.classList.add('fade-in');
    }
}
