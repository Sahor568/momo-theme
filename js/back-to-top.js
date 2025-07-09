document.addEventListener('DOMContentLoaded', function () {
  const backToTopButton = document.getElementById('back-to-top');

  window.addEventListener('scroll', function () {
    if (window.scrollY > 240) {
      backToTopButton.style.display = 'block';
    } else {
      backToTopButton.style.display = 'none';
    }
  });

  backToTopButton.addEventListener('click', function () {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
});
