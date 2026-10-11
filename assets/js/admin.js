(() => {
  const root = document.documentElement;
  const key = 'dentallink-admin-theme';
  document.querySelectorAll('[data-theme-toggle]').forEach((button) => button.addEventListener('click', () => {
    const theme = root.dataset.theme === 'light' ? 'dark' : 'light';
    root.dataset.theme = theme;
    localStorage.setItem(key, theme);
  }));
  document.querySelectorAll('form[data-confirm]').forEach((form) => form.addEventListener('submit', (event) => {
    if (!window.confirm(form.dataset.confirm || 'Confirmer cette action ?')) event.preventDefault();
  }));
})();
