document.addEventListener('DOMContentLoaded', function () {
  const toggleButton = document.getElementById('menu-toggle');
  const menu = document.querySelector('.main-nav');

  toggleButton.addEventListener('click', function () {
    menu.classList.toggle('active');

    // Accessibility: update aria-expanded
    const expanded = toggleButton.getAttribute('aria-expanded') === 'true';
    toggleButton.setAttribute('aria-expanded', !expanded);
  });
});
