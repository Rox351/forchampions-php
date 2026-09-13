document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('[data-menu-toggle]');
    const mainNav = document.querySelector('[data-main-nav]');

    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', () => {
            mainNav.classList.toggle('open');
        });
    }

    const shopBase = document.body.dataset.staticDemo === '1' ? 'loja.html' : 'loja.php';

    const categoryRedirect = document.querySelector('[data-category-redirect]');
    if (categoryRedirect) {
        categoryRedirect.addEventListener('change', (event) => {
            const value = event.target.value;
            if (value === '') {
                return;
            }
            window.location.href = `${shopBase}?categoria=${encodeURIComponent(value)}`;
        });
    }

    const scrollTop = document.querySelector('[data-scroll-top]');
    if (scrollTop) {
        window.addEventListener('scroll', () => {
            scrollTop.classList.toggle('visible', window.scrollY > 400);
        });
        scrollTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
