<?php $pageTitle = 'OEE Dashboard'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-chart-line me-2"></i>OEE Dashboard</h4>
        <?php if (canCreate('efficiency')): ?>
        <a href="?route=efficiency/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Record Data</a>
        <?php endif; ?>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-bg-primary">
                <div class="card-body text-center">
                    <h3><?php echo sanitize($avgOEE ?? '0'); ?>%</h3>
                    <small>Average OEE %</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($bestMachine ?? 'N/A'); ?></h5>
                    <small>Best Machine</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($worstMachine ?? 'N/A'); ?></h5>
                    <small>Worst Machine</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body text-center">
                    <h5><?php echo sanitize($totalRecords ?? 0); ?></h5>
                    <small>Total Records</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">OEE by Machine (Last 30 Days)</div>
        <div class="card-body">
            <canvas id="oeeChart" height="100"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Efficiency Records</div>
        <div class="card-body">
            <table id="dataTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>EfficiencyID</th>
                        <th>Date</th>
                        <th>Shift</th>
                        <th>Machine</th>
                        <th>Availability %</th>
                        <th>Performance %</th>
                        <th>Quality %</th>
                        <th>OEE %</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($efficiencies)): ?>
                        <?php foreach ($efficiencies as $row): ?>
                            <?php $oee = ($row['AvailabilityRate'] ?? 0) * ($row['PerformanceRate'] ?? 0) * ($row['QualityRate'] ?? 0) / 10000; ?>
                            <tr>
                                <td><?php echo sanitize($row['EfficiencyID']); ?></td>
                                <td><?php echo sanitize($row['Date']); ?></td>
                                <td><?php echo sanitize($row['Shift']); ?></td>
                                <td><?php echo sanitize($row['MachineName']); ?></td>
                                <td><?php echo sanitize($row['AvailabilityRate']); ?>%</td>
                                <td><?php echo sanitize($row['PerformanceRate']); ?>%</td>
                                <td><?php echo sanitize($row['QualityRate']); ?>%</td>
                                <td>
                                    <?php $oeeBadge = 'success'; if ($oee < 65) $oeeBadge = 'danger'; elseif ($oee < 85) $oeeBadge = 'warning'; ?>
                                    <span class="badge bg-<?php echo $oeeBadge; ?>"><?php echo number_format($oee, 1); ?>%</span>
                                </td>
                                <td>
                                    <?php if (canEdit('efficiency')): ?>
                                    <a href="?route=efficiency/edit&id=<?php echo sanitize($row['EfficiencyID']); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="?route=efficiency/delete&id=<?php echo sanitize($row['EfficiencyID']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')"><i class="fas fa-trash"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('oeeChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartLabels ?? []); ?>,
                datasets: [{
                    label: 'OEE %',
                    data: <?php echo json_encode($chartData ?? []); ?>,
                    backgroundColor: <?php echo json_encode($chartColors ?? []); ?>,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
    }
});
</script>
