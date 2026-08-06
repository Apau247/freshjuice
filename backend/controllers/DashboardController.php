<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/DashboardModel.php';

class DashboardController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new DashboardModel();
        $this->viewPath = 'dashboard';
    }

    public function index(): void {
        // Only query what this role is allowed to see -- the view hides the
        // matching panels, so fetching the rest would be wasted work and would
        // put data the user has no access to into the page context.
        $data = [
            'stats'                   => $this->model->getStats(),
            'productionByFlavour'     => can('production')  ? $this->model->getProductionByFlavour()   : [],
            'monthlyProduction'       => can('production')  ? $this->model->getMonthlyProduction()     : [],
            'monthlySales'            => can('sales')       ? $this->model->getMonthlySales()          : [],
            'recentBatches'           => can('production')  ? $this->model->getRecentBatches()         : [],
            'recentOrders'            => can('sales')       ? $this->model->getRecentOrders()          : [],
            'oeeByMachine'            => can('efficiency')  ? $this->model->getOEEByMachine()          : [],
            'wasteByType'             => can('waste')       ? $this->model->getWasteByType()           : [],
            'monthlyRevenue'          => can('sales')       ? $this->model->getMonthlyRevenue()        : [],
            'recentSafetyInspections' => can('safety')      ? $this->model->getRecentSafetyInspections(): [],
            'recentImprovements'      => can('improvement') ? $this->model->getRecentImprovements()    : [],
        ];
        $this->render('index', $data);
    }
}