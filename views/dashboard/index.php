<?php $pageTitle = 'Dashboard'; ?>
<?php if (!isset($stats)) $stats = []; ?>
<?php if (!isset($recentBatches)) $recentBatches = []; ?>
<?php if (!isset($recentOrders)) $recentOrders = []; ?>
<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="fs-1 text-success"><i class="bi bi-boxes"></i></div>
                <h5 class="fw-bold mt-2"><?= number_format($stats['rawStock'] ?? 0, 1) ?></h5>
                <small class="text-muted">Total Raw Stock</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="fs-1 text-primary"><i class="bi bi-gear-wide-connected"></i></div>
                <h5 class="fw-bold mt-2"><?= $stats['activeBatches'] ?? 0 ?></h5>
                <small class="text-muted">Active Batches</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="fs-1 text-warning"><i class="bi bi-clock-history"></i></div>
                <h5 class="fw-bold mt-2"><?= $stats['pendingOrders'] ?? 0 ?></h5>
                <small class="text-muted">Pending Orders</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="fs-1 text-info"><i class="bi bi-cup-straw"></i></div>
                <h5 class="fw-bold mt-2"><?= number_format($stats['totalFG'] ?? 0, 1) ?></h5>
                <small class="text-muted">Finished Goods</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="fs-1 text-danger"><i class="bi bi-trash"></i></div>
                <h5 class="fw-bold mt-2"><?= ($stats['wastePct'] ?? 0) ?>%</h5>
                <small class="text-muted">Waste %</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="fs-1 text-success"><i class="bi bi-currency-dollar"></i></div>
                <h5 class="fw-bold mt-2">$<?= number_format($stats['totalRevenue'] ?? 0, 0) ?></h5>
                <small class="text-muted">Revenue</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="fs-1 text-primary me-3"><i class="bi bi-graph-up"></i></div>
                <div>
                    <h5 class="fw-bold mb-0"><?= number_format($stats['avgOEE'] ?? 0, 1) ?>%</h5>
                    <small class="text-muted">Avg OEE (30d)</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="fs-1 text-danger me-3"><i class="bi bi-exclamation-triangle"></i></div>
                <div>
                    <h5 class="fw-bold mb-0"><?= $stats['accidentsOpen'] ?? 0 ?></h5>
                    <small class="text-muted">Open Incidents</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="fs-1 text-warning me-3"><i class="bi bi-lightbulb"></i></div>
                <div>
                    <h5 class="fw-bold mb-0"><?= $stats['capaOpen'] ?? 0 ?> / <?= $stats['capaOverdue'] ?? 0 ?></h5>
                    <small class="text-muted">CAPA Open / Overdue</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="fs-1 text-info me-3"><i class="bi bi-file-earmark-check"></i></div>
                <div>
                    <h5 class="fw-bold mb-0"><?= $stats['permitsExpiring'] ?? 0 ?> / <?= $stats['trainingPending'] ?? 0 ?></h5>
                    <small class="text-muted">Permits Expiring / Training Pending</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="fs-1 text-warning me-3"><i class="bi bi-award"></i></div>
                <div>
                    <h5 class="fw-bold mb-0"><?= $stats['certsExpiring'] ?? 0 ?></h5>
                    <small class="text-muted">Expiring Certifications</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="fs-1 text-info me-3"><i class="bi bi-droplet"></i></div>
                <div>
                    <h5 class="fw-bold mb-0"><?= number_format($stats['waterTotal'] ?? 0, 0) ?>L</h5>
                    <small class="text-muted">Water Usage</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="fs-1 text-warning me-3"><i class="bi bi-lightning"></i></div>
                <div>
                    <h5 class="fw-bold mb-0"><?= number_format($stats['powerTotal'] ?? 0, 0) ?> kWh</h5>
                    <small class="text-muted">Power Consumption</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="fs-1 text-danger me-3"><i class="bi bi-cpu"></i></div>
                <div>
                    <h5 class="fw-bold mb-0"><?= $stats['mDown'] ?? 0 ?> / <?= $stats['mTotal'] ?? 0 ?></h5>
                    <small class="text-muted">Machines Down</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-bar-chart-fill me-2 text-success"></i>Production by Flavour</div>
            <div class="card-body"><canvas id="flavourChart" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-graph-up me-2 text-primary"></i>Monthly Production</div>
            <div class="card-body"><canvas id="monthlyChart" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-pie-chart-fill me-2 text-danger"></i>Waste by Type</div>
            <div class="card-body"><canvas id="wasteChart" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold"><i class="bi bi-gear me-2 text-info"></i>OEE by Machine</div>
            <div class="card-body"><canvas id="oeeChart" height="180"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-gear me-2"></i>Recent Batches</span>
                <a href="?route=production" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0 no-datatable">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Batch</th><th>Flavour</th><th>Qty</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentBatches as $b): ?>
                        <tr>
                            <td><?= sanitize($b['BatchNumber'] ?? $b['BatchID'] ?? '') ?></td>
                            <td><?= sanitize($b['Flavour'] ?? '') ?></td>
                            <td><?= number_format((float)($b['Quantity'] ?? 0), 1) ?></td>
                            <td>
                                <?php $s = $b['Status'] ?? ''; $map = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Rejected'=>'danger','Cancelled'=>'secondary']; ?>
                                <span class="badge bg-<?= $map[$s] ?? 'secondary' ?>"><?= sanitize($s) ?></span>
                            </td>
                            <td><?= sanitize($b['ProductionDate'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; if (empty($recentBatches)): ?><tr><td colspan="5" class="text-center text-muted">No batches found</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cart me-2"></i>Recent Orders</span>
                <a href="?route=sales" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0 no-datatable">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Order#</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentOrders as $o): ?>
                        <tr>
                            <td><?= sanitize($o['OrderID'] ?? '') ?></td>
                            <td><?= sanitize($o['CustomerName'] ?? '') ?></td>
                            <td>$<?= number_format((float)($o['TotalAmount'] ?? 0), 2) ?></td>
                            <td>
                                <?php $s = $o['Status'] ?? ''; $map = ['Pending'=>'warning','Processing'=>'info','Completed'=>'success','Cancelled'=>'secondary']; ?>
                                <span class="badge bg-<?= $map[$s] ?? 'secondary' ?>"><?= sanitize($s) ?></span>
                            </td>
                            <td><?= sanitize($o['OrderDate'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; if (empty($recentOrders)): ?><tr><td colspan="5" class="text-center text-muted">No orders found</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-shield-check me-2 text-warning"></i>Recent Safety Inspections</span>
                <a href="?route=safety" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0 no-datatable">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Date</th><th>Area</th><th>Type</th><th>Level</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentSafetyInspections ?? [] as $s): ?>
                        <tr>
                            <td><?= sanitize($s['InspectionDate'] ?? '') ?></td>
                            <td><?= sanitize($s['Area'] ?? '') ?></td>
                            <td><?= sanitize($s['InspectionType'] ?? '') ?></td>
                            <td><?php $hl = $s['HazardLevel'] ?? ''; $cmap = ['Low'=>'success','Medium'=>'warning','High'=>'danger','Critical'=>'dark']; ?><span class="badge bg-<?= $cmap[$hl] ?? 'secondary' ?>"><?= sanitize($hl) ?></span></td>
                            <td><?php $st = $s['Status'] ?? ''; $smap = ['Open'=>'danger','In Progress'=>'warning','Closed'=>'success']; ?><span class="badge bg-<?= $smap[$st] ?? 'secondary' ?>"><?= sanitize($st) ?></span></td>
                        </tr>
                        <?php endforeach; if (empty($recentSafetyInspections)): ?><tr><td colspan="5" class="text-center text-muted">No inspections found</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-lightbulb me-2 text-info"></i>Open CAPA Initiatives</span>
                <a href="?route=improvement" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0 no-datatable">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Title</th><th>Category</th><th>Target</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentImprovements ?? [] as $i): ?>
                        <tr>
                            <td><?= sanitize($i['Title'] ?? '') ?></td>
                            <td><?= sanitize($i['Category'] ?? '') ?></td>
                            <td><?= sanitize($i['TargetDate'] ?? '') ?></td>
                            <td><?php $is = $i['Status'] ?? ''; $imap = ['Proposed'=>'secondary','Approved'=>'primary','In Progress'=>'warning','Completed'=>'success','Cancelled'=>'danger']; ?><span class="badge bg-<?= $imap[$is] ?? 'secondary' ?>"><?= sanitize($is) ?></span></td>
                        </tr>
                        <?php endforeach; if (empty($recentImprovements)): ?><tr><td colspan="4" class="text-center text-muted">No initiatives found</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($productionByFlavour)): ?>
    new Chart(document.getElementById('flavourChart'), {
        type: 'bar',
        data: {
            labels: [<?php foreach ($productionByFlavour as $f): ?>'<?= addslashes($f['Flavour']) ?>',<?php endforeach; ?>],
            datasets: [{ label: 'Quantity', data: [<?php foreach ($productionByFlavour as $f): ?><?= $f['Total'] ?>,<?php endforeach; ?>], backgroundColor: 'rgba(25,135,84,0.7)', borderColor: '#198754', borderWidth: 1 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
    <?php endif; ?>
    <?php if (!empty($monthlyProduction)): $months = array_reverse($monthlyProduction); ?>
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: [<?php foreach ($months as $m): ?>'<?= $m['Month'] ?>',<?php endforeach; ?>],
            datasets: [{ label: 'Production', data: [<?php foreach ($months as $m): ?><?= $m['Total'] ?>,<?php endforeach; ?>], borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,0.1)', fill: true, tension: 0.3 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
    <?php endif; ?>
    <?php 
    $wasteByType = $stats['wasteByType'] ?? [];
    if (empty($wasteByType) && !empty($stats['totalWaste'])): 
        $wasteByType = [['WasteType'=>'Production','Total'=>$stats['totalWaste']]];
    endif;
    if (!empty($wasteByType)): 
    ?>
    new Chart(document.getElementById('wasteChart'), {
        type: 'doughnut',
        data: {
            labels: [<?php foreach ($wasteByType as $w): ?>'<?= addslashes($w['WasteType']) ?>',<?php endforeach; ?>],
            datasets: [{ data: [<?php foreach ($wasteByType as $w): ?><?= $w['Total'] ?>,<?php endforeach; ?>], backgroundColor: ['#dc3545','#fd7e14','#ffc107','#198754','#0dcaf0','#6f42c1'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
    <?php endif; ?>
    <?php if (!empty($oeeByMachine)): ?>
    new Chart(document.getElementById('oeeChart'), {
        type: 'bar',
        data: {
            labels: [<?php foreach ($oeeByMachine as $o): ?>'<?= addslashes($o['MachineName']) ?>',<?php endforeach; ?>],
            datasets: [{ label: 'OEE %', data: [<?php foreach ($oeeByMachine as $o): ?><?= number_format((float)$o['AvgOEE'], 1) ?>,<?php endforeach; ?>], backgroundColor: 'rgba(13,202,240,0.7)', borderColor: '#0dcaf0', borderWidth: 1 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100 } } }
    });
    <?php endif; ?>
});
</script>
