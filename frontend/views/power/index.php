<?php $pageTitle = 'Power Management'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-lightning me-2"></i><?= $pageTitle ?></h5>
    <div>
        <a href="?route=power/usage/form" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> Add Usage</a>
        <a href="?route=power/generator/form" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Generator Log</a>
    </div>
</div>

<ul class="nav nav-tabs mb-3" id="powerTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="usage-tab" data-bs-toggle="tab" data-bs-target="#usage" type="button"><i class="bi bi-graph-up me-1"></i>Usage Records</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="generator-tab" data-bs-toggle="tab" data-bs-target="#generator" type="button"><i class="bi bi-fuel-pump me-1"></i>Generator Logs</button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="usage">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="dataTable" class="table table-hover align-middle">
                    <thead class="table-light"><tr><th>ID</th><th>Date</th><th>Source</th><th>Consumption (kWh)</th><th>Cost ($)</th><th>Notes</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php if (isset($usageRecords)): foreach ($usageRecords as $u): ?>
                        <tr>
                            <td><?= sanitize($u['PowerUsageID'] ?? $u['power_usage_id'] ?? '') ?></td>
                            <td><?= sanitize($u['Date'] ?? '') ?></td>
                            <td><span class="badge bg-info bg-opacity-10 text-info"><?= sanitize($u['Source'] ?? '') ?></span></td>
                            <td class="fw-semibold"><?= number_format((float)($u['ConsumptionKWh'] ?? 0), 1) ?></td>
                            <td>$<?= number_format((float)($u['Cost'] ?? 0), 2) ?></td>
                            <td><small class="text-muted"><?= sanitize(substr($u['Notes'] ?? '', 0, 50)) ?></small></td>
                            <td>
                                <a href="?route=power/usage/edit&id=<?= urlencode($u['PowerUsageID'] ?? $u['power_usage_id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="?route=power/usage/delete&id=<?= urlencode($u['PowerUsageID'] ?? $u['power_usage_id']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this record?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="generator">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="genDataTable" class="table table-hover align-middle">
                    <thead class="table-light"><tr><th>Log ID</th><th>Date</th><th>Start</th><th>End</th><th>Runtime (hrs)</th><th>Fuel Used</th><th>Reason</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php if (isset($generatorLogs)): foreach ($generatorLogs as $l): ?>
                        <tr>
                            <td><?= sanitize($l['LogID'] ?? $l['log_id'] ?? '') ?></td>
                            <td><?= sanitize($l['Date'] ?? '') ?></td>
                            <td><?= sanitize($l['StartTime'] ?? $l['start_time'] ?? '') ?></td>
                            <td><?= sanitize($l['EndTime'] ?? $l['end_time'] ?? '') ?></td>
                            <td><?= number_format((float)($l['RuntimeHrs'] ?? 0), 1) ?></td>
                            <td><?= number_format((float)($l['FuelUsed'] ?? 0), 1) ?> <?= sanitize($l['FuelUnit'] ?? 'L') ?></td>
                            <td><?= sanitize($l['Reason'] ?? '') ?></td>
                            <td>
                                <a href="?route=power/generator/edit&id=<?= urlencode($l['LogID'] ?? $l['log_id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="?route=power/generator/delete&id=<?= urlencode($l['LogID'] ?? $l['log_id']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this log?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#genDataTable').DataTable({ order: [], pageLength: 25, language: { search: '', searchPlaceholder: 'Search...' }, responsive: true });
    }
});
</script>
