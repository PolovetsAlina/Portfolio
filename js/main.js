const btnDarkMode = document.querySelector(".dark-mode-btn");

// Function to update theme
function updateTheme(isDark) {
    if (isDark) {
        document.body.classList.add("dark");
        btnDarkMode.classList.add("dark-mode-btn--active");
        localStorage.setItem("darkMode", "dark");
    } else {
        document.body.classList.remove("dark");
        btnDarkMode.classList.remove("dark-mode-btn--active");
        localStorage.setItem("darkMode", "light");
    }
}

// 1. Check for saved user preference, if any
const savedTheme = localStorage.getItem('darkMode');

if (savedTheme) {
    updateTheme(savedTheme === 'dark');
} 
// 2. If no saved preference, use system preference
else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    updateTheme(true);
}

// 3. Listen for system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
    // Only apply system theme if user hasn't set a preference
    if (!localStorage.getItem('darkMode')) {
        updateTheme(e.matches);
    }
});

// Toggle theme on button click
btnDarkMode.addEventListener('click', () => {
    const isDark = !document.body.classList.contains('dark');
    updateTheme(isDark);
});