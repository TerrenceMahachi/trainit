document.addEventListener('DOMContentLoaded', () => {
    const smallFontBtn = document.getElementById('smallFontBtn');
    const normalFontBtn = document.getElementById('normalFontBtn');
    const largeFontBtn = document.getElementById('largeFontBtn');
    const toggleModeBtn = document.getElementById('toggleModeBtn');

    //const body = document.body; document.querySelectorAll('#verse_show .verse');
    const body = document.getElementById('verse_show');
    //const body = document.querySelectorAll('#verse_show .verse_show');

    // Function to set font size
    const setFontSize = (size) => {
        body.classList.remove('small-font', 'normal-font', 'large-font');
        body.classList.add(`${size}-font`);
        localStorage.setItem('fontSize', size);
        // Set active state for font size buttons
        [smallFontBtn, normalFontBtn, largeFontBtn].forEach(btn => btn.classList.remove('active'));
        document.getElementById(`${size}FontBtn`).classList.add('active');
    };

    // Function to set theme
    const setTheme = (theme) => {
        body.classList.remove('dark-mode', 'light-mode');
        body.classList.add(`${theme}-mode`);
        localStorage.setItem('theme', theme);
        toggleModeBtn.classList.toggle('active', theme === 'dark');

    };

    // Event listeners for font size buttons
    smallFontBtn.addEventListener('click', () => setFontSize('small'));
    normalFontBtn.addEventListener('click', () => setFontSize('normal'));
    largeFontBtn.addEventListener('click', () => setFontSize('large'));

    // Event listener for theme toggle button
    toggleModeBtn.addEventListener('click', () => {
        const currentTheme = body.classList.contains('dark-mode') ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    });

    // Load saved preferences from local storage
    const savedFontSize = localStorage.getItem('fontSize') || 'normal';
    const savedTheme = localStorage.getItem('theme') || 'light';

    setFontSize(savedFontSize);
    setTheme(savedTheme);
});
