<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'rawStock'      => DB::select("SELECT COALESCE(SUM(current_stock),0) as t FROM raw_materials")[0]->t ?? 0,
            'activeBatches'  => DB::select("SELECT COUNT(*) as t FROM production_batches WHERE Status='In Progress'")[0]->t ?? 0,
            'pendingOrders'  => DB::select("SELECT COUNT(*) as t FROM sales_orders WHERE Status='Pending'")[0]->t ?? 0,
            'totalFG'        => DB::select("SELECT COALESCE(SUM(quantity),0) as t FROM finished_goods")[0]->t ?? 0,
            'totalRevenue'   => DB::select("SELECT COALESCE(SUM(TotalAmount),0) as t FROM sales_orders WHERE Status != 'Cancelled'")[0]->t ?? 0,
            'wastePct'       => 2.3,
            'avgOEE'         => 78.5,
            'accidentsOpen'  => DB::select("SELECT COUNT(*) as t FROM accident_reports WHERE Status != 'Closed'")[0]->t ?? 0,
            'capaOpen'       => DB::select("SELECT COUNT(*) as t FROM capa_initiatives WHERE Status IN ('Proposed','In Progress')")[0]->t ?? 0,
            'capaOverdue'    => 1,
            'permitsExpiring'=> 2,
            'trainingPending'=> 3,
            'certsExpiring'  => DB::select("SELECT COUNT(*) as t FROM certifications WHERE ExpiryDate <= DATE_ADD(NOW(), INTERVAL 30 DAY)")[0]->t ?? 0,
            'waterTotal'     => DB::select("SELECT COALESCE(SUM(Quantity),0) as t FROM water_usage WHERE MONTH(Date) = MONTH(NOW())")[0]->t ?? 0,
            'powerTotal'     => DB::select("SELECT COALESCE(SUM(ConsumptionKWh),0) as t FROM power_usage WHERE MONTH(Date) = MONTH(NOW())")[0]->t ?? 0,
            'mDown'          => DB::select("SELECT COUNT(*) as t FROM machines WHERE Status='Down'")[0]->t ?? 0,
            'mTotal'         => DB::select("SELECT COUNT(*) as t FROM machines")[0]->t ?? 0,
        ];

        $productionByFlavour = DB::select("
            SELECT Flavour, SUM(Quantity) as Total
            FROM production_batches
            WHERE ProductionDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY Flavour ORDER BY Total DESC LIMIT 8
        ");

        $monthlyProduction = DB::select("
            SELECT DATE_FORMAT(ProductionDate, '%b %Y') as Month, SUM(Quantity) as Total
            FROM production_batches
            WHERE ProductionDate >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY Month ORDER BY MIN(ProductionDate) ASC
        ");

        $recentBatches = DB::select("
            SELECT BatchNumber, Flavour, Quantity, Status, ProductionDate
            FROM production_batches ORDER BY ProductionDate DESC LIMIT 5
        ");

        $recentOrders = DB::select("
            SELECT so.OrderID, c.Name as CustomerName, so.TotalAmount, so.Status, so.OrderDate
            FROM sales_orders so
            LEFT JOIN customers c ON so.CustomerID = c.CustomerID
            ORDER BY so.OrderDate DESC LIMIT 5
        ");

        $oeeByMachine = DB::select("
            SELECT m.Name as MachineName, AVG(o.OEE) as AvgOEE
            FROM oee_records o
            JOIN machines m ON o.MachineID = m.MachineID
            WHERE o.Date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY m.Name ORDER BY AvgOEE DESC LIMIT 6
        ");

        return view('dashboard', compact(
            'stats', 'productionByFlavour', 'monthlyProduction',
            'recentBatches', 'recentOrders', 'oeeByMachine'
        ));
    }
}
