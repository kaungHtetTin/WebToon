/**
 * Dark Mode Toggle Functionality
 * Handles theme switching and persistence
 */

(function() {
  "use strict";

  const DARK_MODE_KEY = 'admin_dark_mode';
  const THEME_ATTRIBUTE = 'data-theme';
  
  /**
   * Initialize dark mode
   */
  function initDarkMode() {
    const savedTheme = localStorage.getItem(DARK_MODE_KEY);
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    // Use saved preference, or system preference, or default to light
    const theme = savedTheme || (prefersDark ? 'dark' : 'light');
    setTheme(theme);
    
    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
      if (!localStorage.getItem(DARK_MODE_KEY)) {
        setTheme(e.matches ? 'dark' : 'light');
      }
    });
  }

  /**
   * Set theme
   */
  function setTheme(theme) {
    document.documentElement.setAttribute(THEME_ATTRIBUTE, theme);
    localStorage.setItem(DARK_MODE_KEY, theme);
    updateIcon(theme);
  }

  /**
   * Toggle theme
   */
  function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute(THEME_ATTRIBUTE) || 'light';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    setTheme(newTheme);
  }

  /**
   * Update dark mode icon
   */
  function updateIcon(theme) {
    const icon = document.getElementById('darkModeIcon');
    if (icon) {
      if (theme === 'dark') {
        icon.classList.remove('bi-moon');
        icon.classList.add('bi-sun');
      } else {
        icon.classList.remove('bi-sun');
        icon.classList.add('bi-moon');
      }
    }
  }

  /**
   * Initialize when DOM is ready
   */
  function init() {
    // Initialize theme
    initDarkMode();

    // Add click handler to toggle button
    const toggleBtn = document.getElementById('darkModeToggle');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        toggleTheme();
      });
    }
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();

