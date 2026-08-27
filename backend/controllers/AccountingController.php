<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/WorkerModel.php';

class AccountingController extends Controller
{
    private WorkerModel $workers;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'accounting';
        $this->workers  = new WorkerModel();
    }

    public function index(): void
    {
        $this->requireCan('accounting');
        $db = getDb();

        $month = (int)($this->getInput('month') ?: date('n'));
        $year  = (int)($this->getInput('year')  ?: date('Y'));
        if ($month < 1 || $month > 12) $month = (int)date('n');

        $monthNames = ['', 'January','February','March','April','May','June','July','August','September','October','November','December'];

        $periodStats = $db->prepare(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN Status='Paid' THEN 1 ELSE 0 END) AS paid,
                SUM(CASE WHEN Status='Unpaid' THEN 1 ELSE 0 END) AS unpaid,
                SUM(CASE WHEN Status='Paid' THEN NetPay ELSE 0 END) AS paidAmount,
                SUM(CASE WHEN Status='Unpaid' THEN NetPay ELSE 0 END) AS unpaidAmount,
                SUM(CASE WHEN Status='Paid' THEN Allowances ELSE 0 END) AS totalAllowances,
                SUM(CASE WHEN Status='Paid' THEN Deductions ELSE 0 END) AS totalDeductions
             FROM payroll WHERE PeriodMonth = ? AND PeriodYear = ?");
        $periodStats->execute([$month, $year]);
        $periodStats = $periodStats->fetch();

        $methodBreakdown = $db->prepare(
            "SELECT PaymentMethod, COUNT(*) AS cnt, SUM(NetPay) AS total
             FROM payroll
             WHERE PeriodMonth = ? AND PeriodYear = ? AND Status = 'Paid'
             GROUP BY PaymentMethod ORDER BY total DESC");
        $methodBreakdown->execute([$month, $year]);
        $methodBreakdown = $methodBreakdown->fetchAll();

        $trend = $db->prepare(
            "SELECT PeriodMonth, PeriodYear,
                    SUM(CASE WHEN Status='Paid' THEN NetPay ELSE 0 END) AS paid,
                    SUM(CASE WHEN Status='Unpaid' THEN NetPay ELSE 0 END) AS unpaid
             FROM payroll
             WHERE (PeriodYear > ? OR (PeriodYear = ? AND PeriodMonth >= ? - 5))
               AND (PeriodYear < ? OR (PeriodYear = ? AND PeriodMonth <= ?))
             GROUP BY PeriodYear, PeriodMonth
             ORDER BY PeriodYear ASC, PeriodMonth ASC
             LIMIT 6");
        $trend->execute([$year, $year, $month, $year, $year, $month]);
        $trend = $trend->fetchAll();

        $staffSummary = $db->prepare(
            "SELECT p.StaffID, s.FirstName, s.LastName, s.Position, s.MonthlySalary AS BaseSalary,
                    p.NetPay, p.Allowances, p.Deductions, p.Status, p.PaymentMethod, p.PaymentDate
             FROM payroll p
             JOIN staff s ON p.StaffID = s.StaffID
             WHERE p.PeriodMonth = ? AND p.PeriodYear = ?
             ORDER BY s.LastName ASC, s.FirstName ASC");
        $staffSummary->execute([$month, $year]);
        $staffSummary = $staffSummary->fetchAll();

        $ytd = $db->prepare(
            "SELECT
                SUM(CASE WHEN Status='Paid' THEN NetPay ELSE 0 END) AS totalPaid,
                COUNT(DISTINCT CONCAT(PeriodYear,'-',LPAD(PeriodMonth,2,'0'))) AS monthsProcessed
             FROM payroll WHERE PeriodYear = ?");
        $ytd->execute([$year]);
        $ytd = $ytd->fetch();

        $this->render('index', [
            'month'           => $month,
            'year'            => $year,
            'monthName'       => $monthNames[$month],
            'periodStats'     => $periodStats,
            'methodBreakdown' => $methodBreakdown,
            'trend'           => $trend,
            'staffSummary'    => $staffSummary,
            'ytd'             => $ytd,
        ]);
    }

    public function calculator(): void
    {
        $this->requireCan('accounting');
        $this->render('calculator', []);
    }

    public function printReport(): void
    {
        $this->requireCan('accounting');
        $db = getDb();
        $month = (int)($this->getInput('month') ?: date('n'));
        $year  = (int)($this->getInput('year')  ?: date('Y'));
        $monthNames = ['', 'January','February','March','April','May','June','July','August','September','October','November','December'];

        $staffSummary = $db->prepare(
            "SELECT p.StaffID, s.FirstName, s.LastName, s.Position, s.MonthlySalary AS BaseSalary,
                    p.NetPay, p.Allowances, p.Deductions, p.Status, p.PaymentMethod, p.PaymentDate
             FROM payroll p JOIN staff s ON p.StaffID = s.StaffID
             WHERE p.PeriodMonth = ? AND p.PeriodYear = ?
             ORDER BY s.LastName ASC, s.FirstName ASC");
        $staffSummary->execute([$month, $year]);
        $staffSummary = $staffSummary->fetchAll();

        $periodStats = $db->prepare(
            "SELECT COUNT(*) AS total, SUM(CASE WHEN Status='Paid' THEN 1 ELSE 0 END) AS paid,
                    SUM(CASE WHEN Status='Unpaid' THEN 1 ELSE 0 END) AS unpaid,
                    SUM(CASE WHEN Status='Paid' THEN NetPay ELSE 0 END) AS paidAmount,
                    SUM(CASE WHEN Status='Unpaid' THEN NetPay ELSE 0 END) AS unpaidAmount
             FROM payroll WHERE PeriodMonth = ? AND PeriodYear = ?");
        $periodStats->execute([$month, $year]);
        $periodStats = $periodStats->fetch();

        $this->renderPrint('report_print', [
            'month'         => $month,
            'year'          => $year,
            'monthName'     => $monthNames[$month],
            'staffSummary'  => $staffSummary,
            'periodStats'   => $periodStats,
        ], "Accounting Report — {$monthNames[$month]} {$year}");
    }
}
