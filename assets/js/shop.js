document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const search = (params.get('q') || '').trim().toLowerCase();
    const category = (params.get('categoria') || '').trim().toLowerCase();
    const cards = Array.from(document.querySelectorAll('[data-product-card]'));
    const countEl = document.querySelector('[data-shop-count]');

    let visible = 0;
    cards.forEach((card) => {
        const name = (card.dataset.name || '').toLowerCase();
        const cardCategory = (card.dataset.category || '').toLowerCase();
        const matchesSearch = search === '' || name.includes(search);
        const matchesCategory = category === '' || cardCategory === category;
        const show = matchesSearch && matchesCategory;
        card.style.display = show ? '' : 'none';
        if (show) {
            visible += 1;
        }
    });

    if (countEl) {
        countEl.textContent = `${visible} produto(s) encontrado(s)`;
    }

    const searchInput = document.querySelector('[data-shop-search]');
    if (searchInput && search !== '') {
        searchInput.value = params.get('q') || '';
    }

    const categorySelect = document.querySelector('[data-shop-category]');
    if (categorySelect && category !== '') {
        categorySelect.value = params.get('categoria') || '';
    }
});
