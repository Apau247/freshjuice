<?php $pageTitle = 'Quality Inspections'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-check-circle me-2"></i><?= $pageTitle ?></h5>
    <?php if (canCreate('quality')): ?>
    <a href="?route=quality/create" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Inspection</a>
    <?php endif; ?>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>ID</th><th>Type</th><th>Batch</th><th>Flavour</th><th>Date</th><th>Result</th><th>Inspector</th><th>Defects</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($inspections as $i): ?>
                <tr>
                    <td><?= sanitize($i['InspectionID']) ?></td>
                    <td><span class="badge bg-info bg-opacity-10 text-info"><?= sanitize($i['InspectionType'] ?? $i['inspection_type'] ?? '') ?></span></td>
                    <td><?= sanitize($i['BatchNumber'] ?? $i['batch_number'] ?? '') ?></td>
                    <td><?= sanitize($i['Flavour'] ?? '') ?></td>
                    <td><?= sanitize($i['InspectionDate'] ?? $i['inspection_date'] ?? '') ?></td>
                    <td>
                        <?php $r = $i['Result'] ?? ''; $map = ['Pass'=>'success','Fail'=>'danger','Pending'=>'warning']; ?>
                        <span class="badge bg-<?= $map[$r] ?? 'secondary' ?>"><?= sanitize($r) ?></span>
                    </td>
                    <td><?= sanitize($i['InspectorName'] ?? $i['inspector_name'] ?? '') ?></td>
                    <td><small class="text-muted"><?= sanitize(substr($i['DefectsFound'] ?? $i['defects_found'] ?? '', 0, 50)) ?></small></td>
                    <td>
                        <a href="?route=quality/edit&id=<?= urlencode($i['InspectionID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=quality/delete&id=<?= urlencode($i['InspectionID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this inspection?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
