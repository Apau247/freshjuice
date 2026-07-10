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
        $data = [
            'stats' => $this->model->getStats(),
            'productionByFlavour' => $this->model->getProductionByFlavour(),
            'monthlyProduction' => $this->model->getMonthlyProduction(),
            'monthlySales' => $this->model->getMonthlySales(),
            'recentBatches' => $this->model->getRecentBatches(),
            'recentOrders' => $this->model->getRecentOrders(),
            'oeeByMachine' => $this->model->getOEEByMachine(),
            'wasteByType' => $this->model->getWasteByType(),
            'monthlyRevenue' => $this->model->getMonthlyRevenue(),
            'recentSafetyInspections' => $this->model->getRecentSafetyInspections(),
            'recentImprovements' => $this->model->getRecentImprovements()
        ];
        $this->render('index', $data);
    }
}