-- ================================================================
--  PRODUCT PRICING MIGRATION
--  Adds `product_prices` -- one selling price per product flavour.
--  The Sales Officer / Factory Manager / Admin maintain prices here;
--  the POS cart and reports use them as the default unit price.
--
--  Idempotent: safe to run more than once.
-- ================================================================

CREATE TABLE IF NOT EXISTS product_prices (
    Flavour     VARCHAR(100)   PRIMARY KEY,
    UnitPrice   DECIMAL(12,2)  NOT NULL DEFAULT 0.00,
    UpdatedBy   VARCHAR(50)    DEFAULT NULL,
    updated_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (UpdatedBy) REFERENCES users(UserID) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;
