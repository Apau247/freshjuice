<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

/**
 * Default selling price per product flavour. Prices are keyed by flavour
 * (not by batch) because every bottle of the same juice sells at the same
 * price regardless of which production batch it came from.
 */
class PricingModel extends Model {
    protected string $table = 'product_prices';
    protected string $primaryKey = 'Flavour';

    /** Every known product flavour with its price (null when never priced). */
    public function getCatalogue(): array {
        return $this->query(
            "SELECT flavours.Flavour,
                    pp.UnitPrice,
                    pp.updated_at,
                    u.Name AS UpdatedByName,
                    COALESCE(fg_stock.total, 0) AS StockAvailable,
                    MIN(fg.Unit) AS Unit
             FROM (
                 SELECT DISTINCT Flavour FROM finished_goods
                 UNION
                 SELECT Flavour FROM product_prices
             ) AS flavours
             LEFT JOIN product_prices pp ON pp.Flavour = flavours.Flavour
             LEFT JOIN users u ON pp.UpdatedBy = u.UserID
             LEFT JOIN finished_goods fg ON fg.Flavour = flavours.Flavour
             LEFT JOIN (
                 SELECT Flavour, SUM(QuantityAvailable) AS total
                 FROM finished_goods
                 GROUP BY Flavour
             ) AS fg_stock ON fg_stock.Flavour = flavours.Flavour
             GROUP BY flavours.Flavour, pp.UnitPrice, pp.updated_at, u.Name
             ORDER BY flavours.Flavour"
        );
    }

    /** [Flavour => price] map for the POS cart defaults. Unpriced flavours are omitted. */
    public function getPriceMap(): array {
        $rows = $this->query("SELECT Flavour, UnitPrice FROM product_prices WHERE UnitPrice > 0");
        $map = [];
        foreach ($rows as $r) { $map[(string)$r['Flavour']] = (float)$r['UnitPrice']; }
        return $map;
    }

    /** [FG_ID => price] so each finished-goods row knows its flavour's price. */
    public function getPriceMapByFgId(): array {
        $rows = $this->query(
            "SELECT fg.FG_ID, pp.UnitPrice
             FROM finished_goods fg
             JOIN product_prices pp ON pp.Flavour = fg.Flavour
             WHERE pp.UnitPrice > 0"
        );
        $map = [];
        foreach ($rows as $r) { $map[(string)$r['FG_ID']] = (float)$r['UnitPrice']; }
        return $map;
    }

    public function setPrice(string $flavour, float $price, ?string $userId): bool {
        $this->query(
            "INSERT INTO product_prices (Flavour, UnitPrice, UpdatedBy)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE UnitPrice = VALUES(UnitPrice), UpdatedBy = VALUES(UpdatedBy)",
            [$flavour, round($price, 2), $userId]
        );
        return true;
    }
}
