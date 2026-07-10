<?php $pageTitle = 'Water Management'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-droplet me-2"></i><?= $pageTitle ?></h5>
    <div>
        <a href="?route=water/usage/form" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> Add Usage</a>
        <a href="?route=water/test/form" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Test</a>
    </div>
</div>

<ul class="nav nav-tabs mb-3" id="waterTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="usage-tab" data-bs-toggle="tab" data-bs-target="#usage" type="button"><i class="bi bi-water me-1"></i>Usage Records</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="quality-tab" data-bs-toggle="tab" data-bs-target="#quality" type="button"><i class="bi bi-check2-square me-1"></i>Quality Tests</button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="usage">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="dataTable" class="table table-hover align-middle">
                    <thead class="table-light"><tr><th>ID</th><th>Date</th><th>Usage Type</th><th>Quantity</th><th>Unit</th><th>Purpose</th><th>Recorded By</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php if (isset($usageRecords)): foreach ($usageRecords as $u): ?>
                        <tr>
                            <td><?= sanitize($u['WaterUsageID'] ?? $u['water_usage_id'] ?? '') ?></td>
                            <td><?= sanitize($u['Date'] ?? '') ?></td>
                            <td><span class="badge bg-info bg-opacity-10 text-info"><?= sanitize($u['UsageType'] ?? '') ?></span></td>
                            <td class="fw-semibold"><?= number_format((float)($u['Quantity'] ?? 0), 1) ?></td>
                            <td><?= sanitize($u['Unit'] ?? 'litres') ?></td>
                            <td><?= sanitize($u['Purpose'] ?? '') ?></td>
                            <td><?= sanitize($u['RecordedByName'] ?? $u['recorded_by_name'] ?? '') ?></td>
                            <td>
                                <a href="?route=water/usage/edit&id=<?= urlencode($u['WaterUsageID'] ?? $u['water_usage_id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="?route=water/usage/delete&id=<?= urlencode($u['WaterUsageID'] ?? $u['water_usage_id']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this record?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="quality">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table id="qualityDataTable" class="table table-hover align-middle">
                    <thead class="table-light"><tr><th>ID</th><th>Date</th><th>Test Type</th><th>pH</th><th>Turbidity</th><th>TDS</th><th>Chlorine</th><th>Bacteria</th><th>Result</th><th>Tester</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php if (isset($qualityTests)): foreach ($qualityTests as $t): ?>
                        <tr>
                            <td><?= sanitize($t['WaterTestID'] ?? $t['water_test_id'] ?? '') ?></td>
                            <td><?= sanitize($t['TestDate'] ?? '') ?></td>
                            <td><?= sanitize($t['TestType'] ?? '') ?></td>
                            <td><?= sanitize($t['pH_Level'] ?? $t['ph_level'] ?? '') ?></td>
                            <td><?= sanitize($t['Turbidity'] ?? '') ?></td>
                            <td><?= sanitize($t['TDS'] ?? '') ?></td>
                            <td><?= sanitize($t['Chlorine'] ?? '') ?></td>
                            <td><?= sanitize($t['BacteriaCount'] ?? '') ?></td>
                            <td>
                                <?php $r = $t['Result'] ?? ''; $map = ['Pass'=>'success','Fail'=>'danger','Pending'=>'warning']; ?>
                                <span class="badge bg-<?= $map[$r] ?? 'secondary' ?>"><?= sanitize($r) ?></span>
                            </td>
                            <td><?= sanitize($t['TestedByName'] ?? $t['tested_by_name'] ?? '') ?></td>
                            <td>
                                <a href="?route=water/test/edit&id=<?= urlencode($t['WaterTestID'] ?? $t['water_test_id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="?route=water/test/delete&id=<?= urlencode($t['WaterTestID'] ?? $t['water_test_id']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this test?')"><i class="bi bi-trash"></i></a>
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
        $('#qualityDataTable').DataTable({ order: [], pageLength: 25, language: { search: '', searchPlaceholder: 'Search...' }, responsive: true });
    }
});
</script>
