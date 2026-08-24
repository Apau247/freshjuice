<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/PricingModel.php';

/**
 * Product price settings: one screen where the Sales Officer / Factory
 * Manager / Administrator set the default selling price (GH₵ per unit) of
 * every product flavour. The POS sales cart pre-fills from these prices.
 */
class PricingController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new PricingModel();
        $this->viewPath = 'pricing';
    }

    public function index(): void {
        $this->render('index', [
            'catalogue' => $this->model->getCatalogue(),
            'canEdit'   => canEdit('pricing'),
        ]);
    }

    /** Bulk save: one POST carries every editable price on the page. */
    public function save(): void {
        $this->requireCanEdit('pricing');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect('prices'); return; }

        $prices = json_decode($this->getInput('prices', ''), true);
        if (!is_array($prices)) $prices = [];

        $saved = 0; $skipped = 0;
        foreach ($prices as $flavour => $amt) {
            $flavour = trim((string)$flavour);
            $price = (float)$amt;
            if ($flavour === '' || !is_finite($price) || $price < 0 || $price > 100000) { $skipped++; continue; }
            $this->model->setPrice($flavour, $price, $_SESSION['user_id'] ?? null);
            $saved++;
        }

        logAudit($_SESSION['user_id'] ?? null, 'UPDATE', 'Pricing', null, "Updated {$saved} product price(s)");
        setFlash($saved > 0 ? 'success' : 'error',
            $saved > 0 ? "Saved {$saved} product price(s)." . ($skipped ? " {$skipped} invalid value(s) skipped." : '')
                       : 'No valid prices to save.');
        $this->redirect('prices');
    }
}
