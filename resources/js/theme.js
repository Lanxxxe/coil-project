// Simple theme manager: toggles light/dark, persists in localStorage, updates DOM and announces changes
(function(){
  const STORAGE_KEY = 'theme';
  const root = document.documentElement; // <html>

  function applyTheme(theme) {
    root.setAttribute('data-theme', theme);
    document.body.dataset.theme = theme; // convenience hook
    const btn = document.getElementById('theme-toggle');
    if (btn) {
      // For neumorphic slider, aria-checked=true means dark
      const isDark = theme === 'dark';
      btn.setAttribute('aria-checked', String(isDark));
      const icon = btn.querySelector('.nm-icon');
      if (icon) icon.textContent = isDark ? '🌙' : '☀️';
      btn.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    }
    // announce to listeners
    const ev = new CustomEvent('themechange', { detail: { theme } });
    window.dispatchEvent(ev);
  }

  function getPreferred() {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored === 'light' || stored === 'dark') return stored;
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function toggle() {
    const current = (document.documentElement.getAttribute('data-theme') || getPreferred());
    const next = current === 'dark' ? 'light' : 'dark';
    localStorage.setItem(STORAGE_KEY, next);
    applyTheme(next);
  }

  document.addEventListener('DOMContentLoaded', () => {
    applyTheme(getPreferred());
    const btn = document.getElementById('theme-toggle');
    if (btn) btn.addEventListener('click', toggle);
  });
})();
