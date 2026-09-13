(function () {
    'use strict';

    function formatPrice(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value);
    }

    function normalizePhone(phone) {
        return String(phone || '').replace(/\D/g, '');
    }

    function resolveUnitPrice(quantity, basePrice, tiers) {
        quantity = Math.max(1, parseInt(String(quantity), 10) || 1);
        basePrice = parseFloat(String(basePrice)) || 0;

        if (!Array.isArray(tiers) || tiers.length === 0) {
            return basePrice;
        }

        for (var i = 0; i < tiers.length; i++) {
            var tier = tiers[i];
            var min = parseInt(String(tier.qty_min), 10) || 0;
            var maxRaw = tier.qty_max;
            var max = maxRaw === null || maxRaw === undefined || maxRaw === '' ? null : parseInt(String(maxRaw), 10);

            if (quantity < min) {
                continue;
            }
            if (max === null || quantity <= max) {
                return parseFloat(String(tier.unit_price)) || basePrice;
            }
        }

        return basePrice;
    }

    function buildWhatsAppUrl(phone, productName, quantity, unitPrice) {
        var total = unitPrice * quantity;
        var message =
            'Olá! Vim pelo site For Champions e gostaria de comprar:\n\n' +
            '*' +
            productName +
            '*\n' +
            'Quantidade: ' +
            quantity +
            '\n' +
            'Preço unitário: ' +
            formatPrice(unitPrice) +
            '\n' +
            'Total estimado: ' +
            formatPrice(total) +
            '\n\n' +
            'Pode me ajudar com tamanho e personalização?';

        return 'https://wa.me/' + normalizePhone(phone) + '?text=' + encodeURIComponent(message);
    }

    function initQuantityPricing(root) {
        var qtyInput = root.querySelector('[data-quantity-input]');
        var priceEl = root.querySelector('[data-unit-price-display]');
        var totalEl = root.querySelector('[data-total-price-display]');
        var waBtn = root.querySelector('[data-product-wa-button]');
        var tierRows = root.querySelectorAll('[data-tier-row]');

        if (!qtyInput || !waBtn) {
            return;
        }

        var productName = root.getAttribute('data-product-name') || '';
        var phone = root.getAttribute('data-whatsapp-phone') || '';
        var basePrice = root.getAttribute('data-base-price') || '0';
        var tiers = [];

        try {
            tiers = JSON.parse(root.getAttribute('data-tiers') || '[]');
        } catch (e) {
            tiers = [];
        }

        function sync() {
            var quantity = Math.max(1, parseInt(String(qtyInput.value), 10) || 1);
            qtyInput.value = String(quantity);

            var unitPrice = resolveUnitPrice(quantity, basePrice, tiers);

            if (priceEl) {
                priceEl.textContent = formatPrice(unitPrice);
            }
            if (totalEl) {
                totalEl.textContent = formatPrice(unitPrice * quantity);
            }

            waBtn.setAttribute('href', buildWhatsAppUrl(phone, productName, quantity, unitPrice));

            tierRows.forEach(function (row) {
                var min = parseInt(row.getAttribute('data-qty-min'), 10) || 0;
                var maxAttr = row.getAttribute('data-qty-max');
                var max = maxAttr === '' || maxAttr === 'null' ? null : parseInt(maxAttr, 10);
                var active = quantity >= min && (max === null || quantity <= max);
                row.classList.toggle('is-active', active);
            });
        }

        qtyInput.addEventListener('input', sync);
        qtyInput.addEventListener('change', sync);
        sync();
    }

    document.querySelectorAll('[data-quantity-pricing]').forEach(initQuantityPricing);
})();
