-- Faixas de preco por quantidade (importadas do WooCommerce Dynamic Pricing)

CREATE TABLE IF NOT EXISTS product_quantity_tiers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    qty_min INT UNSIGNED NOT NULL,
    qty_max INT UNSIGNED DEFAULT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    KEY idx_product_tiers (product_id, sort_order),
    CONSTRAINT fk_tiers_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
