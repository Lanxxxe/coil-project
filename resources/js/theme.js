// Simple theme manager: toggles light/dark, persists in localStorage, updates DOM and announces changes
(function(){
  const STORAGE_KEY = 'theme';
  const root = document.documentElement; // <html>

  function applyTheme(theme) {
    root.setAttribute('data-theme', theme);
    document.body.dataset.theme = theme; // convenience hook
    const btn = document.getElementById('theme-toggle');
    if (btn) {
      const icon = btn.querySelector('.theme-icon');
      const label = btn.querySelector('.theme-label');
      // Show the NEXT theme as the button text/icon (swap behavior)
      const next = theme === 'dark' ? 'light' : 'dark';
      if (next === 'light') {
        if (icon) icon.textContent = '☀️';
        if (label) label.textContent = 'Light';
        btn.setAttribute('aria-label', 'Switch to light mode');
        btn.classList.remove('bg-black','text-white');
        btn.classList.add('bg-white/80','text-black');
      } else {
        if (icon) icon.textContent = '🌙';
        if (label) label.textContent = 'Dark';
        btn.setAttribute('aria-label', 'Switch to dark mode');
        btn.classList.remove('bg-white/80','text-black');
        btn.classList.add('bg-black','text-white');
      }
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
