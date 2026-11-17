document.addEventListener('DOMContentLoaded', function () {
  var root = document.documentElement;
  var toggle = document.getElementById('themeToggle');
  var moon = toggle ? toggle.querySelector('.icon-moon') : null;
  var sun = toggle ? toggle.querySelector('.icon-sun') : null;

  function apply(theme) {
    if (theme === 'light') {
      root.classList.remove('theme-neon');
      root.classList.add('theme-light');
      document.cookie = 'theme=light; path=/; max-age=31536000';
      if (moon) moon.setAttribute('hidden', '');
      if (sun) sun.removeAttribute('hidden');
    } else {
      root.classList.remove('theme-light');
      root.classList.add('theme-neon');
      document.cookie = 'theme=neon; path=/; max-age=31536000';
      if (sun) sun.setAttribute('hidden', '');
      if (moon) moon.removeAttribute('hidden');
    }
  }

  if (toggle) {
    toggle.addEventListener('click', function () {
      apply(root.classList.contains('theme-light') ? 'neon' : 'light');
    });
    // Синхронизация иконки с текущей темой
    if (root.classList.contains('theme-light')) {
      if (moon) moon.setAttribute('hidden', '');
      if (sun) sun.removeAttribute('hidden');
    } else {
      if (sun) sun.setAttribute('hidden', '');
      if (moon) moon.removeAttribute('hidden');
    }
    toggle.removeAttribute('hidden');
  }
});